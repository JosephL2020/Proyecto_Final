<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('description')->constrained('departments')->nullOnDelete();
            $table->foreignId('subdivision_id')->nullable()->after('department_id')->constrained('subdivisions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subdivision_id');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
