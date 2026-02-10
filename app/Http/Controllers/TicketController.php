<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, Category, AiSuggestion, TicketStatusHistory, User, TicketAttachment};
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    // ===================== LISTADO =====================
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['assignee','department','subdivision']);

        if ($user) {
            if ($user->isManager()) {
                // ve todo
            } elseif ($user->isDeptManager()) {
                // tickets del departamento que administra
                $deptIds = \App\Models\Department::where('manager_user_id', $user->id)->pluck('id');
                $query->whereIn('department_id', $deptIds);
            } elseif ($user->isDeptSupport()) {
                // tickets de sus subdivisiones (por agent_user_id) o asignados a él
                $subIds = \App\Models\Subdivision::where('agent_user_id', $user->id)->pluck('id');

                $query->where(function ($q) use ($user, $subIds) {
                    $q->where('assigned_to', $user->id)
                      ->orWhereIn('subdivision_id', $subIds);
                });
            } elseif ($user->isIt()) {
                $query->where('assigned_to', $user->id);
            } else {
                $query->where('created_by', $user->id);
            }
        }

        if ($search = $request->input('s')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $tickets = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    // ===================== KANBAN (ARREGLADO PARA DeptManager / DeptSupport) =====================
    public function kanban(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['creator', 'assignee', 'department', 'subdivision']);

        if ($user) {
            if ($user->isManager()) {
                // ve todo
            } elseif ($user->isDeptManager()) {
                // tickets del departamento que administra
                $deptIds = \App\Models\Department::where('manager_user_id', $user->id)->pluck('id');
                $query->whereIn('department_id', $deptIds);
            } elseif ($user->isDeptSupport()) {
                // tickets de sus subdivisiones (por agent_user_id) o asignados a él
                $subIds = \App\Models\Subdivision::where('agent_user_id', $user->id)->pluck('id');

                $query->where(function ($q) use ($user, $subIds) {
                    $q->where('assigned_to', $user->id)
                      ->orWhereIn('subdivision_id', $subIds);
                });
            } elseif ($user->isIt()) {
                $query->where('assigned_to', $user->id);
            } else {
                $query->where('created_by', $user->id);
            }
        }

        // Filtros opcionales (si los usás en la vista)
        if ($search = $request->input('s')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $tickets = $query->orderByDesc('id')->get();

        $columns = [
            'open'        => 'Abiertos',
            'assigned'    => 'Asignados',
            'in_progress' => 'En progreso',
            'resolved'    => 'Resueltos',
            'closed'      => 'Cerrados',
            'cancelled'   => 'Cancelados',
        ];

        $grouped = $tickets->groupBy('status');

        return view('tickets.kanban', compact('columns', 'grouped'));
    }

    // ===================== CREAR =====================
    public function create()
    {
        $categories  = Category::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('tickets.create', compact('categories', 'departments'));
    }

    // ===================== GUARDAR =====================
    public function store(Request $request, RecommendationService $recommendation)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'department_id'  => 'required|exists:departments,id',
            'subdivision_id' => 'required|exists:subdivisions,id',
            'category_id'    => 'nullable|exists:categories,id',
        ]);

        // Validar que la subdivisión pertenezca al departamento
        $sub = \App\Models\Subdivision::where('id', $data['subdivision_id'])
            ->where('department_id', $data['department_id'])
            ->first();

        if (!$sub) {
            return back()->withErrors([
                'subdivision_id' => 'La subdivisión seleccionada no pertenece al departamento elegido.',
            ])->withInput();
        }

        $data['created_by'] = $request->user()->id;
        $data['priority']   = 'medium';

        $nextId = (Ticket::max('id') ?? 0) + 1;
        $data['code'] = 'TCK-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $ticket = Ticket::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket_attachments', 'public');

                $ticket->attachments()->create([
                    'uploaded_by'   => $request->user()->id,
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }

        TicketStatusHistory::create([
            'ticket_id'   => $ticket->id,
            'from_status' => null,
            'to_status'   => 'open',
            'changed_by'  => $request->user()->id,
        ]);

        $raw = $recommendation->suggest($ticket->title, $ticket->description);
        $formatted = $this->buildNumberedSteps($raw, $ticket);

        AiSuggestion::create([
            'ticket_id'   => $ticket->id,
            'suggestions' => $formatted,
        ]);

        $ticket->ai_diagnosis = $formatted;
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('ok', 'Ticket creado');
    }

    public function show(Ticket $ticket, RecommendationService $recommendation)
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['creator', 'assignee', 'category', 'comments.user', 'aiSuggestion', 'attachments']);

        $recsRaw = $ticket->aiSuggestion?->suggestions
                 ?: $recommendation->suggest($ticket->title, $ticket->description);

        $recs = is_array($recsRaw) ? $recsRaw : [$recsRaw];

        return view('tickets.show', compact('ticket', 'recs'));
    }

    public function edit(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $categories = Category::orderBy('name')->get();
        $user = auth()->user();

        // ✅ LIMITADO TAMBIÉN PARA DeptSupport ASIGNADO
        $limited = ($user->isIt() || $user->isDeptSupport()) && (int)$user->id === (int)$ticket->assigned_to;

        $allowedStatuses = $limited
            ? ['in_progress', 'resolved', 'cancelled']
            : ['open', 'assigned', 'in_progress', 'resolved', 'closed', 'cancelled'];

        return view('tickets.edit', compact('ticket', 'categories', 'limited', 'allowedStatuses'));
    }

    public function update(Request $request, Ticket $ticket, RecommendationService $recommendation)
    {
        Gate::authorize('update', $ticket);

        $user = $request->user();

        // ✅ LIMITADO TAMBIÉN PARA DeptSupport ASIGNADO
        $isAssignedLimited = ($user->isIt() || $user->isDeptSupport()) && (int)$user->id === (int)$ticket->assigned_to;

        $data = $isAssignedLimited
            ? $request->validate(['status' => 'required|in:in_progress,resolved,cancelled'])
            : $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'nullable|exists:categories,id',
                'priority'    => 'required|in:low,medium,high',
                'status'      => 'required|in:open,assigned,in_progress,resolved,closed,cancelled',
            ]);

        $from = $ticket->status;

        $ticket->update($isAssignedLimited ? ['status' => $data['status']] : $data);

        if (!$isAssignedLimited && $ticket->wasChanged(['title', 'description'])) {
            $raw  = $recommendation->suggest($ticket->title, $ticket->description);
            $recs = $this->buildNumberedSteps($raw, $ticket);

            AiSuggestion::updateOrCreate(
                ['ticket_id' => $ticket->id],
                ['suggestions' => $recs]
            );

            $ticket->ai_diagnosis = $recs;
            $ticket->save();
        }

        if ($from !== $ticket->status) {
            TicketStatusHistory::create([
                'ticket_id'   => $ticket->id,
                'from_status' => $from,
                'to_status'   => $ticket->status,
                'changed_by'  => $user->id,
            ]);

            if ($ticket->status === 'resolved') {
                $ticket->resolved_at = now();
                $ticket->save();
            }
        }

        return redirect()->route('tickets.index')->with('ok', 'Ticket actualizado correctamente');
    }

    public function move(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $user = $request->user();
        $data = $request->validate([
            'status' => 'required|in:open,assigned,in_progress,resolved,closed,cancelled',
        ]);

        $limited = $user->id === $ticket->assigned_to && ! $user->isManager();
        if ($limited && ! in_array($data['status'], ['in_progress', 'resolved', 'cancelled'])) {
            return response()->json(['ok' => false, 'message' => 'Estado no permitido'], 403);
        }

        $from = $ticket->status;
        $ticket->status = $data['status'];
        $ticket->save();

        if ($from !== $ticket->status) {
            TicketStatusHistory::create([
                'ticket_id'   => $ticket->id,
                'from_status' => $from,
                'to_status'   => $ticket->status,
                'changed_by'  => $user->id,
            ]);
            if ($ticket->status === 'resolved') {
                $ticket->resolved_at = now();
                $ticket->save();
            }
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('ok', 'Eliminado');
    }

    public function generateAi(Ticket $ticket, RecommendationService $recommendation)
    {
        Gate::authorize('view', $ticket);

        $ticket->loadMissing('category');

        $raw  = $recommendation->suggest($ticket->title, $ticket->description);
        $recs = $this->buildNumberedSteps($raw, $ticket);

        AiSuggestion::updateOrCreate(
            ['ticket_id' => $ticket->id],
            ['suggestions' => $recs]
        );

        $ticket->ai_diagnosis = $recs;
        $ticket->save();

        return back()->with('ok', 'Diagnóstico regenerado con IA.');
    }

    public function rate(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        if ($request->user()->id !== $ticket->created_by) {
            abort(403);
        }

        $data = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'rating_comment' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'rating'         => $data['rating'],
            'rating_comment' => $data['rating_comment'] ?? null,
            'rated_by'       => $request->user()->id,
            'rated_at'       => now(),
        ]);

        return back()->with('ok', '¡Gracias por tu calificación!');
    }

    protected function buildNumberedSteps($raw, Ticket $ticket): string
    {
        if (is_array($raw)) {
            $steps = $raw;
        } else {
            $lines = preg_split('/\r\n|\r|\n/', (string) $raw);
            $steps = [];
            foreach ($lines as $line) {
                $line = trim($line);
                $line = ltrim($line, "-•*1234567890. \t");
                $line = trim($line);
                if ($line !== '') $steps[] = $line;
            }
        }

        if (empty($steps)) {
            $category = optional($ticket->category)->name ?: 'el equipo / sistema';
            $steps = [
                "Verificar de nuevo el síntoma reportado en el ticket.",
                "Revisar conexiones físicas, alimentación y estado general de {$category}.",
                "Probar una solución básica (reinicio, actualización, cambio de puerto) y documentar el resultado.",
                "Si el problema persiste, escalar el caso o revisar registros (logs) para un análisis más profundo.",
            ];
        }

        if (count($steps) > 2) shuffle($steps);

        $out = [];
        foreach ($steps as $index => $text) {
            $text = trim($text);
            if ($text === '') continue;
            $out[] = ($index + 1) . '. ' . $text;
        }

        return implode("\n", $out);
    }
}
