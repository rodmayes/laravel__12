<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, ref } from "vue";

const props = defineProps({
    show: Boolean,
    title: String
});

const emit = defineEmits(["close", "updated"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    playtomic_id_summer: "",
});

const timeValue = ref(null);

watch(timeValue, (newDate) => {
    if (newDate instanceof Date) {
        const hh = newDate.getHours().toString().padStart(2, '0');
        const mm = newDate.getMinutes().toString().padStart(2, '0');
        form.name = `${hh}:${mm}`;
    }
});

watch(() => props.show, (showing) => {
    if (showing) {
        form.reset();
        form.clearErrors();
        timeValue.value = null;
    }
});

const store = () => {
    form.post(route("playtomic.timetables.store"), {
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
    <Dialog v-model:visible="props.show" position="top" modal :header="'Create ' + props.title"
            :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="store">
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
                    <Button type="submit" label="Create"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
