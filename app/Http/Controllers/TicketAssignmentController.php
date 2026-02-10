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

        $user = auth()->user();

        // Manager IT: puede asignar globalmente a IT + DeptSupport
        if ($user->isManager()) {
            $assignees = User::whereIn('role', ['IT', 'DeptSupport'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('tickets.assign', [
                'ticket' => $ticket,
                'its'    => $assignees, // mantenemos variable 'its' para no tocar la vista
            ]);
        }

        // Gerente de Departamento: solo a soportes de subdivisión de SU departamento
        if ($user->isDeptManager()) {

            // Verifica que el ticket sea de un depto que administra (seguridad extra)
            $ownsDept = \App\Models\Department::where('manager_user_id', $user->id)
                ->where('id', $ticket->department_id)
                ->exists();

            if (!$ownsDept) {
                abort(403);
            }

            // Traer usuarios que son agentes de subdivisiones de este departamento
            $agentIds = \App\Models\Subdivision::where('department_id', $ticket->department_id)
                ->whereNotNull('agent_user_id')
                ->pluck('agent_user_id')
                ->unique()
                ->values();

            $assignees = User::whereIn('id', $agentIds)
                ->where('role', 'DeptSupport')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('tickets.assign', [
                'ticket' => $ticket,
                'its'    => $assignees,
            ]);
        }

        // Si llegara otro rol aquí (no debería por Policy), lo bloqueamos.
        abort(403);
    }

    /**
     * Procesa la asignación del ticket a un usuario.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        $data = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $actor  = $request->user();
        $target = User::findOrFail($data['assigned_to']);

        // No permitir asignar a inactivos
        if (!$target->is_active) {
            return back()->with('error', 'No puedes asignar el ticket a un usuario inactivo.');
        }

        // Validación de a quién puede asignar según rol
        if ($actor->isManager()) {

            // Manager IT puede asignar a IT / DeptSupport
            if (!in_array($target->role, ['IT', 'DeptSupport'])) {
                return back()->with('error', 'Solo puedes asignar a Soporte IT o Soporte de Departamento.');
            }

        } elseif ($actor->isDeptManager()) {

            // Solo puede asignar a DeptSupport que sean agentes de subdivisiones del depto del ticket
            if ($target->role !== 'DeptSupport') {
                return back()->with('error', 'Solo puedes asignar a soporte del departamento.');
            }

            $isAgentInDept = \App\Models\Subdivision::where('department_id', $ticket->department_id)
                ->where('agent_user_id', $target->id)
                ->exists();

            if (!$isAgentInDept) {
                return back()->with('error', 'Ese usuario no pertenece a una subdivisión de este departamento.');
            }

        } else {
            abort(403);
        }

        $fromStatus = $ticket->status;

        $ticket->assigned_to = $target->id;
        $ticket->status      = 'assigned';
        $ticket->save();

        TicketStatusHistory::create([
            'ticket_id'   => $ticket->id,
            'from_status' => $fromStatus,
            'to_status'   => 'assigned',
            'changed_by'  => $actor->id,
        ]);

        return back()->with('ok', 'Ticket asignado correctamente.');
    }
}
