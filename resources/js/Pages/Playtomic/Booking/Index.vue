<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';

import { onMounted, reactive, ref } from "vue";
import pkg from "lodash";
const { pickBy } = pkg;
import { loadToast } from "@/composables/loadToast";
import axios from "axios";
import { parseISO, subDays, format, isSameDay, isBefore } from "date-fns";

const props = defineProps({
    title: String,
    filters: Object,
    items: Object,
    clubs: Object,
    players: Object,
    timetables: Object,
    perPage: Number,
});

loadToast();

const deleteDialog = ref(false);
const form = useForm({});
const itemsRef = reactive({ ...props.items });

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage ?? 20,
        club: null,
        status: 'on-time',
        booked: null,
        player: null,
        timetable: null
    },
    editOpen: false,
    booking: null
});

const getModifiedDate = (booking) => {
    if (!booking?.started_at || !booking?.club?.days_min_booking) return null;
    return subDays(parseISO(booking.started_at), booking.club.days_min_booking);
};

const formatDate = (date) => format(date, "dd-MM-yyyy");
const isToday = (date) => isSameDay(date, new Date());
const isPast = (date) => isBefore(date, new Date());

const fetchData = () => {
    const params = pickBy(data.params, v => v !== null && v !== undefined);

    axios.post(route('playtomic.bookings.refreshData'), params)
        .then(response => {
            response.data.items.data = response.data.items.data.map(item => {
                const modified = getModifiedDate(item);
                return {
                    ...item,
                    modified_at: modified?.toISOString()
                };
            });

            Object.assign(itemsRef, response.data.items);
        });
};

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("playtomic.bookings.destroy", data.booking?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            fetchData();
        }
    });
};

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    data.params.perPage = event.rows;
    fetchData();
};

const onSortChange = (event) => {
    const sortFields = event.multiSortMeta?.map(sort => ({
        field: sort.field,
        order: sort.order === 1 ? 'asc' : 'desc'
    }));
    data.params.sort = JSON.stringify(sortFields);
    fetchData();
};

const onRefresh = () => fetchData();

onMounted(() => {
    fetchData();
});

const oStatus = ref([
    { name: 'On time', value: 'on-time' },
    { name: 'Closed', value: 'closed' },
    { name: 'Time Out', value: 'time-out' }
]);

const popoverResources = ref();

const toggleResources = (event) => {
    popoverResources.value.toggle(event);
}

