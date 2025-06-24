<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";
import { loadToast } from '@/composables/loadToast';
loadToast();

const props = defineProps({
    show: Boolean,
    title: String
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    days_min_booking: 3,
    timetable_summer: false,
    booking_hour: "08:00:00"
});

const create = () => {
    form.post(route("playtomic.clubs.store"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
    }
});

</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Add ' + props.title" :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="create">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="email">PlaytomicId</label>
                    <InputText id="email" v-model="form.playtomic_id" class="flex-auto" autocomplete="off" placeholder="PlaytomicId" />
                    <small v-if="form.errors.playtomic_id" class="text-red-500">{{ form.errors.playtomic_id }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="days_min_booking">Days min booking</label>
                    <InputText id="days_min_booking" v-model="form.days_min_booking" type="number" placeholder="Days min booking" />
                    <small v-if="form.errors.days_min_booking" class="text-red-500">{{ form.errors.days_min_booking }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="booking_hour">Booking hour</label>
                    <DatePicker id="booking_hour" v-model="form.booking_hour" hourFormat="24" timeOnly fluid :modelValue="form.booking_hour"/>
                    <small v-if="form.errors.booking_hour" class="text-red-500">{{ form.errors.booking_hour }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="timetable_summer">Timetable summer</label>
                    <ToggleButton v-model="form.timetable_summer" onLabel="On" offLabel="Off" modelValue="false"/>
                    <small v-if="form.errors.timetable_summer" class="text-red-500">{{ form.errors.timetable_summer }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Save"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
