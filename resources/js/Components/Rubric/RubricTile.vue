<script setup lang="ts">
import type { IRubricForTile } from '@/types/meik/models';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import DynamicDialog from 'primevue/dynamicdialog';
import Button from 'primevue/button';
import { defineAsyncComponent, reactive } from 'vue';
const RubricDialog = defineAsyncComponent(() => import('./RubricDialog.vue'));
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmPopup from 'primevue/confirmpopup';
import Toast from 'primevue/toast';

const confirm_service = useConfirm();
const emit = defineEmits(['reload']);

// (interne) Attribute der Komponente
const props = defineProps<{
    rubric: { id: string, name: string };
    category: string
}>();
let rubric = reactive(props.rubric);

const dialog = useDialog();
const toast_service = useToast();

const edit = () => {
    console.log(rubric)
    const dialogRef = dialog.open(RubricDialog, {
        props: {
            header: 'TEST',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true,
        },
        data: {
            rubric: rubric,
            category: props.category
        },
        onClose: (options) => {
            const data = options?.data;
            if (data) {
                rubric.name = data.data.name
            }
        }
    });
}

const delete_rubric = (event: any, rubric: any): Promise<void> => {
    console.log(event.currentTarget, rubric)
    return new Promise((resolve: () => void, reject: () => void) => {
        confirm_service.require({
            target: event.currentTarget,
            message: "Sind Sie sicher das Sie die Rubrik löschen wollen?",
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
                accept_delete(rubric).then(resolve, reject)
            },
            reject: reject
        });
    });
};

const accept_delete = (rubric: any): Promise<void> => {
    return new Promise((resolve: () => void, reject: () => void) => {
        ajax_delete(rubric).then(
            () => {
                toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Die Rubrik wurde erfolgreich gelöscht.', life: 3000 });
                reload()
                resolve();
            },
            () => {
                toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Die Rubrik konnte nicht gelöscht werden.', life: 3000 });
                reject();
            }
        );
    });
}

const ajax_delete = (rubric: any): Promise<void> => {
    if (rubric.rubric_id) {
        throw new Error("undefined id");
    }
    const request_config: AxiosRequestConfig<any> = {
        method: "delete",
        url: route('ajax.rubric.delete', { rubric_id: rubric.id })
    }
    return axios.request(request_config);
}

const reload = () => {
    emit('reload')
}


</script>

<template>
    <Toast />
    <ConfirmPopup />
    <div class="h-fit">
        <Button @click="edit">Edit</Button>
        <Button @click="delete_rubric($event, rubric)">Delete</Button>
        <a :href="route('exhibit.overview', { rubric: rubric.id })">
            <div class="rubric-tile">
                <p>{{ rubric.name }}</p>
            </div>
        </a>
    </div>
    <DynamicDialog />
</template>

<style lang="css" scoped>
.rubric-tile {
    width: 300px !important;
    height: 100px;
    border-radius: 20px;
    padding: 20px;
    margin: 10px;
    color: black;
    background-color: #808080;
}
</style>
