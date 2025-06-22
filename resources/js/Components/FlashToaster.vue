<script setup>
import { getCurrentInstance, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

watch(
    () => page.props.flash,
    async (flash) => {
        const $toast = getCurrentInstance()?.appContext.config.globalProperties.$toast;
        if (!flash || typeof flash !== 'object' || !$toast) return;

        await nextTick(); // espera a que se registre el Toast si es necesario

        Object.entries(flash).forEach(([type, message]) => {
            if (!message) return;

            const summary = type.charAt(0).toUpperCase() + type.slice(1);
            const severity = type in $toast ? type : 'info';

            if (Array.isArray(message)) {
                message.forEach(msg => $toast.show(msg, summary, severity));
            } else {
                $toast.show(message, summary, severity);
            }
        });

        // Forzar mostrar la cola si se llenó antes de tener instancia
        $toast._flush?.();
    },
    { immediate: true, deep: true }
);
</script>

<template>
    <!-- No muestra nada -->
</template>
