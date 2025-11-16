<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('s',''));
        $users = User::when($q, fn($x)=>$x->where(fn($w)=>$w->where('name','like',"%$q%")->orWhere('email','like',"%$q%")))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        return view('users.index', compact('users','q'));
    }

    public function create()
    {
        $roles = ['employee'=>'Empleado','it'=>'Soporte Técnico','manager'=>'Gerente de IT'];
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required', Rule::in(['employee','it','manager'])],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('ok','Usuario creado');
    }

    public function edit(User $user)
    {
        $roles = ['employee'=>'Empleado','it'=>'Soporte Técnico','manager'=>'Gerente de IT'];
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
            'role' => ['required', Rule::in(['employee','it','manager'])],
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('ok','Usuario actualizado');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('ok','No puedes eliminar tu propio usuario');
        }
        $user->delete();
        return redirect()->route('users.index')->with('ok','Usuario eliminado');
    }
}
