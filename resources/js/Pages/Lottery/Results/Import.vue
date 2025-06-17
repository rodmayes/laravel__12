<script setup>
import { usePage } from '@inertiajs/vue3';
import {reactive} from "vue";
import { loadToast } from '@/composables/loadToast';
const { toast } = loadToast();

const page = usePage();
const csrfToken = page.props.csrf_token;

const props = defineProps({
    show: Boolean,
    title: String
});

const emit = defineEmits(["close"]);

const data = reactive({
    inProgress: false
});

const onImportError = (event) => {
    toast.add({severity:'error', summary: 'Error', detail: event.xhr.statusText, life: 3000});
}

const onImportSuccess  = (event) => {
    toast.add({severity:'success', summary: 'Upload', detail: 'File uploaded', life: 3000});
    emit('close');
}

const onBeforeSend = (event) => {
    event.xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    data.inProgress = false;
}

const onProgress = () => {
    data.inProgress = true;
}

</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal header="Import results from excel" :style="{ width: '50rem' }" :closable="false">
        <div class="flex flex-col gap-4">
            <FileUpload name="results_file_import" url="/lottery/results/import"
                        :headers="{ 'X-CSRF-TOKEN': csrfToken }"
                        @error="onImportError"
                        accept=".xls,.xlsx" :maxFileSize="1000000"
                        @before-send="onBeforeSend"
                        @upload="onImportSuccess"
                        @progress="onProgress"
            >
                <template #empty>
                    <span>Drag and drop files to here to upload.</span>
                </template>
            </FileUpload>
            <div class="flex justify-end gap-2">
                <Tag severity="warn" icon="pi pi-spin pi-spinner" value="In progress..." v-show="data.inProgress"></Tag>
                <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
            </div>
        </div>
    </Dialog>
</template>
