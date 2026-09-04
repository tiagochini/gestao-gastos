<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth.user);
const initials = computed(() => {
    const name = user.value?.name ?? 'Usuario';
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
});

const menuItems = computed(() => [
    { label: 'Dashboard', href: '/dashboard', icon: 'mdi-home-outline', active: page.url.startsWith('/dashboard') },
    { label: 'Lancamentos', href: '/lancamentos', icon: 'mdi-file-document-outline', active: page.url.startsWith('/lancamentos') },
    { label: 'Mensalidades', href: '/mensalidades', icon: 'mdi-account-cash-outline', active: page.url.startsWith('/mensalidades') },
    { label: 'Categorias', href: '/categorias', icon: 'mdi-tag-outline', active: page.url.startsWith('/categorias') },
    { label: 'Relatorios', href: '/relatorios', icon: 'mdi-chart-bar', active: page.url.startsWith('/relatorios') },
    { label: 'Metas', href: '#', icon: 'mdi-target' },
    { label: 'Contas', href: '#', icon: 'mdi-wallet-outline' },
    { label: 'Usuarios', href: '/usuarios', icon: 'mdi-account-group-outline', active: page.url.startsWith('/usuarios') || page.url.startsWith('/minha-senha') },
]);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <v-app>
        <div class="app-shell">
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-icon">
                        <v-icon icon="mdi-chart-box-outline" />
                    </div>
                    <strong>Gestao de Gastos</strong>
                </div>

                <nav class="nav-list" aria-label="Principal">
                    <Link
                        v-for="item in menuItems"
                        :key="item.label"
                        :href="item.href"
                        class="nav-item"
                        :class="{ active: item.active }"
                    >
                        <v-icon :icon="item.icon" size="22" />
                        <span>{{ item.label }}</span>
                    </Link>
                </nav>

                <button class="logout-button" type="button" @click="logout">
                    <v-icon icon="mdi-logout" size="22" />
                    <span>Sair</span>
                </button>
            </aside>

            <main class="main-area">
                <header class="topbar">
                    <div></div>
                    <div class="user-actions">
                        <v-btn icon="mdi-bell-outline" variant="text" color="default" aria-label="Notificacoes" />
                        <div class="user-chip">
                            <div class="avatar">{{ initials }}</div>
                            <span>{{ user?.name }}</span>
                            <v-icon icon="mdi-chevron-down" size="20" />
                        </div>
                    </div>
                </header>

                <slot />
            </main>
        </div>
    </v-app>
</template>

<style scoped>
.app-shell {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 286px 1fr;
    background: #f8fafc;
    color: #111827;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.sidebar {
    min-height: 100vh;
    border-right: 1px solid #e5e7eb;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    padding: 32px 18px 20px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 6px 34px;
    font-size: 20px;
}

.brand-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    color: #ffffff;
    background: #155bd7;
    box-shadow: 0 8px 18px rgba(21, 91, 215, 0.2);
}

.nav-list {
    display: grid;
    gap: 8px;
}

.nav-item,
.logout-button {
    height: 56px;
    display: flex;
    align-items: center;
    gap: 16px;
    border-radius: 6px;
    color: #4b5563;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
    padding: 0 16px;
}

.nav-item.active {
    color: #155bd7;
    background: #eaf1ff;
    font-weight: 700;
}

.logout-button {
    margin-top: auto;
    border: 0;
    border-top: 1px solid #e5e7eb;
    border-radius: 0;
    background: transparent;
    cursor: pointer;
    text-align: left;
}

.main-area {
    min-width: 0;
    padding: 28px 36px 38px;
}

.topbar {
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}

.user-actions {
    display: flex;
    align-items: center;
    gap: 18px;
}

.user-chip {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    color: #374151;
}

.avatar {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #155bd7;
    color: #ffffff;
    font-weight: 700;
}

@media (max-width: 900px) {
    .app-shell {
        grid-template-columns: 1fr;
    }

    .sidebar {
        min-height: auto;
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 14px 16px;
        border-right: 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .brand {
        padding: 0 0 12px;
        font-size: 18px;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
    }

    .nav-list {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: thin;
    }

    .nav-item {
        flex: 0 0 auto;
        height: 44px;
        padding: 0 12px;
        font-size: 14px;
    }

    .logout-button {
        position: absolute;
        right: 12px;
        top: 10px;
        width: 44px;
        height: 44px;
        justify-content: center;
        border-top: 0;
        padding: 0;
    }

    .logout-button span {
        display: none;
    }

    .main-area {
        padding: 20px;
    }

    .topbar {
        min-height: 42px;
        margin-bottom: 14px;
    }
}

@media (max-width: 560px) {
    .main-area {
        padding: 16px 12px 24px;
    }

    .user-actions {
        width: 100%;
        justify-content: flex-end;
        gap: 8px;
    }

    .user-chip span {
        display: none;
    }

    .avatar {
        width: 36px;
        height: 36px;
        font-size: 13px;
    }
}
</style>
