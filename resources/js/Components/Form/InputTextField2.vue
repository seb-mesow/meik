<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { ISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: ISingleValueForm2<string>,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ props.label }}</label></p>
		<InputText
			type=text :id="form.html_id" :name="form.html_id"
			:modelValue="form.val_in_editing"
			@update:modelValue="(v: string|undefined) => form.on_change_val_in_editing(v)"
			fluid
		/>
		<div v-show="form.errs">
			<p v-for="error in form.errs">{{ error }}</p>
		</div>
	</div>
</template>
