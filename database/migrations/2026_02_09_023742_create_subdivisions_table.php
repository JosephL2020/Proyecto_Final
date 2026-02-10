<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subdivisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('name');

            // Encargado de la subdivisión (usuario que atenderá esta subdivisión)
            $table->foreignId('agent_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Quién creó la subdivisión (Gerente IT o Gerente del Departamento)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['department_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subdivisions');
    }
};
