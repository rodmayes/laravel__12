<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }]
    },
    {
        items: [
            {
                label: 'Playtomic',
                icon: 'pi pi-fw pi-bullseye',
                items: [
                    { label: 'Timetables', icon: 'pi pi-fw pi-clock', to: '/playtomic/timetables', can:'playtomic.timetable_index' },
                    { label: 'Clubs', icon: 'pi pi-fw pi-building-columns', to: '/playtomic/club', can:'playtomic.club_index' },
                    { label: 'Resource', icon: 'pi pi-fw pi-box', to: '/playtomic/resources', class: 'rotated-icon', can:'playtomic.resource_index' },
                    { label: 'Bookings', icon: 'pi pi-fw pi-calendar-clock', to: '/playtomic/bookings', class: 'rotated-icon', can:'playtomic.booking_index' },
                ]
            },

        ]
    },
    {
        items: [
            {
                label: 'Lottery',
                icon: 'pi pi-fw pi-euro',
                items: [
                    { label: 'Combinations', icon: 'pi pi-fw pi-user', to: '/lottery/combinations', can:'lottery.combinations' },
                    { label: 'Results', icon: 'pi pi-fw pi-id-card', to: '/lottery/results', can:'lottery.results' },
                ]
            },

        ]
    },
    {
        items: [
            {
                label: 'Users management',
                icon: 'pi pi-fw pi-cog',
                items: [
                    { label: 'Users', icon: 'pi pi-fw pi-user', to: '/user', can:'user.read' },
                    { label: 'Role', icon: 'pi pi-fw pi-id-card', to: '/role', can:'role.read' },
                    { label: 'Permission', icon: 'pi pi-fw pi-mobile', to: '/permission', class: 'rotated-icon', can:'permission.read' },
                ]
            },

        ]
    },
    {
        items: [
            {
                label: 'Administration',
                icon: 'pi pi-fw pi-cog',
                items: [
                    { label: 'Telescope', icon: 'pi pi-fw pi-user', to: '/telescope', can:'user.read' },
                    { label: 'Horizon', icon: 'pi pi-fw pi-user', to: '/horizon', can:'user.read' },
                    { label: 'Pulse', icon: 'pi pi-fw pi-user', to: '/pulse', can:'user.read' },
                ]
            },

        ]
    },
    {
        items: [
            {
                label: 'Template Source',
                icon: 'pi pi-fw pi-github',
                url: 'https://sakai.primevue.org/',
                target: '_blank'
            }
        ]
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
