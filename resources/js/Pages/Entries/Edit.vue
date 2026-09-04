<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    entry: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const form = useForm({
    entry_date: props.entry.entry_date,
    category_id: props.entry.category_id,
    amount: props.entry.amount,
    description: props.entry.description ?? '',
});

function submit() {
    form.put(`/lancamentos/${props.entry.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Editar lancamento</h1>
            <p>Atualize data, categoria, valor ou descricao.</p>
        </section>

        <v-alert v-if="Object.keys(page.props.errors).length" color="error" variant="tonal" class="mb-5">
            Confira os campos destacados e tente novamente.
        </v-alert>

        <section class="panel entry-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-pencil-outline" size="22" />
                </span>
                <h2>Dados do lancamento</h2>
            </div>

            <form class="entry-form" @submit.prevent="submit">
                <v-text-field
                    v-model="form.entry_date"
                    label="Data"
                    type="date"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.entry_date"
                />
                <v-select
                    v-model="form.category_id"
                    :items="categories"
                    item-title="name"
                    item-value="id"
                    label="Categoria"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.category_id"
                />
                <v-text-field
                    v-model="form.amount"
                    label="Valor"
                    prefix="R$"
                    type="number"
                    step="0.01"
                    min="0.01"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.amount"
                />
                <v-text-field
                    v-model="form.description"
                    label="Descricao"
                    placeholder="Opcional"
                    variant="outlined"
                    color="primary"
                    :error-messages="form.errors.description"
                />

                <div class="actions">
                    <v-btn color="primary" type="submit" :loading="form.processing">Salvar</v-btn>
                    <Link href="/lancamentos" as="span">
                        <v-btn variant="outlined">Voltar</v-btn>
                    </Link>
                </div>
            </form>
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
    max-width: 780px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.entry-panel {
    padding: 24px;
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
    gap: 8px;
}

.actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 8px;
}

@media (max-width: 640px) {
    .page-heading h1 {
        font-size: 28px;
    }

    .entry-panel {
        padding: 18px;
    }
}
</style>
