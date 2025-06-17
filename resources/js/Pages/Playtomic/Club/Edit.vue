<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    club: Object
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    days_min_booking: "",
    timetable_summer: "",
    booking_hour: ""
});

const update = () => {
    form.put(route("playtomic.clubs.update", props.club?.id), {
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
        form.name = props.club?.name;
        form.playtomic_id = props.club?.playtomic_id;
        form.days_min_booking = props.club?.days_min_booking;
        form.timetable_summer = props.club?.timetable_summer;
        form.booking_hour = props.club?.booking_hour;
    }
});


</script>

<template>
     <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title" :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
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
                    <label for="password">Days min Booking</label>
                    <InputText id="days_min_booking" v-model="form.days_min_booking" type="number" placeholder="Days min booking" />
                    <small v-if="form.errors.days_min_booking" class="text-red-500">{{ form.errors.days_min_booking }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="password">Booking hour</label>
                    <DatePicker id="booking_hour" v-model="form.booking_hour" hourFormat="24" timeOnly fluid :modelValue="form.booking_hour"/>
                    <small v-if="form.errors.booking_hour" class="text-red-500">{{ form.errors.booking_hour }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="password_confirmation">Timetable Summer</label>
                    <ToggleButton v-model="form.timetable_summer" onLabel="On" offLabel="Off" />
                    <small v-if="form.errors.timetable_summer" class="text-red-500">{{ form.errors.timetable_summer }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
