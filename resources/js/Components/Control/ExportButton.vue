
<template>
    <div class="card flex justify-center">
        <Toast />
        <ExportButton label="Exportiere..." @click="save" :model="items" />
    </div>
</template>

<script setup>
import { useToast } from "primevue/usetoast";
import ExportButton from 'primevue/splitbutton';
const toast = useToast();


const items = [
    {
        label: 'Exportiere QR-Code1',
        command: () => {
            toast.add({ severity: 'success', summary: 'Exportiere QR-Code1', detail: 'Data Updated', life: 3000 });
            // url:;
        }
    },
    {
        label: 'Exportier QR-Code2',
        command: () => {
            toast.add({ severity: 'warn', summary: 'Exportier QR-Code2', detail: 'Data Deleted', life: 3000 });
            // url:;
        }
    },
    {
    separator: true
    },
    {
        label: 'Exportiere Barcode',
        command: () => {
            toast.add({ severity: 'warn', summary: 'Exportiere Barcode', detail: 'export Barcode', life:3000 });
            // url:;
        }

    },
    
];

const save = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Data Saved', life: 3000 });
};
</script>


