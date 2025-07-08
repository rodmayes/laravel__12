<script setup>
import {computed, reactive, ref} from 'vue';
import {isToday, isPast, parseISO} from "date-fns";
import {formatDate, formatDateLocal} from "@/composables/useFormatters.js";

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

const showLogsDialog = ref(false);

const data = reactive({
    booking: null,
    logs: []
});

const emit = defineEmits(['confirmDelete']);
const cards = computed(() => props.items.data ?? []);

const showLogs = (item) => {
    data.logs = item && item.log ? JSON.parse(item.log) : [];
    showLogsDialog.value = true;
};
</script>

<template>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="item in cards" :key="item.id" class="p-4 border rounded shadow">
                <div class="flex justify-between mb-2">
                    <div>
                        <h3 class="font-bold">{{ item.player.name }}</h3>
                        <p class="text-sm text-gray-500">{{ item.club.name }}</p>
                    </div>
                    <span :class="{
                        'text-green-700': item.status === 'on-time',
                        'text-blue-700': item.status === 'closed',
                        'text-red-700': item.status === 'time-out'
                    }">
                        [{{item.id}}] {{ item.status }}
                    </span>
                </div>
                <p><strong>Booking day: </strong> {{ item.started_at ? formatDateLocal(parseISO(item.started_at)) : '' }}</p>
                <p>
                    <strong>Job day: </strong>
                    <span v-if="item.modified_at && !isNaN(Date.parse(item.modified_at))">
                    {{ item.modified_at }}
                    </span>
                    <span v-else>-</span>
                </p>

                <p><strong>Resources: </strong>
                    <span v-for="r in item.resourcesNames" :key="r.id">{{ r.name }}</span>
                </p>
                <p><strong>Timetables: </strong>
                    <span v-for="t in item.timetablesNames" :key="t.id">{{ t.name }}</span>
                </p>
                <div class="mt-2 flex justify-end gap-2">
                    <Button icon="pi pi-pencil" outlined rounded
                            @click="$inertia.visit(route('playtomic.bookings.edit', item.id))"
                            v-if="can(['playtomic.booking_edit'])" />
                    <Button icon="pi pi-comments" severity="info" outlined rounded
                            @click="showLogs(item)"
                            />
                    <Button icon="pi pi-trash" severity="danger" outlined rounded
                            @click="emit('confirmDelete', item)"
                            v-if="can(['playtomic.booking_delete'])" />
                </div>
            </div>
        </div>
        <Paginator :rows="items.per_page" :totalRecords="items.total"
                   :first="(items.current_page - 1) * items.per_page"
                   @page="props.onPageChange"
                   :rowsPerPageOptions="[5, 10, 20, 50]" class="mt-4" />

        <Dialog v-model:visible="showLogsDialog" :style="{ width: '50rem' }" header="Logs" :modal="true" maximizable >
            <div class="grid grid-cols-1 gap-4">
                <p v-for="log in data.logs">
                    <small>
                        <i class="fas fa-dot-circle"></i> {{log}}
                    </small>
                </p>
            </div>
            <template #footer>
                <Button label="Close" icon="pi pi-times" text @click="showLogsDialog = false" />
            </template>
        </Dialog>
    </div>
</template>
