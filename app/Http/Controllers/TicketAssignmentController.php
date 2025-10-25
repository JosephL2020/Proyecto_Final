<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, TicketStatusHistory, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketAssignmentController extends Controller
{
    public function assignForm(Ticket $ticket){
        Gate::authorize('assign', $ticket);

        // Case-insensitive: mostrará IT y MANAGER sin importar mayúsculas/minúsculas
        $its = User::whereRaw("UPPER(role) IN ('IT', 'MANAGER')")
                   ->orderBy('name')
                   ->get();

        return view('tickets.assign', compact('ticket','its'));
    }

    public function assign(Request $request, Ticket $ticket){
        Gate::authorize('assign', $ticket);

        $data = $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $from = $ticket->status;
        $ticket->assigned_to = $data['assigned_to'];
        $ticket->status = 'assigned';
        $ticket->save();

        TicketStatusHistory::create([
            'ticket_id'   => $ticket->id,
            'from_status' => $from,
            'to_status'   => 'assigned',
            'changed_by'  => $request->user()->id,
        ]);

        return back()->with('ok','Asignado');
    }
}
