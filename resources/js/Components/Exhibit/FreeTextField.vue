<script setup lang="ts">
import { IFreeText } from '@/types/meik/models';
import { create_request_data, IForm } from '@/util/form';
import Editor from 'primevue/editor';
import { reactive, ref, toRaw } from 'vue';
import SimpleInputField from '../Form/SimpleInputField.vue';
import RichTextEditor from '../Form/RichTextEditor.vue';
import ToggleButton from 'primevue/togglebutton';
import AJAXButton from '../Control/AJAXButton.vue';
import axios, { AxiosRequestConfig } from 'axios';
import Button from 'primevue/button';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit_id: string,
	form: IForm<number, IFreeText>;
}>();

const emit = defineEmits<{
	'to_delete': [number]
}>();

const form = reactive(props.form);
const save_button_loading = ref(false);
const delete_button_loading = ref(false);

async function click_save() {
	save_button_loading.value = true;
	try {
		if (form.persisted) {
			console.log(`PUT exhibit.free_text.update ${props.exhibit_id} ${form.id}`);
			console.log(create_request_data(form));
			await axios.request({
				method: "put",
				url: route('exhibit.free_text.update', [props.exhibit_id, form.id]),
				data: create_request_data(form)
			});
		} else {
			console.log(`POST exhibit.free_text.create ${props.exhibit_id} ${form.id}`);
			console.log(create_request_data(form));
			await axios.request({
				method: "post",
				url: route('exhibit.free_text.create', [props.exhibit_id, form.id]),
				data: create_request_data(form)
			});
		}
	} finally {
		save_button_loading.value = false;
		form.persisted = true;
	}
}
async function click_delete() {
	if (form.persisted) {
		delete_button_loading.value = true;
		try {
			console.log(`DELETE exhibit.free_text.delete ${props.exhibit_id} ${form.id}`);
			await axios.request({
				method: "delete",
				url: route('exhibit.free_text.delete', [props.exhibit_id, form.id]),
			})
		} finally {
			delete_button_loading.value = false;
		}
	}
	emit('to_delete', form.id);
}
</script>

<template>
	<div>
		<div>#{{ props.form.id }}</div>
		<div class="flex justify-between">
			<SimpleInputField label="Überschrift" :form_value="form.val.heading"/>
			<ToggleButton v-model="form.val.is_public.val" onLabel='öffentlich' offLabel="intern" class="w-28"/>
		</div>
		<RichTextEditor v-model="form.val.html.val" class="mt-3"/>
		<div class="flex justify-between mt-3">
			<Button @click="click_save" label="Speichern" :loading="save_button_loading"/>
			<Button @click="click_delete" label="Löschen" :loading="delete_button_loading" severity="danger"/>
		</div>
	</div>
</template>

<style lang="css" scoped>
</style>
