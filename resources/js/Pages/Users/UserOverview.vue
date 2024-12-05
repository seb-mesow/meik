<script setup lang="ts">
import { nextTick, ref, Ref } from 'vue';
import { AxiosRequestConfig } from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ToggleSwitch from 'primevue/toggleswitch';
import AJAXConfirmationPopup from '@/Components/AJAXConfirmationPopup.vue';
import Breadcrumb from 'primevue/breadcrumb';

// versch. Interface für typsicheres Programmieren
interface PropUser {
	username: string,
	forename: string,
	surname: string,
	is_admin: boolean,
}

interface User {
	username: string;
	forename: string;
	surname: string;
	is_admin: {
		in_ui: boolean,
		actual: boolean,
	}
}

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
    users: PropUser[];
}>();

const home = ref({
    icon: 'pi pi-home',
    route: 'exhibit.overview'
});
const items = ref([
    {
        label: 'Benutzerverwaltung',
        route: 'users.all'
    },
]);

// (interne) Attribute der Komponente
const ajax_confirmation_popup = ref<InstanceType<typeof AJAXConfirmationPopup>>();
const users_ref = ref<User[]>(props.users.map((prop_user: PropUser): User => {
	return {
		username: prop_user.username,
		forename: prop_user.forename,
 		surname: prop_user.surname,
		is_admin: {
			in_ui: prop_user.is_admin,
			actual: prop_user.is_admin,
		},
	};
}));
const admin_state_toggles_readonly = ref(false);

async function toggle_admin_state(user: User, event: Event): Promise<void> {
	const request_cfg: AxiosRequestConfig = {
		url: route('ajax.user.set_admin', { username: user.username }),
		method: 'patch',
		data: {
			'is_admin': user.is_admin.in_ui,
		}
	};
	
	await nextTick();
	ajax_confirmation_popup.value?.show(event, request_cfg, {
		fullfied() {
			user.is_admin.actual = user.is_admin.in_ui;
		},
		rejected() {
			user.is_admin.in_ui = user.is_admin.actual;
		},
	});
}

</script>

<template>
    <Head title="Benutzerverwaltung" />

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

		<DataTable :value="users_ref" tableStyle="min-width: 50rem">
		    <Column field="username" header="Benutzername"/>
		    <Column field="forename" header="Vorname"/>
		    <Column field="surname" header="Nachname"/>
		    <Column field="is_admin" header="ist Admin?">
				<template #body='{ data }'>
					<ToggleSwitch 
						v-model="data.is_admin.in_ui"
						:readonly="admin_state_toggles_readonly"
						@change.prevent="toggle_admin_state(data, $event)"
					/>
				</template>
			</Column>
		</DataTable>
		<AJAXConfirmationPopup ref="ajax_confirmation_popup"/>
		
		<div class="absolute bottom-4 right-4">
            <Button severity="info" as="a" :href="route('user.new')" icon="pi pi-plus" />
        </div>
    </AuthenticatedLayout>
</template>
