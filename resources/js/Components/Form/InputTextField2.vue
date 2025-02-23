<script setup lang="ts">
import BaseInputField from './BaseInputField.vue';
import InputText from '@/Components/Form/Wrapper/InputText.vue';
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';

defineProps<{
	label: string,
	form: UISingleValueForm2,
	tooltip?: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row">
		<InputText :form="form" />
	</BaseInputField>
</template>
