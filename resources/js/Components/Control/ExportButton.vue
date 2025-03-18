<script setup lang="ts">
import { useToast } from "primevue/usetoast";
import SplitButton from 'primevue/splitbutton';

import Button from 'primevue/button';

import { route } from "ziggy-js";

const props = defineProps<{
	exhibit_id: number,
}>();

const toast = useToast();

const items = [
	{
		label: 'Erstelle BASIC-Skript für QR-Code',
		url: route('ajax.exhibit.get_qr_code_basic_script', { exhibit_id: props.exhibit_id }),
	},
	// {
	// 	label: 'Erstelle Barcode',
	// 	command: () => {
	// 		toast.add({ severity: 'success', summary: 'Exportiere Barcode', detail: 'export Barcode', life:3000 });
	// 		// url:;
	// 	}
	// },
];

const save = () => {
	window.location.href = route('ajax.exhibit.get_qr_code', { exhibit_id: props.exhibit_id });
};
</script>

<template>
	<SplitButton label="Erstelle QR-Code" @click="save" :model="items" />
	<div class="card flex justify-center flex-wrap gap-4">
        <Button label="Primary" raised />
        <Button label="Secondary" severity="secondary" raised />
        <Button label="Success" severity="success" raised />
        <Button label="Info" severity="info" raised />
        <Button label="Warn" severity="warn" raised />
        <Button label="Help" severity="help" raised />
        <Button label="Danger" severity="danger" raised />
        <Button label="Contrast" severity="contrast" raised />
    </div>
</template>
