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
			<InputField class="exhibit-free-text-field-heading grow me-3" label="Überschrift" :form="form.heading"/>
			<ToggleButton
				v-model="form.is_public.val"
				onLabel='öffentlich'
				offLabel="intern"
				severity="primary"
				raised
				class="w-28 is_public-button"
				:class="{
					'is_public-button--public': form.is_public.val,
					'is_public-button--internal': !form.is_public.val
				}"
			/>
		</div>
		<RichTextEditor v-model="form.html" class="mt-3"/>
		<div class="flex justify-between gap-2 mt-3">
			<div class="w-[8.1rem] text-right">
				<Button @click="form.click_save()" label="Speichern" :loading="form.is_save_button_loading" severity="primary" raised/>
			</div>
			<Button
				@click="form.click_delete()"
				:loading="form.is_delete_button_loading"
				icon="pi pi-trash"
				severity="danger"
				rounded
				class="delete-button--icon"
			/>
		</div>
	</div>
</template>

<style lang="css">
.exhibit-free-text-field-heading input {
	font-weight: bold;
	font-size: 1.25rem;
	padding-top: .25rem;
	padding-bottom: .25rem;
	padding-left: .5rem;
	padding-right: .5rem;
	width: 100%;
}
</style>
