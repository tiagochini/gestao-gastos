<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialEntryController;
use App\Http\Controllers\MonthlyPaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'create'])->name('login');
    Route::get('/login', [AuthController::class, 'create']);
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/lancamentos', [FinancialEntryController::class, 'index'])->name('entries.index');
    Route::post('/lancamentos', [FinancialEntryController::class, 'store'])->name('entries.store');
    Route::get('/lancamentos/{entry}/editar', [FinancialEntryController::class, 'edit'])->name('entries.edit');
    Route::put('/lancamentos/{entry}', [FinancialEntryController::class, 'update'])->name('entries.update');
    Route::delete('/lancamentos/{entry}', [FinancialEntryController::class, 'destroy'])->name('entries.destroy');

    Route::get('/mensalidades', [MonthlyPaymentController::class, 'index'])->name('monthly-payments.index');
    Route::post('/mensalidades/membros', [MonthlyPaymentController::class, 'storeMember'])->name('monthly-payments.members.store');
    Route::post('/mensalidades', [MonthlyPaymentController::class, 'storePayment'])->name('monthly-payments.store');
    Route::put('/mensalidades/{payment}/pagar', [MonthlyPaymentController::class, 'markAsPaid'])->name('monthly-payments.pay');

    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');

    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/novo', [UserController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/minha-senha', [UserController::class, 'password'])->name('users.password');
    Route::put('/minha-senha', [UserController::class, 'updatePassword'])->name('users.password.update');

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
