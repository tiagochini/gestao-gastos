<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.put('/minha-senha', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <h1>Minha senha</h1>
            <p>Altere sua senha de acesso ao sistema.</p>
        </section>

        <section class="panel user-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-lock-reset" size="22" />
                </span>
                <h2>Alterar senha</h2>
            </div>

            <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
                {{ page.props.flash.success }}
            </v-alert>

            <form class="user-form" @submit.prevent="submit">
                <v-text-field
                    v-model="form.current_password"
                    label="Senha atual"
                    variant="outlined"
                    color="primary"
                    type="password"
                    autocomplete="current-password"
                    :error-messages="form.errors.current_password"
                />
                <v-text-field
                    v-model="form.password"
                    label="Nova senha"
                    variant="outlined"
                    color="primary"
                    type="password"
                    autocomplete="new-password"
                    :error-messages="form.errors.password"
                />
                <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirmar nova senha"
                    variant="outlined"
                    color="primary"
                    type="password"
                    autocomplete="new-password"
                    :error-messages="form.errors.password_confirmation"
                />

                <div class="actions">
                    <v-btn color="primary" type="submit" :loading="form.processing">Alterar senha</v-btn>
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
