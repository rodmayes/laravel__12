<script setup>
import { useLayout } from '@/sakai/layout/composables/layout';
import { onBeforeMount, ref, watch } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const { layoutState, setActiveMenuItem, onMenuToggle } = useLayout();

const props = defineProps({
    item: {
        type: Object,
        default: () => ({})
    },
    index: {
        type: Number,
        default: 0
    },
    root: {
        type: Boolean,
        default: true
    },
    parentItemKey: {
        type: String,
        default: null
    }
});

const isActiveMenu = ref(false);
const itemKey = ref(null);

onBeforeMount(() => {
    itemKey.value = props.parentItemKey ? props.parentItemKey + '-' + props.index : String(props.index);

    const activeItem = layoutState.activeMenuItem;
    const activeByState = activeItem === itemKey.value || (activeItem && activeItem.startsWith(itemKey.value + '-'));
    const activeByRoute = hasActiveChild(props.item);

    isActiveMenu.value = activeByState || activeByRoute;
});

watch(
    () => layoutState.activeMenuItem,
    (newVal) => {
        const activeByState = newVal === itemKey.value || (newVal && newVal.startsWith(itemKey.value + '-'));
        const activeByRoute = hasActiveChild(props.item);

        isActiveMenu.value = activeByState || activeByRoute;
    }
);

const itemClick = (event, item) => {
    if (item.disabled) {
        event.preventDefault();
        return;
    }

    if ((item.to || item.url) && (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)) {
        onMenuToggle();
    }

    if (item.command) {
        item.command({ originalEvent: event, item: item });
    }

    const foundItemKey = item.items ? (isActiveMenu.value ? props.parentItemKey : itemKey) : itemKey.value;

    setActiveMenuItem(foundItemKey);
};

const hasActiveChild = (item) => {
    if (!item.items) return false;

    return item.items.some((child) => {
        if (child.items) {
            return hasActiveChild(child); // soporte para sub-submenús
        }

        return child.to && page.url.startsWith(child.to);
    });
};


// const checkActiveRoute = (item) => {
//     return route.path === item.to;
// };
</script>

<template>
    <li :class="{ 'layout-root-menuitem': root, 'active-menuitem': isActiveMenu }">
        <!-- <div v-if="root && item.visible !== false" class="layout-menuitem-root-text">{{ item.label }}</div> -->
        <a v-if="(!item.to || item.items) && item.visible !== false" :href="item.url" @click="itemClick($event, item, index)" :class="item.class" :target="item.target" tabindex="0">
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i class="pi pi-fw pi-angle-down layout-submenu-toggler" v-if="item.items"></i>
        </a>
        <nav-link v-if="item.to && !item.items && item.visible !== false" @click="itemClick($event, item, index)" :href="item.to" :class="[item.class, { 'active-route': $page.url.startsWith(item.to) }]">
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i class="pi pi-fw pi-angle-down layout-submenu-toggler" v-if="item.items"></i>
        </nav-link>
        <Transition v-if="item.items && item.visible !== false" name="layout-submenu">
            <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
                <app-menu-item v-show="!child?.can || can([child.can])" v-for="(child, i) in item.items" :key="child" :index="i" :item="child" :parentItemKey="itemKey" :root="false"></app-menu-item>
            </ul>
        </Transition>
    </li>
</template>

<style lang="scss" scoped></style>
