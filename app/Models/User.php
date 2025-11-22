<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Roles estandarizados.
     */
    public const ROLE_MANAGER  = 'Manager';
    public const ROLE_IT       = 'IT';
    public const ROLE_EMPLOYEE = 'Empleado';

    /**
     * Atributos que se pueden asignar en masa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * Atributos ocultos para arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Helpers de rol y estado
    |--------------------------------------------------------------------------
    */

    public static function roleOptions(): array
    {
        return [
            self::ROLE_MANAGER  => 'Gerente IT',
            self::ROLE_IT       => 'Soporte IT',
            self::ROLE_EMPLOYEE => 'Empleado',
        ];
    }

    // Helpers ajustados para que coincidan con el controlador
    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isIt(): bool
    {
        return $this->role === self::ROLE_IT;
    }

    public function isEmployee(): bool
    {
        return !$this->isManager() && !$this->isIt();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones con tickets
    |--------------------------------------------------------------------------
    */

    public function createdTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }
}
