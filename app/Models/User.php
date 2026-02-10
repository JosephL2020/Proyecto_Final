<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Roles
    public const ROLE_MANAGER       = 'Manager';      // Gerente IT (global)
    public const ROLE_IT            = 'IT';           // Soporte IT
    public const ROLE_EMPLOYEE      = 'Empleado';     // Usuario normal
    public const ROLE_DEPT_MANAGER  = 'DeptManager';  // Gerente de departamento
    public const ROLE_DEPT_SUPPORT  = 'DeptSupport';  // Soporte / Encargado de subdivisión

    protected $fillable = [
        'name',
        // 'lastname', // ✅ Solo si tu tabla users realmente tiene esta columna
        'email',
        'password',
        'role',
        'is_active',
        'can_manage_departments',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'              => 'boolean',
        'can_manage_departments' => 'boolean',
        'email_verified_at'      => 'datetime',
        'password'               => 'hashed',
    ];

    public static function roleOptions(): array
    {
        return [
            self::ROLE_MANAGER      => 'Gerente IT',
            self::ROLE_IT           => 'Soporte IT',
            self::ROLE_DEPT_MANAGER => 'Gerente de Departamento',
            self::ROLE_DEPT_SUPPORT => 'Soporte de Subdivisión',
            self::ROLE_EMPLOYEE     => 'Empleado',
        ];
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isIt(): bool
    {
        return $this->role === self::ROLE_IT;
    }

    public function isDeptManager(): bool
    {
        return $this->role === self::ROLE_DEPT_MANAGER;
    }

    public function isDeptSupport(): bool
    {
        return $this->role === self::ROLE_DEPT_SUPPORT;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function canManageDepartments(): bool
    {
        return $this->isManager() || ($this->isIt() && (bool) $this->can_manage_departments);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * ✅ Relación con Department (recomendado)
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    // Tickets
    public function createdTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }
}
