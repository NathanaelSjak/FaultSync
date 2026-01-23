<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $incomeCategories = [
                ['name' => 'Gaji', 'type' => 'income', 'color' => '#10b981', 'icon' => 'fas fa-money-bill-wave'],
                ['name' => 'Bonus', 'type' => 'income', 'color' => '#10b981', 'icon' => 'fas fa-gift'],
                ['name' => 'Investasi', 'type' => 'income', 'color' => '#10b981', 'icon' => 'fas fa-chart-line'],
                ['name' => 'Freelance', 'type' => 'income', 'color' => '#10b981', 'icon' => 'fas fa-laptop'],
                ['name' => 'Lainnya', 'type' => 'income', 'color' => '#10b981', 'icon' => 'fas fa-coins'],
            ];

            // Expense Categories
            $expenseCategories = [
                ['name' => 'Makanan & Minuman', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-utensils'],
                ['name' => 'Transportasi', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-car'],
                ['name' => 'Belanja', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-shopping-cart'],
                ['name' => 'Tagihan', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-file-invoice'],
                ['name' => 'Hiburan', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-film'],
                ['name' => 'Kesehatan', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-heart'],
                ['name' => 'Pendidikan', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-graduation-cap'],
                ['name' => 'Lainnya', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-ellipsis-h'],
            ];

            foreach ($incomeCategories as $category) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'description' => 'Kategori ' . $category['name'],
                    'status' => true,
                ]);
            }

            foreach ($expenseCategories as $category) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'description' => 'Kategori ' . $category['name'],
                    'status' => true,
                ]);
            }
        }
    }
}
