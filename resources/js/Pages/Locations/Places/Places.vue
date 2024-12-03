<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios, { AxiosRequestConfig } from 'axios';
import { nextTick, ref, toRaw } from 'vue';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import AJAXConfirmationPopup from '@/Components/AJAXConfirmationPopup.vue';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const props = defineProps<{
    places: place[],
    count: number
}>()

interface place {
    _id: string|null,
    _rev: string|null,
    name: string|null,
    is_public: boolean|null,
}

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
    { field: 'is_public', header: 'Öffentlich' },
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
        message: 'Sind Sie sicher das Sie den Standort löschen wollen? Untergeordnete Standplätze werden auch gelöscht',
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
                toast.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich gelöscht', life: 3000 });
                fetchData({ page: currentPage, rows: currentPageSize })
            }).catch(() =>
                toast.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht gelöscht werden', life: 3000 })
            );
    }

}

function postData(new_data: any, data: any): void {
    new_data._id = `place:${new_data.name}${(new Date()).getTime()}`
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

    axios.get('ajax/places', { params: { page: event.page, pageSize: event.rows } })
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

    <Head title="places" />
    <Toast />
    <AuthenticatedLayout>
        <ConfirmPopup></ConfirmPopup>
        <div class="absolute bottom-4 right-4">
            <Button :disabled="!allowNew" icon="pi pi-plus" @click="addNew" />
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
                                    <a v-if="data['_id']"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                        :href="route('places.all', { 'place': data._id })">
                                        {{ data[field] }}</a>
                                    <span v-else class="text-green-600">Neuer Standort</span>
                                </template>
                                <template v-else>
                                    <template v-if="data[field] == true">
                                        <i class="pi pi-check"></i>
                                    </template>
                                </template>
                            </template>
                            <template #editor="{ data, field }">
                                <template v-if="field == 'name'">
                                    {{ data }}
                                    <InputText v-model="data[field]" autofocus fluid />
                                </template>
                                <template v-else>
                                    <Checkbox v-model="data[field]" binary />
                                </template>
                            </template>
                        </Column>
                        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                        </Column>
                        <Column style="width: 10%; min-width: 8rem">
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
