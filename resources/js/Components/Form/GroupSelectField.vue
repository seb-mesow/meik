<script setup lang="ts" generic="C = string, P = C">
import BaseInputField from './BaseInputField.vue';
import { UIGroupSelectForm } from '@/form/generic/single/group-select-form';
import AutoComplete from 'primevue/autocomplete';

const props = defineProps<{
	label: string,
	form: UIGroupSelectForm<C, P>,
	optionLabel?: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
	class_label?: string,
	class_errors?: string,
	class_text?: string,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row"
		:class_label="class_label" :class_errors="class_errors" :class_slot="class_text"
	>
		<!-- @vue-expect-error -->
		<AutoComplete
			class="w-full"
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="form.on_change_ui_value_in_editing($event)"
			@blur="form.on_blur($event)"
			:invalid="form.ui_is_invalid.value"
			dropdown
			:suggestions="form.shown_suggestions.value"
			@complete="form.on_complete($event)"
			@before-show="form.on_before_show()"
			@hide="form.on_hide()"
			:optionLabel="optionLabel ?? form.optionLabel"
			optionGroupLabel="children"
			optionGroupChildren="children"
			@keydown.tab="form.on_tab_keydown($event)"
			:pt="{ dropdown: { tabindex: -1 } }"
		>
			<template #optiongroup="{ option }">
				<slot name="optiongroup" v-bind="option.parent"/>
			</template>
			<template #option="{ option }">
				<slot name="option" v-bind="option"/>
			</template>
		</AutoComplete>
	</BaseInputField>
</template>
