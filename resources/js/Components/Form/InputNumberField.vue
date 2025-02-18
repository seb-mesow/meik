<script setup lang="ts">
import BaseInputField from './BaseInputField.vue';
import { UISingleValueForm2 } from '@/form/singlevalueform2';
import InputNumber from 'primevue/inputnumber';

const props = defineProps<{
	label: string,
	form: UISingleValueForm2<number>,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row">
		<!-- @vue-expect-error -->
		<InputNumber
			:inputId="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="(v: number) => form.on_change_ui_value_in_editing(v)"
			fluid
		/>
	</BaseInputField>
</template>
