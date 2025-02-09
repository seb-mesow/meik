<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from 'primevue/button';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitProps, ISelectableValuesProps } from '@/types/page_props/exhibit';
import Form from '@/Components/Form/Form.vue';
import ExportButton from '@/Components/Control/ExportButton.vue';
import {
	ExhibitForm,
	IExhibitForm,
	IExhibitFormConstructorArgs,
	IExhibitType,
	ISelectableValues,
} from '@/form/exhibitform';
import SelectField from '@/Components/Form/SelectField.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import GroupSelectField from '@/Components/Form/GroupSelectField.vue';
import InputNumberField from '@/Components/Form/InputNumberField.vue';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import Fieldset from 'primevue/fieldset';
import SelectButton from 'primevue/selectbutton';

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	selectable_values: ISelectableValuesProps,
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
		url: route('category.details', { category_id: props.category.id })
	});
}
if (props.rubric) {
	items.push({
		label: props.rubric.name,
		url: route('rubric.details', { rubric_id: props.rubric.id }),
	});
}
items.push({
	label: props.exhibit_props?.name ?? 'Neues Exponat',
});

// console.log("Exhibit.vue: props.init_props ==");
// console.log(props.init_props);

const toast_service = useToast();

const exhibit_types: IExhibitType[] = [
	{ id: 'device', name: 'Gerät' },
	{ id: 'book', name: 'Buch' },
];

// (interne) Attribute der Seite
const form_constructor_args: IExhibitFormConstructorArgs = {
	aux: {
		toast_service: toast_service,
		selectable_values: { ...props.selectable_values, ...{ exhibit_type: exhibit_types } },
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

		<Form :action="route('exhibit.create')" method="post">
			<div class="flex gap-x-3">
				<div class="basis-2/3 grid grid-cols-3 gap-x-3">
					<!-- Kerndaten -->
					<InputTextField2 :form="exhibit_form.name" label="Bezeichnung" class="col-span-2"/>
						
					<InputTextField2 :form="exhibit_form.inventory_number" label="Inventarnummer" class="col-span-1"/>
					
					<InputTextField2 :form="exhibit_form.short_description" label="Kurzbeschreibung" class="col-span-full"/>
					
					<GroupSelectField :form="exhibit_form.rubric" label="Rubrik" class="col-span-1">
						<template #optiongroup="category">
							<span class="font-bold">{{ category.parent }}</span>
						</template>
						<template #option="rubric">
							<span class="ps-4">{{ rubric }}</span>
						</template>
					</GroupSelectField>
					
					<SelectField :form="exhibit_form.location" label="Standort" optionLabel="name" class="col-span-1"/>
					
					<SelectField :form="exhibit_form.place" label="Platz" optionLabel="name" class="col-span-1"/>
				</div>
				
				<div class="basis-1/3">
					<!-- Button oben rechts -->
					<ExportButton/>
					
					<a v-if="exhibit_id !== undefined"
						:href="route('exhibit.images.details', { exhibit_id: exhibit_id })">
						<img v-if="props.exhibit_props?.title_image"
							class="m-auto max-h-[15rem]"
							:src="route('ajax.image.get_image', { image_id: props.exhibit_props?.title_image?.id })"
						>
					</a>
				</div>
			</div>
			
			<div class="flex flex-wrap gap-x-3 items-start">
				<!-- Bestandsdaten -->
				<Fieldset legend="Bestandsdaten" toggleable collapsed class="basis-[30rem] flex-1">
					<div class="grid grid-cols-2 gap-x-3">
						<SelectField :form="exhibit_form.preservation_state" label="Erhaltungszustand" optionLabel="name" />
						
						<SelectField :form="exhibit_form.kind_of_property" label="Besitzart" optionLabel="name" class="col-start-1" />
						
						<InputNumberField :form="exhibit_form.current_value" label="Zeitwert" />
					</div>
				</Fieldset>
				
				<!-- Zugangsdaten -->
				<Fieldset legend="Zugangsdaten" toggleable collapsed class="basis-[30rem] flex-1">
					<div class="grid grid-cols-2 gap-x-3">
						<InputTextField2 :form="exhibit_form.acquistion_date" label="Datum" />
						
						<InputTextField2 :form="exhibit_form.source" label="Herkunft" class="col-span-full"/>
						
						<SelectField :form="exhibit_form.kind_of_acquistion" optionLabel="name"  label="Zugangsart" />
						
						<InputNumberField :form="exhibit_form.purchasing_price" label="Kaufpreis" />
					</div>
				</Fieldset>
			</div>
			
			<SelectButton
				:modelValue="exhibit_form.type.val_in_editing"
				@update:modelValue="(v: IExhibitType) => exhibit_form.type.on_change_val_in_editing(v)"
				:options="exhibit_types"
				optionLabel="name"
			/>
			
			<!-- Geräteinformationen -->
			<Fieldset v-show="exhibit_form.show_device_info.value" legend="Geräteinformationen">
				<div class="grid grid-cols-3 gap-x-3">
					<InputTextField2 :form="exhibit_form.manufacturer" label="Hersteller" class="col-span-full" />
					
					<InputTextField2 :form="exhibit_form.device_info.manufactured_from_date" label="gebaut von" class="col-span-1" />
					
					<InputTextField2 :form="exhibit_form.device_info.manufactured_to_date" label="gebaut bis" class="col-span-1" />
					
					<div class="col-span-1 col-start-1 flex gap-x-3">
						<InputNumberField :form="exhibit_form.original_price_amount" label="Originalpreis" class="grow"/>
						
						<SelectField :form="exhibit_form.original_price_currency" optionLabel="id" label="Währung" class="flex-none w-[6rem]" />
					</div>
				</div>
			</Fieldset>
			
			<!-- Buchinformationen -->
			<Fieldset v-show="exhibit_form.show_book_info.value" legend="Buchinformationen">
				<div class="grid grid-cols-3 gap-x-3">
					<InputTextField2 :form="exhibit_form.manufacturer" label="Verlag" class="col-span-full" />
					
					<InputTextField2 :form="exhibit_form.book_info.authors" label="Autoren" class="col-span-full" />
					
					<SelectField :form="exhibit_form.book_info.language" optionLabel="name" label="Sprache" />
					
					<InputTextField2 :form="exhibit_form.book_info.isbn" label="ISBN" />
					
					<div class="flex gap-x-3">
						<InputNumberField :form="exhibit_form.original_price_amount" label="Originalpreis" class="grow!" />
						
						<SelectField :form="exhibit_form.original_price_currency" optionLabel="id" label="Währung" class="flex-none w-[6rem]" />
					</div>
				</div>
			</Fieldset>
			
			<Button v-if="is_new" type='submit' label='Anlegen' />
			<Button v-else type='button' label='Stammdaten speichern' @click="exhibit_form.click_save()" />
		</Form>
		
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

.basicdata-form {
	flex: 2;
}

.images-form {
	flex: 1;
}
</style>
