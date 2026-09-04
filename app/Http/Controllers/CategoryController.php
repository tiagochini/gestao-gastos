<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get(['id', 'name', 'type', 'active']),
            'types' => Category::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->where('type', $request->input('type')),
            ],
            'type' => ['required', Rule::in(Category::TYPES)],
        ]);

        Category::create([
            ...$data,
            'active' => true,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where('type', $request->input('type'))
                    ->ignore($category->id),
            ],
            'type' => ['required', Rule::in(Category::TYPES)],
            'active' => ['required', 'boolean'],
        ]);

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }
}
