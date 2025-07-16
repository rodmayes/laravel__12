<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import {reactive, ref} from 'vue';
import { onMounted, onBeforeUnmount } from 'vue';
import TerminalService from "primevue/terminalservice";
import axios from 'axios';

const props = defineProps({
    title: String,
});

const breadcrum = ref([
    { label: 'Admin' },
    { label: props.title, url: route('admin.terminal.index') }
])

onMounted(() => {
    TerminalService.on('command', commandHandler);
})

onBeforeUnmount(() => {
    TerminalService.off('command', commandHandler);
})

const commandHandler = async (text) => {
    let response;
    let argsIndex = text.indexOf(' ');
    let command = argsIndex !== -1 ? text.substring(0, argsIndex) : text;

    switch(command) {
        case "date":
            response = 'Today is ' + new Date().toDateString();
            break;

        case "greet":
            response = 'Hola ' + text.substring(argsIndex + 1);
            break;

        case "random":
            response = Math.floor(Math.random() * 100);
            break;

        case "artisan":
            try {
                const res = await axios.post('/api/terminal/command', {
                    command: args || 'lottery:generate-magik-numbers' // o cualquier comando por defecto
                });
                response = res.data.output;
            } catch (e) {
                response = e.response?.data?.output || 'Comando fallido';
            }
            break;

        default:
            response = "Unknown command: " + command;
    }

    TerminalService.emit('response', response);
}
</script>

<template>
    <app-layout :items="breadcrum">
    <Terminal
        welcomeMessage="Welcome to terminal"
        prompt="$"
        aria-label="Terminal Service"
    />
    </app-layout>
</template>

<style scoped lang="scss">

</style>
