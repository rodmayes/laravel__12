<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch, ref } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    resource: Object,
    clubs: []
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    priority: 0,
    club_id: "",
    visible: true
});

const timeValue = ref(null);

// Actualiza `form.name` cuando el usuario cambia el DatePicker
watch(timeValue, (newDate) => {
    if (newDate instanceof Date) {
        const hh = newDate.getHours().toString().padStart(2, '0');
        const mm = newDate.getMinutes().toString().padStart(2, '0');
        form.name = `${hh}:${mm}`;
    }
});

// Solo inicializa al abrir el modal
watch(() => props.show, (showing) => {
    if (showing) {
        form.errors = {};
        form.name = props.resource?.name ?? "";
        form.playtomic_id = props.resource?.playtomic_id ?? "";
        form.priority = props.resource?.priority ?? 0;
        form.club_id = props.resource?.club_id ?? "";
        form.visible = props.resource?.visible ?? false;
    }
});

const update = () => {
    form.put(route("playtomic.resources.update", props.resource?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("updated");
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};
</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title"
            :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name"/>
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="playtomic_id">PlaytomicId</label>
                    <InputText id="playtomic_id" v-model="form.playtomic_id" class="flex-auto" autocomplete="off" placeholder="PlaytomicId"/>
                    <small v-if="form.errors.playtomic_id" class="text-red-500">{{ form.errors.playtomic_id }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="clib">Club</label>
                    <Select v-model="form.club_id" :options="props.clubs" optionValue="id" optionLabel="name" placeholder="Select a Club"/>
                </div>
                <div class="flex flex-row gap-4">
                    <!-- Priority -->
                    <div class="flex flex-col gap-2 flex-1">
                        <label for="priority">Priority</label>
                        <InputNumber v-model="form.priority" inputId="horizontal-buttons-priority" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementbuttonicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementbuttonicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                        <small v-if="form.errors.priority" class="text-red-500">{{ form.errors.priority }}</small>
                    </div>

                    <!-- Visible -->
                    <div class="flex flex-col gap-2 flex-1">
                        <label for="visible">Visible</label>
                        <ToggleButton v-model="form.visible" class="w-full max-w-[6rem]" onLabel="On" offLabel="Off" />
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
