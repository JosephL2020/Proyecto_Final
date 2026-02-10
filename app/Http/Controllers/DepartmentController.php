<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Solo Manager IT o IT con permiso pueden administrar departamentos
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user || !$user->canManageDepartments()) {
                return redirect()->route('tickets.index')->with('error', 'No autorizado para administrar departamentos.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $departments = Department::with(['manager'])->orderBy('name')->paginate(15);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:departments,name',
            'manager_name'  => 'required|string|max:255',
            'manager_email' => 'required|email|max:255',
        ]);

        // Crear o tomar usuario gerente del departamento
        $manager = User::where('email', $data['manager_email'])->first();

        if (!$manager) {
            $tempPass = Str::random(12);

            $manager = User::create([
                'name'      => $data['manager_name'],
                'email'     => $data['manager_email'],
                'password'  => Hash::make($tempPass),
                'role'      => User::ROLE_DEPT_MANAGER,
                'is_active' => true,
            ]);
            // Nota: podés hacer que use "Olvidé mi contraseña" para establecer clave final.
        } else {
            $manager->update([
                'name'      => $data['manager_name'],
                'role'      => User::ROLE_DEPT_MANAGER,
                'is_active' => true,
            ]);
        }

        Department::create([
            'name'            => $data['name'],
            'manager_user_id' => $manager->id,
            'created_by'      => $request->user()->id,
        ]);

        return redirect()->route('departments.index')->with('ok', 'Departamento creado correctamente.');
    }

    // ===========================
    // EDITAR / ACTUALIZAR
    // ===========================

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:departments,name,' . $department->id,
            'manager_name'  => 'required|string|max:255',
            'manager_email' => 'required|email|max:255',
        ]);

        $manager = User::where('email', $data['manager_email'])->first();

        if (!$manager) {
            $tempPass = Str::random(12);

            $manager = User::create([
                'name'      => $data['manager_name'],
                'email'     => $data['manager_email'],
                'password'  => Hash::make($tempPass),
                'role'      => User::ROLE_DEPT_MANAGER,
                'is_active' => true,
            ]);
        } else {
            $manager->update([
                'name'      => $data['manager_name'],
                'role'      => User::ROLE_DEPT_MANAGER,
                'is_active' => true,
            ]);
        }

        $department->update([
            'name'            => $data['name'],
            'manager_user_id' => $manager->id,
        ]);

        return redirect()->route('departments.index')->with('ok', 'Departamento actualizado correctamente.');
    }
}
