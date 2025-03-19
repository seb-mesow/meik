<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputTextField2 from '@/Components/Form/InputTextField2.vue';
import { ILoginForm, LoginForm } from '@/form/special/multiple/login-form';
import { Button, Toast, useToast } from 'primevue';
import CheckBoxField from '@/Components/Form/CheckBoxField.vue';
import InputErrors from '@/Components/Form/Wrapper/InputErrors.vue';
import welcome_url from '../../../images/meikWelcome.png';

defineProps<{
	canResetPassword?: boolean;
	status?: string;
}>();

const form = useForm({
	username: '',
	password: '',
	remember: false,
});

const toast = useToast();
const login_form: ILoginForm = new LoginForm({
	aux: {
		toast_service: toast,
	}
});

</script>

<template>
	<Toast/>
	<GuestLayout>
		<Head title="Log in" />
		
		<div class="mx-auto h-dvh w-100 max-w-dvh p-3 flex flex-col justify-center">
			
			<div class="grow max-h-60 w-full mx-auto p-3 rounded-xl bg-white dark:bg-gray-950 text-black dark:text-gray-200 content-center">
				<p class="text-center text-5xl font-bold">MEIK</p>
				<p class="text-center text-2xl mt-3">
					Muesum zur
					Entwicklung der
					Informations- und
					Kommunikationstechnik
				</p>
			</div>
			
			<!-- <div class="2xl:block hidden"><span>>2xl</span></div>
			<div class="2xl:hidden xl:block hidden"><span>2xl > x > xl</span></div>
			<div class="xl:hidden lg:block hidden"><span>xl > x > lg</span></div>
			<div class="lg:hidden md:block hidden"><span>lg > x > md</span></div>
			<div class="md:hidden sm:block hidden"><span>md > x > sm</span></div>
			<div class="sm:hidden"><span>< sm</span></div> -->
			
			<div v-if="status" class="mb-4 text-sm font-medium text-green-600">
				{{ status }}
			</div>
			
			<div class="p-3 rounded-xl bg-gray-50 border-1 border-gray-300 mt-3">
				
				<InputErrors :errs="login_form.ui_errs.value" class="mb-3" />
				
				<div class="grid grid-cols-1 gap-x-3">
					<InputTextField2
						:form="login_form.username" label="Benutzername"
						:grid_col="1" :grid_row="1"
					/>
					
					<InputTextField2
						:form="login_form.password" type="password" label="Passwort"
						:grid_col="1" :grid_row="2"
						classLabel="mt-3"
					/>
					
					<CheckBoxField :form="login_form.remember" label="automatisch einloggen"
						:grid_col="1" :grid_row="3"
						classErrors="mt-3"
					/>
				</div>
				
				<Button
					:disabled="!login_form.is_login_button_enabled.value || login_form.is_login_button_loading.value"
					:loading="login_form.is_login_button_loading.value"
					type='button'
					label="Login"
					@click="login_form.click_login()"
					class="mt-3"
				/>
			</div>
			
			<!-- Form id="login" method="post" :action="route('login')" :form="form" @onFinish="form.reset('password')">
				<div>
					<InputLabel for="username" value="Benutzername" />
					<TextInput
						id="username"
						type="text"
						class="mt-1 block w-full"
						v-model="form.username"
						required
						autofocus
						autocomplete="username"
					/>
					<InputError class="mt-2" :message="form.errors.username" />
				</div>
				<div class="mt-4">
					<InputLabel for="password" value="Password" />
					<TextInput
						id="password"
						type="password"
						class="mt-1 block w-full"
						v-model="form.password"
						required
						autocomplete="current-password"
					/>
					<InputError class="mt-2" :message="form.errors.password" />
				</div>
				<div class="mt-4 block">
					<label class="flex items-center">
						<Checkbox name="remember" v-model:checked="form.remember" />
						<span class="ms-2 text-sm text-black-400 dark:text-gray-400">
							Remember me
						</span>
					</label>
				</div>
				<div class="mt-4 flex items-center justify-end">
					<Link
						v-if="canResetPassword"
						:href="route('password.request')"
						class="rounded-md text-sm text-gray-600 underline hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
					>
						Forgot your password?
					</Link>
					<PrimaryButton
						class="ms-4"
						:class="{ 'opacity-25': form.processing }"
						:disabled="form.processing"
					>
						Log in
					</PrimaryButton>
				</div>
			</Form -->
		</div>
	</GuestLayout>
</template>
