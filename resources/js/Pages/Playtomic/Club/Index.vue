
<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Create from "@/Pages/Playtomic/Club/Create.vue";
import Edit from "@/Pages/Playtomic/Club/Edit.vue";
import { useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { onMounted, reactive, ref, getCurrentInstance, watch, computed } from "vue";
import axios from "axios";

// Toast manual
const inst = getCurrentInstance();
const $toast = inst.appContext.config.globalProperties.$toast;

import pkg from "lodash";
const { pickBy } = pkg;

const props = defineProps({
  title: String,
  filters: Object,
  items: Object,
  perPage: Number
});

const confirm = useConfirm();

const deleteDialog = ref(false);
const form = useForm({});
const itemsRef = reactive({ ...props.items });

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage ?? 10,
    },
    createOpen: false,
    editOpen: false,
    club: null,
});

const fetchData = () => {
    const params = pickBy(data.params);
    axios.post(route('playtomic.club.refreshData'), params).then(response => {
        Object.assign(itemsRef, response.data.items);
    });
};

const syncResourcesConfirm = (item) => {
    confirm.require({
        message: 'Are you sure you want to sync resources \'s Club '+item?.name+'?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Sync'
        },
        accept: () => {
            syncResources(item);
        },
        reject: () => {}
    });
};

const syncResources = (club) => {
    deleteDialog.value = false;

    form.get(route("playtomic.club.sync-resources", club?.id), {
        preserveScroll: true,
        onSuccess: () => {
            fetchData();
        },
        onError: () => {},
        onFinish: () => null,
    });
}

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("playtomic.club.destroy", data.club?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            fetchData();
        },
        onError: () => null,
        onFinish: () => null,
    });
}

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
    { label: 'Playtomic' },
    { label: props.title, url: route('playtomic.club.index') }
]);

</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button v-show="can(['playtomic.club_create'])" label="Create" @click="data.createOpen = true" icon="pi pi-plus" />
                </template>
            </Menubar>
            <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"/>
            <Edit :show="data.editOpen" @close="data.editOpen = false" :club="data.club" :title="props.title"/>
            <ConfirmDialog></ConfirmDialog>

            <DataTable
                :value="itemsRef.data"
                lazy
                paginator
                :rows="itemsRef.per_page"
                :totalRecords="itemsRef.total"
                :first="(itemsRef.current_page - 1) * itemsRef.per_page"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                sortMode="multiple"
                :sortField="'name'"
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
                            <InputText v-model="data.params.search" placeholder="Keyword Search" @input="fetchData"/>
                        </IconField>
                    </div>
                </template>
                <template #paginatorstart>
                    <Button type="button" icon="pi pi-refresh" text @click="onRefresh"/>
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column header="No" field="id" sortable></Column>

                <Column field="name" header="Name" sortable></Column>
                <Column field="playtomic_id" header="PlaytomicId" sortable></Column>
                <Column field="days_min_booking" header="Days Min Booking" sortable></Column>
                <Column field="timetable_summer" header="Timetable Summer" sortable>
                    <template #body="slotProps">
                        <Tag :severity="slotProps.data.club ? 'success' :'secondary'" :value="slotProps.data.club ? 'Yes' :'No'"></Tag>
                    </template>
                </Column>
                <Column field="booking_hour" header="Booking hour" sortable></Column>
                <Column field="number_resources" header="# Resources" sortable></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['playtomic.club_edit'])" icon="pi pi-pencil" outlined rounded class="mr-2"
                                @click="(data.editOpen = true), (data.club = slotProps.data)"
                        />
                        <Button icon="pi pi-sync" outlined rounded severity="warn" class="mr-2" @click="syncResourcesConfirm(slotProps.data)"></Button>
                        <Button v-show="can(['playtomic.club_delete'])" icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.club = slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.club"
                        >Are you sure you want to delete <b>{{ data.club.name }}</b
                        >?</span
                    >
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
