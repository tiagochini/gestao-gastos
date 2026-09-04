<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const showPassword = ref(false);
const form = useForm({
    email: 'admin@turingdesenvolvimento.com',
    password: 'admin@123',
    remember: false,
});

const canSubmit = computed(() => form.email.trim() !== '' && form.password !== '' && !form.processing);

function submit() {
    form.email = form.email.trim();
    form.post('/login', {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div class="d-flex justify-space-between align-center">
        <h3 class="text-h3 text-center mb-0">Login</h3>
    </div>
    <form class="mt-7 loginForm" @submit.prevent="submit">
        <div class="mb-6">
            <v-label>E-mail</v-label>
            <v-text-field
                v-model="form.email"
                aria-label="e-mail"
                class="mt-2"
                required
                hide-details="auto"
                variant="outlined"
                color="primary"
                type="email"
                autocomplete="email"
                :error-messages="form.errors.email"
            ></v-text-field>
        </div>
        <div>
            <v-label>Senha</v-label>
            <v-text-field
                v-model="form.password"
                aria-label="senha"
                required
                variant="outlined"
                color="primary"
                hide-details="auto"
                :type="showPassword ? 'text' : 'password'"
                class="mt-2"
                autocomplete="current-password"
                :error-messages="form.errors.password"
            >
                <template v-slot:append-inner>
                    <v-btn
                        color="secondary"
                        icon
                        rounded
                        variant="text"
                        type="button"
                        :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                        @click="showPassword = !showPassword"
                    >
                        <v-icon :icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'" />
                    </v-btn>
                </template>
            </v-text-field>
        </div>

        <div class="d-flex align-center mt-4 mb-7 mb-sm-0">
            <v-checkbox
                v-model="form.remember"
                label="Manter conectado"
                color="primary"
                class="ms-n2"
                hide-details
            ></v-checkbox>
        </div>
        <v-btn color="primary" :loading="form.processing" block class="mt-5" variant="flat" size="large" :disabled="!canSubmit" type="submit">
            Entrar
        </v-btn>
    </form>
</template>
<style lang="scss">
.loginForm {
    .v-text-field .v-field--active input {
        font-weight: 500;
    }
}
</style>
