<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Category::updateOrCreate(
            ['name' => 'Mensalidades', 'type' => Category::TYPE_INCOME],
            ['active' => true]
        );
    }

    public function down(): void
    {
        Category::where('name', 'Mensalidades')
            ->where('type', Category::TYPE_INCOME)
            ->delete();
    }
};
