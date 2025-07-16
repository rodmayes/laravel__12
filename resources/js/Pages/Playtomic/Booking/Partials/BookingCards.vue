<script setup>
import {computed, reactive, ref} from 'vue';
import {parseISO} from "date-fns";
import {formatDateLocal} from "@/composables/useFormatters.js";
import axios from "axios";
import { loadToast } from '@/composables/loadToast';

const { toast } = loadToast();

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

const setBooked = (item) => {
    axios.get(route('playtomic.bookings.toggle-booked', {booking:item.id}))
        .then(() => {
            toast.add({ severity: 'success', summary: 'Booking', detail: 'Updated booked successfully', life: 3000});
            this.emit('onRefresh');

        })
        .catch(() => {
            toast.add({severity: 'error', summary: 'Booking', detail: 'Updated booked unSuccessfully', life: 3000});
        });
};
</script>

<template>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="item in cards" :key="item.id" class="p-4 border rounded shadow">
                <!-- Cabecera con nombre a la izquierda, estado y fecha a la derecha -->
                <div class="flex justify-between items-start w-full mb-2">
                    <!-- Izquierda: jugador y club -->
                    <div class="flex flex-col">
                        <h3 class="font-bold text-white">{{ item.player.name }}</h3>
                        <p class="text-sm text-gray-400">{{ item.club.name }}</p>
                    </div>

                    <!-- Derecha: estado arriba, fecha debajo -->
                    <div class="flex flex-col items-end text-sm">
                        <!-- Estado del job -->
                        <span
                            :class="{
                            'text-green-700': item.status === 'on-time',
                            'text-blue-700': item.status === 'closed',
                            'text-red-700': item.status === 'time-out'
                          }"
                                                class="font-medium"
                                            >
                          [{{ item.id }}] {{ item.status }}
                        </span>

                        <!-- Fecha modificada -->
                        <p class="text-gray-400 whitespace-nowrap text-sm">
                            <span v-if="item.modified_at && !isNaN(Date.parse(item.modified_at))">
                                Job: {{ item.modified_at }}
                            </span>
                            <span v-else>-</span>
                        </p>
                    </div>
                </div>


                <p><strong>Booking day: </strong> {{ item.started_at ? formatDateLocal(parseISO(item.started_at)) : '' }}</p>

                <p><strong>Resources: </strong>
                    <Tag v-for="r in item.resourcesNames" :key="r.id" :value="r.name"/>
                </p>
                <p><strong>Timetables: </strong>
                    <Tag v-for="t in item.timetablesNames" :key="t.id" :value="t.name"/>
                </p>
                <div class="flex justify-between">
                    <div class="mt-2 flex justify-start gap-2">
                        <Tag v-if="item.is_booked" severity="success" value="Booked" rounded/>
                        <Tag v-else severity="danger" value="Non Booked" rounded/>
                    </div>
                    <div class="mt-2 flex justify-end gap-2">
                        <Button icon="pi pi-pencil" outlined rounded
                                @click="$inertia.visit(route('playtomic.bookings.edit', item.id))"
                                v-if="can(['playtomic.booking_edit'])" />
                        <Button icon="pi pi-comments" severity="info" outlined rounded
                                @click="showLogs(item)"
                                />
                        <Button icon="pi pi-check" severity="warn" outlined rounded v-tooltip="'Toggle booked'"
                                @click="setBooked(item)"
                        />
                        <Button icon="pi pi-trash" severity="danger" outlined rounded
                                @click="emit('confirmDelete', item)"
                                v-if="can(['playtomic.booking_delete'])" />
                    </div>
                </div>
            </div>
        </div>
        <Menubar class="mt-4">
            <template #start>
                <Button icon="pi pi-refresh" text @click="emit('onRefresh')"/>
            </template>

            <template #end>
            </template>
        </Menubar>
        <Paginator :rows="items.per_page" :totalRecords="items.total"
                   :first="(items.current_page - 1) * items.per_page"
                   @page="props.onPageChange"
                   :rowsPerPageOptions="[5, 10, 20, 50]" class="mt-4" />

        <Dialog v-model:visible="showLogsDialog" :style="{ width: '50rem' }" header="Logs" :modal="true" maximizable >
            <div class="grid grid-cols-1 gap-2">
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
