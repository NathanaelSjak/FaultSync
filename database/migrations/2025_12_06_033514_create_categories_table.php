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
            
            // Foreign key to users table
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            // Basic category info
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'transfer'])->default('expense');
            
            // Additional fields for better UX
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->default('#6c757d'); // Hex color code
            $table->string('icon', 50)->default('fas fa-folder'); // FontAwesome class
            $table->boolean('status')->default(true); // Active/inactive
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'name']);
            $table->index(['user_id', 'created_at']);
            $table->index('slug');
            
            // Unique constraint: prevent duplicate category names per user per type
            $table->unique(['user_id', 'name', 'type']);
        });
        
        // Optional: Create a view for frequently accessed data
        $this->createCategoriesView();
    }
    
    private function createCategoriesView(): void
    {
        if (app()->environment('testing')) {
            return;
        }
        
        \DB::statement("
            CREATE OR REPLACE VIEW v_categories AS
            SELECT 
                c.*,
                u.name as user_name,
                u.email as user_email,
                creator.name as created_by_name,
                updater.name as updated_by_name,
                CASE 
                    WHEN c.type = 'income' THEN 'Pendapatan'
                    WHEN c.type = 'expense' THEN 'Pengeluaran'
                    WHEN c.type = 'transfer' THEN 'Transfer'
                    ELSE c.type
                END as type_label,
                CASE 
                    WHEN c.status = 1 THEN 'Aktif'
                    ELSE 'Nonaktif'
                END as status_label
            FROM categories c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN users creator ON c.created_by = creator.id
            LEFT JOIN users updater ON c.updated_by = updater.id
            WHERE c.deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        // Drop view first if exists
        if (!app()->environment('testing')) {
            \DB::statement('DROP VIEW IF EXISTS v_categories');
        }
        
        Schema::dropIfExists('categories');
    }
};