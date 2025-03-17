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
	classLabel?: string,
	classErrors?: string,
	classText?: string,
}>();
</script>

<template>
	<BaseInputField
		:form="form" :label="label"
		:grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row"
		:classLabel="classLabel" :classErrors="classErrors" :classSlot="classText"
	>
		<InputText :form="form" :type="type" />
	</BaseInputField>
</template>
