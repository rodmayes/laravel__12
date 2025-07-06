<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import images from '@/assets/images.js';

const app_name = import.meta.env.VITE_APP_NAME;

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <img src="/images/letter-r.jpeg" class="w-10 flex-none">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Welcome to {{ app_name }}!</div>
                        <span class="text-muted-color font-medium">Sign in to continue</span>
                    </div>

                    <form @submit.prevent="submit">
                        <div>
                            <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                            <InputText id="email1" type="text" placeholder="Email" class="w-full md:w-[30rem] mb-4" v-model="form.email" />
                            <Message v-if="form.errors.email" class="mb-4" severity="error">{{ form.errors.email }}</Message>

                            <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Password</label>
                            <Password id="password1" v-model="form.password" placeholder="Password" :toggleMask="true" class="mb-4" fluid :feedback="false"></Password>
                            <Message v-if="form.errors.password" class="mb-4" severity="error">{{ form.errors.password }}</Message>

                            <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                                <div class="flex items-center">
                                    <Checkbox v-model="form.remember" id="rememberme1" binary class="mr-2"></Checkbox>
                                    <label for="rememberme1">Remember me</label>
                                </div>
                                <Link
                                    :href="route('password.request')"
                                    class="font-medium no-underline ml-2 text-right cursor-pointer text-primary"
                                >
                                    Forgot password?
                                </Link>
                            </div>
                            <Button type="submit" label="Sign In" class="w-full"></Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
