<script setup lang="ts">
import InputField from '@/Components/InputField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Form } from '@/types/meik/technical';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { ref } from 'vue';
// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	form: Form<{
		inventory_number: string,
		name: string,
		manufacturer: string
	}>
}>();
// (interne) Attribute der Komponente
console.log(props.form);
</script>

<template>
	<Head title="Exponat" />

	<AuthenticatedLayout>
		<template #header>
			<h2	class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
				Exponat
			</h2>
		</template>
		<InputField :form_value="form.vals.inventory_number" label="Inventarnummer"/>
		<InputField :form_value="form.vals.name" label="Bezeichnung"/>
		<InputField :form_value="form.vals.manufacturer" label="Hersteller"/>
		<Button as="a" label="Abschnitt hinzufügen" :href="route('new-user')"/>
	</AuthenticatedLayout>
</template>
