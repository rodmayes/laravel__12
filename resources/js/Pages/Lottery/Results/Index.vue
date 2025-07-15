<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Create from "@/Pages/Lottery/Results/Create.vue";
import Edit from "@/Pages/Lottery/Results/Edit.vue";
import Import from "@/Pages/Lottery/Results/Import.vue";
import { useForm } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from "vue";
import pkg from "lodash";
import axios from "axios";
import {formatDateTime} from "@/composables/useFormatters.js";

const { pickBy } = pkg;

const props = defineProps({
    title: String,
    results: Object,
    perPage: Number,
});

const deleteDialog = ref(false);
const form = useForm({});
const resultsRef = reactive({ ...props.results }); // Carga inicial desde el controlador

const data = reactive({
    params: {
        search: '',
        page: 1,
        perPage: props.perPage ?? 20,
        sort: JSON.stringify([{ field: 'date_at', order: 'desc' }]),
    },
    createOpen: false,
    editOpen: false,
    importOpen: false,
    result: null,
});

const fetchData = () => {
    const params = pickBy(data.params);
    axios.post(route('lottery.results.refreshData'), params).then(response => {
        Object.assign(resultsRef, response.data.results);
    });
};

const deleteData = () => {
    deleteDialog.value = false;
    form.delete(route("lottery.results.destroy", data.result?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            fetchData();
        },
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

const breadcrum = ref([
    { label: 'Lottery' },
    { label: props.title, url: route('lottery.results.index') }
]);
</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button v-show="can(['lottery.results_create'])" label="Create" @click="data.createOpen = true" icon="pi pi-plus" severity="success" size="small" class="mr-2" />
                    <Button v-show="can(['lottery.results_import'])" label="Import results from excel" @click="data.importOpen = true" icon="pi pi-file-import" severity="secondary" size="small" />
                </template>
            </Menubar>

            <Create :show="data.createOpen" @close="data.createOpen = false; fetchData()" :title="props.title" />
            <Edit :show="data.editOpen" @close="data.editOpen = false; fetchData()" :result="data.result" :title="props.title" />
            <Import :show="data.importOpen" @close="data.importOpen = false; fetchData()" />

            <DataTable
                :value="resultsRef.data"
                lazy
                paginator
                :rows="resultsRef.per_page"
                :totalRecords="resultsRef.total"
                :first="(resultsRef.current_page - 1) * resultsRef.per_page"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                sortMode="multiple"
                :sortField="'date_at'"
                :sortOrder="-1"
                @page="onPageChange"
                @sort="onSortChange"
                tableStyle="min-width: 50rem"
                size="small"
                stripedRows
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <span class="text-xl font-bold">{{ props.title }}</span>
                        <IconField>
                            <InputIcon><i class="pi pi-search" /></InputIcon>
                            <InputText v-model="data.params.search" placeholder="Keyword Search" @input="fetchData" />
                        </IconField>
                    </div>
                </template>
                <template #paginatorstart>
                    <Button type="button" icon="pi pi-refresh" text @click="onRefresh" />
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column header="No" field="id" sortable></Column>
                <Column field="date_at" header="Date" sortable>
                    <template #body="{data}">
                        {{ formatDateTime(data.date_at) }}
                    </template>
                </Column>
                <Column field="numbers" header="Numbers" sortable></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['lottery.results_update'])" icon="pi pi-pencil" outlined rounded class="mr-2" @click="data.editOpen = true; data.result = slotProps.data" />
                        <Button v-show="can(['lottery.results_delete'])" icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.result = slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.result">
                        Are you sure you want to delete <b>{{ data.result.name }}</b>?
                    </span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Yes" icon="pi pi-check" @click="deleteData" />
                </template>
            </Dialog>
        </div>
    </app-layout>
</template>

<style scoped lang="scss">
</style>
