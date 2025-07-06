<script setup>
import { parseISO, isToday, isPast } from "date-fns";
import {reactive, ref} from "vue";
import { formatDateLocal, getModifiedDate } from "@/composables/useFormatters.js";

const props = defineProps({
    items: Object,
    onPageChange: Function,
    onSortChange: Function,
    onRefresh: Function,
    deleteDialog: Object,
    data: Object,
    deleteData: Function,
    router: Object,
    can: Function,
});

const popoverResources = ref();

const rData = reactive({
    booking: null
});

const toggleResources = (event) => popoverResources.value.toggle(event);
</script>

<template>
    <DataTable
        :value="items.data"
        paginator lazy :rows="items.per_page"
        :totalRecords="items.total"
        :first="(items.current_page - 1) * items.per_page"
        :rowsPerPageOptions="[5, 10, 20, 50]"
        sortMode="multiple"
        @page="onPageChange" @sort="onSortChange"
    >
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

        <Column field="id" header="No" sortable/>
        <Column field="player.name" header="Player" sortable/>
        <Column field="club.name" header="Club" sortable/>
        <Column field="started_at" header="Book Day" sortable>
            <template #body="{ data }">{{ data.started_at ? formatDateLocal(parseISO(data.started_at)) : ''}}</template>
        </Column>
        <Column field="modified_at" header="Job date" sortable>
            <template #body="{ data }">
                <span :class="{ 'text-red-500': isToday(data.modified_at) }">
                    {{data.modified_at}}
                </span>
            </template>
        </Column>
        <Column field="timetables" header="Timetables">
            <template #body="{ data }">
                <Tag v-for="t in data.timetablesNames" :key="t.id" :value="t.name"/>
            </template>
        </Column>
        <Column field="status" header="Status">
            <template #body="{data}">
                <span :class="{
                        'text-green-700': data.status === 'on-time',
                        'text-blue-700': data.status === 'closed',
                        'text-red-700': data.status === 'time-out'
                    }">
                        {{ data.status }}
                    </span>
            </template>
        </Column>
        <Column header="Resources">
            <template #body="{ data }">
                <Button @click="(event) => { rData.booking = data; toggleResources(event); }" variant="text">Resources</Button>
            </template>
        </Column>
        <Column header="Actions">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2"
                        v-if="can(['playtomic.booking_edit'])"
                        @click="router.visit(route('playtomic.bookings.edit', data.id))"/>
                <Button icon="pi pi-trash" severity="danger" outlined rounded
                        v-if="can(['playtomic.booking_delete'])"
                        @click="() => { deleteDialog.value = true; rData.booking = data; }"/>
            </template>
        </Column>
    </DataTable>

    <Dialog v-model:visible="deleteDialog.value" header="Confirm" modal :style="{ width: '450px' }">
        <span>Are you sure you want to delete booking <b>{{ rData.booking?.player?.name }}</b>?</span>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="deleteDialog.value = false"/>
            <Button label="Yes" icon="pi pi-check" @click="deleteData"/>
        </template>
    </Dialog>

    <Popover ref="popoverResources">
        <div>
            <h3>Resources</h3>
        </div>
        <div class="px-3 py-2">
            <p v-for="r in rData.booking?.resourcesNames" :key="r.id">
                <span>{{ r.name }}</span>
            </p>
        </div>
    </Popover>
</template>
