<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, ref } from "vue";

import { loadToast } from '@/composables/loadToast';
loadToast();

const props = defineProps({
    show: Boolean,
    title: String,
    timetable: Object
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    playtomic_id_summer: "",
});

const timeValue = ref(null);

// Actualiza `form.name` cuando el usuario cambia el DatePicker
watch(timeValue, (newDate) => {
    if (newDate instanceof Date) {
        const hh = newDate.getHours().toString().padStart(2, '0');
        const mm = newDate.getMinutes().toString().padStart(2, '0');
        form.name = `${hh}:${mm}`;
    }
});

// Solo inicializa al abrir el modal
watch(() => props.show, (showing) => {
    if (showing) {
        form.errors = {};
        form.name = props.timetable?.name ?? "";
        form.playtomic_id = props.timetable?.playtomic_id ?? "";
        form.playtomic_id_summer = props.timetable?.playtomic_id_summer ?? "";

        if (form.name) {
            const [hours, minutes] = form.name.split(":").map(Number);
            const date = new Date();
            date.setHours(hours || 0);
            date.setMinutes(minutes || 0);
            date.setSeconds(0);
            timeValue.value = date;
        } else {
            timeValue.value = null;
        }
    }
});

const update = () => {
    form.put(route("playtomic.timetables.update", props.timetable?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("updated");
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};
</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
            :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Time</label>
                    <DatePicker
                        v-model="timeValue"
                        inline
                        timeOnly
                    />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="playtomic_id">PlaytomicId</label>
                    <InputText id="playtomic_id" v-model="form.playtomic_id" class="flex-auto" autocomplete="off"
                               placeholder="PlaytomicId"/>
                    <small v-if="form.errors.playtomic_id" class="text-red-500">{{ form.errors.playtomic_id }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="playtomic_id_summer">PlaytomicId Summer</label>
                    <InputText id="playtomic_id_summer" v-model="form.playtomic_id_summer" class="flex-auto"
                               autocomplete="off" placeholder="PlaytomicId Summer"/>
                    <small v-if="form.errors.playtomic_id_summer"
                           class="text-red-500">{{ form.errors.playtomic_id_summer }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
