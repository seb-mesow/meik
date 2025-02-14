<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { create_request_data, type IForm } from '@/util/form';
import axios from 'axios';
import Button from 'primevue/button';
import { ref } from 'vue';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import { IExhibitForm } from '@/types/meik/models';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitInitPageProps } from '@/types/page_props/exhibit';
import InputField from '@/Components/Form/InputField.vue';
import Form from '@/Components/Form/Form.vue';
import ExportButton from '@/Components/Control/ExportButton.vue';

// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	name?: string,
	init_props: IExhibitInitPageProps,
	category?: {
		id: string,
		name: string,
	},
	rubric?: {
		id: string,
		name: string,
	}
}>();

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
let items: { label: string, url?: string }[] = [];
if (props.category) {
	items.push({
		label: props.category.name,
		url: route('rubric.overview', { category_id: props.category.id })
	});
}
if (props.rubric) {
	items.push({
		label: props.rubric.name,
		url: route('exhibit.overview', { rubric: props.rubric.id }),
	});
}
items.push({
	label: props?.name ?? 'Neues Exponat',
});

// console.log("Exhibit.vue: props.init_props ==");
// console.log(props.init_props);

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
	errs: props.init_props.errs ?? [],
	title_image: props.init_props.title_image,
}

const exhibit_id = form.id;
const is_new = exhibit_id === undefined;
const button_save_metadata_is_loading = ref(false);

async function save_metadata(event: MouseEvent) {
	button_save_metadata_is_loading.value = true;
	if (!is_new) {
		event.preventDefault();
		try {
			console.log("AJAX Request senden");
			await axios.request({
				method: 'patch',
				url: route('ajax.exhibit.set_metadata', { exhibit_id: exhibit_id }),
				data: create_request_data(form) // TODO sendet unnützerweise auch free_texts
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
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>

		<div class="upper-forms">
			<Form class="metadata-form" :action="route('exhibit.create')" method="post">
				<InputField :form="form.val.inventory_number" label="Inventarnummer" />
				<InputField :form="form.val.name" label="Bezeichnung" />
				<InputField :form="form.val.manufacturer" label="Hersteller" />
				<Button v-if="is_new" :loading="button_save_metadata_is_loading" type='submit' label='Speichern' />
				<Button v-else :loading="button_save_metadata_is_loading" type='button' @click="save_metadata"
					label='Metadaten speichern' />
			</Form>
				

			<div class="images-form flex flex-col items-start p-4">
				
				<!-- Button oben rechts -->
				<ExportButton class="bg-blue-500 text-white rounded" />
				<a :href="route('exhibit.images.details', { exhibit_id: exhibit_id })">
					<img
						v-if="form.title_image"
						class="title-image"
						:src="route('ajax.image.get_image', { image_id: form.title_image.id })"
					>
				</a>

				
			</div>
		</div>
		<FreeTextFields v-if="exhibit_id !== undefined" :init_props="form.val.free_texts" :exhibit_id="exhibit_id" />
	</AuthenticatedLayout>
</template>


<style lang="css" scoped>
.upper-forms {
	display: flex;
	flex-wrap: wrap;
	column-gap: 1rem;
}

.metadata-form {
	flex: 18rem;
}

.images-form {
	flex: 18rem;
}

.title-image {
	object-fit: inherit;
}
</style>
