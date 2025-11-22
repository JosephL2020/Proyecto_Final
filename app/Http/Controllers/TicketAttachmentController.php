<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        // Cualquiera autenticado que pueda ver el ticket puede adjuntar
        Gate::authorize('view', $ticket);

        $request->validate([
            'files.*' => 'required|file|max:5120', // 5 MB por archivo
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

                $path = $file->store('ticket_attachments', 'public');

                $ticket->attachments()->create([
                    'uploaded_by'   => auth()->id() ?: $ticket->created_by, // evita NULL
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }

        return back()->with('ok', 'Adjuntos guardados correctamente.');
    }

    public function download(TicketAttachment $attachment)
    {
        Gate::authorize('view', $attachment->ticket);

        return Storage::disk('public')
            ->download($attachment->path, $attachment->original_name);
    }

    public function destroy(Ticket $ticket, TicketAttachment $attachment)
    {
        Gate::authorize('update', $ticket);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('ok', 'Adjunto eliminado.');
    }
}
