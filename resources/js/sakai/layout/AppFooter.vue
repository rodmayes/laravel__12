<script setup>

const app_name = import.meta.env.VITE_APP_NAME;
</script>

<template>
    <div class="layout-footer">
        {{ app_name }}
        <a href="https://primevue.org" target="_blank" rel="noopener noreferrer" class="text-primary font-bold hover:underline">PrimeVue</a>
    </div>
</template>
