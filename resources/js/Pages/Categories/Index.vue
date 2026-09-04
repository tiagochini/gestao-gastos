<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    types: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const form = useForm({
    name: '',
    type: 'despesa',
});

function submit() {
    form.post('/categorias', {
        preserveScroll: true,
        onSuccess: () => form.reset('name'),
    });
}

function toggle(category) {
    useForm({
        name: category.name,
        type: category.type,
        active: !category.active,
    }).put(`/categorias/${category.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Categorias</h1>
            <p>Defina se cada categoria representa uma receita ou uma despesa.</p>
        </section>

        <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
            {{ page.props.flash.success }}
        </v-alert>
        <v-alert v-if="Object.keys(page.props.errors).length" color="error" variant="tonal" class="mb-5">
            Confira os campos destacados e tente novamente.
        </v-alert>

        <section class="panel category-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-tag-plus-outline" size="22" />
                </span>
                <h2>Nova categoria</h2>
            </div>

            <form class="category-form" @submit.prevent="submit">
                <v-text-field
                    v-model="form.name"
                    label="Nome"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.name"
                />
                <v-select
                    v-model="form.type"
                    :items="types"
                    label="Tipo"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.type"
                />
                <v-btn color="primary" type="submit" :loading="form.processing">
                    <v-icon icon="mdi-plus" />
                    Adicionar
                </v-btn>
            </form>
        </section>

        <section class="panel table-panel">
            <div class="panel-title table-title">
                <span class="title-icon">
                    <v-icon icon="mdi-tag-outline" size="22" />
                </span>
                <h2>Categorias cadastradas</h2>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="categories.length === 0">
                            <td colspan="4" class="empty-cell">
                                <div class="empty-state">
                                    <v-icon icon="mdi-tag-plus-outline" size="34" />
                                    <strong>Nenhuma categoria cadastrada</strong>
                                    <span>Crie categorias para classificar receitas e despesas.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="category in categories" :key="category.id">
                            <td>{{ category.name }}</td>
                            <td>
                                <span class="type-tag" :class="category.type">{{ category.type }}</span>
                            </td>
                            <td>
                                <span class="status-tag" :class="category.active ? 'active' : 'inactive'">
                                    {{ category.active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <v-btn
                                    size="small"
                                    variant="text"
                                    :color="category.active ? 'error' : 'primary'"
                                    @click="toggle(category)"
                                >
                                    {{ category.active ? 'Desativar' : 'Ativar' }}
                                </v-btn>
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

.category-panel {
    padding: 24px;
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

.category-form {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) 180px 160px;
    gap: 18px;
    align-items: start;
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
    min-width: 680px;
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
.actions-cell {
    text-align: right;
}

.type-tag,
.status-tag {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.type-tag.receita,
.status-tag.active {
    color: #15803d;
    background: #dcfce7;
}

.type-tag.despesa {
    color: #dc2626;
    background: #fee2e2;
}

.status-tag.inactive {
    color: #6b7280;
    background: #f3f4f6;
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

@media (max-width: 760px) {
    .category-form {
        grid-template-columns: 1fr;
    }
}
</style>
