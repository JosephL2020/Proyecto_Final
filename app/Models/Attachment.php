<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'filename',
        'path',
        'mime'
    ];

    // Relación con Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
