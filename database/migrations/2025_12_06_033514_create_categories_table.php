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

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');
            $table->enum('type', ['income', 'expense']);
            $table->text('description')->nullable();

            $table->string('color', 20)->default('#6c757d');
            $table->string('icon', 50)->default('fas fa-folder');
            $table->boolean('status')->default(true);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'name', 'type']);

            $table->index(['user_id', 'type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};