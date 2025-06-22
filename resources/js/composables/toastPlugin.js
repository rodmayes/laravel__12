import FlashToaster from '@/Components/FlashToaster.vue';

export default {
    install(app) {
        let toastInstance = null;
        const queue = [];

        const show = (detail, summary = 'OK', severity = 'success') => {
            const payload = { severity, summary, detail, life: 3000 };
            if (toastInstance) {
                toastInstance.add(payload);
            } else {
                queue.push(payload);
            }
        };

        const flushQueue = () => {
            if (toastInstance && queue.length) {
                queue.forEach(item => toastInstance.add(item));
                queue.length = 0;
            }
        };

        const toastAPI = {
            show,
            success: (msg, summary = 'Success') => show(msg, summary, 'success'),
            error: (msg, summary = 'Error') => show(msg, summary, 'error'),
            info: (msg, summary = 'Info') => show(msg, summary, 'info'),
            warn: (msg, summary = 'Warning') => show(msg, summary, 'warn'),
            _registerInstance(instance) {
                toastInstance = instance;
                flushQueue();
            },
            _flush: flushQueue
        };

        app.config.globalProperties.$toast = toastAPI;
        app.provide('$toast', toastAPI);

        app.component('FlashToaster', FlashToaster);
    }
};
