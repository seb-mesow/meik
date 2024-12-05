<script setup lang="ts">
import SimpleInputField from '@/Components/Form/SimpleInputField.vue';
import FreeTextField from '@/Components/Exhibit/FreeTextField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { create_request_data, type IForm } from '@/util/form';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import Button from 'primevue/button';
import { ref } from 'vue';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import { IExhibitForm} from '@/types/meik/models';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitInitPageProps } from '@/types/page_props/exhibit';
// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	name?: string,
	init_props: IExhibitInitPageProps,
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

console.log("Exhibit.vue: props.init_props ==");
console.log(props.init_props);

// (interne) Attribute der Seite
const form: IExhibitForm = {
	id: props.init_props.id,
	val: {
		inventory_number: {
			id: 'inventory_number',
			val: props.init_props.val.inventory_number.val,
			errs: props.init_props.val.inventory_number.errs ?? [],
		},
		name: {
			id: 'name',
			val: props.init_props.val.name.val,
			errs: props.init_props.val.name.errs ?? [],
		},
		manufacturer: {
			id: 'manufacturer',
			val: props.init_props.val.manufacturer.val,
			errs: props.init_props.val.manufacturer.errs ?? [],
		},
		free_texts: {
			id: 'free_texts',
			val: props.init_props.val.free_texts.val,
			errs: props.init_props.val.free_texts.errs,
		}
	},
	errs: props.init_props.errs ?? []
}

const exhibit_id = form.id;
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
				url: route('ajax.exhibit.set_metadata', exhibit_id),
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
			<SimpleInputField :form="form.val.inventory_number" label="Inventarnummer" />
			<SimpleInputField :form="form.val.name" label="Bezeichnung" />
			<SimpleInputField :form="form.val.manufacturer" label="Hersteller" />
			<Button v-if="is_new"
				:loading="button_save_metadata_is_loading" 
				type='submit'
				label='Speichern'
			/>
			<Button v-else
				:loading="button_save_metadata_is_loading" 
				type='button'
				@click="save_metadata"
				label='Metadaten speichern'
			/>
		</Form>
		<FreeTextFields v-if="exhibit_id !== undefined" :init_props="form.val.free_texts" :exhibit_id="exhibit_id" />
	</AuthenticatedLayout>
</template>
