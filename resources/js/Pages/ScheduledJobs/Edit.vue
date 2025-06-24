<script setup>
import { useForm } from "@inertiajs/vue3";
import {watch, watchEffect} from "vue";
import Dialog from "primevue/dialog";
import { formatDate } from '@/composables/useFormatters';
import { loadToast } from '@/composables/loadToast';
loadToast();

const props = defineProps({
    show: Boolean,
    title: String,
    job: Object
});

const emit = defineEmits(["update:show"]);

const form = useForm({
    schedulable: props.job?.schedulable,
    schedulable_type: props.job?.schedulable_type,
    schedulable_id: props.job?.schedulable_id,
    status: props.job?.status,
    executed_at: props.job?.executed_at,
    cancelled_at: props.job?.cancelled_at,
    payload: props.job?.payload,
    job_id: props.job?.job_id,
    scheduled_for: props.job?.scheduled_for?.substring(0, 16),
});

watch(() => props.job, (job) => {
    form.job_id = job?.job_id;
    form.scheduled_for = job?.scheduled_for?.substring(0, 16);
});

const update = () => {
    form.put(route("scheduled-jobs.update", props.job.id), {
        onSuccess: () => {
            emit("update:show", false);
        },
    });
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
        form.schedulable = props.job?.schedulable;
        form.schedulable_type = props.job?.schedulable_type;
        form.schedulable_id = props.job?.schedulable_id;
        form.status = props.job?.status;
        form.executed_at = props.job?.executed_at;
        form.cancelled_at = props.job?.cancelled_at;
        form.payload = props.job?.payload;
        form.job_id = props.job?.jpb_id;
        form.scheduled_for = props.job?.scheduled_for?.substring(0, 16);

    }
});

</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Edit ' + props.title" :style="{ width: '50rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="flex justify-begin mt-6 gap-2">
                    <label class="required">Schedulable: </label>
                    {{ form.schedulable.name }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Schedulable type: </label>
                    {{ form.schedulable_type }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Schedulable Id: </label>
                    {{ form.schedulable_id }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Job Id: </label>
                    {{ form.job_id }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Status: </label>
                    {{ form.status }}
                </div>
                <div  class="flex justify-begin gap-2">
                    <label class="required">Scheduled: </label>
                    {{ formatDate(form.scheduled_for) }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Executed: </label>
                    {{ formatDate(form.executed_at) }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Cancelled: </label>
                    {{ formatDate(form.cancelled_at) }}
                </div>
                <div class="flex justify-begin gap-2">
                    <label class="required">Payload: </label>
                    {{ form.payload }}
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
