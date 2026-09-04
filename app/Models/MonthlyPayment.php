<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'financial_entry_id', 'reference_month', 'due_date', 'paid_date', 'amount', 'status', 'notes'])]
class MonthlyPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pendente';
    public const STATUS_PAID = 'pago';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
    ];

    protected function casts(): array
    {
        return [
            'reference_month' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function financialEntry(): BelongsTo
    {
        return $this->belongsTo(FinancialEntry::class);
    }
}
