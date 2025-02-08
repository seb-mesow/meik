<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from 'primevue/button';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitProps } from '@/types/page_props/exhibit';
import Form from '@/Components/Form/Form.vue';
import ExportButton from '@/Components/Control/ExportButton.vue';
import {
	ExhibitForm,
	IExhibitForm,
	IExhibitFormConstructorArgs,
	ISelectableValues,
} from '@/form/exhibitform';
import SelectField from '@/Components/Form/SelectField.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import GroupSelectField from '@/Components/Form/GroupSelectField.vue';
import InputNumberField from '@/Components/Form/InputNumberField.vue';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	selectable_values: ISelectableValues,
	exhibit_props?: IExhibitProps,
	category?: {
		id: string,
		name: string,
	},
	rubric?: {
		id: string,
		name: string,
	}
}>();

console.log('props.selectable_values ==');
console.log(props.selectable_values);
console.log('props.exhibit_props ==');
console.log(props.exhibit_props);

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
	label: props.exhibit_props?.name ?? 'Neues Exponat',
});

// console.log("Exhibit.vue: props.init_props ==");
// console.log(props.init_props);

const toast_service = useToast();

// (interne) Attribute der Seite
const form_constructor_args: IExhibitFormConstructorArgs = {
	aux: {
		toast_service: toast_service,
		selectable_values: props.selectable_values
	},
};
if (props.exhibit_props) {
	form_constructor_args.data = {
		id: props.exhibit_props.id,
	
		// Kerndaten
		inventory_number: props.exhibit_props.inventory_number,
		name: props.exhibit_props.name,
		short_description: props.exhibit_props.short_description,
		rubric: props.exhibit_props.rubric,
		location_id: props.exhibit_props.location_id,
		place_id: props.exhibit_props.place_id,
		// TODO connected_exhibits
		
		// Bestandsdaten
		preservation_state_id: props.exhibit_props.preservation_state_id,
		current_value: props.exhibit_props.current_value,
		kind_of_property_id: props.exhibit_props.kind_of_property_id,
		
		// Zugangsdaten
		acquistion_info: props.exhibit_props.acquistion_info,
		
		// Geräte- und Buchinformationen
		manufacturer: props.exhibit_props.manufacturer,
		original_price: props.exhibit_props.original_price,
		
		// Geräteinformationen
		device_info: props.exhibit_props.device_info,
		
		// Buchinformationen
		book_info: props.exhibit_props.book_info,
	}
}
const exhibit_form: IExhibitForm = new ExhibitForm(form_constructor_args);
const exhibit_id = exhibit_form.id;
const is_new = exhibit_id === undefined;
</script>

<template>
	<Toast/>
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
				
				<!-- Kerndaten -->
				<InputTextField2 :form="exhibit_form.name" label="Bezeichnung" />
				
				<InputTextField2 :form="exhibit_form.inventory_number" label="Inventarnummer" />
				
				<InputTextField2 :form="exhibit_form.short_description" label="Kurzbeschreibung" />
				
				<GroupSelectField :form="exhibit_form.rubric" label="Rubrik">
					<template #optiongroup="category">
						<span class="font-bold">{{ category.parent }}</span>
					</template>
					<template #option="rubric">
						<span class="ps-4">{{ rubric }}</span>
					</template>
				</GroupSelectField>
				
				<SelectField :form="exhibit_form.location" label="Standort" optionLabel="name" />
				
				<SelectField :form="exhibit_form.place" label="Platz" optionLabel="name" />
				
				<!-- Bestandsdaten -->
				<SelectField :form="exhibit_form.preservation_state" label="Erhaltungszustand" optionLabel="name" />
				
				<InputNumberField :form="exhibit_form.current_value" label="Zeitwert" />
				
				<SelectField :form="exhibit_form.kind_of_property" label="Besitzart" optionLabel="name" />
				
				<!-- Zugangsdaten -->
				<InputTextField2 :form="exhibit_form.acquistion_date" label="Datum" />
				
				<InputTextField2 :form="exhibit_form.source" label="Herkunft" />
				
				<SelectField :form="exhibit_form.kind_of_acquistion" label="Zugangsart" optionLabel="name" />
			
				<InputNumberField :form="exhibit_form.purchasing_price" label="Kaufpreis" />
				
				<!-- Geräteinformationen -->
				<InputTextField2 :form="exhibit_form.manufacturer" label="Hersteller" />
				
				<InputTextField2 v-if="exhibit_form.device_info" :form="exhibit_form.device_info.manufactured_from_date" label="gebaut von" />
				
				<InputTextField2 v-if="exhibit_form.device_info" :form="exhibit_form.device_info.manufactured_to_date" label="gebaut bis" />
				
				<InputNumberField :form="exhibit_form.original_price_amount" label="Originalpreis" />
				
				<SelectField :form="exhibit_form.original_price_currency" label="Währung" optionLabel="id" />
				
				<!-- Buchinformationen -->
				<InputTextField2 :form="exhibit_form.manufacturer" label="Verlag" />
				
				<InputTextField2 v-if="exhibit_form.book_info" :form="exhibit_form.book_info.authors" label="Autoren" />
				
				<SelectField v-if="exhibit_form.book_info" :form="exhibit_form.book_info.language" label="Sprache" />
				
				<InputTextField2 v-if="exhibit_form.book_info" :form="exhibit_form.book_info.isbn" label="ISBN" />

				<InputNumberField :form="exhibit_form.original_price_amount" label="Originalpreis" />
				
				<SelectField :form="exhibit_form.original_price_currency" label="Währung" optionLabel="id" />
				
				<Button v-if="is_new" type='submit' label='Anlegen' />
				<Button v-else type='button' label='Stammdaten speichern' @click="exhibit_form.click_save()" />
				
			</Form>

			<div class="images-form flex flex-col items-start p-4">
				
				<!-- Button oben rechts -->
				<ExportButton class="bg-blue-500 text-white rounded" />
				
				<a v-if="exhibit_id !== undefined"
					:href="route('exhibit.images.details', { exhibit_id: exhibit_id })">
					<img v-if="props.exhibit_props?.title_image"
						class="title-image"
						:src="route('ajax.image.get_image', { image_id: props.exhibit_props?.title_image?.id })"
					>
				</a>

			</div>
		</div>
		
		<FreeTextFields v-if="props.exhibit_props"
			:init_props="props.exhibit_props.free_texts" :exhibit_id="props.exhibit_props.id"
		/>
		
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
