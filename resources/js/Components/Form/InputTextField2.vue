<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { UISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: UISingleValueForm2,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ props.label }}</label></p>
		<InputText
			type=text :id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing.value"
			@update:modelValue="(v: string|undefined) => form.on_change_val_in_editing(v)"
			fluid
		/>
		<div v-show="form.errs.value.length > 0">
			<p v-for="error in form.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
	</div>
</template>
