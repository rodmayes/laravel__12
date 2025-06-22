
<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Create from "@/Pages/Permission/Create.vue";
import Edit from "@/Pages/Permission/Edit.vue";
import { usePage, useForm } from '@inertiajs/vue3';

import { onMounted, reactive, ref, watch, computed } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
const { _, debounce, pickBy } = pkg;

const props = defineProps({
    title: String,
    filters: Object,
    permissions: Object,
    roles: Object,
    perPage: Number,
});

const deleteDialog = ref(false);
const form = useForm({});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        createOpen: false,
        editOpen: false,
    },
    permission: null,
});

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("permission.destroy", data.permission?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
}


const onPageChange = (event) => {
    router.get(route('permission.index'), {page: event.page + 1, perPage: event.rows }, { preserveState: true });
};

const onSortChange = (event) => {
    const sortFields = event.multiSortMeta?.map(sort => ({
        field: sort.field,
        order: sort.order === 1 ? 'asc' : 'desc'
    }));

    data.params.sort = JSON.stringify(sortFields); // por ejemplo, en JSON para el backend
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("permission.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150)
);

const breadcrum = ref([
    { label: 'Users Management' },
    { label: 'Permissions', url: route('permission.index') }
]);

</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button v-show="can(['permission.create'])" label="Create" @click="data.createOpen = true" icon="pi pi-plus" severity="success" size="small"/>
                </template>
            </Menubar>
            <Create
                :show="data.createOpen"
                @close="data.createOpen = false"
                :title="props.title"
            />
            <Edit
                :show="data.editOpen"
                @close="data.editOpen = false"
                :permission="data.permission"
                :title="props.title"
            />
            <DataTable :value="permissions.data" lazy paginator :rows="permissions.per_page" :totalRecords="permissions.total" :first="(permissions.current_page - 1) * permissions.per_page"
                       @page="onPageChange" @sort="onSortChange" tableStyle="min-width: 50rem" size="small" stripedRows :rowsPerPageOptions="[5, 10, 20, 50]"
                       sortMode="multiple" sortField="id" sort-order="1">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <span class="text-xl font-bold">{{props.title}}</span>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="data.params.search" placeholder="Keyword Search" />
                        </IconField>
                    </div>
                </template>
                <template #empty> No data found. </template>
                <template #loading> Loading data. Please wait. </template>

                <Column header="No" field="id" sortable></Column>

                <Column field="name" header="Name" sortable></Column>
                <Column field="guard_name" header="Guard" sortable></Column>
                <Column field="created_at" header="Created" sortable></Column>
                <Column field="updated_at" header="Updated" sortable></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['permission.update'])" icon="pi pi-pencil" outlined rounded class="mr-2"  @click="
                                                    (data.editOpen = true),
                                                        (data.permission = slotProps.data)" />
                        <Button v-show="can(['permission.delete'])" icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.permission = slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.permission"
                        >Are you sure you want to delete <b>{{ data.permission.name }}</b
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
