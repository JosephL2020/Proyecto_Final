<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ticket::class => TicketPolicy::class,
        // Si tienes otras policies, agrégalas aquí...
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |----------------------------------------------------------------------
        | Gate: Solo Gerente IT puede administrar usuarios
        |----------------------------------------------------------------------
        */
        Gate::define('manage-users', function (User $user) {
            return $user->isManager();
        });

        /*
        |----------------------------------------------------------------------
        | Gate: Ver dashboard
        |----------------------------------------------------------------------
        | Por ahora solo Manager, puedes ampliar a IT más adelante.
        */
        Gate::define('view-dashboard', function (User $user) {
            return $user->isManager();
        });

        /*
        |----------------------------------------------------------------------
        | Aquí puedes agregar más gates si los necesitas
        |----------------------------------------------------------------------
        */
    }
}
