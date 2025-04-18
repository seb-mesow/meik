<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from 'primevue/button';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitProps, ISelectableValuesProps } from '@/types/page_props/exhibit';
import ExportButton from '@/Components/Control/ExportButton.vue';
import {
	ExhibitForm,
	IExhibitForm,
	IExhibitFormConstructorArgs,
	IExhibitType,
} from '@/form/special/multiple/exhibit-form';
import SelectField from '@/Components/Form/SelectField.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import GroupSelectField from '@/Components/Form/GroupSelectField.vue';
import InputNumberField from '@/Components/Form/InputNumberField.vue';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import Fieldset from 'primevue/fieldset';
import SelectButton from 'primevue/selectbutton';
import DateField from '@/Components/Form/DateField.vue';
import OriginalPriceField from '@/Components/Form/OriginalPriceField.vue';
import { ref } from 'vue';
import MultipleSelectField from '@/Components/Form/MultipleSelectField.vue';

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

const suggested_exhibits = ref([]);
// const connected_exhibits = ref(props.connected_exhibits);

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
if (props.category === undefined && props.rubric === undefined) {
	items.push({
		label: 'Exponate',
		url: route('exhibit.overview'),
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
	// let device_info = undefined;
	// if (props.exhibit_props.device_info) {
	// 	device_info = {
	// 		manufactured_from_date: PartialDate.parse_iso(props.exhibit_props.device_info.manufactured_from_date),
	// 		manufactured_to_date: PartialDate.parse_iso(props.exhibit_props.device_info.manufactured_to_date),
	// 	};
	// }
	// const acquisition_info = {
	// 	...props.exhibit_props.acquisition_info,
	// 	...{ date: DateUtil.parse_iso_date(props.exhibit_props.acquisition_info.date) }
	// };

	form_constructor_args.data = {
		id: props.exhibit_props.id,

		// Kerndaten
		inventory_number: props.exhibit_props.inventory_number,
		name: props.exhibit_props.name,
		short_description: props.exhibit_props.short_description,
		// @ts-expect-error Wenn die exhibit_props gegeben sind (= Aufruf bestehendes Exhibit), sind immer auch die Properties category und rubric gegeben.
		rubric: props.rubric,
		location_id: props.exhibit_props.location_id,
		place_id: props.exhibit_props.place_id,
		connected_exhibit_ids: props.exhibit_props.connected_exhibit_ids, // TODO connected_exhibits

		// Bestandsdaten
		preservation_state_id: props.exhibit_props.preservation_state_id,
		current_value: props.exhibit_props.current_value,
		kind_of_property_id: props.exhibit_props.kind_of_property_id,

		// Zugangsdaten
		acquisition_info: props.exhibit_props.acquisition_info,

		// Geräte- und Buchinformationen
		manufacturer: props.exhibit_props.manufacturer,
		manufacture_date: props.exhibit_props.manufacture_date,
		original_price: props.exhibit_props.original_price,

		// Geräteinformationen
		device_info: props.exhibit_props.device_info,

		// Buchinformationen
		book_info: props.exhibit_props.book_info,
	}
} else {
	form_constructor_args.preset = {
		rubric: props.rubric,
	};
}
const exhibit_form: IExhibitForm = new ExhibitForm(form_constructor_args);
const does_exist: boolean = exhibit_form.id !== undefined;
const partial_date_tooltip = 'gültige Formate sind\nTT.MM.JJJJ\nTT. MONAT JJJJ\nMONAT JJJJ\nJJJJ';

</script>

<template>
	<Toast />
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb class="!bg-gray-100 dark:!bg-gray-800" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>

		<!-- Hier kommt keine äußere Card hin und kein Overflow! -->
			<div class="gap-x-3 pb-4" :class="{ 'flex': does_exist, 'items-center': does_exist }">
				
				<!-- Kerndaten -->
				<div class="h-min bg-gray-100 dark:bg-gray-900 border-[2px] border-gray-300 dark:border-gray-800 p-4 rounded-md"
					:class="{ 'basis-2/3': does_exist }"
				>
					<div class="grid grid-cols-3 gap-x-3">
						<InputTextField2 :form="exhibit_form.name" label="Bezeichnung" :grid_col="1" :grid_col_span="2"
							:grid_row="1" />

						<InputTextField2 :form="exhibit_form.inventory_number" label="Inventarnummer" :grid_col="3"
							:grid_row="1" />

						<InputTextField2 :form="exhibit_form.short_description" label="Kurzbeschreibung" :grid_col="1"
							:grid_col_span="3" :grid_row="2" class_label="mt-2" />

						<GroupSelectField :form="exhibit_form.rubric" label="Rubrik" :grid_col="1" :grid_row="3" class_label="mt-2">
							<template #optiongroup="category">
								<span class="font-bold">{{ category.name }}</span>
							</template>
							<template #option="rubric">
								<span class="ps-4">{{ rubric.name }}</span>
							</template>
						</GroupSelectField>

						<SelectField :form="exhibit_form.location" label="Standort" :grid_col="2" :grid_row="3" class_label="mt-2"/>

						<SelectField :form="exhibit_form.place" label="Platz" :grid_col="3" :grid_row="3" class_label="mt-2"/>
					</div>
				</div>
				
				<!-- Export-Button und Titelbild -->
				<div v-if="exhibit_form.id !== undefined"
					class="basis-1/3 flex flex-col gap-y-3 justify-evenly items-center">
					<ExportButton :exhibit_id="exhibit_form.id" />

					<a :href="route('exhibit.images.details', { exhibit_id: exhibit_form.id })">
						<img v-if="props.exhibit_props?.title_image"
							class="max-h-[15rem] object-contain"
							:src="route('ajax.image.get_image', { image_id: props.exhibit_props?.title_image?.id })"
							:height="props.exhibit_props?.title_image?.image_height"
							:width="props.exhibit_props?.title_image?.image_width"
						/>
						<div v-else class="">
							<Button severity="primary" raised label="Bilder hinzufügen"/>
						</div>
					</a>
				</div>
				
			</div>
			
			<div class="flex flex-wrap gap-3 pb-3 items-start">
				<!-- Bestandsdaten -->
				<Fieldset legend="Bestandsdaten *" toggleable :collapsed="does_exist" class="basis-[30rem] flex-1 !bg-gray-100 dark:!bg-gray-900 !border-gray-300 dark:!border-gray-800 !border-2" >
					<div class="grid grid-cols-2 gap-x-3">
						<SelectField :form="exhibit_form.preservation_state" label="Erhaltungszustand" :grid_col="1"
							:grid_row="1"/>

						<SelectField :form="exhibit_form.kind_of_property" label="Besitzart" :grid_col="1"
							:grid_row="2" class_label="mt-2"/>

						<InputNumberField :form="exhibit_form.current_value" price label="Zeitwert" currency="EUR"
							:grid_col="2" :grid_row="2" class_label="mt-2"/>
					</div>
				</Fieldset>

				<!-- Zulaufsdaten -->
				<Fieldset legend="Zulaufsdaten *" toggleable :collapsed="does_exist" class="basis-[30rem] flex-1 !bg-gray-100 dark:!bg-gray-900 !border-gray-300 dark:!border-gray-800 !border-2">
					<div class="grid grid-cols-2 gap-x-3">
						<DateField :form="exhibit_form.acquisition_info.date" label="Datum" :grid_col="1"
							:grid_row="1"/>

						<InputTextField2 :form="exhibit_form.acquisition_info.source" label="Herkunft" :grid_col="1"
							:grid_col_span="2" :grid_row="2" class_label="mt-2"/>

						<SelectField :form="exhibit_form.acquisition_info.kind" label="Zulaufsart" :grid_col="1"
							:grid_row="3" class_label="mt-2"/>

						<InputNumberField :form="exhibit_form.acquisition_info.purchasing_price" price label="Kaufpreis"
							currency="EUR" :grid_col="2" :grid_row="3" class_label="mt-2"/>
					</div>
				</Fieldset>
			</div>


			<Fieldset class="exhibit-type-fieldset gap-3 !bg-gray-100 dark:!bg-gray-900 !border-gray-300 dark:!border-gray-800 !border-2" legend="Geräteinformationen">
				<template #legend>
					<SelectButton :modelValue="exhibit_form.type.ui_value_in_editing"
						@update:modelValue="(v: IExhibitType) => exhibit_form.type.on_change_ui_value_in_editing(v)"
						:options="exhibit_types" optionLabel="name" :allowEmpty="false" severity="primary" raised
						class="exhibit-type-selectbutton"
					>
						<template #option="{ option }">
							<span class="w-[2.5rem]">{{ option.name }}</span>
						</template>
					</SelectButton>
				</template>

				<!-- Geräteinformationen -->
				<div v-show="exhibit_form.show_device_info.value" class="grid grid-cols-3 gap-x-3">
					<InputTextField2 :form="exhibit_form.manufacturer" label="Hersteller" :grid_col="1"
						:grid_col_span="3" :grid_row="1"/>

					<InputTextField2 :form="exhibit_form.manufacture_date" :tooltip="partial_date_tooltip"
						label="Baujahr" :grid_col="1" :grid_row="2" class_label="mt-2"/>

					<InputTextField2 :form="exhibit_form.device_info.manufactured_from_date"
						:tooltip="partial_date_tooltip" label="gebaut von" :grid_col="2" :grid_row="2" class_label="mt-2"/>

					<InputTextField2 :form="exhibit_form.device_info.manufactured_to_date"
						:tooltip="partial_date_tooltip" label="gebaut bis" :grid_col="3" :grid_row="2" class_label="mt-2"/>

					<OriginalPriceField :form_amount="exhibit_form.original_price.amount"
						:form_currency="exhibit_form.original_price.currency" :grid_col="1" :grid_row="3" class_label="mt-2"/>

					<MultipleSelectField :form="exhibit_form.connected_exhibits" label="Verknüpfte Exponate"
						:grid_col="1" :grid_col_span="3" :grid_row="4" class_label="mt-2"/>
				</div>

				<!-- Buchinformationen -->
				<div v-show="exhibit_form.show_book_info.value" class="grid grid-cols-3 gap-x-3">
					<InputTextField2 :form="exhibit_form.manufacturer" label="Verlag" :grid_col="1" :grid_col_span="3"
						:grid_row="1" />

					<InputTextField2 :form="exhibit_form.manufacture_date" :tooltip="partial_date_tooltip"
						label="Erscheinungsjahr" :grid_col="1" :grid_row="2" class_label="mt-2"/>

					<InputTextField2 :form="exhibit_form.book_info.authors" label="Autoren" :grid_col="2"
						:grid_col_span="2" :grid_row="2" class_label="mt-2"/>

					<OriginalPriceField :form_amount="exhibit_form.original_price.amount"
						:form_currency="exhibit_form.original_price.currency" :grid_col="1" :grid_row="3" class_label="mt-2"/>

					<SelectField :form="exhibit_form.book_info.language" label="Sprache" :grid_col="2" :grid_row="3" class_label="mt-2"/>

					<InputTextField2 :form="exhibit_form.book_info.isbn" label="ISBN" :grid_col="3" :grid_row="3" class_label="mt-2"/>

					<MultipleSelectField :form="exhibit_form.connected_exhibits" label="Verknüpfte Exponate"
						:grid_col="1" :grid_col_span="3" :grid_row="4" class_label="mt-2" />
				</div>
			</Fieldset>
			
			<div class="text-right my-4" :class="{
				'w-[14.1rem]': does_exist,
				'w-[7.3rem]': !does_exist,
			}">
				<Button
					:disabled="!exhibit_form.is_save_button_enabled.value || exhibit_form.is_save_button_loading.value"
					:loading="exhibit_form.is_save_button_loading.value"
					:label="does_exist ? 'Stammdaten speichern' : 'Anlegen'"
					@click="exhibit_form.click_save()"
					severity="primary"
					raised
				/>
			</div>
			
			<hr class="mt-6 mb-6">
			
			<FreeTextFields v-if="props.exhibit_props"
				:init_props="props.exhibit_props.free_texts"
				:exhibit_id="props.exhibit_props.id"
			/>
		
	</AuthenticatedLayout>
	
	<!-- <div class="bg-gray-50 hidden"></div> -->
</template>

<style lang="css">
.exhibit-type-fieldset .p-fieldset-legend {
	padding: 0;
	border-width: 0;
}
.exhibit-type-selectbutton {
	& .p-togglebutton {
		color: oklch(0.439 0 0);
		background-color: var(--color-gray-200); /* !bg-gray-100 dark:!bg-gray-900 */
		.p-dark & {
			color: var(--color-gray-300);
			background-color: var(--color-gray-700); /* !bg-gray-100 dark:!bg-gray-900 */
		}
		&.p-togglebutton-checked {
			font-weight: bold;
			color: black;
			background: rgba(0, 0, 0, 0); /* !bg-gray-100 dark:!bg-gray-900 */
			.p-dark & {
				color: white;
			}
		}
	}
}
</style>
