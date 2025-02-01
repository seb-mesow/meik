<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { create_request_data, type IForm } from '@/util/form';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import Button from 'primevue/button';
import { ref } from 'vue';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import { IExhibitForm } from '@/types/meik/models';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitInitPageProps } from '@/types/page_props/exhibit';
import InputField from '@/Components/Form/InputField.vue';
import Form from '@/Components/Form/Form.vue';
import ExportButton from '@/Components/Control/ExportButton.vue';

import AutoComplete, { AutoCompleteCompleteEvent, AutoCompleteOptionSelectEvent } from 'primevue/autocomplete';
// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	name?: string,
	init_props: IExhibitInitPageProps,
	rubric: any
}>();

const suggested_exhibits = ref([]);
const connected_exhibits = ref(props.init_props.val.connected_exhibits);
const suggested_rubrics = ref([]);
const rubric = ref(props.init_props.val.rubric)
const items = ref()


const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
items.value = [
	{
		label: 'Exponate',
		url: route('exhibit.overview'),
	},
	{
		label: props?.name ?? 'Neues Exponat',
	},
];

const update_breadcrumbs = (rubric: any) => {
	return [
		{
			label: 'Kategorien',
			url: route('category.overview')
		},
		{
			label: rubric.category,
			url: route('rubric.overview', { category: rubric.category })
		},
		{
			label: rubric.name,
			url: route('exhibit.overview', { rubric: rubric.id }),
		},
		{
			label: props?.name ?? 'Neues Exponat',
		},
	];
}

if (props.rubric) {
 	items.value = update_breadcrumbs(rubric.value)
}


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
			console.log(`form ==`);
			console.log(form);
			const request_data = create_request_data(form);
			console.log(`request_data ==`);
			console.log(request_data);
			request_data.connected_exhibits = connected_exhibits.value.map((ce) => ce.id);
			request_data.rubric = rubric.value.id
			await axios.request({
				method: 'patch',
				url: route('ajax.exhibit.set_metadata', { exhibit_id: exhibit_id }),
				data: request_data // TODO sendet unnützerweise auch free_texts
			});
			console.log("AJAX Request erfolgreich");
			items.value = update_breadcrumbs(rubric.value)
			console.log(items.value)
			
		} catch (e) {
			console.log("Fehler bei AJAX Request");
			console.log(e);
		}
		button_save_metadata_is_loading.value = false;
	}
}


async function search_exhibits(event: AutoCompleteCompleteEvent): Promise<void> {
	const query = event.query
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route(`ajax.exhibit.search`),
		// Hier könnten auch weitere exhibit id eingebracht werden. 
		params: {
			'excluded': [exhibit_id],
			'query': query
		}
	};
	return axios.request(request_config).then(
		(response: AxiosResponse) => {
			suggested_exhibits.value = response.data
			console.log(suggested_exhibits.value)
		}
	);
}

async function search_rubrics(event: AutoCompleteCompleteEvent): Promise<void> {
	const query = event.query
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route(`ajax.rubric.search`),
		params: {
			'query': query
		}
	};
	return axios.request(request_config).then(
		(response: AxiosResponse) => {
			suggested_rubrics.value = response.data
			console.log(suggested_exhibits.value)
		}
	);
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
				<p><label for="multiple-ac-1">Verknüpfte Exponate</label></p>
				<AutoComplete forceSelection v-model="connected_exhibits" inputId="multiple-ac-1" multiple fluid dropdown
					:suggestions="suggested_exhibits" @complete="search_exhibits" optionLabel="name" />
				<p><label for="ac-2">Rubrik</label></p>
				<AutoComplete forceSelection v-model="rubric" inputId="ac-2" fluid
					:suggestions="suggested_rubrics" @complete="search_rubrics" optionLabel="name"  dropdown/>
				<Button v-if="is_new" :loading="button_save_metadata_is_loading" type='submit' label='Speichern' />
				<Button v-else :loading="button_save_metadata_is_loading" type='button' @click="save_metadata"
					label='Metadaten speichern' />
			</Form>


			<div class="images-form flex flex-col items-start p-4">

				<!-- Button oben rechts -->
				<ExportButton class="bg-blue-500 text-white rounded" />
				<a :href="route('exhibit.images.details', { exhibit_id: exhibit_id })">
					<img v-if="form.title_image" class="title-image"
						:src="route('ajax.image.get_image', { image_id: form.title_image.id })">
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
