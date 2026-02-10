<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Subdivision;
use App\Models\User;
use Illuminate\Http\Request;

class SubdivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function authorizeDepartmentManage(Request $request, Department $department): void
    {
        $user = $request->user();

        // Manager IT o IT con permiso
        if ($user && $user->canManageDepartments()) {
            return;
        }

        // Gerente de ese departamento
        if ($user && $user->isDeptManager() && (int) $department->manager_user_id === (int) $user->id) {
            return;
        }

        abort(403);
    }

    public function index(Request $request, Department $department)
    {
        $this->authorizeDepartmentManage($request, $department);

        $subdivisions = Subdivision::with('agent')
            ->where('department_id', $department->id)
            ->orderBy('name')
            ->get();

        return view('subdivisions.index', compact('department', 'subdivisions'));
    }

    public function create(Request $request, Department $department)
    {
        $this->authorizeDepartmentManage($request, $department);

        // Solo usuarios DeptSupport del MISMO departamento
        $agents = User::where('role', User::ROLE_DEPT_SUPPORT)
            ->where('is_active', true)
            ->where('department_id', $department->id)
            ->orderBy('name')
            ->get();

        return view('subdivisions.create', compact('department', 'agents'));
    }

    public function store(Request $request, Department $department)
    {
        $this->authorizeDepartmentManage($request, $department);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // Encargado opcional, pero si viene debe existir
            'agent_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // Validación de nombre único por depto
        if (
            Subdivision::where('department_id', $department->id)
                ->where('name', $data['name'])
                ->exists()
        ) {
            return back()->withErrors([
                'name' => 'Esta subdivisión ya existe en este departamento.',
            ])->withInput();
        }

        // Si viene agent_user_id, validar que sea DeptSupport y del mismo depto
        if (!empty($data['agent_user_id'])) {
            $agent = User::find($data['agent_user_id']);

            if (
                !$agent ||
                $agent->role !== User::ROLE_DEPT_SUPPORT ||
                (int) $agent->department_id !== (int) $department->id
            ) {
                return back()->withErrors([
                    'agent_user_id' => 'El encargado debe ser un usuario DeptSupport del mismo departamento.',
                ])->withInput();
            }
        }

        Subdivision::create([
            'department_id' => $department->id,
            'name'          => $data['name'],
            'agent_user_id' => $data['agent_user_id'] ?? null,
            'created_by'    => $request->user()->id,
        ]);

        return redirect()
            ->route('departments.subdivisions.index', $department)
            ->with('ok', 'Subdivisión creada correctamente.');
    }

    /**
     * JSON para el formulario de tickets
     */
    public function options(Department $department)
    {
        $subs = Subdivision::where('department_id', $department->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subs);
    }
}
