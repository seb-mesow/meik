<script setup lang="ts">
import type { IFreeTextsForms } from '@/types/meik/models';
import { create_form, IForm } from '@/util/form';
import FreeTextField from './FreeTextField.vue';
import Button from 'primevue/button';
import { reactive, Ref, ref } from 'vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit_id: string,
	form: IFreeTextsForms;
}>();

const forms = reactive(props.form); // ref geht nicht
console.log('forms ==');
console.log(forms);
// TODO differente between visual index and index for db
function append_form() {
	let greatest_index = -1;
	for (const index_str in forms.val) {
		const index = parseInt(index_str);
		if (index > greatest_index) {
			greatest_index = index;
		}
	}
	const index = greatest_index+1;
	const form: = ;
	forms.val[index] = form;
}

function delete_form(index: number) {
	forms.val.splice(index, 1);
	index = 0;
	for (const i in forms.val) {
		forms.val[i].id = index++;
	}
}
</script>

<template>
	<div v-for="free_text_form in forms.val">
		<hr class="my-3">
		<FreeTextField :form="free_text_form" :exhibit_id="exhibit_id" @to_delete="delete_form"/>
	</div>
	<hr class="my-3">
	<Button @click="append_form" label="Abschnitt hinzufügen"/>
</template>

<style lang="css" scoped>
</style>
