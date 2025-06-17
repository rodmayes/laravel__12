<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    club: Object
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: "",
    playtomic_id: "",
    playtomic_id_summer: "",
});

const update = () => {
    form.put(route("playtomic.clubs.update", props.club?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
        form.name = props.club?.name;
        form.playtomic_id = props.club?.playtomic_id;
        form.playtomic_id_summer = props.club?.playtomic_id_summer;
    }
});


</script>

<template>
     <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title" :style="{ width: '30rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="name">Name</label>
                    <InputText id="name" v-model="form.name" class="flex-auto" autocomplete="off" placeholder="Name" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="email">PlaytomicId</label>
                    <InputText id="email" v-model="form.playtomic_id" class="flex-auto" autocomplete="off" placeholder="PlaytomicId" />
                    <small v-if="form.errors.playtomic_id" class="text-red-500">{{ form.errors.playtomic_id }}</small>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="email">PlaytomicId Summer</label>
                    <InputText id="email" v-model="form.playtomic_id_summer" class="flex-auto" autocomplete="off" placeholder="PlaytomicId Summer" />
                    <small v-if="form.errors.playtomic_id_summer" class="text-red-500">{{ form.errors.playtomic_id_summer }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')"></Button>
                    <Button type="submit" label="Update"></Button>
                </div>
            </div>
        </form>
    </Dialog>
</template>
