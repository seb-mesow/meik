<script setup lang="ts">
import { route } from 'ziggy-js';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';
import { IUsersInitPageProps } from '@/types/page_props/users';
import { UIUsersForm, UsersForm } from '@/form/special/overview/users-form';
import { UIUserForm } from '@/form/special/multiple/user-form';
import { Select } from 'primevue';

const props = defineProps<IUsersInitPageProps>()

const home = {
	icon: 'pi pi-home',
	url: route('exhibit.overview'),
};
const items = [
	{
		label: 'Benutzerverwaltung',
		url: route('user.overview'),
	},
];

const confirm_service = useConfirm();
const toast_service = useToast();

const form: UIUsersForm = new UsersForm({
	users: props.users,
	total_count: props.total_count,
	count_per_page: props.count_per_page,
	selectable_values: props.selectable_values,
	toast_service: toast_service,
	confirm_service: confirm_service,
});

function child_form(data: any, index: number): UIUserForm {
	// return data;
	return form.children.value[index];
}
</script>

<template>
	<Toast/>
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
		
		<DataTable
			:value="form.children.value"
			paginator
			:totalRecords="form.total_count.value"
			:rows="form.count_per_page"
			:rowsPerPageOptions="[8, 20, 50]"
			@page="form.on_page($event)"
			lazy
			v-model:editingRows="form.children_in_editing.value"
			editMode="row"
			@row-edit-init="form.on_row_edit_init($event)"
			@row-edit-save="form.on_row_edit_save($event)"
			@row-edit-cancel="form.on_row_edit_cancel($event)"
		>
			
			<Column field="username" header="Benutzername" style="width: 20%">
				<template #body="{ data, index }">
					<span>{{ child_form(data, index).username.ui_value_in_editing }}</span>
				</template>
			</Column>
			
			<Column field="forename" header="Vorname" style="width: 20%">
				<template #body="{ data, index }">
					<span>{{ child_form(data, index).forename.ui_value_in_editing }}</span>
				</template>
			</Column>
			
			<Column field="surname" header="Nachname" style="width: 20%">
				<template #body="{ data, index }">
					<span>{{ child_form(data, index).surname.ui_value_in_editing }}</span>
				</template>
			</Column>
			
			<Column field="role" header="Rolle" style="width: 15%">
				<template #body="{ data, index }">
					<span>{{ child_form(data, index).role.ui_value_in_editing.value?.name }}</span>
				</template>
				<template #editor="{ data, index }">
					<div>
						<p v-for="error in child_form(data, index).role.ui_errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
					</div>
					<!-- @vue-expect-error -->
					<Select
						:id="child_form(data, index).role.html_id"
						:name="child_form(data, index).role.html_id"
						:modelValue="child_form(data, index).role.ui_value_in_editing.value"
						@update:modelValue="(v: boolean) => child_form(data, index).role.on_change_ui_value_in_editing(v)"
						:options="child_form(data, index).role.options.value"
						optionLabel="name"
						@blur="child_form(data, index).role.on_blur($event)"
						:invalid="child_form(data, index).role.ui_is_invalid.value"
						placeholder="auswählen"
					/>
				</template>
			</Column>
			
			<Column :rowEditor="true" style="width: 15%; min-width: 8rem" bodyStyle="text-align:center" />
			
			<Column style="width: 10%; min-width: 8rem">
				<template #body="{ data, index }">
					<Button
						:disabled="!child_form(data, index).delete_button_enabled"
						class="border-none" icon="pi pi-trash" outlined rounded severity="danger"
						@click="child_form(data, index).request_delete($event)"
					/>
				</template>
			</Column>
			
			<template #empty>Keine Benutzer vorhanden</template>
			
		</DataTable>
		
	</AuthenticatedLayout>
</template>
