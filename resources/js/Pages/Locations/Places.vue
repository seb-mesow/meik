<script setup lang="ts">
import { route } from 'ziggy-js';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';
import { IPlaceInitPageProps, IPlacesInitPageProps } from '@/types/page_props/place';
import { UIPlacesForm, PlacesForm } from '@/form/special/overview/places-form';
import { UIPlaceForm } from '@/form/special/multiple/place-form';
import { permissions } from '@/util/permissions';

const props = defineProps<{
	location_name: string,
	init_props: IPlacesInitPageProps,
}>()

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
const items = [
	{
		label: 'Standorte',
		url: route('location.overview'),
	},
	{
		label: props.location_name,
		url: route('place.overview', { location_id: props.init_props.location_id }),
	},
];

const confirm_service = useConfirm();
const toast_service = useToast();

const form: UIPlacesForm = new PlacesForm({
	location_id: props.init_props.location_id,
	places: props.init_props.places.map((_props: IPlaceInitPageProps) => {
		return {
			id: _props.id,
			name: _props.name,
		};
	}),
	total_count: props.init_props.total_count,
	count_per_page: props.init_props.count_per_page,
	toast_service: toast_service,
	confirm_service: confirm_service,
});

function child_form(data: any, index: number): UIPlaceForm {
	// return data;
	return form.children.value[index];
}
</script>

<template>
	<Toast />
	<ConfirmPopup/>
	
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb class="!bg-white dark:!bg-gray-800"  :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<DataTable
			:value="form.children.value"
			paginator
			:totalRecords="form.total_count.value"
			:rows="form.count_per_page"
			:rowsPerPageOptions="[10, 20, 50]"
			@page="form.on_page($event)"
			lazy
			v-model:editingRows="form.children_in_editing.value"
			editMode="row"
			@row-edit-init="form.on_row_edit_init($event)"
			@row-edit-save="form.on_row_edit_save($event)"
			@row-edit-cancel="form.on_row_edit_cancel($event)"
		>
			
			<Column field="name" header="Name" style="width: 25%">
				<template #body="{ data, index }">
					<span>{{ child_form(data, index).name.ui_value_in_editing }}</span>
				</template>
				<template #editor="{ data, index }">
					<div>
						<p v-for="error in child_form(data, index).name.ui_errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
					</div>
					<!-- @vue-expect-error -->
					<InputText
						type=text :id="child_form(data, index).name.html_id" :name="child_form(data, index).name.html_id"
						:modelValue="child_form(data, index).name.ui_value_in_editing.value"
						@update:modelValue="(v: string) => child_form(data, index).name.on_change_ui_value_in_editing(v)"
						@blur="child_form(data, index).name.on_blur($event)"
						:invalid="child_form(data, index).name.ui_is_invalid.value"
						fluid
					/>
				</template>
			</Column>
			
			<Column v-if="permissions.place.update" :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" />
			
			<Column v-if="permissions.place.delete" style="width: 10%; min-width: 8rem">
				<template #body="{ data, index }">
					<Button
						:disabled="!child_form(data, index).delete_button_enabled"
						class="border-none" icon="pi pi-trash" outlined rounded severity="danger" raised
						@click="child_form(data, index).request_delete($event)"
					/>
				</template>
			</Column>
			
			<template #empty>Dieser Standort hat keine Plätze.</template>
			
		</DataTable>
		
		<div v-if="permissions.place.create" class="fixed bottom-4 right-4">
			<Button
				@click="form.prepend_new_form()"
				:disabled="!form.create_button_enabled.value"
				severity="primary"
				raised
				icon="pi pi-plus"
			/>
		</div>
		
	</AuthenticatedLayout>
</template>
