<script setup>
import { useForm } from "@inertiajs/vue3";
import { watchEffect } from "vue";

import { loadToast } from '@/composables/loadToast';
const { toast } = loadToast();

const props = defineProps({
    show: Boolean,
    title: String,
    result: Object
});

const emit = defineEmits(["close"]);

const form = useForm({
    date_at: "",
    numbers: [],
    complementary: null
});

const update = () => {
    if (form.numbers.length !== 6) {
        toast.add({severity:'error', summary: 'Error', detail: 'You must select exactly 6 numbers', life: 3000});
        return;
    }

    if (!form.complementary) {
        toast.add({severity:'error', summary: 'Error', detail: 'You must select one complementary number', life: 3000});
        return;
    }

    const payload = {
        date_at: form.date_at instanceof Date
            ? form.date_at.toISOString().slice(0, 10)
            : form.date_at, // por si ya viene bien
        numbers: JSON.stringify(form.numbers),
        complementary: form.complementary
    };

    form.put(route("lottery.results.update", props.result?.id), {
        ...payload,
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => {
            if (form.response && form.response.status === 500) {
                const message = form.response.data?.message || 'Unexpected error';
                toast.add({ severity: 'error', summary: 'Server Error', detail: message, life: 4000 });
            }
        },
        onFinish: () => null,
    });
};

const toggleNumber = (num) => {
    const index = form.numbers.indexOf(num);
    if (index === -1) {
        if (form.numbers.length < 6) {
            form.numbers.push(num);
        }
    } else {
        form.numbers.splice(index, 1);
    }
};

const numbersGrid = Array.from({ length: 49 }, (_, i) => i + 1);
const rows = [];
for (let i = 0; i < numbersGrid.length; i += 10) {
    rows.push(numbersGrid.slice(i, i + 10));
}

const selectComplementary = (num) => {
    form.complementary = num;
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
        form.date_at = props.result?.date_at ? new Date(props.result.date_at) : null;

        try {
            const raw = props.result?.numbers
                ? JSON.parse(props.result.numbers)
                : [];

            let numbers = raw.map(n => parseInt(n));
            form.numbers = numbers.slice(0, 6);
            form.complementary = numbers.length > 6 ? numbers[6] : null;

        } catch (e) {
            form.numbers = [];
            form.complementary = null;
        }
    }
});
</script>

<template>
    <Dialog v-model:visible="props.show" position="top" modal :header="'Update ' + props.title" :style="{ width: '60rem' }" :closable="false">
        <form @submit.prevent="update">
            <div class="flex flex-col gap-4">

                <!-- Responsive Layout -->
                <div class="flex flex-col lg:flex-row gap-10 items-start justify-center w-full">

                <!-- Date Picker -->
                    <div class="flex flex-col gap-2">
                        <label for="date_at">Date</label>
                        <DatePicker
                            v-model="form.date_at"
                            inline
                            dateFormat="yy-mm-dd"
                            :touchUI="false"
                            :showIcon="false"
                            class="w-full lg:w-[20rem] min-w-[18rem]"
                            :style="{ minWidth: '18rem' }"
                        />
                        <small v-if="form.errors.date_at" class="text-red-500">{{ form.errors.date_at }}</small>
                    </div>

                    <!-- Numbers Grid -->
                    <div class="flex flex-col gap-2 flex-1">
                        <label for="numbers">Numbers (select 6 from 1–49)</label>
                        <div class="flex flex-col gap-2">
                            <div
                                v-for="(row, rIndex) in rows"
                                :key="'row-' + rIndex"
                                class="flex flex-wrap gap-2"
                            >
                                <Button
                                    v-for="num in row"
                                    :key="'btn-' + num"
                                    @click="toggleNumber(num)"
                                    :severity="form.numbers.includes(num) ? '' : 'secondary'"
                                    class="w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center
                                    border-gray-300 dark:border-gray-600"
                                >
                                    {{ num }}
                                </Button>
                            </div>
                        </div>
                        <small v-if="form.errors.numbers" class="text-red-500">{{ form.errors.numbers }}</small>
                    </div>
                </div>

                <!-- Complementary number (1 to 49, inline selection) -->
                <div class="flex flex-col gap-2">
                    <label for="complementary">Complementary Number (select one)</label>
                    <div class="flex flex-wrap gap-2 max-w-full overflow-x-auto">
                        <Button
                            v-for="num in 49"
                            :key="'complementary-' + num"
                            @click="selectComplementary(num)"
                            :severity="form.complementary === num ? '' : 'secondary'"
                            class="w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center
                            border-gray-300 dark:border-gray-600"
                        >
                            {{ num }}
                        </Button>
                    </div>
                    <small v-if="form.errors.complementary" class="text-red-500">{{ form.errors.complementary }}</small>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" severity="secondary" @click="emit('close')" />
                    <Button type="submit" label="Update" />
                </div>

            </div>
        </form>
    </Dialog>
</template>
