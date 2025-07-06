<script setup>
import {useLayout} from '@/sakai/layout/composables/layout';
import {computed, ref, watch, watchEffect, onMounted, getCurrentInstance} from 'vue';
import Toast from 'primevue/toast';
import AppFooter from './AppFooter.vue';
import AppSidebar from './AppSidebar.vue';
import AppTopbar from './AppTopbar.vue';
import FlashToaster from '@/Components/FlashToaster.vue';

const toastRef = ref();

const props = defineProps({
    items: Object
});

onMounted(() => {
    const inst = getCurrentInstance()?.appContext.config.globalProperties.$toast;
    if (inst && toastRef.value) {
        inst._registerInstance(toastRef.value);
    }

    window.addEventListener('inertia:finish', () => {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    })
});

const {layoutConfig, layoutState, isSidebarActive, resetMenu} = useLayout();

const outsideClickListener = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

const containerClass = computed(() => {
    return {
        'layout-overlay': layoutConfig.menuMode === 'overlay',
        'layout-static': layoutConfig.menuMode === 'static',
        'layout-static-inactive': layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive,
        'layout-mobile-active': layoutState.staticMenuMobileActive
    };
});

const bindOutsideClickListener = () => {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                resetMenu();
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
};
const unbindOutsideClickListener = () => {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener);
        outsideClickListener.value = null;
    }
};
const isOutsideClicked = (event) => {
    const sidebarEl = document.querySelector('.layout-sidebar');
    const topbarEl = document.querySelector('.layout-menu-button');

    return !(sidebarEl?.contains(event.target) || topbarEl?.contains(event.target));
};

const home = ref({
    icon: 'pi pi-home'
});

let breadcrumbItems = ref([]);

watchEffect(() => {
    if (props.items) {
        breadcrumbItems = props.items;
    }
});
</script>

<template>
    <div class="layout-wrapper" :class="containerClass">
        <app-topbar/>
        <app-sidebar/>
        <div class="layout-main-container">
            <div class="layout-main">
                <Breadcrumb :home="home" :model="breadcrumbItems">
                    <template #item="{ item }">
                        <a v-if="item.url" class="cursor-pointer" :href="item.url">
                            <span :class="item.icon">{{ item.label }}</span>
                        </a>
                        <span :class="item.icon" v-else>{{ item.label }}</span>
                    </template>
                    <template #separator> /</template>
                </Breadcrumb>
                <slot></slot>
            </div>
            <app-footer/>
        </div>
        <div class="layout-mask animate-fadein"></div>
    </div>
    <Toast ref="toastRef"/>
    <FlashToaster />
</template>
