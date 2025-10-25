<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, TicketComment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket){
        Gate::authorize('view', $ticket);
        $data = $request->validate(['body'=>'required|string']);
        TicketComment::create([
            'ticket_id'=>$ticket->id,
            'user_id'=>$request->user()->id,
            'body'=>$data['body'],
        ]);
        return back()->with('ok','Comentario agregado');
    }
}