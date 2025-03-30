<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';

const props = withDefaults(defineProps<{
	form: UISingleValueForm2<string|undefined>,
	type?: string,
	tooltip?: string,
	autocomplete?: boolean|undefined,
}>(), {
	autocomplete: undefined,
});
</script>

<template>
	<!-- @vue-expect-error -->
	<InputText
		:type="type ?? 'text'"
		:id="form.html_id"
		:pt:root:name="form.html_id"
		:modelValue="form.ui_value_in_editing"
		@update:modelValue="(v: string|undefined) => form.on_change_ui_value_in_editing(v)"
		@blur="form.on_blur($event)"
		:invalid="form.ui_is_invalid.value"
		v-tooltip.top="{ value: tooltip, showDelay: 1000 }"
		fluid
		:pt="{
			root: { autocomplete: (props.autocomplete === true ? 'on' : (props.autocomplete === false ? 'off' : undefined)) }
		}"
	/>
</template>
