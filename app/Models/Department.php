<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_user_id',
        'created_by',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function subdivisions()
    {
        return $this->hasMany(Subdivision::class);
    }
}
