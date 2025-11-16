<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('resolved_at');
            $table->unsignedBigInteger('rated_by')->nullable()->after('rating');
            $table->timestamp('rated_at')->nullable()->after('rated_by');
        });
    }
    public function down(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['rating','rated_by','rated_at']);
        });
    }
};
