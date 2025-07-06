import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import Aura from '@primevue/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import toastPlugin from './composables/toastPlugin'

import '@/sakai/assets/styles.scss';
import '@/sakai/assets/tailwind.css';

const appName = import.meta.env.VITE_APP_NAME;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                locale: {
                    firstDayOfWeek: 1,
                },
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: '.app-dark'
                    }
                }
            })
            .use(ToastService)
            .component('Toast', Toast)
            .use(toastPlugin)
            .use(ConfirmationService)
            .mixin({
                methods: {
                    can(permissions) {
                        const allPermissions = this.$page.props.auth.can;
                        return permissions.some(item => allPermissions[item]);
                    }
                }
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
