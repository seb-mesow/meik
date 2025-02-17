<script setup lang="ts" generic="U">
import AutoComplete from 'primevue/autocomplete';
import { UISelectForm } from '@/form/selectform';

const props = defineProps<{
	label: string,
	form: UISelectForm<U>,
	optionLabel?: string,
}>();
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ label }}</label></p>
		<!-- @vue-expect-error -->
		<AutoComplete
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="(v: U) => form.on_change_ui_value_in_editing(v)"
			:invalid="form.ui_is_invalid.value"
			dropdown
			:suggestions="form.shown_suggestions.value"
			@complete="form.on_complete($event)"
			@clear="form.on_clear()"
			:optionLabel="optionLabel"
			fluid
			@keydown.tab="form.on_tab_keydown($event)"
			:pt="{ dropdown: { tabindex: -1 } }"
		>
			<template #option="{ option }">
				<slot name="option" v-bind="option"></slot>
			</template>
		</AutoComplete>
		<div v-show="form.errs.value.length > 0">
			<p v-for="error in form.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
	</div>
</template>
