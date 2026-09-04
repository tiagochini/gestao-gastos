<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'monthly_amount', 'active'])]
class Member extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function monthlyPayments(): HasMany
    {
        return $this->hasMany(MonthlyPayment::class);
    }
}
