<script setup lang="ts">
import InputField from '@/Components/Form/SimpleInputField.vue';
import FreeTextField from '@/Components/Exhibit/FreeTextField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { create_request_data, type IForm } from '@/util/form';
import { Head } from '@inertiajs/vue3';
import axios, { AxiosResponse } from 'axios';
import Button from 'primevue/button';
import Form from '@/Components/Form/Form.vue';
import { ref } from 'vue';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import { IFreeText } from '@/types/meik/models';
import Breadcrumb from 'primevue/breadcrumb';

// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	id?: string,
	name?: string
	form: IForm<'exhibit', {
		inventory_number: string,
		name: string,
		manufacturer: string,
		free_texts: IFreeText[],
	}>
}>();

const home = ref({
	icon: 'pi pi-home',
	route: 'exhibit.overview'
});
const items = ref([
	{
		label: props?.name ?? 'Neues Exponat'
	},
]);

// (interne) Attribute der Seite
const form = props.form;

const exhibit_id = props.id;
const is_new = exhibit_id === undefined;
const button_save_metadata_is_loading = ref(false);

async function save_metadata(event: SubmitEvent) {
	button_save_metadata_is_loading.value = true;
	if (!is_new) {
		event.preventDefault();
		try {
			console.log("AJAX Request senden");
			await axios.request({
				method: 'patch',
				url: route('exhibit.set_metadata', exhibit_id),
				data: create_request_data(form)
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
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a v-if="item.route" class="cursor-pointer text-2xl" :href="route(item.route)">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
					<span class="text-2xl" v-else>
						{{ item.label }}
					</span>
				</template>
			</Breadcrumb>
		</template>

		<Form :action="route('exhibit.create')" method="post">
			<InputField :form_value="form.val.inventory_number" label="Inventarnummer" />
			<InputField :form_value="form.val.name" label="Bezeichnung" />
			<InputField :form_value="form.val.manufacturer" label="Hersteller" />
			<Button v-if="is_new" :loading="button_save_metadata_is_loading" type='submit' label='Speichern' />
			<Button v-else :loading="button_save_metadata_is_loading" type='button' @click="save_metadata"
				label='Metadaten speichern' />
		</Form>
		<FreeTextFields v-if="exhibit_id !== undefined" :form="form.val.free_texts" :exhibit_id="exhibit_id" />
	</AuthenticatedLayout>
</template>
