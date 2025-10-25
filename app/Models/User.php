<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','department_id'];
    protected $hidden = ['password','remember_token'];

    public function department(){ return $this->belongsTo(Department::class); }

    public function isManager(): bool { return $this->role === 'Manager'; }
    public function isIT(): bool { return $this->role === 'it' || $this->role === 'Manager'; }
}