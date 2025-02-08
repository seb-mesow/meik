<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from 'primevue/button';
import FreeTextFields from '@/Components/Exhibit/FreeTextFields.vue';
import Breadcrumb from 'primevue/breadcrumb';
import { IExhibitProps } from '@/types/page_props/exhibit';
import Form from '@/Components/Form/Form.vue';
import ExportButton from '@/Components/Control/ExportButton.vue';
import { ExhibitForm, IExhibitForm, IExhibitFormFormConstructorArgs } from '@/form/exhibitform';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import { ISelectForm, SelectForm } from '@/form/selectform';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	all_locations: string[],
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
let form_constructor_args: IExhibitFormFormConstructorArgs|undefined = undefined;
if (props.exhibit_props) {
	form_constructor_args = {
		id: props.exhibit_props.id,
		inventory_number: props.exhibit_props.inventory_number,
		name: props.exhibit_props.name,
		short_description: props.exhibit_props.short_description,
		rubric: props.rubric?.name ?? '',
		place: props.exhibit_props.place,
		manufacturer: props.exhibit_props.manufacturer ?? '',
		toast_service: toast_service,
	};
}
const exhibit_form: IExhibitForm = new ExhibitForm(form_constructor_args);
const exhibit_id = exhibit_form.id;
const is_new = exhibit_id === undefined;

const location_form: ISelectForm = new SelectForm({
	val: '',
	async get_shown_suggestions(input: string): Promise<string[]> {
		console.log(`get_shown_suggestions('${input}')`);
		return props.all_locations.filter((location: string): boolean => location.includes(input));
	},
}, 'location');
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
				<InputTextField2 :form="exhibit_form.name" label="Bezeichnung" />
				<InputTextField2 :form="exhibit_form.inventory_number" label="Inventarnummer" />
				<InputTextField2 :form="exhibit_form.short_description" label="Kurzbeschreibung" />
				<SelectField :form="location_form" label="Standort" />
				<InputTextField2 :form="exhibit_form.manufacturer" label="Hersteller" />
				
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
