<script setup lang="ts">
import { useToast } from "primevue/usetoast";
import SplitButton from 'primevue/splitbutton';
import axios, { AxiosRequestConfig } from "axios";
import { route } from "ziggy-js";

const props = defineProps<{
	exhibit_id: number,
}>();

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
		label: 'Exportiere BASIC-Skript für QR-Code',
		url: route('ajax.exhibit.get_qr_code_basic_script', { exhibit_id: props.exhibit_id }),
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