const breadcrum = ref([
    { label: 'Playtomic' },
    { label: props.title, url: route('playtomic.bookings.index') }
]);
</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button
                        v-show="can(['playtomic.booking_create'])"
                        label="Add Booking"
                        @click="router.visit(route('playtomic.bookings.create'))"
                        icon="pi pi-plus"
                        severity="success"
                        size="small"
                    />
                </template>
                <template #end>
                    <Select v-model="data.params.club" :options="props.clubs" optionValue="id" optionLabel="name" placeholder="Filter by Club" showClear @change="fetchData" class="mr-2" />
                    <Select v-model="data.params.player" :options="props.players" optionValue="id" optionLabel="name" placeholder="Filter by Player" showClear @change="fetchData" class="mr-2" />
                    <SelectButton v-model="data.params.status" :options="oStatus" optionValue="value" optionLabel="name" @change="fetchData" />
                </template>
            </Menubar>

            <DataTable
                :value="itemsRef.data"
                lazy
                paginator
                :rows="itemsRef.per_page"
                :totalRecords="itemsRef.total"
                :first="(itemsRef.current_page - 1) * itemsRef.per_page"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                sortMode="multiple"
                :sortField="'modified_at'"
                :sortOrder="-1"
                @page="onPageChange"
                @sort="onSortChange"
                tableStyle="min-width: 50rem"
                size="small"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <span class="text-xl font-bold">{{ props.title }}</span>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="data.params.search" placeholder="Keyword Search" @input="fetchData" />
                        </IconField>
                    </div>
                </template>

                <template #paginatorstart>
                    <Button type="button" icon="pi pi-refresh" text @click="onRefresh" />
                </template>

                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="id" header="No" sortable />
                <Column field="player.name" header="Player" sortable />
                <Column field="started_at" header="Book day" sortable>
                    <template #body="slotProps">
                        {{ formatDate(parseISO(slotProps.data.started_at)) }}
                    </template>
                </Column>
                <Column field="timetables" header="Timetables" sortable>
                    <template #body="slotProps">
                        <Tag v-for="t in slotProps.data?.timetablesNames" :key="t.id" :value="t.name"/>
                    </template>
                </Column>
                <Column field="club.name" header="Club" sortable />

                <!-- Columna: Fecha modificada -->
                <Column field="modified_at" header="Job date" sortable>
                    <template #body="slotProps">
                    <span :class="{ 'text-red-500': isToday(parseISO(slotProps.data.modified_at)) }">
                      {{ formatDate(parseISO(slotProps.data.modified_at)) }}
                    </span>
                    </template>
                </Column>

                <Column header="Options">
                    <template #body="slotProps">
                        <div class="flex items-center gap-1">
                            <!-- booking_preference -->
                            <span v-if="slotProps.data.booking_preference === 'timetable'" class="text-pink-400" v-tooltip.top="`Preference ${slotProps.data.booking_preference}`">
                            <i class="pi pi-clock"></i>
                            </span>
                            <span v-else class="text-blue-500" v-tooltip.top="`Preference ${slotProps.data.booking_preference}`">
                            <i class="pi pi-table-tennis"></i>
                            </span>
                            <!-- status -->
                            <span v-if="slotProps.data.status === 'on-time'" class="text-green-800" v-tooltip.top="'On Time'">
                            <i class="pi pi-calendar"></i>
                            </span>
                            <span v-else-if="slotProps.data.status === 'time-out'" class="text-indigo-900" v-tooltip.top="'Time out'">
                            <i class="pi pi-calendar-times"></i>
                            </span>
                            <span v-else class="text-gray-800" v-tooltip.top="'Closed'">
                            <i class="pi pi-times-circle"></i>
                            </span>
                            <!-- isBooked / fecha -->
                            <span v-if="slotProps.data.isBooked" class="text-green-800" v-tooltip.top="'Booked!!'">
                            <i class="pi pi-smile-beam"></i>
                            </span>
                            <span v-else-if="isPast(parseISO(slotProps.data.modified_at)) || isToday(parseISO(slotProps.data.modified_at))" class="text-red-800" v-tooltip.top="'No Booked!!'">
                            <i class="pi pi-dizzy"></i>
                            </span>
                            <span v-else class="text-primary-800" v-tooltip.top="'Waiting'">
                            <i class="pi pi-grimace"></i>
                            </span>
                        </div>
                    </template>
                </Column>

                <Column header="Resources">
                    <template #body="slotProps">
                        <Button @click="(event) => { data.booking = slotProps.data; toggleResources(event); }" variant="text">Resources</Button>
                    </template>
                </Column>

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['playtomic.booking_edit'])" outlined rounded class="mr-2" @click="router.visit(route('playtomic.bookings.edit', slotProps.data.id))" icon="pi pi-pencil"/>
                        <Button v-show="can(['playtomic.booking_delete'])" icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.booking = slotProps.data"/>
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.booking">Are you sure you want to delete <b>{{ data.booking.name }}</b>?</span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Yes" icon="pi pi-check" @click="deleteData" />
                </template>
            </Dialog>
        </div>

        <Popover ref="popoverResources">
            <div>
                <h3>Resources</h3>
            </div>
            <div class="px-3 py-2">
                <p v-for="r in data.booking?.resourcesNames" :key="r.id">
                <span>{{ r.name }}</span>
                </p>
            </div>
        </Popover>
    </app-layout>
</template>

<style scoped>
</style>
