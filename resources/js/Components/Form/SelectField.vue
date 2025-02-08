<script setup lang="ts" generic="ValType extends any, KeyType extends string|number">
import AutoComplete from 'primevue/autocomplete';
import { ISelectForm } from '@/form/selectform';

const props = defineProps<{
	label: string,
	form: ISelectForm<ValType>,
}>();
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ props.label }}</label></p>
		<AutoComplete
			class="w-full"
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.val_in_editing"
			@update:modelValue="(v) => props.form.on_change_val_in_editing(v)"
			dropdown
			:suggestions="props.form.shown_suggestions.value"
			@complete="form.on_complete($event)"
		/>
		<div v-show="form.errs">
			<p v-for="error in form.errs">{{ error }}</p>
		</div>
	</div>
</template>
