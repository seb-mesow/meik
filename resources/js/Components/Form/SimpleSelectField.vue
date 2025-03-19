<script setup lang="ts" generic="U">
import { UISimpleSelectForm } from '@/form/generic/single/simple-select-form';
import BaseInputField from './BaseInputField.vue';
import Select from 'primevue/select';

const props = defineProps<{
	label: string,
	form: UISimpleSelectForm<U>,
	// optionLabel?: string,
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
		<!-- @vue-expect-error -->
		<Select
			:id="form.html_id"
			:name="form.html_id"
			:modelValue="form.ui_value_in_editing.value"
			@update:modelValue="(v: boolean) => form.on_change_ui_value_in_editing(v)"
			:options="form.options.value"
			optionLabel="name"
			@blur="form.on_blur($event)"
			:invalid="form.ui_is_invalid.value"
			placeholder="auswählen"
		/>
	</BaseInputField>
</template>
