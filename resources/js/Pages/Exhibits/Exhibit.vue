<script setup lang="ts">
import InputField from '@/Components/InputField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FormValues } from '@/types/meik';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { ref } from 'vue';
// versch. Interface für typsicheres Programmieren

// Argumente an die Seite (siehe Controller)
const props = defineProps<{
	form: FormValues<{
		id: string,
		name: string,
		manufacturer: string,
	}>
}>();


// (interne) Attribute der Komponente


</script>

<template>
	<Head title="Benutzerverwaltung" />

	<AuthenticatedLayout>
		<template #header>
			<h2	class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
				Exponat
			</h2>
		</template>
		<InputField :form_value="form.id" label="Inventarnummer"/>
		<InputField :form_value="form.name" label="Bezeichnung"/>
		<InputField :form_value="form.manufacturer" label="Hersteller"/>
		<Button as="a" label="Abschnitt hinzufügen" :href="route('new-user')"/>
	</AuthenticatedLayout>
</template>
