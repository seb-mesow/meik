<script setup lang="ts">
import InputNumber from 'primevue/inputnumber';
import { ISingleValueForm2, UISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: UISingleValueForm2<number>,
}>();

</script>

<template>
	<div>
		<p><label :for="props.form.html_id">{{ props.label }}</label></p>
		<InputNumber
			:inputId="props.form.html_id"
			:modelValue="props.form.ui_value_in_editing.value"
			@update:modelValue="(v: number) => props.form.on_change_val_in_editing(v)"
			fluid
		/>
		<div v-show="props.form.errs.value.length > 0">
			<p v-for="error in props.form.errs.value">{{ error }}</p>
		</div>
	</div>
</template>
