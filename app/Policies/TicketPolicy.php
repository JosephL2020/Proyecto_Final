<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Ver listado de tickets.
     * 
     * Todos pueden acceder al listado, pero luego el controlador filtra por rol.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Ver un ticket.
     *
     * Manager → puede ver todos
     * IT      → puede ver todos
     * Empleado → solo sus propios tickets
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isManager() || $user->isIt()) {
            return true;
        }

        return $ticket->created_by === $user->id;
    }

    /**
     * Crear tickets.
     *
     * Todos los roles pueden crear tickets.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Empleado', 'IT', 'Manager'], true);
    }

    /**
     * Actualizar un ticket.
     *
     * Manager → puede editar cualquier ticket
     * IT      → puede editar solo tickets asignados a él
     * Empleado → no puede editar
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isManager()) {
            return true;
        }

        if ($user->isIt() && $ticket->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Eliminar tickets.
     *
     * Solo Manager.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isManager();
    }

    /**
     * Asignar tickets.
     *
     * Solo Manager.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isManager();
    }
}
