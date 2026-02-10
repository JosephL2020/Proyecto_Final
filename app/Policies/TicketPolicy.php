<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    // ✅ NUEVO: Permiso para ver Kanban
    public function viewKanban(User $user): bool
    {
        if ($user->isManager()) return true;

        if ($user->isIt() || $user->isDeptManager() || $user->isDeptSupport()) return true;

        return false;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        // Gerente IT ve todo
        if ($user->isManager()) return true;

        // Soporte IT: solo asignados a él
        if ($user->isIt()) return $ticket->assigned_to === $user->id;

        // Gerente de Departamento: tickets del departamento que administra
        if ($user->isDeptManager()) {
            return Department::where('manager_user_id', $user->id)
                ->where('id', $ticket->department_id)
                ->exists();
        }

        // Soporte de Subdivisión: tickets de su subdivisión (o asignados a él)
        if ($user->isDeptSupport()) {
            if ($ticket->assigned_to === $user->id) return true;

            return \App\Models\Subdivision::where('agent_user_id', $user->id)
                ->where('id', $ticket->subdivision_id)
                ->exists();
        }

        // Empleado: solo los suyos
        return $ticket->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isActive();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        // Gerente IT
        if ($user->isManager()) return true;

        // Gerente de Departamento: puede editar tickets de su depto
        if ($user->isDeptManager()) {
            return \App\Models\Department::where('manager_user_id', $user->id)
                ->where('id', $ticket->department_id)
                ->exists();
        }

        // IT o Soporte de Subdivisión: solo si está asignado
        if (($user->isIt() || $user->isDeptSupport()) && $ticket->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isManager();
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        // Gerente IT puede asignar todo
        if ($user->isManager()) return true;

        // Gerente de Departamento puede asignar tickets de su depto
        if ($user->isDeptManager()) {
            return \App\Models\Department::where('manager_user_id', $user->id)
                ->where('id', $ticket->department_id)
                ->exists();
        }

        return false;
    }
}
