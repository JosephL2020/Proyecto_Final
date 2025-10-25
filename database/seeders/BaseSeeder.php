<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Category;

class BaseSeeder extends Seeder
{
    public function run(): void
    {
        
        $itDept = Department::firstOrCreate(['name' => 'IT']);
        Department::firstOrCreate(['name' => 'RRHH']);
        Department::firstOrCreate(['name' => 'Finanzas']);

        
        foreach (['Impresoras', 'Correo', 'Red', 'Hardware', 'Software'] as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

    
        User::firstOrCreate(
            ['email' => 'manager@corp.local'],
            [
                'name' => 'IT Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department_id' => $itDept->id
            ]
        );

        User::firstOrCreate(
            ['email' => 'it1@corp.local'],
            [
                'name' => 'Gino IT',
                'password' => Hash::make('password'),
                'role' => 'IT',
                'department_id' => $itDept->id
            ]
        );

        User::firstOrCreate(
            ['email' => 'it2@corp.local'],
            [
                'name' => 'Daleth IT',
                'password' => Hash::make('password'),
                'role' => 'IT',
                'department_id' => $itDept->id
            ]
        );

        User::firstOrCreate(
            ['email' => 'empleado@corp.local'],
            [
                'name' => 'Susana empleada',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department_id' => null
            ]
        );
    }
}