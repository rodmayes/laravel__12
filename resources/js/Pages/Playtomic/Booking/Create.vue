<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { useForm } from '@inertiajs/vue3';
import { ref, watch  } from "vue";
import axios from 'axios';

const props = defineProps({
    clubs: Object,
    resources: Object,
    timetables: Object,
    bookingPreferences: Object,
    players: Object,
    durations: Object,
    status: Object,
    startDate: String,
    title: String
});

const form = useForm({
    started_at: props.startDate || new Date().toISOString().split('T')[0],
    club_id: '',
    resources: [],
    timetables: [],
    booking_preference: '',
    player_email: null,
    duration: '',
    public: false,
});

const submit = () => {
    form.post(route('playtomic.bookings.store'), {
        onSuccess: () => form.reset(),
    });
};

const availableResources = ref(props.resources);

watch(() => form.club_id, async (newClubId) => {
    if (!newClubId) {
        availableResources.value = [];      // Vaciar recursos
        form.resources = [];                // Limpiar selección
        return;
    }

    try {
        const response = await axios.post(route('playtomic.resources.filter', { club: newClubId }));
        availableResources.value = response.data;
        form.resources = [];
    } catch (error) {
        console.error('Error fetching resources:', error);
        availableResources.value = [];
    }
});

const breadcrum = ref([
    { label: 'Playtomic' },
    { label: props.title, url: route('playtomic.bookings.index') },
    { label: 'Create', url: route('playtomic.bookings.create') }
]);
</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Create Booking</h1>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Columna izquierda: Date Picker -->
                    <div class="md:col-span-1">
                        <label value="Start Date" required />
                        <DatePicker
                            :default-date="form.started_at"
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

                    <!-- Columna derecha: todos los campos en vertical -->
                    <div class="md:col-span-2 flex flex-col gap-4">
                        <div  class="flex flex-col gap-2">
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
                <!-- Botones al final del form, fuera del grid -->
                <div class="flex justify-end mt-6 gap-2">
                    <Button type="submit" label="Save"/>
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
