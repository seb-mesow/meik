<script setup lang="ts" generic="C = string, P = C">
import BaseInputField from './BaseInputField.vue';
import { UIGroupSelectForm } from '@/form/groupselectform';
import AutoComplete from 'primevue/autocomplete';

const props = defineProps<{
	label: string,
	form: UIGroupSelectForm<C, P>,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<BaseInputField :form="form" :label="label" :grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row">
		<!-- @vue-expect-error -->
		<AutoComplete
			class="w-full"
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="form.on_change_ui_value_in_editing($event)"
			:invalid="form.ui_is_invalid.value"
			dropdown
			:suggestions="form.shown_suggestions.value"
			@complete="form.on_complete($event)"
			@clear="form.on_clear()"
			optionGroupLabel="children"
			optionGroupChildren="children"
			@keydown.tab="form.on_tab_keydown($event)"
			:pt="{ dropdown: { tabindex: -1 } }"
		>
			<template #optiongroup="{ option }">
				<slot name="optiongroup" v-bind="option"></slot>
			</template>
			<template #option="{ option }">
				<slot name="option" v-bind="option"></slot>
			</template>
		</AutoComplete>
	</BaseInputField>
</template>
