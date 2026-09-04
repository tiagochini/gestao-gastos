<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    active: props.user.active,
});

function submit() {
    form.put(`/usuarios/${props.user.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Editar usuario</h1>
            <p>Atualize os dados de acesso e o status do usuario.</p>
        </section>

        <section class="panel user-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-account-edit-outline" size="22" />
                </span>
                <h2>Dados do usuario</h2>
            </div>

            <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
                {{ page.props.flash.success }}
            </v-alert>

            <form class="user-form" @submit.prevent="submit">
                <v-text-field
                    v-model="form.name"
                    label="Nome"
                    variant="outlined"
                    color="primary"
                    autocomplete="name"
                    :error-messages="form.errors.name"
                />
                <v-text-field
                    v-model="form.email"
                    label="E-mail"
                    variant="outlined"
                    color="primary"
                    type="email"
                    autocomplete="email"
                    :error-messages="form.errors.email"
                />
                <v-switch
                    v-model="form.active"
                    color="primary"
                    label="Usuario ativo"
                    :error-messages="form.errors.active"
                    hide-details="auto"
                    class="mb-4"
                />

                <div class="actions">
                    <v-btn color="primary" type="submit" :loading="form.processing">Salvar</v-btn>
                    <Link href="/usuarios" as="span">
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
    max-width: 760px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.user-panel {
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

.user-form {
    display: grid;
    gap: 8px;
}

.actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 8px;
}
</style>
