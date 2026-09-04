<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
    byCategory: {
        type: Array,
        required: true,
    },
    byMonth: {
        type: Array,
        required: true,
    },
});

const filterForm = useForm({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
    category_id: props.filters.category_id,
    type: props.filters.type,
});

const typeOptions = [
    { title: 'Receitas', value: 'receita' },
    { title: 'Despesas', value: 'despesa' },
];

function applyFilters() {
    filterForm.get('/relatorios', {
        preserveScroll: true,
        preserveState: true,
    });
}

function clearFilters() {
    filterForm.start_date = '';
    filterForm.end_date = '';
    filterForm.category_id = null;
    filterForm.type = '';
    filterForm.get('/relatorios', {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Relatorios</h1>
            <p>Analise receitas, despesas e saldo por periodo, categoria e mes.</p>
        </section>

        <section class="panel filter-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-filter-outline" size="22" />
                </span>
                <h2>Filtros</h2>
            </div>

            <form class="filter-form" @submit.prevent="applyFilters">
                <v-text-field
                    v-model="filterForm.start_date"
                    label="Data inicial"
                    type="date"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    :error-messages="filterForm.errors.start_date"
                />
                <v-text-field
                    v-model="filterForm.end_date"
                    label="Data final"
                    type="date"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    :error-messages="filterForm.errors.end_date"
                />
                <v-select
                    v-model="filterForm.category_id"
                    :items="categories"
                    item-title="name"
                    item-value="id"
                    label="Categoria"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    clearable
                    :error-messages="filterForm.errors.category_id"
                />
                <v-select
                    v-model="filterForm.type"
                    :items="typeOptions"
                    label="Tipo"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    clearable
                    :error-messages="filterForm.errors.type"
                />
                <div class="filter-actions">
                    <v-btn color="primary" type="submit" :loading="filterForm.processing">Filtrar</v-btn>
                    <v-btn variant="outlined" type="button" @click="clearFilters">Limpar</v-btn>
                </div>
            </form>
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <span>Receitas</span>
                <strong class="income">{{ summary.income }}</strong>
            </article>
            <article class="summary-card">
                <span>Despesas</span>
                <strong class="expense">{{ summary.expense }}</strong>
            </article>
            <article class="summary-card">
                <span>Saldo</span>
                <strong :class="summary.balanceType">{{ summary.balance }}</strong>
            </article>
            <article class="summary-card">
                <span>Lancamentos</span>
                <strong>{{ summary.entriesCount }}</strong>
            </article>
        </section>

        <section class="report-grid">
            <article class="panel table-panel">
                <div class="panel-title table-title">
                    <span class="title-icon">
                        <v-icon icon="mdi-tag-outline" size="22" />
                    </span>
                    <h2>Por categoria</h2>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th class="text-right">Lancamentos</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="byCategory.length === 0">
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state">
                                        <v-icon icon="mdi-chart-box-outline" size="34" />
                                        <strong>Nenhum dado encontrado</strong>
                                        <span>Ajuste os filtros ou cadastre novos lancamentos.</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="row in byCategory" :key="`${row.type}-${row.name}`">
                                <td>{{ row.name }}</td>
                                <td><span class="type-tag" :class="row.type">{{ row.type }}</span></td>
                                <td class="text-right">{{ row.entriesCount }}</td>
                                <td class="total-cell" :class="row.type">{{ row.type === 'receita' ? '+' : '-' }} {{ row.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel table-panel">
                <div class="panel-title table-title">
                    <span class="title-icon">
                        <v-icon icon="mdi-calendar-month-outline" size="22" />
                    </span>
                    <h2>Por mes</h2>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th class="text-right">Receitas</th>
                                <th class="text-right">Despesas</th>
                                <th class="text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="byMonth.length === 0">
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state">
                                        <v-icon icon="mdi-calendar-search-outline" size="34" />
                                        <strong>Nenhum dado no periodo</strong>
                                        <span>Escolha outro intervalo para analisar.</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="row in byMonth" :key="row.month">
                                <td>{{ row.month }}</td>
                                <td class="total-cell receita">+ {{ row.income }}</td>
                                <td class="total-cell despesa">- {{ row.expense }}</td>
                                <td class="total-cell" :class="row.balanceType">{{ row.balance }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </AppLayout>
</template>

<style scoped>
.page-heading {
    margin-bottom: 30px;
}

.page-heading h1 {
    font-size: 34px;
    line-height: 1.1;
    font-weight: 800;
    color: #111827;
    margin: 0 0 10px;
}

.page-heading p {
    margin: 0;
    color: #6b7280;
    font-size: 16px;
}

.panel,
.summary-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.filter-panel {
    padding: 24px;
    margin-bottom: 24px;
}

.panel-title {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
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

.filter-form {
    display: grid;
    grid-template-columns: repeat(4, minmax(150px, 1fr)) auto;
    gap: 18px;
    align-items: start;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.summary-card {
    padding: 20px;
    display: grid;
    gap: 8px;
}

.summary-card span {
    color: #6b7280;
    font-weight: 700;
}

.summary-card strong {
    color: #111827;
    font-size: 22px;
}

.income,
.receita,
.positive {
    color: #15803d !important;
}

.expense,
.despesa,
.negative {
    color: #dc2626 !important;
}

.report-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
}

.table-panel {
    overflow: hidden;
}

.table-title {
    padding: 24px 24px 18px;
    margin-bottom: 0;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 600px;
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
.total-cell {
    text-align: right;
}

.total-cell {
    font-weight: 800;
    white-space: nowrap;
}

.type-tag {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.type-tag.receita {
    color: #15803d;
    background: #dcfce7;
}

.type-tag.despesa {
    color: #dc2626;
    background: #fee2e2;
}

.empty-cell {
    text-align: center;
    color: #6b7280;
    padding: 34px 24px;
}

.empty-state {
    display: grid;
    justify-items: center;
    gap: 8px;
}

.empty-state strong {
    color: #111827;
    font-size: 16px;
}

@media (max-width: 1180px) {
    .filter-form,
    .summary-grid,
    .report-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .page-heading h1 {
        font-size: 28px;
    }

    .filter-panel {
        padding: 18px;
    }

    .filter-form,
    .summary-grid,
    .report-grid {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
}
</style>
