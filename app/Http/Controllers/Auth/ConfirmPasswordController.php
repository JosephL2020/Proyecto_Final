<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    

    use ConfirmsPasswords;

    /**
     * 
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * 
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
