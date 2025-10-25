<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','title','description','category_id','priority','status','created_by','assigned_to','resolved_at'
    ];

    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
    public function assignee(){ return $this->belongsTo(User::class, 'assigned_to'); }
    public function category(){ return $this->belongsTo(Category::class); }
    public function comments(){ return $this->hasMany(TicketComment::class); }
    public function histories(){ return $this->hasMany(TicketStatusHistory::class); }
    public function aiSuggestion(){ return $this->hasOne(AiSuggestion::class); }
}