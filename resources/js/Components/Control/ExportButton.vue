<script setup>
import { useToast } from "primevue/usetoast";
import SplitButton from 'primevue/splitbutton';

const toast = useToast();

const items = [
    {
        label: 'Exportiere QR-Code1',
        command: () => {
            toast.add({ severity: 'success', summary: 'Exportiere QR-Code1', detail: 'Qr-Code1 Export erfolgreich', life: 3000 });
            // url:;
        }
    },
    {
        label: 'Exportier QR-Code2',
        command: () => {
            toast.add({ severity: 'success', summary: 'Exportier QR-Code2', detail: 'Qr-Code2 Export erfolgreich', life: 3000 });
            // url:;
        }
    },
    {
    separator: true
    },
    {
        label: 'Exportiere Barcode',
        command: () => {
            toast.add({ severity: 'success', summary: 'Exportiere Barcode', detail: 'export Barcode', life:3000 });
            // url:;
        }

    },
    
];

const save = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Data Saved', life: 3000 });
};
</script>

<template>
	<SplitButton label="Erstelle QR-Code" @click="save" :model="items" />
</template>
