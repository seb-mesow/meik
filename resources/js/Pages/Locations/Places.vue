<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { reactive, Reactive, ref } from 'vue';
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
import { IPlaceFormConstructorArgs, IPlacesForm, PlacesForm } from '@/form/placessform';

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

const form: Reactive<IPlacesForm> = reactive(new PlacesForm({
	location_id: props.init_props.location_id,
	places: props.init_props.places.map((_props: IPlaceInitPageProps): IPlaceFormConstructorArgs => {
		return {
			id: _props.id,
			name: { 
				val: _props.name,
				errs: [],
			},
		};
	}),
	total_count: props.init_props.total_count,
	toast_service: toast_service,
	confirm_service: confirm_service,
}));
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
					:value="form.children"
					paginator
					:totalRecords="form.total_count"
					:rows="form.count_per_page"
					:rowsPerPageOptions="[10, 20, 50]"
					@page="form.on_page($event)"
					lazy
					v-model:editingRows="form.children_in_editing"
					editMode="row"
					@row-edit-init="form.on_row_edit_init($event)"
					@row-edit-save="form.on_row_edit_save($event)"
					@row-edit-cancel="form.on_row_edit_cancel($event)"
				>
					<Column field="name" header="Name" style="width: 25%">
						<template #body="{ data }">
							<span v-if="data.id"
								class="font-medium"
							>
								{{ data.name.val }}
							</span>
							<span v-else class="text-green-600">Neuer Platz</span>
						</template>
						<template #editor="{ data }">
							<!-- {{ data }} -->
							<InputText v-model="data.name.val_in_editing" autofocus fluid />
						</template>
					</Column>
					<Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" />
					<Column style="width: 10%; min-width: 8rem">
						<template #body="{ data }">
							<Button
								:disabled="!data.delete_button_enabled"
								class="border-none" icon="pi pi-trash" outlined rounded severity="danger"
								@click="data.delete($event)"
							/>
						</template>
					</Column>
				</DataTable>
			</template>
		</Card>
		
	</AuthenticatedLayout>
</template>
