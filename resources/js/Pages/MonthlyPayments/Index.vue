<script setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    today: { type: String, required: true },
    currentMonth: { type: String, required: true },
    members: { type: Array, required: true },
    payments: { type: Array, required: true },
    summary: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const page = usePage();
const memberForm = useForm({
    name: '',
    phone: '',
    monthly_amount: '',
});
const paymentForm = useForm({
    member_id: null,
    reference_month: props.currentMonth,
    due_date: '',
    paid_date: props.today,
    amount: '',
    status: 'pendente',
    notes: '',
});
const filterForm = useForm({
    reference_month: props.filters.reference_month,
    status: props.filters.status,
});
const statusOptions = [
    { title: 'Pendentes', value: 'pendente' },
    { title: 'Pagas', value: 'pago' },
];

function submitMember() {
    memberForm.post('/mensalidades/membros', {
        preserveScroll: true,
        onSuccess: () => memberForm.reset(),
    });
}

function selectMember(memberId) {
    const member = props.members.find((item) => item.id === memberId);
    paymentForm.amount = member ? Number(member.monthly_amount).toFixed(2) : '';
}

function submitPayment() {
    paymentForm.post('/mensalidades', {
        preserveScroll: true,
        onSuccess: () => {
            paymentForm.reset('member_id', 'amount', 'due_date', 'notes');
            paymentForm.reference_month = props.currentMonth;
            paymentForm.status = 'pendente';
            paymentForm.paid_date = props.today;
        },
    });
}

function applyFilters() {
    filterForm.get('/mensalidades', {
        preserveScroll: true,
        preserveState: true,
    });
}

