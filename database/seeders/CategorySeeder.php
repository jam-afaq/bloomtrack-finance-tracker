<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            // Income Categories
            ['name' => 'Salary', 'type' => 'income', 'color' => '#10B981', 'icon' => 'currency-dollar'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#059669', 'icon' => 'briefcase'],
            ['name' => 'Investment', 'type' => 'income', 'color' => '#047857', 'icon' => 'chart-bar'],
            ['name' => 'Business', 'type' => 'income', 'color' => '#065F46', 'icon' => 'building-office'],
            ['name' => 'Other Income', 'type' => 'income', 'color' => '#064E3B', 'icon' => 'plus-circle'],
            
            // Expense Categories
            ['name' => 'Food & Dining', 'type' => 'expense', 'color' => '#EF4444', 'icon' => 'cake'],
            ['name' => 'Transportation', 'type' => 'expense', 'color' => '#DC2626', 'icon' => 'truck'],
            ['name' => 'Shopping', 'type' => 'expense', 'color' => '#B91C1C', 'icon' => 'shopping-bag'],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#991B1B', 'icon' => 'film'],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'color' => '#7F1D1D', 'icon' => 'lightning-bolt'],
            ['name' => 'Healthcare', 'type' => 'expense', 'color' => '#F59E0B', 'icon' => 'heart'],
            ['name' => 'Education', 'type' => 'expense', 'color' => '#D97706', 'icon' => 'academic-cap'],
            ['name' => 'Travel', 'type' => 'expense', 'color' => '#B45309', 'icon' => 'airplane'],
            ['name' => 'Housing', 'type' => 'expense', 'color' => '#92400E', 'icon' => 'home'],
            ['name' => 'Other Expenses', 'type' => 'expense', 'color' => '#78350F', 'icon' => 'minus-circle'],
        ];

        // Get all users and create default categories for each
        $users = User::all();
        
        foreach ($users as $user) {
            foreach ($defaultCategories as $categoryData) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $categoryData['name'],
                    'type' => $categoryData['type'],
                    'color' => $categoryData['color'],
                    'icon' => $categoryData['icon'],
                    'is_default' => true,
                ]);
            }
        }
    }
}
