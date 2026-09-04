<script setup>

import Guest from "@/Layouts/Guest.vue";
import FormLogin from "@/Pages/Authentication/FormLogin.vue";
</script>

<template>
    <Guest>
        <v-col cols="12" lg="12" class="d-flex align-center login-page">
            <v-container>
                <div class="d-flex align-center justify-center" style="min-height: calc(100vh - 148px)">
                    <v-row justify="center">
                        <v-col cols="12" sm="8" md="5" lg="4">
                            <div class="login-brand">
                                <div class="brand-icon">
                                    <v-icon icon="mdi-chart-box-outline" />
                                </div>
                                <strong>Gestao de Gastos</strong>
                            </div>
                            <v-card elevation="10" class="login-card">
                                <v-card-text class="pa-sm-10 pa-6">
                                    <FormLogin/>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>
            </v-container>
        </v-col>
    </Guest>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
}

.login-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-bottom: 24px;
    color: #111827;
    font-size: 22px;
}

.brand-icon {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    color: #ffffff;
    background: #155bd7;
    box-shadow: 0 8px 18px rgba(21, 91, 215, 0.2);
}

.login-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
}
</style>
