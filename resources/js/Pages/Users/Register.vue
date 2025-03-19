<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Toast from 'primevue/toast';
import Breadcrumb from 'primevue/breadcrumb';
import Button from 'primevue/button';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import { ISelectableValuesProps } from '@/types/page_props/users';
import { route } from 'ziggy-js';
import { NewUserForm, UINewUserForm } from '@/form/special/multiple/user-new-form';
import { useToast } from 'primevue';
import SimpleSelectField from '@/Components/Form/SimpleSelectField.vue';

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	selectable_values: ISelectableValuesProps,
}>();

const home = {
	icon: 'pi pi-home',
	url: route('exhibit.overview'),
};
const items = [
	{
		label: 'Benutzerverwaltung',
		url: route('user.overview'),
	},
	{
		label: 'Neuer Benutzer',
	}
];

const toast_service = useToast();

const form: UINewUserForm = new NewUserForm({
	selectable_values: {
		role: props.selectable_values.role,
	},
	aux: {
		toast_service: toast_service,
	}
});
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
		
		<div class="mx-auto w-100">
			
			<div class="grid grid-cols-1 gap-x-3">
				<InputTextField2 :form="form.forename" label="Vorname" :grid_col="1" :grid_row="1"/>
			
				<InputTextField2 :form="form.surname" label="Nachname" :grid_col="1" :grid_row="2"/>
				
				<SimpleSelectField :form="form.role" label="Rolle" :grid_col="1" :grid_row="3"/>
				
				<InputTextField2 :form="form.username" label="Benutzername" :grid_col="1" :grid_row="4"/>
				
				<InputTextField2 :form="form.password" label="Passwort" type="password" :grid_col="1" :grid_row="5"/>
				
				<InputTextField2 :form="form.password_again" label="Passwort wiederholen" type="password" :grid_col="1" :grid_row="6"/>
			</div>
		
			<div class="mt-4 flex items-center justify-end">
				<!-- <Link
					:href="route('login')"
					class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
				>
					Already registered?
				</Link> -->
				
				<!-- :loading="form.is_save_button_loading.value" -->
				
				<Button
					:disabled="!form.is_save_button_enabled.value"
					@click="form.click_save()"
					label="Anlegen"
					severity="primary"
					class="ms-4"
				/>
			</div>
			
		</div>
		
	</AuthenticatedLayout>
</template>
