<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Redirección después de autenticarse.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->isManager()) {
            // Gerente IT → Panel de control
            return redirect()->route('dashboard.index');
        }

        // IT y Empleado → Listado de tickets
        return redirect()->route('tickets.index');
    }
}
