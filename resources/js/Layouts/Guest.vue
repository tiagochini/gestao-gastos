<script setup>
</script>
<template>
    <div class="background">
        <slot />
    </div>
</template>

<style scoped>
.background {
    min-height: 100vh;
    background: #f8fafc;
    position: relative;
}
</style>
