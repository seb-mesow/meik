<script setup lang="ts">
import DataTable, {
	DataTablePageEvent,
	DataTableRowEditCancelEvent,
	DataTableRowEditInitEvent,
	DataTableRowEditSaveEvent
} from 'primevue/datatable';
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
import { ILocationInitPageProps, ILocationsInitPageProps } from '@/types/page_props/location';

interface ILocationForm {
	id?: string;
	name: string;
	is_public: boolean;
	delete_button_enabled: boolean; // funktioniert auch ohne explizite Reactivity
}

const props = defineProps<{
	init_props: ILocationsInitPageProps
}>()

let page_number = 0;
let count_per_page = props.init_props.locations.length;
const rows: Ref<ILocationForm[]> = ref(new_forms_from_props(props.init_props.locations));
const total_count: Ref<number> = ref(props.init_props.total_count);
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

const editing_rows: Ref<ILocationForm[]> = ref([]);
// watch(editing_rows, (new_editing_rows) => {
// 	console.log(`editing_rows.length == ${new_editing_rows.length}`);
// });

function new_forms_from_props(locations_init_props: ILocationInitPageProps[]): ILocationForm[] {
	return locations_init_props.map((location: ILocationInitPageProps): ILocationForm => {
		return {
			id: location.id,
			name: location.name,
			is_public: location.is_public,
			delete_button_enabled: true,
		}
	});
}

function prepend_form() {
	create_button_enabled.value = false;
	const newRow: ILocationForm = {
		name: '',
		is_public: false,
		delete_button_enabled: false,
	};
	editing_rows.value.unshift(newRow);
	rows.value.unshift(newRow);
}

async function on_row_edit_init(event: DataTableRowEditInitEvent) {
	console.log('on_row_edit_init');
	create_button_enabled.value = false;
	event.data.delete_button_enabled = false;
	// Die zu bearbeitende Zeile wird automatisch zu editing_rows hinzugefügt.
}

async function on_row_edit_save(event: DataTableRowEditSaveEvent) {
	console.log('on_row_edit_save');
	let { data, newData } = event;
	// data ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
	// newData ist wahrscheinlich das Objekt in der editingRows-Property oder ein Proxy darauf
	// und beim Updaten eine Kopie des Objektes in der rows-Property.
	
	console.log("event ==");
	console.log(event);
	
	// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
	
	if (!newData.name) {
		toast.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
		
		editing_rows.value.unshift(data);
		// Es muss das IDENTISCHE Zeilen-Objekt in editing_rows erhalten blieben
		// (newData ist ein Kopie und damit zwar gleich aber nicht identisch.)
		return;
	}

	if (newData.id) {
		await ajax_update({
			id: newData.id,
			name: newData.name,
			is_public: newData.is_public
		});
	} else {
		newData.id = await ajax_create({
			name: newData.name,
			is_public: newData.is_public
		});
	}
	// Werte der Zeile im Objekt rows setzen:
	data.id = newData.id;
	data.name = newData.name;
	data.is_public = newData.is_public;
	data.delete_button_enabled = true;
	// Die Rows sollen bewusst nicht geupdated werden:
	// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
	create_button_enabled.value = true;
	console.log("Ende");
};

async function on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
	console.log('on_row_edit_cancel');
	let { data, newData } = event;
	if (newData.id) {
		data.delete_button_enabled = true;
	} else { // war create
		rows.value.shift();
	}
	create_button_enabled.value = true;
}

function delete_confirm(event: any, location: ILocationForm) {
	confirm.require({
		target: event.currentTarget,
		message: "Sind Sie sicher das Sie den Standort löschen wollen? Untergeordnete Plätze werden auch gelöscht.",
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
			return accept_delete(location);
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

async function accept_delete(location: ILocationForm): Promise<void> {
	if (!location.id) {
		throw new Error('accept_delete(): Missing id of location');
	}
	return ajax_delete(location.id).then(
		() => {
			rows.value = rows.value.filter((rows_location: ILocationForm): boolean => rows_location !== location);
			toast.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich gelöscht.', life: 3000 });
		},
		() => {
			toast.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht gelöscht werden.', life: 3000 });
		}
	);
}

async function ajax_delete(location_id: string): Promise<void> {
	const request_config: AxiosRequestConfig<IDeleteLocationRequestData> = {
		method: "delete",
		url: route('ajax.location.delete', { location_id: location_id })
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
	const responses = await ajax_get_paginated({
		page_number: page_number,
		count_per_page: count_per_page
	});
	rows.value = new_forms_from_props(responses.locations);
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
</script>

<template>
	<Toast/>
	<ConfirmPopup/>
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
		
		<div class="fixed bottom-4 right-4">
			<Button severity="info" :disabled="!create_button_enabled" icon="pi pi-plus" @click="prepend_form" />
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
					v-model:editingRows="editing_rows"
					editMode="row"
					@row-edit-init="on_row_edit_init"
					@row-edit-save="on_row_edit_save"
					@row-edit-cancel="on_row_edit_cancel"
				>
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
								:disabled="!data.delete_button_enabled"
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
