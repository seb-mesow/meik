<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface IUser {
	name: string;
	is_admin: string;	
}

const props = defineProps<{
    users: IUser[];
}>();
</script>

<template>
    <Head title="Benutzerverwaltung" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

		<DataTable :value="users" tableStyle="min-width: 50rem">
		    <Column field="name" header="Benutzername"></Column>
		    <Column field="is_admin" header="ist Admin?"></Column>
		</DataTable>
    </AuthenticatedLayout>
</template>
