<script setup lang="ts">
import { route } from 'ziggy-js';
import Checkbox from '@/Components/Form/old/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/old/InputError.vue';
import InputLabel from '@/Components/Form/old/InputLabel.vue';
import PrimaryButton from '@/Components/Control/PrimaryButton.vue';
import TextInput from '@/Components/Form/old/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
	canResetPassword?: boolean;
	status?: string;
}>();

const form = useForm({
	username: '',
	password: '',
	remember: false,
});

const submit = () => {
	form.post(route('login'), {
		onFinish: () => {
			form.reset('password');
		},
	});
};
</script>

<template>
	<GuestLayout>
		<Head title="Log in" />
		<div v-if="status" class="mb-4 text-sm font-medium text-green-600">
			{{ status }}
		</div>
		<form @submit.prevent="submit">
			<div>
				<InputLabel for="username" value="Username" />
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
		</form>
	</GuestLayout>
</template>
