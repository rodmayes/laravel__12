// Por ejemplo, en un composable: useDarkMode.js
import { ref, watchEffect } from 'vue'

const darkMode = ref(localStorage.getItem('theme') === 'dark')

watchEffect(() => {
    if (darkMode.value) {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    }
})

export function useDarkMode() {
    return { darkMode }
}
