<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios, { AxiosRequestConfig } from 'axios';
import { nextTick, ref } from 'vue';
import AJAXConfirmationPopup from '@/Components/AJAXConfirmationPopup.vue';

const props = defineProps(['locations', 'count'])

async function fetchData(page: any): Promise<void> {
    console.log(page)
    // axios.get('/api/locations', { params: { page } })
    //     .then(response => {
    //         this.locations = response.data;
    //         this.currentPage = page;
    //     })
    //     .catch(error => {
    //         console.error(error);
    //     });
}


</script>

<template>

    <Head title="Locations" />
    <DataTable :totalRecords="count" @page="fetchData($event)" lazy :value="locations" paginator :rows="10"
        @data="fetchData" :rowsPerPageOptions="[10, 20, 50]">
        <Column field="name" header="Name"></Column>
        <Column field="is_public" header="Public"></Column>
    </DataTable>
</template>
