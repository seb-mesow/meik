<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { ref } from 'vue';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';

const props = defineProps(['locations', 'count'])

let currentPage = 0;
let currentPageSize = 10;

let rows = ref(props.locations);
let rowNumber = ref(props.count);
let deleteLocationDialog = ref();

let selectedRow = ref()

const columns = ref([
    { field: 'name', header: 'Name' },
    { field: 'is_public', header: 'Public' },
]);

const editingRows = ref([]);

const onRowEditComplete = (event: any) => {
    let { data, newData, newValue, field } = event;
    data[field] = newValue;

    if (newData._id) {
        putData(newData, data);
    } else {
        postData(newData, data)
    }
};

const confirmDeleteProduct = (id: any) => {
    selectedRow.value = id;
    deleteLocationDialog.value = true;
};

async function putData(new_data: any, data: any): Promise<void> {
    axios.put('/ajax/locations', new_data)
        .then(response => {
            Object.assign(data, response.data)
        })
        .catch(error => {
        });
}

async function deleteLocation(id: string): Promise<void> {
    deleteLocationDialog.value = false;
    rowNumber.value -= 1;
    axios.delete(`/ajax/locations/${id}`)
        .then(() => {
            fetchData({ page: currentPage, rows: currentPageSize })
        });
}

async function postData(new_data: any, data: any): Promise<void> {
    new_data._id = `location:${new_data.name}${(new Date()).getTime()}`
    axios.post('/ajax/locations', new_data)
        .then(response => {
            Object.assign(data, response.data)
        })
        .catch(error => {
        });
}

async function fetchData(event: any): Promise<void> {
    currentPage = event.page
    currentPageSize = event.rows

    axios.get('ajax/locations', { params: { page: event.page, pageSize: event.rows } })
        .then(response => {
            rows.value = response.data;
        })
        .catch(error => {
        });
}

const addNew = () => {
    rows.value.unshift({ name: null, is_public: false })
}

</script>

<style lang="scss">
@import 'primeicons/primeicons.css';
</style>


<template>

    <Head title="Locations" />

    <Dialog v-model:visible="deleteLocationDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span>Möchten Sie den Standort wirklich löschen?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="deleteLocationDialog = false" />
            <Button label="Yes" icon="pi pi-check" @click="deleteLocation(selectedRow)" />
        </template>
    </Dialog>

    <div class="absolute bottom-4 right-4">
        <Button icon="pi pi-plus" @click="addNew" />
    </div>

    <DataTable :totalRecords="rowNumber" @page="fetchData($event)" lazy :value="rows" paginator :rows="10" @data="fetchData"
        :rowsPerPageOptions="[1, 10, 20, 50]" editMode="row" v-model:editingRows="editingRows"
        @row-edit-save="onRowEditComplete($event)" dataKey="_id">

        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" style="width: 25%">
            <template #body="{ data, field }">
                <template v-if="field == 'name'">
                    <a :href="route('places.all', {'location': data._id})">{{ data[field] ?? 'Neuer Standort' }}</a>
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
                    <Checkbox v-model="data[field]" binary />
                </template>
            </template>
        </Column>
        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center"></Column>
        <Column style="width: 10%; min-width: 8rem">
            <template #body="{ data }">
                <Button class="border-none" icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteProduct(data['_id'])" />
            </template>
        </Column>
    </DataTable>

</template>
