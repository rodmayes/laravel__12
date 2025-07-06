// composables/useDarkMode.js
import { watch } from 'vue';

export function useDarkMode(themeRef) {
    const applyTheme = (theme) => {
        const root = document.documentElement;

        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }

        localStorage.setItem('theme', theme);
    };

    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        themeRef.value = savedTheme;
        applyTheme(savedTheme);
    };

    watch(themeRef, (newVal) => {
        applyTheme(newVal);
    });

    return { initTheme };
}
