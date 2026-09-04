<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FinancialEntry;
use App\Models\Member;
use App\Models\MonthlyPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class FinancialDomainTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categorias', [
            'name' => 'Freelance',
            'type' => Category::TYPE_INCOME,
        ]);

        $response->assertRedirect('/categorias');
        $this->assertDatabaseHas('categories', [
            'name' => 'Freelance',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);
    }

    public function test_authenticated_user_can_update_category_status(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Teste',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->put("/categorias/{$category->id}", [
            'name' => 'Teste',
            'type' => Category::TYPE_EXPENSE,
            'active' => false,
        ]);

        $response->assertRedirect('/categorias');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'active' => false,
        ]);
    }

    public function test_authenticated_user_can_create_financial_entry(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Alimentacao Teste',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->post('/lancamentos', [
            'entry_date' => '2026-08-05',
            'category_id' => $category->id,
            'amount' => 45.90,
            'description' => 'Almoco',
        ]);

        $response->assertRedirect('/lancamentos');
        $this->assertDatabaseHas('financial_entries', [
            'category_id' => $category->id,
            'entry_date' => '2026-08-05',
            'amount' => 45.90,
            'description' => 'Almoco',
        ]);
    }

    public function test_financial_entry_description_is_optional(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Receita Teste',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->post('/lancamentos', [
            'entry_date' => '2026-08-05',
            'category_id' => $category->id,
            'amount' => 100,
        ]);

        $response->assertRedirect('/lancamentos');
        $this->assertDatabaseHas('financial_entries', [
            'category_id' => $category->id,
            'description' => null,
        ]);
    }

    public function test_authenticated_user_can_update_financial_entry(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Categoria Original',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);
        $entry = FinancialEntry::create([
            'entry_date' => '2026-08-05',
            'category_id' => $category->id,
            'amount' => 10,
            'description' => 'Original',
        ]);

        $response = $this->actingAs($user)->put("/lancamentos/{$entry->id}", [
            'entry_date' => '2026-08-06',
            'category_id' => $category->id,
            'amount' => 22.50,
            'description' => 'Atualizado',
        ]);

        $response->assertRedirect('/lancamentos');
        $this->assertDatabaseHas('financial_entries', [
            'id' => $entry->id,
            'entry_date' => '2026-08-06',
            'amount' => 22.50,
            'description' => 'Atualizado',
        ]);
    }

    public function test_authenticated_user_can_delete_financial_entry(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Categoria Exclusao',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);
        $entry = FinancialEntry::create([
            'entry_date' => '2026-08-05',
            'category_id' => $category->id,
            'amount' => 10,
        ]);

        $response = $this->actingAs($user)->delete("/lancamentos/{$entry->id}");

        $response->assertRedirect('/lancamentos');
        $this->assertDatabaseMissing('financial_entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_entry_cannot_use_inactive_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Categoria Inativa',
            'type' => Category::TYPE_EXPENSE,
            'active' => false,
        ]);

        $response = $this->actingAs($user)->post('/lancamentos', [
            'entry_date' => '2026-08-05',
            'category_id' => $category->id,
            'amount' => 10,
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertSame(0, FinancialEntry::where('category_id', $category->id)->count());
    }

    public function test_dashboard_shows_summary_cards(): void
    {
        FinancialEntry::query()->delete();

        $user = User::factory()->create();
        $incomeCategory = Category::create([
            'name' => 'Aab Resumo Receita',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);
        $expenseCategory = Category::create([
            'name' => 'Aaa Resumo Despesa',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        FinancialEntry::create([
            'entry_date' => now()->toDateString(),
            'category_id' => $incomeCategory->id,
            'amount' => 300,
        ]);
        FinancialEntry::create([
            'entry_date' => now()->toDateString(),
            'category_id' => $expenseCategory->id,
            'amount' => 120,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('cards', 3)
                ->where('cards.0.label', 'Mes atual')
                ->where('cards.1.label', 'Ultimos 3 meses')
                ->where('cards.2.label', 'Ano atual')
                ->has('cards.0.categories')
                ->where('cards.0.categories.0.name', 'Aaa Resumo Despesa')
                ->where('cards.0.categories.0.type', Category::TYPE_EXPENSE)
                ->where('cards.0.categories.1.name', 'Aab Resumo Receita')
                ->where('cards.0.categories.1.type', Category::TYPE_INCOME)
            );
    }

    public function test_entries_can_be_filtered_by_period_category_and_type(): void
    {
        $user = User::factory()->create();
        $incomeCategory = Category::create([
            'name' => 'Filtro Receita',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);
        $expenseCategory = Category::create([
            'name' => 'Filtro Despesa',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        FinancialEntry::create([
            'entry_date' => '2026-08-05',
            'category_id' => $incomeCategory->id,
            'amount' => 100,
            'description' => 'Entrada filtrada',
        ]);
        FinancialEntry::create([
            'entry_date' => '2026-07-01',
            'category_id' => $expenseCategory->id,
            'amount' => 50,
            'description' => 'Saida fora',
        ]);

        $this->actingAs($user)
            ->get("/lancamentos?start_date=2026-08-01&end_date=2026-08-31&category_id={$incomeCategory->id}&type=receita")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Entries/Index')
                ->has('entries', 1)
                ->where('entries.0.description', 'Entrada filtrada')
                ->where('filters.start_date', '2026-08-01')
                ->where('filters.end_date', '2026-08-31')
                ->where('filters.category_id', $incomeCategory->id)
                ->where('filters.type', 'receita')
            );
    }

    public function test_reports_show_summary_category_and_month_data(): void
    {
        FinancialEntry::query()->delete();

        $user = User::factory()->create();
        $incomeCategory = Category::create([
            'name' => 'Relatorio Receita',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);
        $expenseCategory = Category::create([
            'name' => 'Relatorio Despesa',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        FinancialEntry::create([
            'entry_date' => '2026-08-05',
            'category_id' => $incomeCategory->id,
            'amount' => 500,
        ]);
        FinancialEntry::create([
            'entry_date' => '2026-08-06',
            'category_id' => $expenseCategory->id,
            'amount' => 150,
        ]);

        $this->actingAs($user)
            ->get('/relatorios?start_date=2026-08-01&end_date=2026-08-31')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Reports/Index')
                ->where('summary.income', 'R$ 500,00')
                ->where('summary.expense', 'R$ 150,00')
                ->where('summary.balance', 'R$ 350,00')
                ->has('byCategory', 2)
                ->has('byMonth', 1)
                ->where('byMonth.0.month', '08/2026')
            );
    }

    public function test_reports_can_be_filtered_by_type(): void
    {
        FinancialEntry::query()->delete();

        $user = User::factory()->create();
        $incomeCategory = Category::create([
            'name' => 'Relatorio Tipo Receita',
            'type' => Category::TYPE_INCOME,
            'active' => true,
        ]);
        $expenseCategory = Category::create([
            'name' => 'Relatorio Tipo Despesa',
            'type' => Category::TYPE_EXPENSE,
            'active' => true,
        ]);

        FinancialEntry::create([
            'entry_date' => '2026-08-05',
            'category_id' => $incomeCategory->id,
            'amount' => 500,
        ]);
        FinancialEntry::create([
            'entry_date' => '2026-08-06',
            'category_id' => $expenseCategory->id,
            'amount' => 150,
        ]);

        $this->actingAs($user)
            ->get('/relatorios?start_date=2026-08-01&end_date=2026-08-31&type=despesa')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Reports/Index')
                ->where('summary.income', 'R$ 0,00')
                ->where('summary.expense', 'R$ 150,00')
                ->where('filters.type', Category::TYPE_EXPENSE)
                ->has('byCategory', 1)
                ->where('byCategory.0.type', Category::TYPE_EXPENSE)
            );
    }

    public function test_authenticated_user_can_create_paid_monthly_payment_as_income_entry(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'name' => 'Maria da Guia',
            'phone' => '65999990000',
            'monthly_amount' => 80,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->post('/mensalidades', [
            'member_id' => $member->id,
            'reference_month' => '2026-08',
            'due_date' => '2026-08-10',
            'paid_date' => '2026-08-08',
            'amount' => 80,
            'status' => MonthlyPayment::STATUS_PAID,
            'notes' => 'Pix',
        ]);

        $response->assertRedirect('/mensalidades?reference_month=2026-08');
        $this->assertDatabaseHas('monthly_payments', [
            'member_id' => $member->id,
            'reference_month' => '2026-08-01',
            'paid_date' => '2026-08-08',
            'status' => MonthlyPayment::STATUS_PAID,
            'amount' => 80,
        ]);
        $this->assertDatabaseHas('financial_entries', [
            'entry_date' => '2026-08-08',
            'amount' => 80,
            'description' => 'Mensalidade Maria da Guia - 08/2026',
        ]);
    }

    public function test_monthly_payments_page_shows_summary(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'name' => 'Joao de Aruanda',
            'monthly_amount' => 100,
            'active' => true,
        ]);

        MonthlyPayment::create([
            'member_id' => $member->id,
            'reference_month' => '2026-08-01',
            'due_date' => '2026-08-10',
            'amount' => 100,
            'status' => MonthlyPayment::STATUS_PENDING,
        ]);

        $this->actingAs($user)
            ->get('/mensalidades?reference_month=2026-08')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MonthlyPayments/Index')
                ->where('summary.pending', 'R$ 100,00')
                ->where('summary.count_pending', 1)
                ->has('payments', 1)
                ->where('payments.0.member', 'Joao de Aruanda')
            );
    }
}
