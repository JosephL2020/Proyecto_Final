<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Ticket $ticket): bool
    {
        return ($user->role === 'it' || $user->role === 'manager') || $ticket->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['employee','it','manager'], true);
    }

    public function update(User $user, Ticket $ticket): bool
    {
      
        return ($user->role === 'manager') || $ticket->assigned_to === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return ($user->role === 'manager') || $ticket->assigned_to === $user->id;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->role === 'manager';
    }
}
