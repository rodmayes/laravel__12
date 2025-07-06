<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    visible: Boolean,
    availableCommands: Array,
    job: Object,
    isEditing: Boolean,
});

const emit = defineEmits(['update:visible', 'refresh']);

const form = useForm({
    command: '',
    parameters: [],
    scheduled_for: null,
    time: null,
    active: true
});

const schedule = {
    type: ref('daily'),
    hour: ref(0),
    minute: ref(0)
};

const schedules = [
    { label: "Cada x minutos", value: "every-x-minutes" },
    { label: "Diario", value: "daily" },
    { label: "Semanal", value: "weekly" },
    { label: "Mensual", value: "monthly" },
    { label: "Anual", value: "yearly" },
    { label: "Primer día del mes", value: "first-of-month" },
    { label: "Primer lunes del mes", value: "first-monday-of-month" },
];

const getScheduleFromCron = (cronExpr) => {
    const [min, hour, dom, month, dow] = cronExpr.trim().split(/\s+/);
    const isAll = (field) => field === '*';
    const isNumber = (s) => /^\d+$/.test(s);
    const weekDays = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

    // 1) First <weekday> of month: dom="1-7", month="*", dow single number
    if (dom === '1-7' && isAll(month) && isNumber(dow)) {
        const wd = weekDays[+dow];
        return `first_${wd}_of_month`;
    }
    // 2) First day of month: dom="1", month="*"
    if (dom === '1' && isAll(month) && isAll(dow)) {
        return 'first_day_of_month';
    }
    // 3) Last day of month (cron “L” sintaxis extendida)
    if (/L$/.test(dom)) {
        return 'last_day_of_month';
    }
    // 4) Quarterly: mes con paso de 3 (*/3 o 1-12/3), día fijo (p.ej. 1)
    if ((/\*\/3/.test(month) || /\.?\/3/.test(month)) && isNumber(dom)) {
        return 'quarterly';
    }
    // 5) Annually: mes número fijo, día número fijo
    if (isNumber(month) && isNumber(dom) && isAll(dow)) {
        return 'annually';
    }
    // 6) Monthly: día del mes fijo, mes=*
    if (isAll(month) && isNumber(dom) && isAll(dow)) {
        return 'monthly';
    }
    // 7) Daily: día y mes y dow = *
    if (isAll(dom) && isAll(month) && isAll(dow)) {
        return 'daily';
    }
    // No detectado:
    return null;
};

const buildCronExpression = ({type, hour, minute}) => {
    const h = Number(hour ?? 0);
    const m = Number(minute ?? 0);

    switch (type) {
        case 'every-minute':
            return '* * * * *';
        case 'every-x-minutes':
            return `*/${m} * * * *`;
        case 'daily':
            return `${m} ${h} * * *`;
        case 'weekly':
            return `${m} ${h} * * 1`;
        case 'monthly':
            return `${m} ${h} 1 * *`;
        case 'yearly':
            return `${m} ${h} 1 1 *`;
        case 'first-of-month':
            return `${m} ${h} 1 * *`;
        case 'first-monday-of-month':
            return `${m} ${h} 1-7 * 1`;
        default:
            return '';
    }
};

const pad = (num) => String(num).padStart(2, '0');

watch(() => props.visible, (val) => {
    form.errors = {};
    if (val && props.job) {
        form.command = props.job.command;
        form.parameters = props.job.parameters || [];
        form.active = props.job.active;

        const cron = props.job.scheduled_for?.trim() ?? '';
        schedule.type.value = getScheduleFromCron(cron);

        if (cron) {
            const parts = cron.split(' ');
            if (parts.length === 5) {
                const [minute, hour] = parts;
                schedule.hour.value = Number(hour) || 0;
                schedule.minute.value = Number(minute) || 0;
            }
        }
    } else {
        form.reset();
        schedule.type.value = 'daily';
        schedule.hour.value = 0;
        schedule.minute.value = 0;
    }
});

const addParam = () => form.parameters.push('');
const removeParam = (i) => form.parameters.splice(i, 1);

const submit = () => {
    form.time = `${pad(schedule.hour.value)}:${pad(schedule.minute.value)}`;
    form.scheduled_for = buildCronExpression({
        type: schedule.type.value,
        hour: schedule.hour.value,
        minute: schedule.minute.value
    });

    const method = props.isEditing ? 'put' : 'post';
    const url = props.isEditing
        ? route('scheduled-job-commands.update', props.job.id)
        : route('scheduled-job-commands.store');

    form[method](url, {
        onSuccess: () => {
            emit('refresh');
            emit('update:visible', false);
        }
    });
};
</script>

<template>
    <Dialog v-model:visible="props.visible" modal header="Programmer command" style="width: 500px">
        <div class="mb-4">
            <label for="command">Command</label>
            <Select v-model="form.command" :options="props.availableCommands" filter
                    placeholder="Select command" class="w-full" optionLabel="label" optionValue="value"/>
            <small v-if="form.errors.command" class="text-red-500">{{ form.errors.command }}</small>
        </div>

        <div class="mb-4">
            <label>Parameters</label>
            <div v-for="(param, i) in form.parameters" :key="i" class="flex items-center gap-2 mb-1">
                <InputText v-model="form.parameters[i]" class="flex-1"/>
                <Button icon="pi pi-times" text @click="removeParam(i)"/>
            </div>
            <Button icon="pi pi-plus" label="Add parameter" text @click="addParam"/>
        </div>

        <div class="mb-4">
            <label>Frecuencia</label>
            <Select v-model="schedule.type.value"
                    :options="schedules"
                    placeholder="Frecuencia"
                    class="w-full"
                    optionLabel="label" optionValue="value"
            />
            <small v-if="form.errors.scheduled_for" class="text-red-500">{{ form.errors.scheduled_for }}</small>
        </div>
        <div v-if="schedule.type.value === 'every-x-minutes'" class="mb-4">
            <label>Every x minutes</label>
            <InputNumber v-model="schedule.minute.value" :min="1" :max="59" class="w-full" />
        </div>

        <div class="mb-4">
            <label>Hora</label>
            <div class="flex gap-2">
                <Select v-model="schedule.hour.value" :options="Array.from({ length: 24 }, (_, i) => i)" class="w-full"
                        placeholder="Hora"/>
                <Select v-model="schedule.minute.value" :options="Array.from({ length: 60 }, (_, i) => i)"
                        class="w-full" placeholder="Minuto"/>
                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
            </div>
        </div>

        <div class="mb-4">
            <label>Status</label>
            <ToggleButton v-model="form.active" onLabel="Active" offLabel="Inactive" class="mb-3"/>
            <small v-if="form.errors.naactiveme" class="text-red-500">{{ form.errors.active }}</small>
        </div>

        <template #footer>
            <Button label="Cancel" text @click="emit('update:visible', false)"/>
            <Button label="Save" icon="pi pi-check" @click="submit"/>
        </template>
    </Dialog>
</template>
