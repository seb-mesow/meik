<script setup lang="ts">
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import { ChangePasswordForm, UIChangePasswordForm } from '@/form/special/multiple/change-password-form';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Breadcrumb, Button, Toast, useToast } from 'primevue';
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
		url: route('account.details'),
	},
	{
		label: 'Passwort ändern',
	},
];

const toast_service = useToast();

const form: UIChangePasswordForm = new ChangePasswordForm({
	aux: {
		toast_service: toast_service,
	}
});
</script>

<template>
	<Head title="Profile" />
	
	<Toast />
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
			<!-- <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Update Password
			</h2>

			<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
				Ensure your account is using a long, random password to stay
				secure.
			</p>
		</header> -->
			
		<div class="mx-auto min-h-full w-90 max-w-dvh">
			
			<div class="p-3 rounded-xl bg-gray-200 dark:bg-gray-900 border-1 border-gray-300 dark:border-gray-600 mt-3">
				
				<div class="grid grid-cols-1 gap-x-3">
					<InputTextField2 :form="form.old_password" label="Altes Password" type="password" :grid_col="1" :grid_row="1" :autocomplete="false"/>
				
					<InputTextField2 :form="form.new_password" label="Neues Passwort" type="password" :grid_col="1" :grid_row="2" class_label="mt-2" :autocomplete="false"/>
					
					<InputTextField2 :form="form.new_password_again" label="Neues Passwort wiederholen" type="password" :grid_col="1" :grid_row="3" class_label="mt-2" :autocomplete="false"/>
				</div>
				
				<div class="mt-4 flex items-center justify-end">
					<Button
						:disabled="!form.is_save_button_enabled.value"
						@click="form.click_save()"
						label="Passwort ändern"
						severity="danger"
						class="ms-4"
					/>
				</div>
				
			<!-- <div>
				<InputLabel for="current_password" value="Current Password" />

				<TextInput
					id="current_password"
					ref="currentPasswordInput"
					v-model="form.current_password"
					type="password"
					class="mt-1 block w-full"
					autocomplete="current-password"
				/>

				<InputError
					:message="form.errors.current_password"
					class="mt-2"
				/>
			</div>

			<div>
				<InputLabel for="password" value="New Password" />

				<TextInput
					id="password"
					ref="passwordInput"
					v-model="form.password"
					type="password"
					class="mt-1 block w-full"
					autocomplete="new-password"
				/>

				<InputError :message="form.errors.password" class="mt-2" />
			</div>

			<div>
				<InputLabel
					for="password_confirmation"
					value="Confirm Password"
				/>

				<TextInput
					id="password_confirmation"
					v-model="form.password_confirmation"
					type="password"
					class="mt-1 block w-full"
					autocomplete="new-password"
				/>

				<InputError
					:message="form.errors.password_confirmation"
					class="mt-2"
				/>
			</div>

			<div class="flex items-center gap-4">
				<PrimaryButton :disabled="form.processing">Save</PrimaryButton>

				<Transition
					enter-active-class="transition ease-in-out"
					enter-from-class="opacity-0"
					leave-active-class="transition ease-in-out"
					leave-to-class="opacity-0"
				>
					<p
						v-if="form.recentlySuccessful"
						class="text-sm text-gray-600 dark:text-gray-400"
					>
						Saved.
					</p>
				</Transition>
			</div> -->
			</div>
		</div>
	</AuthenticatedLayout>
</template>