function markAsPaid(payment) {
    router.put(`/mensalidades/${payment.id}/pagar`, {}, { preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <div>
                <h1>Mensalidades</h1>
                <p>Controle as contribuicoes mensais de manutencao do terreiro.</p>
            </div>
        </section>

        <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
            {{ page.props.flash.success }}
        </v-alert>
        <v-alert v-if="Object.keys(page.props.errors).length" color="error" variant="tonal" class="mb-5">
            Confira os campos destacados e tente novamente.
        </v-alert>

        <section class="summary-grid">
            <article class="summary-card">
                <span>Recebido no mes</span>
                <strong class="paid">{{ summary.paid }}</strong>
                <small>{{ summary.count_paid }} mensalidade(s) pagas</small>
            </article>
            <article class="summary-card">
                <span>Pendente no mes</span>
                <strong class="pending">{{ summary.pending }}</strong>
                <small>{{ summary.count_pending }} mensalidade(s) pendentes</small>
            </article>
        </section>

        <section class="forms-grid">
            <div class="panel">
                <div class="panel-title">
                    <span class="title-icon"><v-icon icon="mdi-account-plus-outline" size="22" /></span>
                    <h2>Novo membro</h2>
                </div>

                <form class="stack-form" @submit.prevent="submitMember">
                    <v-text-field v-model="memberForm.name" label="Nome" variant="outlined" hide-details="auto" :error-messages="memberForm.errors.name" />
                    <v-text-field v-model="memberForm.phone" label="Telefone" variant="outlined" hide-details="auto" :error-messages="memberForm.errors.phone" />
                    <v-text-field
                        v-model="memberForm.monthly_amount"
                        label="Valor mensal"
                        prefix="R$"
                        type="number"
                        step="0.01"
                        min="0.01"
                        variant="outlined"
                        hide-details="auto"
                        :error-messages="memberForm.errors.monthly_amount"
                    />
                    <v-btn color="primary" type="submit" :loading="memberForm.processing">
                        <v-icon icon="mdi-plus" />
                        Cadastrar
                    </v-btn>
                </form>
            </div>

            <div class="panel">
                <div class="panel-title">
                    <span class="title-icon"><v-icon icon="mdi-cash-plus" size="22" /></span>
                    <h2>Lancar mensalidade</h2>
                </div>

                <form class="payment-form" @submit.prevent="submitPayment">
                    <v-select
                        v-model="paymentForm.member_id"
                        :items="members"
                        item-title="name"
                        item-value="id"
                        label="Membro"
                        variant="outlined"
                        hide-details="auto"
                        :error-messages="paymentForm.errors.member_id"
                        @update:model-value="selectMember"
                    />
                    <v-text-field v-model="paymentForm.reference_month" label="Mes" type="month" variant="outlined" hide-details="auto" :error-messages="paymentForm.errors.reference_month" />
                    <v-text-field v-model="paymentForm.due_date" label="Vencimento" type="date" variant="outlined" hide-details="auto" :error-messages="paymentForm.errors.due_date" />
                    <v-text-field v-model="paymentForm.amount" label="Valor" prefix="R$" type="number" step="0.01" min="0.01" variant="outlined" hide-details="auto" :error-messages="paymentForm.errors.amount" />
                    <v-select v-model="paymentForm.status" :items="statusOptions" label="Status" variant="outlined" hide-details="auto" :error-messages="paymentForm.errors.status" />
                    <v-text-field
                        v-if="paymentForm.status === 'pago'"
                        v-model="paymentForm.paid_date"
                        label="Data de pagamento"
                        type="date"
                        variant="outlined"
                        hide-details="auto"
                        :error-messages="paymentForm.errors.paid_date"
                    />
                    <v-text-field v-model="paymentForm.notes" label="Observacao" variant="outlined" hide-details="auto" :error-messages="paymentForm.errors.notes" />
                    <v-btn color="primary" type="submit" :loading="paymentForm.processing">
                        <v-icon icon="mdi-plus" />
                        Registrar
                    </v-btn>
                </form>
            </div>
        </section>

        <section class="panel table-panel">
            <div class="table-header">
                <div class="panel-title">
                    <span class="title-icon"><v-icon icon="mdi-calendar-check-outline" size="22" /></span>
                    <h2>Controle mensal</h2>
                </div>
                <form class="filter-form" @submit.prevent="applyFilters">
                    <v-text-field v-model="filterForm.reference_month" type="month" label="Mes" variant="outlined" density="comfortable" hide-details />
                    <v-select v-model="filterForm.status" :items="statusOptions" label="Status" clearable variant="outlined" density="comfortable" hide-details />
                    <v-btn color="primary" type="submit" :loading="filterForm.processing">Filtrar</v-btn>
                </form>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Membro</th>
                            <th>Telefone</th>
                            <th>Vencimento</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th class="text-right">Valor</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="payments.length === 0">
                            <td colspan="7" class="empty-cell">Nenhuma mensalidade lancada para este filtro.</td>
                        </tr>
                        <tr v-for="payment in payments" :key="payment.id">
                            <td>{{ payment.member }}</td>
                            <td>{{ payment.phone || '-' }}</td>
                            <td>{{ payment.due_date || '-' }}</td>
                            <td>{{ payment.paid_date || '-' }}</td>
                            <td><span class="status-tag" :class="payment.status">{{ payment.status }}</span></td>
                            <td class="amount">R$ {{ payment.amount }}</td>
                            <td class="actions-cell">
                                <v-btn v-if="payment.status === 'pendente'" size="small" color="primary" variant="text" @click="markAsPaid(payment)">
                                    Marcar paga
                                </v-btn>
                                <span v-else>-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.page-heading {
    margin-bottom: 28px;
}

.page-heading h1 {
    font-size: 34px;
    line-height: 1.1;
    font-weight: 800;
    color: #111827;
    margin: 0 0 10px;
}

.page-heading p,
.summary-card span,
.summary-card small {
    margin: 0;
    color: #6b7280;
}

.summary-grid,
.forms-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
    margin-bottom: 28px;
}

.panel,
.summary-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.summary-card {
    display: grid;
    gap: 8px;
    padding: 22px 24px;
}

.summary-card strong {
    font-size: 28px;
}

.paid {
    color: #15803d;
}

.pending {
    color: #b45309;
}

.panel {
    padding: 24px;
}

.panel-title {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 22px;
}

.panel-title h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
}

.title-icon {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #155bd7;
    background: #eaf1ff;
}

.stack-form,
.payment-form {
    display: grid;
    gap: 16px;
}

.payment-form {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.payment-form button {
    justify-self: start;
}

.table-panel {
    padding: 0;
    overflow: hidden;
}

.table-header {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 18px;
    padding: 24px 24px 0;
}

.filter-form {
    display: grid;
    grid-template-columns: 170px 170px auto;
    gap: 12px;
    align-items: start;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 860px;
    border-collapse: collapse;
}

th,
td {
    padding: 15px 24px;
    border-top: 1px solid #e5e7eb;
    text-align: left;
    color: #374151;
    font-size: 15px;
}

th {
    color: #6b7280;
    font-size: 14px;
    font-weight: 800;
}

.text-right,
.amount,
.actions-cell {
    text-align: right;
}

.amount {
    font-weight: 800;
}

.status-tag {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.status-tag.pago {
    color: #15803d;
    background: #dcfce7;
}

.status-tag.pendente {
    color: #b45309;
    background: #fef3c7;
}

.empty-cell {
    text-align: center;
    color: #6b7280;
    padding: 34px 24px;
}

@media (max-width: 1100px) {
    .summary-grid,
    .forms-grid,
    .payment-form {
        grid-template-columns: 1fr;
    }

    .table-header {
        display: grid;
    }
}

@media (max-width: 640px) {
    .page-heading h1 {
        font-size: 28px;
    }

    .filter-form {
        grid-template-columns: 1fr;
    }
}
</style>
