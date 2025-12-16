<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Category info
            $table->string('name');
            $table->enum('type', ['income', 'expense']);
            $table->text('description')->nullable();

            // UI helpers
            $table->string('color', 20)->default('#6c757d');
            $table->string('icon', 50)->default('fas fa-folder');
            $table->boolean('status')->default(true);

            // Meta
            $table->softDeletes();
            $table->timestamps();

            // Prevent duplicate category names per user & type
            $table->unique(['user_id', 'name', 'type']);

            // Indexes
            $table->index(['user_id', 'type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};