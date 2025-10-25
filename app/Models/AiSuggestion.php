<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiSuggestion extends Model
{
    use HasFactory;
    protected $fillable = ['ticket_id','suggestions'];
    protected $casts = ['suggestions' => 'array'];

    public function ticket(){ return $this->belongsTo(Ticket::class); }
}