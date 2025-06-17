<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { ref, reactive } from "vue";
import axios from "axios";
import { loadToast } from '@/composables/loadToast';
import ProgressSpinner from 'primevue/progressspinner';

const loading = ref(false);

const props = defineProps({
    title: String
});

loadToast();
const { toast } = loadToast();

const data = reactive({
    sendMail: false,
    makeNumbersConfirm: false,
    excludedNumbers: [],
    combinations: []
});

const makeMagikNumbers = () => {
    data.makeNumbersConfirm = false;
    loading.value = true;
    axios.post(route('lottery.combinations.make-magik-numbers'), {excludedNumbers: data.excludedNumbers})
        .then(response => {
            data.combinations = response.data.combinations;
            toast.add({ severity: 'success', summary: 'Magik numbers', detail: 'Magik numbers generated successfully', life: 3000});
        })
        .catch(() => {
            toast.add({severity: 'error', summary: 'Magik numbers', detail: 'Magik numbers generated unSuccessfully', life: 3000});
        })
        .finally(() => {
        loading.value = false;
    });
};

const onSendMail = () => {
    data.sendMail = false;
    axios.post(route('lottery.combinations.send-mail-with-combinations'), {combinations: data.combinations})
        .then(() => {
            toast.add({severity: 'success', summary: 'Mail', detail: 'Mail send successfully', life: 3000});
            data.sendMail = false;
        })
        .catch(() => {
                toast.add({severity: 'error', summary: 'Mail', detail: 'Magik send unSuccessfully', life: 3000});
        });
};

const toggleNumber = (num) => {
    const index = data.excludedNumbers.indexOf(num);
    if (index === -1) {
        data.excludedNumbers.push(num);
    } else {
        data.excludedNumbers.splice(index, 1);
    }
};

const getSortedCombinations = () => {
    return data.combinations.map(comb => [...comb].sort((a, b) => a - b));
};

const breadcrum = ref([
    { label: 'Lottery' },
    { label: props.title, url: route('lottery.combinations.index') }
]);
</script>

<template>
    <app-layout :items="breadcrum">
        <div class="card">
            <Menubar class="mb-4">
                <template #start>
                    <Button v-show="can(['lottery.magik_numbers_create'])" label="Make magik numbers" @click="data.makeNumbersConfirm = true" icon="pi pi-dollar"
                            severity="success" size="small" class="mr-2" :disabled="loading"/>
                    <Button v-show="data.combinations.length > 0" label="Send mail with combinations" @click="onSendMail" icon="pi pi-envelope"
                            severity="warn" size="small" class="mr-2"/>
                </template>
            </Menubar>

            <Card>
                <template #title>Exclude numbers</template>
                <template #content>
                    <p class="m-0">
                        Set excluded number for combinations
                    </p>
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap gap-2 max-w-full overflow-x-auto">
                            <Button
                                v-for="num in 49"
                                :key="'complementary-' + num"
                                @click="toggleNumber(num)"
                                :severity="data.excludedNumbers.includes(num) ? '' : 'secondary'"
                                class="w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center
                            border-gray-300 dark:border-gray-600"
                            >
                                {{ num }}
                            </Button>
                        </div>
                    </div>
                </template>
            </Card>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4">
                <Card v-for="(combination, idx) in getSortedCombinations()" :key="'comb-' + idx">
                    <template #title>Combination {{ idx + 1 }}</template>
                    <template #content>
                        <div class="flex flex-wrap gap-2">
                            <Tag
                                v-for="(number, i) in combination"
                                :key="i"
                                :value="number"
                                severity="secondary"
                            />
                        </div>
                    </template>
                </Card>
            </div>

        </div>
    </app-layout>
    <Dialog v-model:visible="loading" modal :closable="false" :style="{ width: '300px' }">
        <template #header>
            <span class="font-bold">Generating combinations</span>
        </template>
        <div class="flex justify-center items-center py-6">
            <ProgressSpinner />
        </div>
        <template #footer>
            <span class="text-sm text-gray-500">Please wait...</span>
        </template>
    </Dialog>
    <Dialog v-model:visible="data.makeNumbersConfirm" :style="{ width: '450px' }" header="Confirm" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span>
                Are you sure you want to generate magik numbers?
            </span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="data.makeNumbersConfirm = false" />
            <Button label="Yes" icon="pi pi-check" @click="makeMagikNumbers" />
        </template>
    </Dialog>
</template>

<style scoped lang="scss">
</style>
