<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import {router, usePage} from '@inertiajs/vue3';
import {onMounted, reactive, ref} from 'vue';
import { DataTable, Column } from 'primevue';
import Button from 'primevue/button';
import {formatDateUnix} from "@/composables/useFormatters.js";
import {loadToast} from "@/composables/loadToast";
import axios from "axios";
import pkg from "lodash";
const { pickBy } = pkg;

loadToast();

const props = defineProps({
    title: String,
    perPage: Number,
    filters: Object,
});

const page = usePage();
const breadcrum = ref([
    { label: 'Admin' },
    { label: props.title, url: route('admin.jobs.index') }
])

const data = reactive({
    params: {
        search: props.filters?.search,
        perPage: props.perPage ?? 20,
        model: null,
        status: null,
        executed: null,
        page: 1,
        sort: [{'field': 'available_at', 'order': 'asc'}],
    },
    job: null,
});

const itemsRef = reactive([]);

const fetchData = () => {
    const params = pickBy(data.params);
    axios.post(route('admin.jobs.refreshData'), params)
        .then(response => {
            Object.assign(itemsRef, response.data.items);
        });
};

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    data.params.perPage = event.rows;
    fetchData();
};

const onSortChange = (event) => {
    const sortFields = event.multiSortMeta?.map(
        sort => ({
            field: sort.field,
            order: sort.order === 1 ? 'asc' : 'desc'
        }));
    data.params.sort = sortFields; //JSON.stringify(sortFields);
    fetchData();
};

const onRefresh = () => fetchData();

onMounted(() => {
    fetchData();
});

const deleteDialog = ref(false);
const deleteData = () => {
    deleteDialog.value = false;

    router.delete(route('admin.jobs.delete', data.job.id), {
        onSuccess: () => {
            deleteDialog.value = false;
            fetchData();
        }
    });
};
</script>

<template>
    <app-layout :items="breadcrum">
        <DataTable
            :value="itemsRef.data"
            lazy
            paginator
            :rows="itemsRef.per_page"
            :totalRecords="itemsRef.total"
            :first="(itemsRef.current_page - 1) * itemsRef.per_page"
            :rowsPerPageOptions="[5, 10, 20, 50]"
            sortMode="multiple"
            @page="onPageChange"
            @sort="onSortChange"
            tableStyle="min-width: 50rem"
            size="small"
        >
            <template #paginatorstart>
                <Button type="button" icon="pi pi-refresh" text @click="onRefresh"/>
            </template>
            <template #empty> No data found. </template>
            <template #loading> Loading data. Please wait. </template>
            <Column field="id" header="ID" sortable/>
            <Column field="type" header="Job" sortable/>
            <Column field="bookingId" header="Model ID" sortable>
                <template #body="{ data }">
                  <span v-if="data.type === 'LaunchPrebookingJob'">
                      <a class="text" :href="route('playtomic.bookings.edit', data.details.booking.id)" target="_blank">{{ data.details.booking.name }}</a>
                  </span>
                    <span v-else-if="data.type === 'UserLoginJob'">
                      {{ data.details.user.email }}
                  </span>
                    <span v-else class="text-gray-400 italic">N/A</span>
                </template>
            </Column>
            <Column field="reserved_at" header="Status" sortable>
                <template #body="{ data }">
                    <span :class="data.reserved_at ? 'text-blue-500' : 'text-orange-500'">
                    {{ data.reserved_at ? 'Processed' : 'Pending' }}
                    </span>
                </template>
            </Column>
            <Column field="available_at" header="Scheduled at" sortable>
                <template #body="{ data }">
                    <span>{{ formatDateUnix(data.available_at) }}</span>
                </template>
            </Column>
            <Column field="attempts" header="Intent" sortable/>
            <Column field="created_at" header="Created" sortable>
                <template #body="{ data }">
                    <span>{{ formatDateUnix(data.created_at) }}</span>
                </template>
            </Column>
            <Column header="Action">
                <template #body="slotProps">
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.job = slotProps.data" />
                </template>
            </Column>
        </DataTable>
    </app-layout>

    <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl"/>
            <span v-if="data.job"
            >Are you sure you want to delete <b>{{ data.job.type }}</b
            >?</span
            >
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
            <Button label="Yes" icon="pi pi-check" @click="deleteData" />
        </template>
    </Dialog>
</template>
