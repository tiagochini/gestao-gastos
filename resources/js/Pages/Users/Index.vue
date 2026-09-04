<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const page = usePage();

function deactivate(user) {
    if (!window.confirm(`Desativar o usuario ${user.name}?`)) {
        return;
    }

    router.delete(`/usuarios/${user.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <section class="page-heading">
            <div>
                <h1>Usuarios</h1>
                <p>Gerencie quem pode acessar o sistema interno.</p>
            </div>
            <div class="heading-actions">
                <Link href="/minha-senha" as="span">
                    <v-btn variant="outlined">Minha senha</v-btn>
                </Link>
                <Link href="/usuarios/novo" as="span">
                    <v-btn color="primary">
                        <v-icon icon="mdi-plus" />
                        Novo usuario
                    </v-btn>
                </Link>
            </div>
        </section>

        <v-alert v-if="page.props.flash.success" color="success" variant="tonal" class="mb-5">
            {{ page.props.flash.success }}
        </v-alert>
        <v-alert v-if="Object.keys(page.props.errors).length && !page.props.errors.user" color="error" variant="tonal" class="mb-5">
            Confira os dados informados e tente novamente.
        </v-alert>

        <section class="panel table-panel">
            <div class="panel-title">
                <span class="title-icon">
                    <v-icon icon="mdi-account-group-outline" size="22" />
                </span>
                <h2>Usuarios cadastrados</h2>
            </div>

            <div v-if="page.props.errors.user" class="px-6 pb-4">
                <v-alert color="error" variant="tonal">{{ page.props.errors.user }}</v-alert>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="empty-cell">
                                <div class="empty-state">
                                    <v-icon icon="mdi-account-plus-outline" size="34" />
                                    <strong>Nenhum usuario cadastrado</strong>
                                    <span>Crie um usuario para permitir acesso ao sistema.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span class="status-tag" :class="user.active ? 'active' : 'inactive'">
                                    {{ user.active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <Link :href="`/usuarios/${user.id}/editar`" as="span">
                                    <v-btn icon="mdi-pencil-outline" size="small" variant="text" aria-label="Editar" />
                                </Link>
                                <v-btn
                                    icon="mdi-account-off-outline"
                                    size="small"
                                    variant="text"
                                    color="error"
                                    aria-label="Desativar"
                                    :disabled="!user.active || page.props.auth.user.id === user.id"
                                    @click="deactivate(user)"
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
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
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

.heading-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.panel {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.panel-title {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 24px 18px;
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

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 720px;
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

.status-tag {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
}

.status-tag.active {
    color: #15803d;
    background: #dcfce7;
}

.status-tag.inactive {
    color: #6b7280;
    background: #f3f4f6;
}

.actions-cell {
    white-space: nowrap;
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

@media (max-width: 720px) {
    .page-heading {
        display: grid;
    }
}
</style>
