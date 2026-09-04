<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinancialEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'type' => ['nullable', Rule::in(Category::TYPES)],
        ]);

        $startDate = $filters['start_date'] ?? Carbon::today()->startOfMonth()->toDateString();
        $endDate = $filters['end_date'] ?? Carbon::today()->endOfMonth()->toDateString();

        $entries = FinancialEntry::query()
            ->with('category:id,name,type')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->whereHas('category', fn ($query) => $query->where('type', $type)))
            ->get();

        $income = $entries
            ->where('category.type', Category::TYPE_INCOME)
            ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

        $expense = $entries
            ->where('category.type', Category::TYPE_EXPENSE)
            ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

        return Inertia::render('Reports/Index', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category_id' => isset($filters['category_id']) ? (int) $filters['category_id'] : null,
                'type' => $filters['type'] ?? '',
            ],
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
            'summary' => [
                'income' => $this->money($income),
                'expense' => $this->money($expense),
                'balance' => $this->money($income - $expense),
                'balanceType' => $income - $expense >= 0 ? 'positive' : 'negative',
                'entriesCount' => $entries->count(),
            ],
            'byCategory' => $this->byCategory($entries),
            'byMonth' => $this->byMonth($entries),
        ]);
    }

    private function byCategory($entries): array
    {
        return $entries
            ->groupBy(fn (FinancialEntry $entry): string => $entry->category->type.'|'.$entry->category->name)
            ->map(fn ($group): array => [
                'name' => $group->first()->category->name,
                'type' => $group->first()->category->type,
                'total' => $this->money($group->sum(fn (FinancialEntry $entry): float => (float) $entry->amount)),
                'entriesCount' => $group->count(),
            ])
            ->sortBy([
                ['type', 'asc'],
                ['name', 'asc'],
            ])
            ->values()
            ->all();
    }

    private function byMonth($entries): array
    {
        return $entries
            ->groupBy(fn (FinancialEntry $entry): string => $entry->entry_date->format('Y-m'))
            ->map(function ($group, string $month): array {
                $income = $group
                    ->where('category.type', Category::TYPE_INCOME)
                    ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

                $expense = $group
                    ->where('category.type', Category::TYPE_EXPENSE)
                    ->sum(fn (FinancialEntry $entry): float => (float) $entry->amount);

                return [
                    'month' => Carbon::createFromFormat('Y-m', $month)->format('m/Y'),
                    'income' => $this->money($income),
                    'expense' => $this->money($expense),
                    'balance' => $this->money($income - $expense),
                    'balanceType' => $income - $expense >= 0 ? 'positive' : 'negative',
                ];
            })
            ->sortKeys()
            ->values()
            ->all();
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
