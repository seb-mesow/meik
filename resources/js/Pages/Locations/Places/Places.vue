<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps(['places'])

</script>

<template>
    <Head title="Exhibits" />
    <DataTable :value="places">
        <Column field="name" header="Name"></Column>
        <Column field="is_public" header="Public"></Column>
    </DataTable>
</template>
