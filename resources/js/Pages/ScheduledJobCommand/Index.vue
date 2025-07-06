<script setup>
import { ref, reactive, onMounted } from 'vue';
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { router } from '@inertiajs/vue3';
import DialogForm from './DialogForm.vue';
import pkg from "lodash";
const {pickBy} = pkg;
import axios from "axios";
import { loadToast } from '@/composables/loadToast';
loadToast();

const props = defineProps({
    title: String,
    filters: Object,
    items: Object,
    availableCommands: Array,
    perPage: Number,
});

const jobCommands = ref(props.items);
const selectedJob = ref(null);
const dialogVisible = ref(false);
const isEditing = ref(false);

const openNew = () => {
    selectedJob.value = null;
    isEditing.value = false;
    dialogVisible.value = true;
};

const editJob = (job) => {
    selectedJob.value = job;
    isEditing.value = true;
    dialogVisible.value = true;
};

const deleteJob = (job) => {
    if (confirm(`¿Seguro que deseas eliminar el comando "${job.command}"?`)) {
        router.delete(route('scheduled-job-commands.destroy', job.id), {
            onSuccess: () => fetchData()
        });
    }
};
const fetchData = () => {
    const cleanParams = pickBy(data.params, (v) => v !== null && v !== undefined);
    axios.post(route("scheduled-job-commands.index"), cleanParams).then((res) => {
        props.items.value = res.data.items;
    });
};

onMounted(fetchData);

const data = reactive({
    params: {
        search: props.filters.search,
        perPage: props.perPage ?? 20,
        timetable: null,
        page: 1,
        sort: {'field': 'command', 'order': '-1'},
    },
    booking: null,
});

const breadcrum = ref([
    {label: "Administration"},
    {label: props.title, url: route("scheduled-job-commands.index")},
]);

</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <Button label="Nuevo" icon="pi pi-plus" @click="openNew" />
            </div>

            <DataTable :value="jobCommands.data" stripedRows responsiveLayout="scroll">
                <template #header>
                    <div class="flex justify-between">
                        <span class="text-xl font-bold">{{ props.title}}</span>
                        <InputText v-model="data.params.search" placeholder="Search..." @input="onRefresh"/>
                    </div>
                </template>
                <template #paginatorstart>
                    <Button icon="pi pi-refresh" text @click="onRefresh"/>
                </template>

                <template #empty>No data found.</template>

                <Column field="command" header="Command" sortable/>
                <Column header="Scheduled" field="scheduled_for" sortable/>
                <Column header="Parameters" :body="row => row.parameters?.join(', ') ?? '-'" sortable/>
                <Column field="status" header="Status" sortable/>
                <Column header="Actions">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" class="mr-2" @click="editJob(data)" outlined rounded/>
                        <Button icon="pi pi-trash" severity="danger" @click="deleteJob(data)" outlined rounded/>
                    </template>
                </Column>
            </DataTable>

            <Paginator
                :rows="jobCommands.per_page"
                :totalRecords="jobCommands.total"
                :first="(jobCommands.current_page - 1) * jobCommands.per_page"
                :rowsPerPageOptions="[5, 10, 20]"
                @page="event => router.visit(route('scheduled-job-commands.index', { page: event.page + 1 }))"
                class="mt-3"
            />

            <DialogForm
                v-model:visible="dialogVisible"
                :available-commands="props.availableCommands"
                :job="selectedJob"
                :is-editing="isEditing"
                @refresh="fetchData"
            />
        </div>
    </app-layout>
</template>
