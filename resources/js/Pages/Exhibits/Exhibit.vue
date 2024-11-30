<script setup lang="ts">
import InputField from '@/Components/InputField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IForm } from '@/types/meik/technical';
import { Head } from '@inertiajs/vue3';
import axios, { AxiosResponse } from 'axios';
import Button from 'primevue/button';
import Form from '@/Components/Form.vue';
import { ref } from 'vue';

// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	id?: string
	form: IForm<{
		id?: string,
		inventory_number: string,
		name: string,
		manufacturer: string
	}>
}>();

// (interne) Attribute der Seite
const form = props.form;
console.log(props.id);
//@ts-ignore
const is_new = props.id === undefined;
console.log(is_new);
const button_save_metadata_is_loading = ref(false);

async function save_metadata(event: SubmitEvent) {
	button_save_metadata_is_loading.value = true;
	if (!is_new) {
		event.preventDefault();
		try {
			console.log("AJAX Request senden");
			await axios.request({
				method: 'patch',
				url: route('exhibit.set_metadata', form.vals.id),
				data: {
					inventory_number: form.vals.inventory_number,
					manufacturer: form.vals.manufacturer,
					name: form.vals.name,
				}
			});
			console.log("AJAX Request erfolgreich");
		} catch (e) {
			console.log("Fehler bei AJAX Request");
			console.log(e);
		}
		button_save_metadata_is_loading.value = false;
	}
}
</script>

<template>
	<Head title="Exponat" />

	<AuthenticatedLayout>
		<template #header>
			<h2	class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
				Exponat
			</h2>
		</template>
		<Form :action="route('exhibit.create')" method="post">
			<InputField :form_value="form.vals.inventory_number" label="Inventarnummer"/>
			<InputField :form_value="form.vals.name" label="Bezeichnung"/>
			<InputField :form_value="form.vals.manufacturer" label="Hersteller"/>
			<Button v-if="is_new"
				:loading="button_save_metadata_is_loading" 
				type='submit'
				label='Speichern'
			/>
			<Button v-else
				:loading="button_save_metadata_is_loading" 
				type='button'
				@click="save_metadata"
				label='Metadatenn speichern'
			/>
		</Form>
		<Button v-if="!is_new" label="Abschnitt hinzufügen" :href="route('user.new')"/>
	</AuthenticatedLayout>
</template>
