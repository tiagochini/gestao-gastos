<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'active'])]
class Category extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'receita';
    public const TYPE_EXPENSE = 'despesa';

    public const TYPES = [
        self::TYPE_INCOME,
        self::TYPE_EXPENSE,
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class);
    }
}
