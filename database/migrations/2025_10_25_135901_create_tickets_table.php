<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); 
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('priority', ['low','medium','high'])->default('medium');
            $table->enum('status', ['open','assigned','in_progress','resolved','closed','cancelled'])->default('open');
            $table->foreignId('created_by')->constrained('users'); 
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); 
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tickets'); }
};