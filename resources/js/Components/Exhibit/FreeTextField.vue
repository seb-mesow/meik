<script setup lang="ts">
import InputField from '../Form/old/InputField.vue';
import RichTextEditor from '../Form/RichTextEditor.vue';
import ToggleButton from 'primevue/togglebutton';
import Button from 'primevue/button';
import { IFreeTextForm } from '@/form/special/multiple/freetext-form';

const props = defineProps<{
	form: Readonly<IFreeTextForm>
}>();

const form = props.form;
</script>

<template>
	<div>
		<div class="flex justify-between">
			<InputField label="Überschrift" :form="form.heading"/>
			<ToggleButton severity="primary" raised v-model="form.is_public.val" onLabel='öffentlich' offLabel="intern" class="w-28"/>
		</div>
		<RichTextEditor v-model="form.html" class="mt-3"/>
		<div class="flex justify-end gap-2 mt-3">
			<Button @click="form.click_save()" label="Speichern" :loading="form.is_save_button_loading" severity="primary" raised/>
			<Button @click="form.click_delete()" label="Löschen" :loading="form.is_delete_button_loading" severity="danger" raised/>
		</div>
	</div>
</template>

<style lang="css" scoped>
</style>
