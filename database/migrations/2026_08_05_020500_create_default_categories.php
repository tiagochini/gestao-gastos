<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Category::whereIn('name', [
            'Salario',
            'Extra',
            'Alimentacao',
            'Transporte',
            'Mercado',
            'Saude',
            'Lazer',
        ])->delete();
    }
};
