<script setup lang="ts">
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';
import Checkbox from './Wrapper/Checkbox.vue';

const props = defineProps<{
	form: UISingleValueForm2<boolean>,
	label: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
	classErrors?: string,
	classCheckbox?: string,
}>();
const _grid_col_end: number = props.grid_col + (props.grid_col_span ?? 1);
const _grid_row_start: number = (props.grid_row - 1) * 3 + 1;
const _grid_row_end: number = _grid_row_start + 1;
</script>

<template>
	<div :style="`grid-area: ${_grid_row_start+0} / ${grid_col} / ${_grid_row_end+1} / ${_grid_col_end};`"
		:class="classErrors"
	>
		<p v-for="error in form.ui_errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
	</div>
	<div :style="`grid-area: ${_grid_row_start+2} / ${grid_col} / ${_grid_row_end+2} / ${_grid_col_end};`"
		:class="classCheckbox"
	>
		<Checkbox :form="form" />
		<label class="ms-3" :for="form.html_id">{{ props.label + (form.is_required.value ? ' *': '' ) }}</label>
	</div>
</template>
