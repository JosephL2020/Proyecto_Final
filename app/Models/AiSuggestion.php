<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    protected $fillable = ['ticket_id','suggestions'];

    protected $casts = [
        'suggestions' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
