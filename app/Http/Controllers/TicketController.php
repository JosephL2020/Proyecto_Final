<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, Category, AiSuggestion, TicketStatusHistory};
use App\Services\SugerenciasSoporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $q = Ticket::with(['creator','assignee','category'])
            ->when(!$user->isIT(), fn($x) =>
                $x->where(fn($w)=>$w->where('created_by',$user->id)
                                    ->orWhere('assigned_to',$user->id))
            )
            ->when($request->filled('category_id'), fn($x) =>
                $x->where('category_id', $request->integer('category_id'))
            )
            ->when($s = $request->get('s'), fn($x) =>
                $x->where(fn($w)=>$w->where('title','like',"%$s%")
                                    ->orWhere('description','like',"%$s%"))
            )
            ->orderByDesc('created_at');

        $tickets = $q->paginate(10)->appends($request->only('s','category_id'));

        return view('tickets.index', compact('tickets'));
    }

    public function create(){
        $categories = Category::orderBy('name')->get();
        return view('tickets.create', compact('categories'));
    }

  public function store(Request $request, SugerenciasSoporte $sugerencias){
    $data = $request->validate([
        'title'        => 'required|string|max:255',
        'description'  => 'required|string',
        'category_id'  => 'nullable|exists:categories,id',
    ]);

         $data['created_by'] = $request->user()->id;
    $data['priority']   = 'medium'; // ← prioridad por defecto
    $nextId = (Ticket::max('id') ?? 0) + 1;
    $data['code'] = 'TCK-'.str_pad((string)$nextId, 6, '0', STR_PAD_LEFT);

    $ticket = Ticket::create($data);

    TicketStatusHistory::create([
        'ticket_id'   => $ticket->id,
        'from_status' => null,
        'to_status'   => 'open',
        'changed_by'  => $request->user()->id,
    ]);

    $recomendaciones = $sugerencias->get($ticket->title, $ticket->description);
    AiSuggestion::create([
        'ticket_id'   => $ticket->id,
        'suggestions' => $recomendaciones
    ]);

    return redirect()->route('tickets.show', $ticket)->with('ok','Ticket creado');
}

    public function show(Ticket $ticket, SugerenciasSoporte $sugerencias){
        Gate::authorize('view', $ticket);
        $ticket->load(['creator','assignee','category','comments.user','aiSuggestion']);
        $recs = $ticket->aiSuggestion?->suggestions ?: $sugerencias->get($ticket->title, $ticket->description);
        return view('tickets.show', ['ticket'=>$ticket, 'recs'=>$recs]);
    }

    public function edit(Ticket $ticket){
        Gate::authorize('update', $ticket);
        $categories = Category::orderBy('name')->get();

        $limited = auth()->id() === $ticket->assigned_to && !auth()->user()->isManager();
        $allowedStatuses = $limited
            ? ['in_progress','resolved','cancelled']
            : ['open','assigned','in_progress','resolved','closed','cancelled'];

        return view('tickets.edit', compact('ticket','categories','limited','allowedStatuses'));
    }

    public function update(Request $request, Ticket $ticket, SugerenciasSoporte $sugerencias){
        Gate::authorize('update', $ticket);

      $isAssignedLimited = auth()->id() === $ticket->assigned_to && !auth()->user()->isManager();

if ($isAssignedLimited) {
    $data = $request->validate([
        'status' => 'required|in:in_progress,resolved,cancelled',
    ]);
        } else {
            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'nullable|exists:categories,id',
                'priority'    => 'required|in:low,medium,high',
                'status'      => 'required|in:open,assigned,in_progress,resolved,closed,cancelled'
            ]);
        }

        $from = $ticket->status;

        if ($isAssignedLimited) {
            $ticket->update(['status' => $data['status']]);
        } else {
            $ticket->update($data);
        }

        if (!$isAssignedLimited && $ticket->wasChanged(['title','description'])) {
            $recs = $sugerencias->get($ticket->title, $ticket->description);
            AiSuggestion::updateOrCreate(
                ['ticket_id' => $ticket->id],
                ['suggestions' => $recs]
            );
        }

        if ($from !== $ticket->status) {
            TicketStatusHistory::create([
                'ticket_id'   => $ticket->id,
                'from_status' => $from,
                'to_status'   => $ticket->status,
                'changed_by'  => $request->user()->id,
            ]);
            if ($ticket->status === 'resolved') {
                $ticket->resolved_at = now();
                $ticket->save();
            }
        }

        return back()->with('ok','Actualizado');
    }

    public function destroy(Ticket $ticket){
        Gate::authorize('delete', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('ok','Eliminado');
    }

    public function generateAi(Ticket $ticket, SugerenciasSoporte $sugerencias){
        Gate::authorize('view', $ticket);
        $recs = $sugerencias->get($ticket->title, $ticket->description);
        AiSuggestion::updateOrCreate(['ticket_id' => $ticket->id], ['suggestions' => $recs]);
        return response()->json(['ok' => true, 'recs' => $recs]);
    }

    public function rate(Request $request, Ticket $ticket){
        Gate::authorize('view', $ticket);
        if ($request->user()->id !== $ticket->created_by) abort(403);

        $data = $request->validate([
            'rating'          => 'required|integer|min:1|max:5',
            'rating_comment'  => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'rating'         => $data['rating'],
            'rating_comment' => $data['rating_comment'] ?? null,
            'rated_by'       => $request->user()->id,
            'rated_at'       => now(),
        ]);

        return back()->with('ok','¡Gracias por tu calificación!');
    }
}
