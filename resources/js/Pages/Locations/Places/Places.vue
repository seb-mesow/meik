<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { ref } from 'vue';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';

const props = defineProps<{
    places: place[],
    count: number,
    location_id: string,
    location_name: string
}>()

interface place {
    _id: string | null,
    _rev: string | null,
    name: string | null,
    is_public: boolean | null,
}

const home = ref({
    icon: 'pi pi-home',
    route: 'exhibit.overview'
});
const items = ref([
    {
        label: props.location_name,
        route: 'location.overview'
    },
    {
        label: 'Plätze',
        route: 'place.overview'
    },
]);

let currentPage = 0;
let currentPageSize = 10;
let rows = ref(props.places);
let rowNumber = ref(props.count);
let deleteplaceDialog = ref();
let allowNew = ref(true);
const confirm = useConfirm();
const toast = useToast();

const columns = ref([
    { field: 'name', header: 'Name' },
    // { field: 'is_public', header: 'Öffentlich' },
]);

const editingRows = ref([]);

const onRowEditComplete = (event: any) => {
    let { data, newData } = event;

    if (!newData.name) {
        editingRows.value.push(data)
        toast.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 })
        return
    }

    if (newData._id) {
        putData(newData, data);
    } else {
        postData(newData, data)
    }
    allowNew.value = true
};

const delete_confirm = (event: any, place: any) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Sind Sie sicher das Sie den Platz löschen wollen?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Abbrechen',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Bestätigen'
        },
        accept: () => {
            deleteplace(place._id)
        },
        reject: () => {

        }
    });
};

function putData(new_data: any, data: any): void {
    axios.put('/ajax/places', new_data)
        .then(response => {
            Object.assign(data, response.data)
        })
        .catch(error => {
        });
}

function deleteplace(id: string): void {
    deleteplaceDialog.value = false;
    rowNumber.value -= 1;

    if (id == 'new') {
        fetchData({ page: currentPage, rows: currentPageSize })
    } else {
        axios.delete(`/ajax/places/${id}`)
            .then(() => {
                toast.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Platz wurde erfolgreich gelöscht', life: 3000 });
                fetchData({ page: currentPage, rows: currentPageSize })
            }).catch(() =>
                toast.add({ severity: 'error', summary: 'Fehler', detail: 'Der Platz konnte nicht gelöscht werden', life: 3000 })
            );
    }

}

function postData(new_data: any, data: any): void {
    new_data._id = `place:${new_data.name}${(new Date()).getTime()}`
    new_data.location = props.location_id;
    axios.post('/ajax/places', new_data)
        .then(response => {
            Object.assign(data, response.data)
        })
        .catch(error => {
        });
}

function fetchData(event: any): void {
    currentPage = event.page
    currentPageSize = event.rows

    axios.get('ajax/places', { params: { location: props.location_id, page: event.page, pageSize: event.rows } })
        .then(response => {
            rows.value = response.data;
        })
        .catch(error => {
        });
}

const addNew = () => {
    const newRow = { id: '', name: null, is_public: false }
    rows.value.unshift(newRow)
    allowNew.value = false;
    rowNumber.value += 1;
    editingRows.value.unshift(newRow)
}
</script>

<style lang="scss">
@import 'primeicons/primeicons.css';
</style>


<template>
    <Toast />
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb :home="home" :model="items">
                <template #item="{ item }">
                    <a class="cursor-pointer text-2xl" :href="route(item.route)">
                        <span v-if="item.icon" :class="item.icon"></span>
                        <span v-else>{{ item.label }}</span>
                    </a>
                </template>
            </Breadcrumb>
        </template>
		
        <ConfirmPopup></ConfirmPopup>
		
        <div class="absolute bottom-4 right-4">
            <Button severity="info" :disabled="!allowNew" icon="pi pi-plus" @click="addNew" />
        </div>
		
        <div class="p-4">
            <Card>
                <template #content>
                    <DataTable :totalRecords="rowNumber" @page="fetchData($event)" lazy :value="rows" paginator
                        :rows="10" @data="fetchData" :rowsPerPageOptions="[10, 20, 50]" editMode="row"
                        v-model:editingRows="editingRows" @row-edit-save="onRowEditComplete($event)">

                        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"
                            style="width: 25%">
                            <template #body="{ data, field }">
                                <template v-if="field == 'name'">

                                    <span v-if="data._id">{{ data[field] }}</span>
                                    <span v-else class="text-green-600">Neuer Platz</span>
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
                        <Column :rowEditor="true" style="width: 10%;" bodyStyle="text-align:center">
                        </Column>
                        <Column style="width: 10%;">
                            <template #body="{ data }">
                                <Button class="border-none" icon="pi pi-trash" outlined rounded severity="danger"
                                    @click="delete_confirm($event, data)" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>
		
    </AuthenticatedLayout>
</template>
