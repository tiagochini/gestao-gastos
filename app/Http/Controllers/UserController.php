<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'active', 'created_at']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            ...$data,
            'active' => true,
        ]);

        return redirect()
            ->route('users.create')
            ->with('success', 'Usuario criado com sucesso.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->only('id', 'name', 'email', 'active'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'active' => ['required', 'boolean'],
        ]);

        if ($request->user()->is($user) && ! $data['active']) {
            return back()->withErrors([
                'active' => 'Voce nao pode desativar o proprio usuario.',
            ]);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'user' => 'Voce nao pode desativar o proprio usuario.',
            ]);
        }

        $user->update(['active' => false]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario desativado com sucesso.');
    }

    public function password(): Response
    {
        return Inertia::render('Users/Password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('users.password')
            ->with('success', 'Senha alterada com sucesso.');
    }
}
