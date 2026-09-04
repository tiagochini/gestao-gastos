<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@turingdesenvolvimento.com',
        ], [
            'name' => 'Administrador',
            'password' => 'admin@123',
            'active' => true,
        ]);

        foreach ([
            ['name' => 'Salario', 'type' => Category::TYPE_INCOME],
            ['name' => 'Extra', 'type' => Category::TYPE_INCOME],
            ['name' => 'Alimentacao', 'type' => Category::TYPE_EXPENSE],
            ['name' => 'Transporte', 'type' => Category::TYPE_EXPENSE],
            ['name' => 'Mercado', 'type' => Category::TYPE_EXPENSE],
            ['name' => 'Saude', 'type' => Category::TYPE_EXPENSE],
            ['name' => 'Lazer', 'type' => Category::TYPE_EXPENSE],
        ] as $category) {
            Category::updateOrCreate($category, ['active' => true]);
        }
    }
}
