<script setup lang="ts">
import InputField from '../Form/old/InputField.vue';
import RichTextEditor from '../Form/RichTextEditor.vue';
import ToggleButton from 'primevue/togglebutton';
import Button from 'primevue/button';
import { IFreeTextForm } from '@/form/freetextform';

const props = defineProps<{
	form: Readonly<IFreeTextForm>
}>();

const form = props.form;
</script>

<template>
	<div>
		<p>DEBUG: {{ form.id }}</p>
		<div class="flex justify-between">
			<InputField label="Überschrift" :form="form.heading"/>
			<ToggleButton v-model="form.is_public.val" onLabel='öffentlich' offLabel="intern" class="w-28"/>
		</div>
		<RichTextEditor v-model="form.html" class="mt-3"/>
		<div class="flex justify-between mt-3">
			<Button @click="form.click_save()" label="Speichern" :loading="form.is_save_button_loading"/>
			<Button @click="form.click_delete()" label="Löschen" :loading="form.is_delete_button_loading" severity="danger"/>
		</div>
	</div>
</template>

<style lang="css" scoped>
</style>
