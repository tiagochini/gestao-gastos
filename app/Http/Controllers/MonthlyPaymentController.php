<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinancialEntry;
use App\Models\Member;
use App\Models\MonthlyPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MonthlyPaymentController extends Controller
{
    public function index(): Response
    {
        $filters = request()->validate([
            'reference_month' => ['nullable', 'date_format:Y-m'],
            'status' => ['nullable', Rule::in(MonthlyPayment::STATUSES)],
        ]);

        $referenceMonth = Carbon::createFromFormat('Y-m', $filters['reference_month'] ?? Carbon::today()->format('Y-m'))
            ->startOfMonth();

        $payments = MonthlyPayment::query()
            ->with('member:id,name,phone,active')
            ->whereDate('reference_month', $referenceMonth->toDateString())
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('status')
            ->orderBy(Member::select('name')->whereColumn('members.id', 'monthly_payments.member_id'))
            ->get()
            ->map(fn (MonthlyPayment $payment): array => [
                'id' => $payment->id,
                'member' => $payment->member->name,
                'phone' => $payment->member->phone,
                'amount' => number_format((float) $payment->amount, 2, ',', '.'),
                'due_date' => $payment->due_date?->format('d/m/Y'),
                'paid_date' => $payment->paid_date?->format('d/m/Y'),
                'status' => $payment->status,
                'notes' => $payment->notes,
            ]);

        $allMonthPayments = MonthlyPayment::query()
            ->whereDate('reference_month', $referenceMonth->toDateString())
            ->get();

        $paidTotal = $allMonthPayments
            ->where('status', MonthlyPayment::STATUS_PAID)
            ->sum(fn (MonthlyPayment $payment): float => (float) $payment->amount);
        $pendingTotal = $allMonthPayments
            ->where('status', MonthlyPayment::STATUS_PENDING)
            ->sum(fn (MonthlyPayment $payment): float => (float) $payment->amount);

        return Inertia::render('MonthlyPayments/Index', [
            'today' => Carbon::today()->format('Y-m-d'),
            'currentMonth' => $referenceMonth->format('Y-m'),
            'members' => Member::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'monthly_amount']),
            'payments' => $payments,
            'summary' => [
                'paid' => $this->money($paidTotal),
                'pending' => $this->money($pendingTotal),
                'count_paid' => $allMonthPayments->where('status', MonthlyPayment::STATUS_PAID)->count(),
                'count_pending' => $allMonthPayments->where('status', MonthlyPayment::STATUS_PENDING)->count(),
            ],
            'filters' => [
                'reference_month' => $referenceMonth->format('Y-m'),
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function storeMember(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'monthly_amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
        ]);

        Member::create([
            ...$data,
            'active' => true,
        ]);

        return redirect()
            ->route('monthly-payments.index')
            ->with('success', 'Membro cadastrado com sucesso.');
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')->where('active', true)],
            'reference_month' => ['required', 'date_format:Y-m'],
            'due_date' => ['nullable', 'date'],
            'paid_date' => ['nullable', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'status' => ['required', Rule::in(MonthlyPayment::STATUSES)],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $member = Member::findOrFail($data['member_id']);
        $referenceMonth = Carbon::createFromFormat('Y-m', $data['reference_month'])->startOfMonth();

        if (MonthlyPayment::where('member_id', $member->id)->whereDate('reference_month', $referenceMonth->toDateString())->exists()) {
            throw ValidationException::withMessages([
                'member_id' => 'Este membro ja possui mensalidade lancada neste mes.',
            ]);
        }

        DB::transaction(function () use ($data, $member, $referenceMonth): void {
            $payment = MonthlyPayment::create([
                ...$data,
                'reference_month' => $referenceMonth->toDateString(),
                'paid_date' => $data['status'] === MonthlyPayment::STATUS_PAID
                    ? ($data['paid_date'] ?: Carbon::today()->toDateString())
                    : null,
            ]);

            if ($payment->status === MonthlyPayment::STATUS_PAID) {
                $payment->update([
                    'financial_entry_id' => $this->createFinancialEntry($payment, $member)->id,
                ]);
            }
        });

        return redirect()
            ->route('monthly-payments.index', ['reference_month' => $referenceMonth->format('Y-m')])
            ->with('success', 'Mensalidade registrada com sucesso.');
    }

    public function markAsPaid(MonthlyPayment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment): void {
            $payment->load('member');

            if (! $payment->financial_entry_id) {
                $payment->financial_entry_id = $this->createFinancialEntry($payment, $payment->member)->id;
            }

            $payment->status = MonthlyPayment::STATUS_PAID;
            $payment->paid_date = Carbon::today()->toDateString();
            $payment->save();
        });

        return redirect()
            ->route('monthly-payments.index', ['reference_month' => $payment->reference_month->format('Y-m')])
            ->with('success', 'Mensalidade marcada como paga.');
    }

    private function createFinancialEntry(MonthlyPayment $payment, Member $member): FinancialEntry
    {
        $category = Category::firstOrCreate(
            ['name' => 'Mensalidades', 'type' => Category::TYPE_INCOME],
            ['active' => true]
        );

        return FinancialEntry::create([
            'category_id' => $category->id,
            'entry_date' => $payment->paid_date?->toDateString() ?: Carbon::today()->toDateString(),
            'amount' => $payment->amount,
            'description' => 'Mensalidade '.$member->name.' - '.$payment->reference_month->format('m/Y'),
        ]);
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
