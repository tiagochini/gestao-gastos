<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    cards: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <div>
                <h1>Dashboard</h1>
                <p>Resumo financeiro consolidado dos seus lancamentos.</p>
            </div>
            <Link href="/lancamentos" as="span">
                <v-btn color="primary">
                    <v-icon icon="mdi-plus" />
                    Novo lancamento
                </v-btn>
            </Link>
        </section>

        <section class="summary-grid">
            <article v-for="card in cards" :key="card.label" class="summary-card">
                <div class="card-heading">
                    <span class="title-icon">
                        <v-icon icon="mdi-calendar-range-outline" size="22" />
                    </span>
                    <div>
                        <h2>{{ card.label }}</h2>
                        <p>{{ card.period }}</p>
                    </div>
                </div>

                <div class="metrics">
                    <div class="metric">
                        <span>Receitas</span>
                        <strong class="income">{{ card.income }}</strong>
                    </div>
                    <div class="metric">
                        <span>Despesas</span>
                        <strong class="expense">{{ card.expense }}</strong>
                    </div>
                    <div class="metric balance">
                        <span>Saldo</span>
                        <strong :class="card.balanceType">{{ card.balance }}</strong>
                    </div>
                </div>

                <div class="category-breakdown">
                    <h3>Por categoria</h3>
                    <div v-if="card.categories.length === 0" class="empty-breakdown">
                        Nenhum lancamento no periodo.
                    </div>
                    <div v-for="category in card.categories" :key="`${card.label}-${category.type}-${category.name}`" class="category-row">
                        <span class="category-tag" :class="category.type">
                            {{ category.name }}
                        </span>
                        <strong :class="category.type">{{ category.type === 'receita' ? '+' : '-' }} {{ category.total }}</strong>
                    </div>
                </div>
            </article>
        </section>
    </AppLayout>
</template>

<style scoped>
.page-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
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

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}

.summary-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    padding: 24px;
}

.card-heading {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
}

.card-heading h2 {
    margin: 0 0 4px;
    font-size: 20px;
    font-weight: 800;
}

.card-heading p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

.title-icon {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #155bd7;
    background: #eaf1ff;
    flex: 0 0 auto;
}

.metrics {
    display: grid;
    gap: 14px;
}

.metric {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    min-height: 42px;
}

.metric span {
    color: #6b7280;
    font-weight: 700;
}

.metric strong {
    font-size: 18px;
    color: #111827;
}

.metric.balance {
    border-top: 1px solid #e5e7eb;
    padding-top: 14px;
}

.income,
.positive {
    color: #15803d !important;
}

.expense,
.negative {
    color: #dc2626 !important;
}

.category-breakdown {
    margin-top: 18px;
    border-top: 1px solid #e5e7eb;
    padding-top: 18px;
    display: grid;
    gap: 10px;
}

.category-breakdown h3 {
    margin: 0 0 4px;
    color: #111827;
    font-size: 15px;
    font-weight: 800;
}

.category-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.category-row strong {
    white-space: nowrap;
}

.category-row strong.receita {
    color: #15803d;
}

.category-row strong.despesa {
    color: #dc2626;
}

.category-tag {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.category-tag.receita {
    color: #15803d;
    background: #dcfce7;
}

.category-tag.despesa {
    color: #dc2626;
    background: #fee2e2;
}

.empty-breakdown {
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 1120px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .page-heading {
        display: grid;
    }

    .page-heading h1 {
        font-size: 28px;
    }
}
</style>
