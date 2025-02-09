<script setup lang="ts">
import InputNumber from 'primevue/inputnumber';
import { ISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: ISingleValueForm2<number>,
}>();

</script>

<template>
	<div>
		<p><label :for="props.form.html_id">{{ props.label }}</label></p>
		<InputNumber
			:inputId="props.form.html_id"
			:modelValue="props.form.val_in_editing"
			@update:modelValue="(v: number) => props.form.on_change_val_in_editing(v)"
			fluid
		/>
		<div v-show="props.form.errs">
			<p v-for="error in props.form.errs">{{ error }}</p>
		</div>
	</div>
</template>
