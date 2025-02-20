<script setup lang="ts">
import BaseInputField from './BaseInputField.vue';
import { UISingleValueForm2 } from '@/form/single/generic/single-value-form2';
import DatePicker from 'primevue/datepicker';

const props = defineProps<{
	form: UISingleValueForm2<Date>,
	label: string,
	tooltip?: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row">
		<!-- @vue-expect-error -->
		<DatePicker
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="(v: Date|undefined) => form.on_change_ui_value_in_editing(v)"
			@blur="form.on_blur($event)"
			:invalid="form.ui_is_invalid.value"
			v-tooltip.top="{ value: props.tooltip, showDelay: 1000 }"
			fluid
		/>
	</BaseInputField>
</template>
