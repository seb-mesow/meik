<script setup lang="ts" generic="F, S">
import { UISingleValueForm2 } from '@/form/single/generic/single-value-form2';

const props = defineProps<{
	form1: UISingleValueForm2<F>,
	form2: UISingleValueForm2<S>,
	label1: string,
	label2: string,
	flex1: string,
	flex2: string,
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
		class="flex gap-x-3"
	>
		<label :for="form1.html_id"
			:style="`flex: ${flex1};`"
		>{{ props.label1 + (form1.is_required ? ' *': '' ) }}</label>
		<label :for="form2.html_id"
			:style="`flex: ${flex2};`"
		>{{ props.label2 + (form2.is_required ? ' *': '' ) }}</label>
	</div>
	<div :style="`grid-area: ${_grid_row_start+1} / ${grid_col} / ${_grid_row_end+1} / ${_grid_col_end};`"
		class="flex gap-x-3"
	>
		<div :style="`flex: ${flex1};`">
			<p v-for="error in form1.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
		<div :style="`flex: ${flex2};`">
			<p v-for="error in form2.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
	</div>
	<div :style="`grid-area: ${_grid_row_start+2} / ${grid_col} / ${_grid_row_end+2} / ${_grid_col_end};`"
		class="flex gap-x-3"
	>
		<div :style="`flex: ${flex1};`">
			<slot name="form1"/>
		</div>
		<div :style="`flex: ${flex2};`">
			<slot name="form2"/>
		</div>
	</div>
</template>
