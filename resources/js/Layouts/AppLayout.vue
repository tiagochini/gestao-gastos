<script setup>
import { icons } from '@/icons';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const mobileMenuOpen = ref(false);

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
    { label: 'Dashboard', href: '/dashboard', icon: icons.homeOutline, active: page.url.startsWith('/dashboard') },
    { label: 'Lançamentos', href: '/lancamentos', icon: icons.fileDocumentOutline, active: page.url.startsWith('/lancamentos') },
    { label: 'Mensalidades', href: '/mensalidades', icon: icons.accountCashOutline, active: page.url.startsWith('/mensalidades') },
    { label: 'Categorias', href: '/categorias', icon: icons.tagOutline, active: page.url.startsWith('/categorias') },
    { label: 'Relatórios', href: '/relatorios', icon: icons.chartBar, active: page.url.startsWith('/relatorios') },
    { label: 'Usuários', href: '/usuarios', icon: icons.accountGroupOutline, active: page.url.startsWith('/usuarios') || page.url.startsWith('/minha-senha') },
]);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <v-app>
        <div class="app-shell">
            <aside class="sidebar" :class="{ 'mobile-open': mobileMenuOpen }">
                <div class="brand">
                    <div class="brand-icon">
                        <v-icon :icon="icons.chartBoxOutline" />
                    </div>
                    <strong>Gestao de Gastos</strong>
                    <button class="sidebar-close" type="button" aria-label="Fechar menu" @click="mobileMenuOpen = false">
                        <v-icon :icon="icons.close" size="24" />
                    </button>
                </div>

                <nav class="nav-list" aria-label="Principal">
                    <Link
                        v-for="item in menuItems"
                        :key="item.label"
                        :href="item.href"
                        class="nav-item"
                        :class="{ active: item.active }"
                        @click="mobileMenuOpen = false"
                    >
                        <v-icon :icon="item.icon" size="22" />
                        <span>{{ item.label }}</span>
                    </Link>
                </nav>

                <button class="logout-button" type="button" @click="logout">
                    <v-icon :icon="icons.logout" size="22" />
                    <span>Sair</span>
                </button>
            </aside>

            <button
                v-if="mobileMenuOpen"
                class="sidebar-backdrop"
                type="button"
                aria-label="Fechar menu"
                @click="mobileMenuOpen = false"
            ></button>

            <main class="main-area">
                <header class="topbar">
                    <div class="mobile-header">
                        <button
                            class="menu-button"
                            type="button"
                            aria-label="Abrir menu"
                            :aria-expanded="mobileMenuOpen"
                            @click="mobileMenuOpen = true"
                        >
                            <v-icon :icon="icons.menu" size="25" />
                        </button>
                        <strong>Gestao de Gastos</strong>
                    </div>
                    <div class="user-actions">
                        <v-btn :icon="icons.bellOutline" variant="text" color="default" aria-label="Notificações" />
                        <div class="user-chip">
                            <div class="avatar">{{ initials }}</div>
                            <span>{{ user?.name }}</span>
                            <v-icon :icon="icons.chevronDown" size="20" />
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

.mobile-header,
.sidebar-close,
.sidebar-backdrop {
    display: none;
}

@media (max-width: 900px) {
    .app-shell {
        display: block;
        min-width: 0;
    }

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: min(320px, 86vw);
        min-width: 0;
        min-height: 100dvh;
        top: 0;
        z-index: 30;
        padding: 22px 18px 20px;
        border-right: 1px solid #e5e7eb;
        transform: translateX(-100%);
        visibility: hidden;
        pointer-events: none;
        transition: transform 180ms ease;
        overflow-y: auto;
        box-shadow: 18px 0 36px rgba(15, 23, 42, 0.16);
    }

    .sidebar.mobile-open {
        transform: translateX(0);
        visibility: visible;
        pointer-events: auto;
    }

    .brand {
        padding: 0 4px 26px;
        font-size: 18px;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
    }

    .nav-list {
        display: grid;
        gap: 8px;
    }

    .nav-item {
        height: 52px;
        padding: 0 14px;
        font-size: 15px;
    }

    .logout-button {
        position: static;
        width: 100%;
        height: 52px;
        justify-content: flex-start;
        border-top: 1px solid #e5e7eb;
        padding: 0 14px;
    }

    .logout-button span {
        display: inline;
    }

    .sidebar-close {
        width: 40px;
        height: 40px;
        margin-left: auto;
        display: grid;
        place-items: center;
        border: 0;
        border-radius: 6px;
        color: #4b5563;
        background: transparent;
        cursor: pointer;
    }

    .sidebar-backdrop {
        position: fixed;
        inset: 0;
        z-index: 20;
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        background: rgba(15, 23, 42, 0.42);
        cursor: pointer;
    }

    .main-area {
        width: 100%;
        min-width: 0;
        padding: 20px;
    }

    .topbar {
        min-height: 42px;
        margin-bottom: 14px;
    }

    .mobile-header {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-header strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .menu-button {
        width: 42px;
        height: 42px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        border: 1px solid #dbe3ee;
        border-radius: 7px;
        color: #155bd7;
        background: #ffffff;
        cursor: pointer;
    }

    .user-actions {
        gap: 8px;
    }

    .user-chip span,
    .user-chip > .v-icon {
        display: none;
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
