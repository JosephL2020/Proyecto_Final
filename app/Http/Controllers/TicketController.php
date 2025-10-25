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
            ->when(!$user->isIT(), fn($x)=>$x->where('created_by',$user->id))
            ->orderByDesc('created_at');

        if ($s = $request->get('s')) {
            $q->where(fn($w)=>$w->where('title','like',"%$s%")
                                ->orWhere('description','like',"%$s%"));
        }

        $tickets = $q->paginate(10);
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
            'priority'     => 'required|in:low,medium,high',
        ]);

        $data['created_by'] = $request->user()->id;
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

    public function show(Ticket $ticket){
        Gate::authorize('view', $ticket);
        $ticket->load(['creator','assignee','category','comments.user','aiSuggestion']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket){
        Gate::authorize('update', $ticket);
        $categories = Category::orderBy('name')->get();
        return view('tickets.edit', compact('ticket','categories'));
    }

    public function update(Request $request, Ticket $ticket){
        Gate::authorize('update', $ticket);
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:open,assigned,in_progress,resolved,closed,cancelled'
        ]);

        $from = $ticket->status;
        $ticket->update($data);

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
        Gate::authorize('update', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('ok','Eliminado');
    }
}
