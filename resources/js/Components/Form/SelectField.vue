<script setup lang="ts" generic="ValType extends any">
import AutoComplete from 'primevue/autocomplete';
import { ISelectForm } from '@/form/selectform';

const props = defineProps<{
	label: string,
	form: ISelectForm<ValType>,
	optionLabel?: string,
}>();
</script>

<template>
	<div>
		<p><label :for="props.form.html_id">{{ props.label }}</label></p>
		<AutoComplete
			:id="props.form.html_id" :name="props.form.html_id"
			:modelValue="props.form.val_in_editing"
			@update:modelValue="(v: ValType) => props.form.on_change_val_in_editing(v)"
			dropdown
			:suggestions="props.form.shown_suggestions.value"
			@complete="props.form.on_complete($event)"
			:optionLabel="props.optionLabel"
			fluid
		>
			<template #option="{ option }">
				<slot name="option" v-bind="option"></slot>
			</template>
		</AutoComplete>
		<div v-show="props.form.errs">
			<p v-for="error in props.form.errs">{{ error }}</p>
		</div>
	</div>
</template>
