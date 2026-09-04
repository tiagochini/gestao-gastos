<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/usuarios', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Novo usuario</h1>
            <p>Cadastre usuarios que poderao acessar o sistema.</p>
        </section>

        <section class="panel user-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-account-plus-outline" size="22" />
                </span>
                <h2>Dados de acesso</h2>
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
                <v-text-field
                    v-model="form.password"
                    label="Senha"
                    variant="outlined"
                    color="primary"
                    type="password"
                    autocomplete="new-password"
                    :error-messages="form.errors.password"
                />
                <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirmar senha"
                    variant="outlined"
                    color="primary"
                    type="password"
                    autocomplete="new-password"
                    :error-messages="form.errors.password_confirmation"
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
