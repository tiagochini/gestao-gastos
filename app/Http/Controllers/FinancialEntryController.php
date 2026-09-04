<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinancialEntry;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FinancialEntryController extends Controller
{
    public function index(): Response
    {
        $filters = request()->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'type' => ['nullable', Rule::in(['receita', 'despesa'])],
        ]);

        $entries = FinancialEntry::query()
            ->with('category:id,name,type')
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '<=', $date))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->whereHas('category', fn ($query) => $query->where('type', $type)))
            ->latest('entry_date')
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(fn (FinancialEntry $entry): array => [
                'id' => $entry->id,
                'date' => $entry->entry_date->format('d/m/Y'),
                'entry_date' => $entry->entry_date->format('Y-m-d'),
                'category' => $entry->category->name,
                'type' => $entry->category->type,
                'description' => $entry->description,
                'amount' => number_format((float) $entry->amount, 2, ',', '.'),
            ]);

        return Inertia::render('Entries/Index', [
            'today' => Carbon::today()->format('Y-m-d'),
            'categories' => Category::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
            'entries' => $entries,
            'filters' => [
                'start_date' => $filters['start_date'] ?? '',
                'end_date' => $filters['end_date'] ?? '',
                'category_id' => isset($filters['category_id']) ? (int) $filters['category_id'] : null,
                'type' => $filters['type'] ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FinancialEntry::create($this->validatedData($request));

        return redirect()
            ->route('entries.index')
            ->with('success', 'Lancamento criado com sucesso.');
    }

    public function edit(FinancialEntry $entry): Response
    {
        return Inertia::render('Entries/Edit', [
            'entry' => [
                'id' => $entry->id,
                'entry_date' => $entry->entry_date->format('Y-m-d'),
                'category_id' => $entry->category_id,
                'amount' => (float) $entry->amount,
                'description' => $entry->description,
            ],
            'categories' => Category::query()
                ->where('active', true)
                ->orWhere('id', $entry->category_id)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
        ]);
    }

    public function update(Request $request, FinancialEntry $entry): RedirectResponse
    {
        $entry->update($this->validatedData($request, $entry));

        return redirect()
            ->route('entries.index')
            ->with('success', 'Lancamento atualizado com sucesso.');
    }

    public function destroy(FinancialEntry $entry): RedirectResponse
    {
        $entry->delete();

        return redirect()
            ->route('entries.index')
            ->with('success', 'Lancamento excluido com sucesso.');
    }

    private function validatedData(Request $request, ?FinancialEntry $entry = null): array
    {
        return $request->validate([
            'entry_date' => ['required', 'date'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query
                        ->where('active', true)
                        ->when($entry, fn ($query) => $query->orWhere('id', $entry->category_id))
                ),
            ],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
