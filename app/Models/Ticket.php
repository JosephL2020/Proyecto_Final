<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','title','description',
        'department_id','subdivision_id',
        'category_id','priority','status',
        'created_by','assigned_to','resolved_at',
        'rating','rating_comment','rated_by','rated_at'
    ];

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'resolved_at' => 'datetime',
        'rated_at'    => 'datetime',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }

    public function department() { return $this->belongsTo(Department::class); }
    public function subdivision() { return $this->belongsTo(Subdivision::class); }

    public function category() { return $this->belongsTo(Category::class); }
    public function comments() { return $this->hasMany(TicketComment::class); }
    public function histories() { return $this->hasMany(TicketStatusHistory::class); }
    public function aiSuggestion() { return $this->hasOne(AiSuggestion::class); }

    public function attachments()
    {
        return $this->hasMany(\App\Models\TicketAttachment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'Abierto',
            'assigned'    => 'Asignado',
            'in_progress' => 'En progreso',
            'resolved'    => 'Resuelto',
            'closed'      => 'Cerrado',
            'cancelled'   => 'Cancelado',
            default       => ucfirst($this->status),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low'    => 'Baja',
            'medium' => 'Media',
            'high'   => 'Alta',
            default  => ucfirst($this->priority),
        };
    }

    public function estadoNombre(): string
    {
        return [
            'open'        => 'Abierto',
            'assigned'    => 'Asignado',
            'in_progress' => 'En progreso',
            'resolved'    => 'Resuelto',
            'closed'      => 'Cerrado',
            'cancelled'   => 'Cancelado',
        ][$this->status] ?? ucfirst($this->status);
    }
}
