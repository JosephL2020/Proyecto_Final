<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'uploaded_by',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Formato bonito del tamaño
    public function getSizeLabelAttribute(): string
    {
        if (!$this->size) {
            return '0 KB';
        }

        $kb = $this->size / 1024;
        if ($kb >= 1024) {
            return number_format($kb / 1024, 1).' MB';
        }

        return number_format($kb, 1).' KB';
    }
}
