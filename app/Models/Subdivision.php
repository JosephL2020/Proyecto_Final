<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'agent_user_id',
        'created_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }
}
