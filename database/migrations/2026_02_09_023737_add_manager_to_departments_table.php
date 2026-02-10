<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('manager_user_id')->nullable()->after('name')->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('manager_user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('manager_user_id');
        });
    }
};
