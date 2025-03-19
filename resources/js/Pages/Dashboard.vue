<script setup lang="ts">
import LogoutButton from '@/Components/Control/LogoutButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
	<Head title="Dashboard" />
	<AuthenticatedLayout>
		<template #header>
			<h2
				class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
			>
				Dashboard
			</h2>
		</template>

		<div class="py-12">
			<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
				<div
					class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
				>
					<Form id="logout" method="post" :action="route('logout')">
						<Button severity="primary" raised type="submit">Logout
						</Button>
					</Form>
					<div class="p-6 text-gray-900 dark:text-gray-100">
						You're logged in!
					</div>
					<LogoutButton/>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
