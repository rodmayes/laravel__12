<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';

const props = defineProps({
    show: Boolean,
    user: Object
});

const emit = defineEmits(['close', 'updated']);

const form = useForm({
    playtomic_id: '',
    playtomic_password: ''
});

const savingId = ref(false);
const savingPassword = ref(false);
const refreshing = ref(false);

watchEffect(() => {
    if (props.show && props.user) {
        form.reset();
        form.playtomic_id = props.user.playtomic_id || '';
    }
});

const saveId = () => {
    savingId.value = true;
    form.put(route('playtomic.user.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => emit('updated'),
        onFinish: () => savingId.value = false
    });
};

const savePassword = () => {
    savingPassword.value = true;
    form.put(route('playtomic.user.save-password', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('updated');
            form.playtomic_password = '';
        },
        onFinish: () => savingPassword.value = false
    });
};

const refreshToken = () => {
    refreshing.value = true;
    form.get(route('playtomic.user.refresh-token', props.user.id), {
        onSuccess: () => emit('updated'),
        onFinish: () => refreshing.value = false
    });
};
</script>

<template>
    <Dialog
        v-model:visible="props.show"
        header="Editar Playtomic"
        modal
        :style="{ width: '40rem' }"
        :closable="false"
    >
        <div class="flex flex-col gap-4">
            <!-- ID -->
            <div>
                <label for="playtomic_id" class="block mb-1">Playtomic ID</label>
                <InputGroup>
                    <InputText
                        id="playtomic_id"
                        type="text"
                        v-model="form.playtomic_id"
                        class="w-full"
                    />
                    <InputGroupAddon>
                        <Button
                            icon="pi pi-save"
                            severity="secondary"
                            @click="saveId"
                            :loading="savingId"
                            title="Guardar ID"
                        />
                    </InputGroupAddon>
                </InputGroup>
                <small v-if="form.errors.playtomic_id" class="text-red-500">{{ form.errors.playtomic_id }}</small>
            </div>

            <!-- Password -->
            <div>
                <label for="playtomic_password" class="block mb-1">Playtomic Password</label>
                <div class="p-inputgroup">
                    <InputGroup>
                        <InputText
                            id="playtomic_password"
                            type="password"
                            v-model="form.playtomic_password"
                            class="w-full"
                            autocomplete="new-password"
                        />
                        <InputGroupAddon>
                            <Button
                                icon="pi pi-save"
                                severity="secondary"
                                @click="savePassword"
                                :loading="savingPassword"
                            />
                        </InputGroupAddon>
                    </InputGroup>

                </div>
                <small v-if="form.errors.playtomic_password" class="text-red-500">{{
                        form.errors.playtomic_password
                    }}</small>
            </div>

            <!-- Refresh Token -->
            <div class="flex justify-between items-center gap-2 mt-4">
                <Button label="Refresh Playtomic Token" icon="pi pi-refresh" @click="refreshToken"
                        :loading="refreshing"/>
                <Button label="Cerrar" severity="secondary" @click="emit('close')"/>
            </div>

            <!-- Token Info -->
            <div class="border-t pt-4 mt-4">
                <div>
                    <strong>Token:</strong>
                    <pre class="p-2 bg-gray-100 rounded">{{ props.user.playtomic_token || '-' }}</pre>
                </div>
                <div class="mt-2">
                    <strong>Refresh Token:</strong>
                    <pre class="p-2 bg-gray-100 rounded">{{ props.user.playtomic_refresh_token || '-' }}</pre>
                </div>
            </div>
        </div>
    </Dialog>
</template>
