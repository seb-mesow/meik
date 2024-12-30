<script setup lang="ts">
import DataTable, { DataTablePageEvent, DataTableRowEditSaveEvent } from 'primevue/datatable';
import Column from 'primevue/column';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { Ref, ref, watch } from 'vue';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';
import {
	ICreateLocation200ResponseData,
	ICreateLocationRequestData, 
	IDeleteLocationRequestData, 
	IGetLocationsPaginated200ResponseData,
	IGetLocationsPaginatedQueryParams, 
	IUpdateLocationRequestData
} from '@/types/ajax/location';

interface Location {
	id?: string;
	name?: string;
	is_public?: boolean;
	invalid?: boolean;
}

const props = defineProps<{
	locations: Location[],
	total_count: number,
}>()

let page_number = 0;
let count_per_page = props.locations.length;
const rows: Ref<Location[]> = ref(props.locations);
const total_count: Ref<number> = ref(props.total_count);
// console.log(`rows.length == ${rows.value.length}`);
// console.log(`total_count == ${total_count.value}`);
// watch(rows, (new_rows) => {
// 	console.log(`rows.length == ${new_rows.length}`);
// });
// watch(total_count, (new_total_count) => {
// 	console.log(`total_count == ${new_total_count}`);
// });
const create_button_enabled: Ref<boolean> = ref(true);
const confirm = useConfirm();
const toast = useToast();

const home = ref({
	icon: 'pi pi-home',
	route: 'exhibit.overview'
});
const items = ref([
	{
		label: 'Standorte',
		route: 'location.overview'
	},
]);

const columns = ref([
	{ field: 'name', header: 'Name' },
	{ field: 'is_public', header: 'Öffentlich' },
]);

const editingRows: Ref<Location[]> = ref([]);

async function on_row_edit_save(event: DataTableRowEditSaveEvent) {
	let { data, newData } = event;

	if (!newData.name) {
		editingRows.value.push(data);
		toast.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
		return;
	}

	if (newData.id) {
		await ajax_update({ id: newData.id, name: newData.name, is_public: newData.is_public });
	} else {
		data.id = await ajax_create({ name: newData.name, is_public: newData.is_public });
		data.name = newData.name;
		data.is_public = newData.is_public;
	}
	// Die Rows sollen bewusst nicht geupdated werden:
	// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
	create_button_enabled.value = true;
};

function delete_confirm(event: any, location: any) {
	confirm.require({
		target: event.currentTarget,
		message: 'Sind Sie sicher das Sie den Standort löschen wollen? Untergeordnete Plätze werden auch gelöscht.',
		icon: 'pi pi-exclamation-triangle',
		rejectProps: {
			label: 'Abbrechen',
			severity: 'secondary',
			outlined: true
		},
		acceptProps: {
			label: 'Bestätigen'
		},
		accept: () => {
			return accept_delete(location.id);
		},
		reject: () => {
		}
	});
};

async function ajax_update(params: { id: string, name: string, is_public: boolean }): Promise<void> {
	const request_config: AxiosRequestConfig<IUpdateLocationRequestData> = {
		method: "put",
		url: route('ajax.location.update', { location_id: params.id }),
		data: {
			val: {
				name: {
					val: params.name
				},
				is_public: {
					val: params.is_public
				}
			}
		}
	};
	return axios.request(request_config);
}

async function accept_delete(id: string): Promise<void> {
	total_count.value -= 1;

	ajax_delete(id).then(
		() => {
			toast.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich gelöscht.', life: 3000 });
		},
		() => {
			toast.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht gelöscht werden.', life: 3000 });
		}
	);
}

async function ajax_delete(id: string): Promise<void> {
	const request_config: AxiosRequestConfig<IDeleteLocationRequestData> = {
		method: "delete",
		url: route('ajax.localtion.delete', { location_id: id })
	};
	return axios.request(request_config);
}

async function ajax_create(params: { name: string, is_public: boolean }): Promise<string> {
	const request_config: AxiosRequestConfig<ICreateLocationRequestData> = {
		method: "post",
		url: route('ajax.location.create'),
		data: {
			val: {
				name: {
					val: params.name
				},
				is_public: {
					val: params.is_public
				}
			}
		}
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<ICreateLocation200ResponseData>) => {
			return response.data;
		}
	);
}

async function on_page(event: DataTablePageEvent): Promise<void> {
	page_number = event.page;
	count_per_page = event.rows;
	return update_rows();
}

async function update_rows(): Promise<void> {
	const responses = await ajax_get_paginated({ page_number: page_number, count_per_page: count_per_page });
	rows.value = responses.locations;
	total_count.value = responses.total_count;
}

async function ajax_get_paginated(params: IGetLocationsPaginatedQueryParams): Promise<IGetLocationsPaginated200ResponseData> {
	console.log(`ajax.location.get_paginated ${params.page_number} ${params.count_per_page}`);
	const request_config: AxiosRequestConfig<IGetLocationsPaginatedQueryParams> = {
		method: "get",
		url: route('ajax.location.get_paginated'),
		params: params // bei GET nicht data !
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<IGetLocationsPaginated200ResponseData>) => {
			return response.data;
		}
	);
}

function append_form() {
	create_button_enabled.value = false;
	const newRow: Location = { name: '', is_public: false };
	rows.value.unshift(newRow);
	editingRows.value.unshift(newRow);
	total_count.value += 1;
}
</script>

<template>
	<Toast />
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="route(item.route)">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<ConfirmPopup></ConfirmPopup>
		
		<div class="fixed bottom-4 right-4">
			<Button severity="info" :disabled="!create_button_enabled" icon="pi pi-plus" @click="append_form" />
		</div>
		
		<Card>
			<template #content>
				<DataTable
					:value="rows" 
					paginator
					:totalRecords="total_count"
					:rows="count_per_page"
					:rowsPerPageOptions="[10, 20, 50]"
					@page="on_page"
					lazy
				>
					<!-- lazy
					@data="on_page"
					editMode="row"
					v-model:editingRows="editingRows" 
					@row-edit-save="on_row_edit_save($event)" -->
					<!-- TODO @data eventuell raus -->
					<Column field="name" header="Name" style="width: 25%">
						<template #body="{ data, field }">
							<a v-if="data.id"
								class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
								:href="route('place.overview', { 'location': data._id })"
							>
								{{ data[field] }}
							</a>
							<span v-else class="text-green-600">Neuer Standort</span>
						</template>
						<template #editor="{ data, field }">
							<!-- {{ data }} -->
							<InputText v-model="data[field]" autofocus fluid />
						</template>
					</Column>
					<Column field="is_public" header="öffentlich" style="width: 25%">
						<template #body="{ data, field }">
							<template v-if="data[field] === true">
								<i class="pi pi-check"></i>
							</template>
						</template>
						<template #editor="{ data, field }">
							<Checkbox v-model="data[field]" binary />
						</template>
					</Column>
					<Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" />
					<Column style="width: 10%; min-width: 8rem">
						<template #body="{ data }">
							<Button 
								class="border-none" icon="pi pi-trash" outlined rounded severity="danger"
								@click="delete_confirm($event, data)"
							/>
						</template>
					</Column>
				</DataTable>
			</template>
		</Card>
		
	</AuthenticatedLayout>
</template>
