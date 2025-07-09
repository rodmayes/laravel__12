<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Edit from "./Edit.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { reactive, ref, getCurrentInstance, onMounted } from "vue";
import axios from "axios";
import pickBy from "lodash/pickBy";
import {formatDate, formatDateTime} from '@/composables/useFormatters';
import { loadToast } from '@/composables/loadToast';
loadToast();

const props = defineProps({
    title: String,
    filters: Object,
    perPage: Number
});

const deleteDialog = ref(false);
const form = useForm({});
const itemsRef = reactive([]);

const data = reactive({
    params: {
        search: props.filters.search || "",
        field: props.filters.field || "",
        order: props.filters.order || "",
        perPage: props.perPage ?? 10,
        page: 1,
        status: null,
        sort: [{'field': 'scheduled_for', 'order': 'desc'}],
    },
    editOpen: false,
    showOpen: false,
    job: null,
});

const fetchData = () => {
    const params = pickBy(data.params);
    axios.post(route("scheduled-jobs.refreshData"), params).then((res) => {
        Object.assign(itemsRef, res.data.items);
    });
};

const openEdit = (job) => {
    data.job = job;
    data.editOpen = true;
};

const confirmDelete = (job) => {
    data.job = job;
    deleteDialog.value = true;
};

const deleteData = () => {
    form.delete(route("scheduled-jobs.destroy", data.job?.id), {
        preserveScroll: true,
        onSuccess: () => {
            fetchData();
        },
        onFinish: () => (deleteDialog.value = false),
    });
};

const onPageChange = (event) => {
    data.params.page = event.page + 1;
    data.params.perPage = event.rows;
    fetchData();
};

const onSortChange = (event) => {
    const sortFields = event.multiSortMeta?.map((s) => ({
        field: s.field,
        order: s.order === 1 ? "asc" : "desc",
    }));
    data.params.sort = sortFields;
    fetchData();
};

const getSeverity = (status) => {
    switch (status) {
        case 'failed':
            return 'danger';

        case 'executed':
            return 'success';

        case 'pending':
            return 'info';

        case 'cancelled':
            return 'warn';

    }
};

const breadcrum = ref([
    { label: "Jobs" },
    { label: props.title, url: route("scheduled-jobs.index") },
]);

onMounted(() => {
    fetchData();
});
</script>

<template>
    <AppLayout :items="breadcrum">
        <div class="card">
            <Edit :show="data.editOpen" @close="data.editOpen = false" :job="data.job" :title="props.title" />
            <ConfirmDialog />

            <DataTable
                :value="itemsRef.data"
                lazy
                paginator
                :rows="itemsRef.per_page"
                :totalRecords="itemsRef.total"
                :first="(itemsRef.current_page - 1) * itemsRef.per_page"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                sortMode="multiple"
                sortField="scheduled_for"
                :sortOrder="-1"
                @page="onPageChange"
                @sort="onSortChange"
                tableStyle="min-width: 50rem"
                size="small"
                rowGroupMode="subheader" groupRowsBy="schedulable.name"
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
                    <Button type="button" icon="pi pi-refresh" text @click="fetchData"/>
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="id" header="ID" sortable />
                <Column field="scheduled_for" header="Scheduled" sortable>
                    <template #body="{ data: job }">
                        {{ formatDateTime(job.scheduled_for) }}
                    </template>
                </Column>
                <Column field="job_id" header="Job ID" sortable />
                <Column field="status" header="Status">
                    <template #body="{data}">
                        <Tag :value="data.status" :severity="getSeverity(data.status)" />
                    </template>
                </Column>
                <Column field="payload" header="Payload" />
                <Column field="executed_at" header="Executed">
                    <template #body="{ data: job }">
                        {{ formatDateTime(job.executed_at) }}
                    </template>
                </Column>
                <Column field="cancelled_at" header="Cancelled">
                    <template #body="{ data: job }">
                        {{ formatDate(job.cancelled_at) }}
                    </template>
                </Column>

                <Column header="Actions" :exportable="false" style="min-width: 12rem">
                    <template #body="{ data: job }">
                        <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="openEdit(job)" severity="info" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDelete(job)" />
                    </template>
                </Column>
                <template #groupheader="{data}">
                    <div class="flex items-center gap-2">
                        <Link :href="route('playtomic.bookings.edit', data.schedulable_id)" class="text-blue-600 dark:text-blue-300">
                            {{ data.schedulable.name }} - {{ formatDate(data.scheduled_for) }}
                        </Link>
                    </div>
                </template>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.job">Are you sure you want to delete job <b>{{ data.job.job_id }}</b>?</span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Yes" icon="pi pi-check" @click="deleteData" />
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
