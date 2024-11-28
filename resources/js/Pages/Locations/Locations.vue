<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { ref } from 'vue';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';

const props = defineProps(['locations', 'count'])

const columns = ref([
    { field: 'name', header: 'Name' },
    { field: 'is_public', header: 'Public' },
]);

const editingRows = ref([]);

const onRowEditComplete = (event) => {
    let { data, newData, newValue, field } = event;
    data[field] = newValue;

    if (newData._id) {
        putData(newData, data);
    } else {
        postData(newData, data)
    }
};

async function putData(new_data: any, data: any): Promise<void> {
    console.log(new_data);
    axios.put('/ajax/locations', new_data)
        .then(response => {
            Object.assign(data, response.data)
        })
        .catch(error => {
            console.error(error);
        });
}

async function postData(new_data: any, data: any): Promise<void> {
    new_data._id = `location:${new_data.name}${(new Date()).getTime()}` 
    axios.post('/ajax/locations', new_data)
        .then(response => { 
            Object.assign(data, response.data)
        })
        .catch(error => {
            console.error(error);
        });
}

async function fetchData(page: any): Promise<void> {
    console.log(page)
    axios.get('/api/locations', { params: { page } })
        .then(response => {
            // locations = response.data;
            // currentPage = page;
        })
        .catch(error => {
            console.error(error);
        });
}

const addNew = () => {
    props.locations.unshift({name: null, is_public: false})
}

</script>

<style lang="scss">
@import 'primeicons/primeicons.css';
</style>


<template>

    <Head title="Locations" />

    <div class="absolute bottom-4 right-4">
        <Button icon="pi pi-plus" @click="addNew" />
    </div>

    <DataTable :totalRecords="count" @page="fetchData($event)" lazy :value="locations" paginator :rows="10"
        @data="fetchData" :rowsPerPageOptions="[10, 20, 50]" editMode="row"
        v-model:editingRows="editingRows"
        @row-edit-save="onRowEditComplete($event)"
        dataKey="_id">

        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" style="width: 25%">
            <template #body="{ data, field }">
                <template v-if="field == 'name'">
                {{ data[field]  ?? 'Neuer Standort'}}
                </template>
                <template v-else>
                    <template v-if="data[field] == true">
                        <i class="pi pi-check"></i>
                    </template>
                </template>
            </template>
            <template #editor="{ data, field }">
                <template v-if="field == 'name'">
                    <InputText v-model="data[field]" autofocus fluid />
                </template>
                <template v-else>
                    <Checkbox v-model="data[field]" binary/>
                </template>
            </template>
        </Column>
        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center"></Column>
    </DataTable>

</template>
