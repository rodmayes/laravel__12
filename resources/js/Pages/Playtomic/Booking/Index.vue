<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import {ref, reactive, onMounted} from "vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import BookingTable from "./Partials/BookingTable.vue";
import BookingCards from "./Partials/BookingCards.vue";
import {loadToast} from "@/composables/loadToast";
import pkg from "lodash";
import {formatDateLocal, getModifiedDate} from "@/composables/useFormatters.js";

const {pickBy} = pkg;

loadToast();

const props = defineProps({
    title: String,
    filters: Object,
    clubs: Object,
    players: Object,
    timetables: Object,
    perPage: Number,
});

const items = ref([]);
const mode = ref("card");

const oStatus = ref([
    {name: "On time", value: "on-time"},
    {name: "Closed", value: "closed"},
    {name: "Time Out", value: "time-out"},
]);

const data = reactive({
    params: {
        search: props.filters.search,
        perPage: props.perPage ?? 20,
        club: null,
        status: 'on-time',
        player: null,
        timetable: null,
        page: 1,
        sort: [{'field': 'started_at', 'order': 'desc'}],
    },
    booking: null,
});

const deleteDialog = ref(false);

const fetchData = () => {
    const cleanParams = pickBy(data.params, (v) => v !== null && v !== undefined);
    axios.post(route("playtomic.bookings.refreshData"), cleanParams).then((res) => {
        items.value = {
            ...res.data.items,
            data: res.data.items.data.map(item => {
                const modified = getModifiedDate(item);
                return {
                    ...item,
                    modified_at: modified ? formatDateLocal(modified) : null
                };
            })
        };
        });
};

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    data.params.perPage = event.rows;
    fetchData();
};

const onSortChange = (event) => {
    const sortMeta = event.multiSortMeta?.map(sort => ({
        field: sort.field,
        order: sort.order === 1 ? 'asc' : 'desc',
    }));
    data.params.sort = JSON.stringify(sortMeta);
    fetchData();
};

const deleteData = () => {
    router.delete(route('playtomic.bookings.destroy', data.booking.id), {
        onSuccess: () => {
            deleteDialog.value = false;
            fetchData();
        }
    });
};

const breadcrum = ref([
    {label: "Playtomic"},
    {label: props.title, url: route("playtomic.bookings.index")},
]);

onMounted(fetchData);
</script>

<template>
    <AppLayout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button
                        v-show="can(['playtomic.booking_create'])"
                        label="Add Booking"
                        icon="pi pi-plus"
                        severity="success"
                        size="small"
                        @click="router.visit(route('playtomic.bookings.create'))"
                    />
                </template>

                <template #end>
                    <Select v-model="data.params.club" :options="props.clubs" optionLabel="name" optionValue="id"
                            placeholder="Club" class="mr-2" showClear @change="fetchData"/>
                    <Select v-model="data.params.player" :options="props.players" optionLabel="name" optionValue="id"
                            placeholder="Player" class="mr-2" showClear @change="fetchData"/>
                    <SelectButton v-model="data.params.status" :options="oStatus" optionLabel="name" optionValue="value"
                                  class="mr-2" @change="fetchData"/>
                    <SelectButton v-model="mode" :options="[
                            { label: 'Table', value: 'table' },
                            { label: 'Cards', value: 'card' }
                        ]" optionLabel="label" optionValue="value"/>
                </template>
            </Menubar>

            <BookingTable v-if="mode === 'table'"
                          :items="items"
                          :deleteDialog="deleteDialog"
                          :data="data"
                          :onPageChange="onPageChange"
                          :onSortChange="onSortChange"
                          :onRefresh="fetchData"
                          :deleteData="deleteData"
                          :router="router"
                          :can="can"
            />

            <BookingCards v-else
                          :items="items"
                          :deleteDialog="deleteDialog"
                          :data="data"
                          :onPageChange="onPageChange"
                          :onSortChange="onSortChange"
                          :onRefresh="fetchData"
                          :deleteData="deleteData"
                          :router="router"
                          :can="can"
            />
        </div>
    </AppLayout>
</template>
