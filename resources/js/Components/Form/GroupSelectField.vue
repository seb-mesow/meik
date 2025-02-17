<script setup lang="ts" generic="C = string, P = C">
import AutoComplete from 'primevue/autocomplete';

import { UIGroupSelectForm } from '@/form/groupselectform';

const props = defineProps<{
	label: string,
	form: UIGroupSelectForm<C, P>,
}>();
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ props.label }}</label></p>
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
		<div v-show="form.errs.value.length > 0">
			<p v-for="error in form.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
	</div>
</template>
