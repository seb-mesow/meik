<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { ISingleValueForm } from '@/form/singlevalueform';
import { ISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: ISingleValueForm2<string>,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.id">{{ props.label }}</label></p>
		<InputText
			class="w-full"
			type=text :id="form.id" :name="form.id"
			:modelValue="form.val_in_editing"
			@update:modelValue="(v) => form.on_change_val_in_editing(v)"
		/>
		<div v-show="form.errs">
			<p v-for="error in form.errs">{{ error }}</p>
		</div>
	</div>
</template>
