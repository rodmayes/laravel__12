<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import {router, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from "vue";
import axios from 'axios';

import { loadToast } from '@/composables/loadToast';
import { formatDateForInput, formatDateLocal } from "@/composables/useFormatters.js";
loadToast();

const props = defineProps({
    booking: Object,
    clubs: Object,
    resources: Object,
    timetables: Object,
    bookingPreferences: Object,
    players: Object,
    durations: Object,
    title: String,
});

const form = useForm({
    started_at: new Date(props.booking.started_at ?? new Date()),
    club_id: props.booking.club_id,
    resources: (typeof props.booking.resources === 'string' ? props.booking.resources.split(',').map(Number) : props.booking.resources) ?? [],
    timetables: (typeof props.booking.timetables === 'string' ? props.booking.timetables.split(',').map(Number) : props.booking.timetables) ?? [],
    booking_preference: props.booking.booking_preference,
    player_email: props.booking.player_email,
    duration: props.booking.duration,
    public: props.booking.public,
});

const submit = () => {
    form.started_at = formatDateLocal(form.started_at);
    form.put(route('playtomic.bookings.update', props.booking.id), {
        onSuccess: () => {
            form.reset();
            router.visit(route('playtomic.bookings.index'));
        }
    });
};

const availableResources = ref(props.resources);

onMounted(async () => {
    if (form.club_id) {
        const response = await axios.post(route('playtomic.resources.filter', { club: form.club_id }));
        availableResources.value = response.data;
    }
});

watch(() => form.club_id, async (newClubId) => {
    if (!newClubId) {
        availableResources.value = [];
        form.resources = [];
        return;
    }

    try {
        const response = await axios.post(route('playtomic.resources.filter', { club: newClubId }));
        availableResources.value = response.data;
        form.resources = []; // limpiar selección al cambiar club
    } catch (error) {
        console.error('Error fetching resources:', error);
        availableResources.value = [];
    }
});

const breadcrum = ref([
    { label: 'Playtomic' },
    { label: props.title, url: route('playtomic.bookings.index') },
    { label: 'Edit', url: route('playtomic.bookings.edit', props.booking.id) }
]);
</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Edit Booking</h1>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date Picker -->
                    <div class="md:col-span-1">
                        <label value="Start Date" class="required" />
                        <DatePicker
                            v-model="form.started_at"
                            inline
                            dateFormat="yy-mm-dd"
                            :touchUI="false"
                            :showIcon="false"
                            class="w-full"
                            :style="{ minWidth: '18rem' }"
                        />
                        <small v-if="form.errors.started_at" class="text-red-500">{{ form.errors.started_at }}</small>
                    </div>

                    <!-- Resto de campos -->
                    <div class="md:col-span-2 flex flex-col gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="required">Club</label>
                            <Select
                                v-model="form.club_id"
                                :options="props.clubs"
                                optionValue="id"
                                optionLabel="name"
                                placeholder="Select a club"
                            />
                            <small v-if="form.errors.club_id" class="text-red-500">{{ form.errors.club_id }}</small>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="resources" required>Resources</label>
                            <MultiSelect
                                v-model="form.resources"
                                :options="availableResources"
                                optionValue="id"
                                optionLabel="name"
                                placeholder="Select resources"
                                filter
                            />
                            <small v-if="form.errors.resources" class="text-red-500">{{ form.errors.resources }}</small>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="timetables" required>Timetables</label>
                            <MultiSelect
                                v-model="form.timetables"
                                :options="props.timetables"
                                optionValue="id"
                                optionLabel="name"
                                placeholder="Select timetables"
                                filter
                            />
                            <small v-if="form.errors.timetables" class="text-red-500">{{ form.errors.timetables }}</small>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="booking_preference" required>Booking Preference</label>
                            <Select
                                v-model="form.booking_preference"
                                :options="props.bookingPreferences"
                                optionValue="id"
                                optionLabel="name"
                                placeholder="Select preference"
                            />
                            <small v-if="form.errors.booking_preference" class="text-red-500">{{ form.errors.booking_preference }}</small>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label required>Player</label>
                            <Select
                                v-model="form.player_email"
                                :options="props.players"
                                optionValue="email"
                                optionLabel="name"
                                placeholder="Select player"
                            />
                            <small v-if="form.errors.player_email" class="text-red-500">{{ form.errors.player_email }}</small>
                        </div>

                        <div class="flex justify-between">
                            <div>
                                <label for="duration" class="required mr-2">Duration</label>
                                <Select
                                    v-model="form.duration"
                                    :options="props.durations"
                                    optionValue="id"
                                    optionLabel="name"
                                    placeholder="Select duration"
                                />
                                <small v-if="form.errors.duration" class="text-red-500">{{ form.errors.duration }}</small>
                            </div>
                            <div>
                                <label for="public" class="mr-2">Is Public</label>
                                <ToggleButton v-model="form.public" onLabel="Public" offLabel="No public" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6 gap-2">
                    <Button type="submit" label="Update" />
                    <a :href="route('playtomic.bookings.index')" class="border border-gray-300 px-4 py-2 rounded-md">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </app-layout>
</template>

<style scoped>
input:invalid,
select:invalid {
    border-color: red;
}
</style>
