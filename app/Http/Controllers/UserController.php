<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Protege todo el controlador para que solo Managers puedan acceder.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();

            if (!$user || !$user->isManager()) {
                return redirect()
                    ->route('tickets.index')
                    ->with('error', 'Solo el Gerente de IT puede acceder a la gestión de usuarios.');
            }

            return $next($request);
        });
    }

    /**
     * Listado de usuarios con paginación y filtro por rol.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtrar por rol exacto
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if (!is_null($request->input('active'))) {
            $active = $request->input('active') === '1';
            $query->where('is_active', $active);
        }

        if ($s = $request->input('s')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Paginación: 10 usuarios por página
        $users = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // STATS ACTUALIZADO (incluye roles nuevos)
        $stats = [
            'total'        => User::count(),
            'managers'     => User::where('role', 'Manager')->count(),
            'its'          => User::where('role', 'IT')->count(),
            'deptManagers' => User::where('role', 'DeptManager')->count(),
            'deptSupports' => User::where('role', 'DeptSupport')->count(),
            'employees'    => User::where('role', 'Empleado')->count(),
            'active'       => User::where('is_active', true)->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Form crear usuario.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('users.create', compact('departments'));
    }

    /**
     * Guardar nuevo usuario.
     */
    public function store(Request $request)
    {
        $validRoles = ['Manager', 'IT', 'Empleado', 'DeptManager', 'DeptSupport'];

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', Rule::in($validRoles)],
            'is_active'=> ['nullable', 'boolean'],

            'can_manage_departments' => ['nullable', 'boolean'],

            // Obligatorio para todos menos Manager
            'department_id' => [
                Rule::requiredIf(fn () => $request->input('role') !== 'Manager'),
                'nullable',
                'integer',
                'exists:departments,id',
            ],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['can_manage_departments'] = $data['can_manage_departments'] ?? false;

        // Manager no va amarrado a departamento
        if (($data['role'] ?? null) === 'Manager') {
            $data['department_id'] = null;
        }

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('ok', 'Usuario creado correctamente.');
    }

    /**
     * Editar usuario (pasa departamentos)
     */
    public function edit(User $user)
    {
        $departments = Department::orderBy('name')->get();
        return view('users.edit', compact('user', 'departments'));
    }

    /**
     * Actualizar usuario (CON department_id + checkboxes + password confirmed)
     */
    public function update(Request $request, User $user)
    {
        $validRoles = ['Manager', 'IT', 'Empleado', 'DeptManager', 'DeptSupport'];

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', Rule::in($validRoles)],

            'is_active' => ['nullable', 'boolean'],
            'can_manage_departments' => ['nullable', 'boolean'],

            // Obligatorio para todos menos Manager
            'department_id' => [
                Rule::requiredIf(fn () => $request->input('role') !== 'Manager'),
                'nullable',
                'integer',
                'exists:departments,id',
            ],
        ]);

        // Contraseña opcional (con confirmación)
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        // Defaults para checkboxes (si no viene, es false)
        $data['is_active'] = $request->boolean('is_active');
        $data['can_manage_departments'] = $request->boolean('can_manage_departments');

        // Manager no va amarrado a departamento
        if (($data['role'] ?? null) === 'Manager') {
            $data['department_id'] = null;
        }

        // Evitar desactivar tu propia cuenta
        if ((int) $user->id === (int) $request->user()->id && $data['is_active'] === false) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('ok', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('ok', 'Usuario eliminado.');
    }

    /**
     * Activar / desactivar usuario.
     */
    public function toggleActive(Request $request, User $user)
    {
        // Solo el Manager puede hacer esto
        if (!$request->user() || !$request->user()->isManager()) {
            abort(403);
        }

        // Evitar que el gerente se desactive a sí mismo
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('ok', 'Estado del usuario actualizado correctamente.');
    }
}
