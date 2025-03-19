<script setup lang="ts">
import BaseInputField from './BaseInputField.vue';
import InputText from '@/Components/Form/Wrapper/InputText.vue';
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';

defineProps<{
	label: string,
	form: UISingleValueForm2,
	type?: string,
	tooltip?: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
	class_label?: string,
	class_errors?: string,
	class_text?: string,
}>();
</script>

<template>
	<BaseInputField
		:form="form" :label="label"
		:grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row"
		:class_label="class_label" :class_errors="class_errors" :class_slot="class_text"
	>
		<InputText :form="form" :type="type" />
	</BaseInputField>
</template>
