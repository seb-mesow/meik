<script setup lang="ts" generic="U">
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';

const props = defineProps<{
	form: UISingleValueForm2<U>,
	label: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
const _grid_col_end: number = props.grid_col + (props.grid_col_span ?? 1);
const _grid_row_start: number = (props.grid_row - 1) * 3 + 1;
const _grid_row_end: number = _grid_row_start + 1;
</script>

<template>
	<div :style="`grid-area: ${_grid_row_start} / ${grid_col} / ${_grid_row_end} / ${_grid_col_end};`"
		class=""
	>
		<label :for="form.html_id">{{ props.label + (form.is_required.value ? ' *': '' ) }}</label>
	</div>
	<div :style="`grid-area: ${_grid_row_start+1} / ${grid_col} / ${_grid_row_end+1} / ${_grid_col_end};`"
		class=""
		>
		<p v-for="error in form.ui_errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
	</div>
	<div :style="`grid-area: ${_grid_row_start+2} / ${grid_col} / ${_grid_row_end+2} / ${_grid_col_end};`"
		class=""
	>
		<slot/>
	</div>
</template>
