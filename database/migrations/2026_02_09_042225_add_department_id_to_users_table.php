<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // Si ya existe la columna, no hacemos nada (conserva datos y evita error)
        if (!Schema::hasColumn('users', 'department_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')
                    ->nullable()
                    ->after('role');

                // Si querés también crear la FK aquí (solo si la columna se creó):
                $table->foreign('department_id')
                    ->references('id')
                    ->on('departments')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'department_id')) {

            // Intentamos eliminar FK de forma segura (puede fallar si el nombre no coincide)
            Schema::table('users', function (Blueprint $table) {
                // Convención usual: users_department_id_foreign
                try {
                    $table->dropForeign(['department_id']);
                } catch (\Throwable $e) {
                    // Si no existe o tiene otro nombre, no rompemos el rollback
                }
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('department_id');
            });
        }
    }
};
