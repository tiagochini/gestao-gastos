<script setup>
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    today: {
        type: String,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    entries: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const form = useForm({
    entry_date: props.today,
    category_id: null,
    description: '',
    amount: '',
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

function submit() {
    form.post('/lancamentos', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('category_id', 'description', 'amount');
            form.entry_date = props.today;
        },
    });
}

function categoryClass(type) {
    return type === 'receita' ? 'tag-green' : 'tag-red';
}

function applyFilters() {
    filterForm.get('/lancamentos', {
        preserveScroll: true,
        preserveState: true,
    });
}

function clearFilters() {
    filterForm.start_date = '';
    filterForm.end_date = '';
    filterForm.category_id = null;
    filterForm.type = '';
    filterForm.get('/lancamentos', {
        preserveScroll: true,
        preserveState: true,
    });
}

function hasFilters() {
    return Boolean(filterForm.start_date || filterForm.end_date || filterForm.category_id || filterForm.type);
}

function destroyEntry(entry) {
    if (!window.confirm(`Excluir o lancamento de ${entry.date}?`)) {
        return;
    }

    router.delete(`/lancamentos/${entry.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Lancamentos</h1>
            <p>Registre receitas e despesas de forma simples.</p>
        </section>

        <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
            {{ page.props.flash.success }}
        </v-alert>
        <v-alert v-if="Object.keys(page.props.errors).length" color="error" variant="tonal" class="mb-5">
            Confira os campos destacados e tente novamente.
        </v-alert>

        <section class="panel new-entry-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-file-plus-outline" size="22" />
                </span>
                <h2>Novo lancamento</h2>
            </div>

            <form class="entry-form" @submit.prevent="submit">
                <label>
                    <span>Data</span>
                    <v-text-field
                        v-model="form.entry_date"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        hide-details="auto"
                        :error-messages="form.errors.entry_date"
                    />
                </label>

                <label>
                    <span>Categoria</span>
                    <v-select
                        v-model="form.category_id"
                        :items="categories"
                        item-title="name"
                        item-value="id"
                        placeholder="Selecione uma categoria"
                        variant="outlined"
                        density="comfortable"
                        hide-details="auto"
                        :error-messages="form.errors.category_id"
                    >
                        <template #item="{ props: itemProps, item }">
                            <v-list-item v-bind="itemProps">
                                <template #append>
                                    <span class="type-mini" :class="item.raw.type">{{ item.raw.type }}</span>
                                </template>
                            </v-list-item>
                        </template>
                    </v-select>
                </label>

                <label>
                    <span>Descricao</span>
                    <v-text-field
                        v-model="form.description"
                        placeholder="Opcional"
                        variant="outlined"
                        density="comfortable"
                        hide-details="auto"
                        :error-messages="form.errors.description"
                    />
                </label>

                <label>
                    <span>Valor</span>
                    <v-text-field
                        v-model="form.amount"
                        placeholder="0,00"
                        variant="outlined"
                        density="comfortable"
                        hide-details="auto"
                        prefix="R$"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :error-messages="form.errors.amount"
                    />
                </label>

                <v-btn color="primary" size="large" class="add-button" type="submit" :loading="form.processing">
                    <v-icon icon="mdi-plus" />
                    Adicionar
                </v-btn>
            </form>
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

        <section class="panel table-panel">
            <div class="panel-title table-title">
                <span class="title-icon">
                    <v-icon icon="mdi-format-list-bulleted" size="22" />
                </span>
                <h2>Lancamentos</h2>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descricao</th>
                            <th class="text-right">Valor</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="entries.length === 0">
                            <td colspan="5" class="empty-cell">
                                <div class="empty-state">
                                    <v-icon icon="mdi-file-document-plus-outline" size="34" />
                                    <strong>{{ hasFilters() ? 'Nenhum lancamento encontrado' : 'Nenhum lancamento cadastrado' }}</strong>
                                    <span>
                                        {{ hasFilters() ? 'Ajuste os filtros para ampliar a busca.' : 'Use o formulario acima para registrar a primeira receita ou despesa.' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="entry in entries" :key="entry.id">
                            <td>{{ entry.date }}</td>
                            <td>
                                <span class="category-tag" :class="categoryClass(entry.type)">
                                    <v-icon :icon="entry.type === 'receita' ? 'mdi-trending-up' : 'mdi-trending-down'" size="16" />
                                    {{ entry.category }}
                                </span>
                            </td>
                            <td>{{ entry.description || '-' }}</td>
                            <td class="amount" :class="entry.type">{{ entry.type === 'receita' ? '+' : '-' }} R$ {{ entry.amount }}</td>
                            <td class="actions-cell">
                                <Link :href="`/lancamentos/${entry.id}/editar`" as="span">
                                    <v-btn icon="mdi-pencil-outline" size="small" variant="text" aria-label="Editar" />
                                </Link>
                                <v-btn
                                    icon="mdi-trash-can-outline"
                                    size="small"
                                    variant="text"
                                    color="error"
                                    aria-label="Excluir"
                                    @click="destroyEntry(entry)"
                                />
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

.panel {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.new-entry-panel,
.filter-panel {
    padding: 24px 24px 22px;
    margin-bottom: 28px;
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

.entry-form {
    display: grid;
    grid-template-columns: minmax(170px, 1fr) minmax(220px, 1.25fr) minmax(240px, 1.2fr) minmax(180px, 1.2fr);
    gap: 24px 32px;
    align-items: end;
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
    align-items: start;
}

.entry-form label {
    display: grid;
    gap: 10px;
}

.entry-form label span {
    color: #1f2937;
    font-weight: 700;
}

.add-button {
    justify-self: end;
    min-width: 158px;
    height: 52px;
    grid-column: 4;
    border-radius: 6px;
    box-shadow: 0 8px 18px rgba(21, 91, 215, 0.22);
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
    border-collapse: collapse;
    min-width: 760px;
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
    color: #111827;
    font-weight: 800;
}

.amount.receita {
    color: #15803d;
}

.amount.despesa {
    color: #dc2626;
}

.category-tag,
.type-mini {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.type-mini {
    min-height: 24px;
    font-size: 12px;
}

.tag-green,
.type-mini.receita {
    color: #15803d;
    background: #dcfce7;
}

.tag-red,
.type-mini.despesa {
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

.actions-cell {
    white-space: nowrap;
}

@media (max-width: 1160px) {
    .entry-form {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
    }

    .filter-form {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
    }

    .add-button {
        grid-column: 2;
    }
}

@media (max-width: 640px) {
    .page-heading h1 {
        font-size: 28px;
    }

    .entry-form {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .filter-form {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .filter-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .add-button {
        grid-column: auto;
        justify-self: stretch;
    }

    .new-entry-panel,
    .filter-panel {
        padding: 18px;
    }
}
</style>
