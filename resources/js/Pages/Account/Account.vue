<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Breadcrumb, Button } from 'primevue';
import { route } from 'ziggy-js';

defineProps<{
	mustVerifyEmail?: boolean;
	status?: string;
}>();

const home = {
	icon: 'pi pi-home',
	url: route('exhibit.overview'),
};
const items = [
	{
		label: 'Konto',
	},
];
</script>

<template>
	<Head title="Profile" />

	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb class="!bg-gray-100 dark:!bg-gray-800" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>

		<div class="mx-auto min-h-full w-90 max-w-dvh">
			
			<div class="p-3 rounded-xl bg-gray-200 dark:bg-gray-900 border-1 border-gray-300 dark:border-gray-600 text-center mt-3">
				
				<Button
					class="m-auto"
					as="a"
					:href="route('account.change_password')"
					label="Passwort ändern"
					severity="danger"
				/>
				
			</div>
			
		</div>
	</AuthenticatedLayout>
</template>
