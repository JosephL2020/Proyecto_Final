<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, TicketStatusHistory, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketAssignmentController extends Controller
{
    /**
     * Muestra el formulario para asignar / reasignar un ticket.
     */
    public function assignForm(Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        // Mostramos usuarios con rol IT o MANAGER (insensible a mayúsculas/minúsculas)
        $its = User::whereRaw("UPPER(role) IN ('IT', 'MANAGER')")
            ->orderBy('name')
            ->get();

        return view('tickets.assign', compact('ticket', 'its'));
    }

    /**
     * Procesa la asignación del ticket a un usuario.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        $data = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $fromStatus = $ticket->status;

        $ticket->assigned_to = $data['assigned_to'];
        $ticket->status = 'assigned';
        $ticket->save();

        TicketStatusHistory::create([
            'ticket_id'   => $ticket->id,
            'from_status' => $fromStatus,
            'to_status'   => 'assigned',
            'changed_by'  => $request->user()->id,
        ]);

        return back()->with('ok', 'Ticket asignado correctamente.');
    }
}
