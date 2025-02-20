<script setup lang="ts" generic="U">
import BaseInputField from './BaseInputField.vue';
import { UISelectForm } from '@/form/single/generic/select-form';
import AutoComplete from 'primevue/autocomplete';

const props = defineProps<{
	label: string,
	form: UISelectForm<U>,
	optionLabel?: string,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row">
		<!-- @vue-expect-error -->
		<AutoComplete
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="(v: U) => form.on_change_ui_value_in_editing(v)"
			@blur="form.on_blur($event)"
			:invalid="form.ui_is_invalid.value"
			dropdown
			:suggestions="form.shown_suggestions.value"
			@complete="form.on_complete($event)"
			@before-show="form.on_before_show()"
			@hide="form.on_hide()"
			:optionLabel="optionLabel ?? form.optionLabel"
			fluid
			@keydown.tab="form.on_tab_keydown($event)"
			:pt="{ dropdown: { tabindex: -1 } }"
		>
			<template #option="{ option }">
				<slot name="option" v-bind="option"></slot>
			</template>
		</AutoComplete>
	</BaseInputField>
</template>
