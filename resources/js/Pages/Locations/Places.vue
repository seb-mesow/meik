<script setup lang="ts">
import { route } from 'ziggy-js';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { reactive, Reactive, ShallowReactive, shallowReactive, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';
import { IPlaceInitPageProps, IPlacesInitPageProps } from '@/types/page_props/place';
import { IPlacesForm, PlacesForm } from '@/form/placessform';
import { IPlaceForm, UIPlaceForm } from '@/form/placeform';

const props = defineProps<{
	location_name: string,
	init_props: IPlacesInitPageProps,
}>()

const home = {
	icon: 'pi pi-home',
	url: route('exhibit.overview'),
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

const form: IPlacesForm = new PlacesForm({
	location_id: props.init_props.location_id,
	places: props.init_props.places.map((_props: IPlaceInitPageProps) => {
		return {
			id: _props.id,
			name: _props.name,
		};
	}),
	total_count: props.init_props.total_count,
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
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<div class="fixed bottom-4 right-4">
			<Button severity="info" :disabled="!form.create_button_enabled" icon="pi pi-plus" @click="form.prepend_form()" />
		</div>
		
		<Card>
			<template #content>
				<DataTable
					:value="form.children.value"
					paginator
					:totalRecords="form.total_count"
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
							<span v-if="data.id">{{ child_form(data, index).name.ui_value_in_editing }}</span>
							<span v-else class="text-green-600">Neuer Platz</span>
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
					<Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" />
					<Column style="width: 10%; min-width: 8rem">
						<template #body="{ data, index }">
							<Button
								:disabled="!child_form(data, index).delete_button_enabled"
								class="border-none" icon="pi pi-trash" outlined rounded severity="danger"
								@click="child_form(data, index).delete($event)"
							/>
						</template>
					</Column>
				</DataTable>
			</template>
		</Card>
		
	</AuthenticatedLayout>
</template>
