<?php

namespace App\Http\Controllers;

use App\Models\FinancialEntry;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();

        return Inertia::render('Dashboard', [
            'cards' => [
                $this->periodCard('Mes atual', $today->copy()->startOfMonth(), $today->copy()->endOfMonth()),
                $this->periodCard('Ultimos 3 meses', $today->copy()->subMonths(2)->startOfMonth(), $today->copy()->endOfMonth()),
                $this->periodCard('Ano atual', $today->copy()->startOfYear(), $today->copy()->endOfYear()),
            ],
        ]);
    }

    private function periodCard(string $label, Carbon $startDate, Carbon $endDate): array
    {
        $entries = FinancialEntry::query()
            ->with('category:id,name,type')
            ->whereBetween('entry_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $income = $entries
            ->where('category.type', 'receita')
            ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

        $expense = $entries
            ->where('category.type', 'despesa')
            ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

        return [
            'label' => $label,
            'period' => $startDate->format('d/m/Y').' ate '.$endDate->format('d/m/Y'),
            'income' => $this->money($income),
            'expense' => $this->money($expense),
            'balance' => $this->money($income - $expense),
            'balanceType' => $income - $expense >= 0 ? 'positive' : 'negative',
            'categories' => $entries
                ->groupBy(fn (FinancialEntry $entry): string => $entry->category->type.'|'.$entry->category->name)
                ->map(fn ($group): array => [
                    'name' => $group->first()->category->name,
                    'type' => $group->first()->category->type,
                    'total' => $this->money($group->sum(fn (FinancialEntry $entry): float => (float) $entry->amount)),
                ])
                ->sortBy([
                    ['type', 'asc'],
                    ['name', 'asc'],
                ])
                ->values(),
        ];
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
