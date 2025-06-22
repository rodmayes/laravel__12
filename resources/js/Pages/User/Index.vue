
<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import Create from "@/Pages/User/Create.vue";
import Edit from "@/Pages/User/Edit.vue";
import UserPlaytomic from "@/Pages/User/UserPlaytomic.vue";
import { usePage, useForm } from '@inertiajs/vue3';

import { onMounted, reactive, ref, watch, computed } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
const { _, debounce, pickBy } = pkg;

const props = defineProps({
    title: String,
    filters: Object,
    users: Object,
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
        perPage: props.perPage,
        createOpen: false,
        editOpen: false,
        userPlaytomicOpen: false
    },
    user: null
});

const deleteData = () => {
    deleteDialog.value = false;

    form.delete(route("user.destroy", data.user?.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
}

const onPageChange = (event) => {
    router.get(route('user.index'), { page: event.page + 1, perPage: event.rows }, { preserveState: true });
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("user.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150)
);

const breadcrum = ref([
    { label: props.title, url: 'user' }
]);


</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Create
                :show="data.createOpen"
                @close="data.createOpen = false"
                :roles="props.roles"
                :title="props.title"
            />
            <Edit
                :show="data.editOpen"
                @close="data.editOpen = false"
                :roles="props.roles"
                :user="data.user"
                :title="props.title"
            />
            <UserPlaytomic
                :show="data.userPlaytomicOpen"
                @close="data.userPlaytomicOpen = false"
                :user="data.user"
                :title="props.title"
            />
            <Button v-show="can(['user.create'])" label="Create" @click="data.createOpen = true" icon="pi pi-plus" />
            <DataTable :value="users.data" paginator :rows="users.per_page" :totalRecords="users.total" :first="(users.current_page - 1) * users.per_page"
                       @page="onPageChange" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" :sortField="'name'" :sortOrder="-1">
                <template #header>
                    <div class="flex justify-end">
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

                <Column header="No">
                    <template #body="slotProps">
                        {{slotProps.index + 1}}
                    </template>
                </Column>

                <Column field="name" header="Name" sortable></Column>
                <Column field="email" header="Email" sortable></Column>
                <Column header="Roles" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.roles.map(role => role.name).join(', ') }}
                    </template>
                </Column>
                <Column field="created_at" header="Created" sortable></Column>
                <Column field="updated_at" header="Updated" sortable></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-show="can(['user.update'])" icon="pi pi-pencil" outlined rounded class="mr-2"
                         @click="(data.editOpen = true), (data.user = slotProps.data)" />
                        <Button v-show="can(['user.update'])" icon="pi pi-paypal" outlined rounded class="mr-2" severity="info"
                         @click="(data.userPlaytomicOpen = true), (data.user = slotProps.data)" />
                        <Button v-show="can(['user.delete'])" icon="pi pi-trash" outlined rounded severity="danger" @click="deleteDialog = true; data.user = slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                <div class="flex items-center gap-4">
                    <i class="pi pi-exclamation-triangle !text-3xl" />
                    <span v-if="data.user"
                        >Are you sure you want to delete <b>{{ data.user.name }}</b
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
