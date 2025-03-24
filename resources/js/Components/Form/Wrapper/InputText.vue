<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';
import Tooltip from 'primevue/tooltip';

defineProps<{
	form: UISingleValueForm2<string|undefined>,
	type?: string,
	tooltip?: string,
}>();
</script>

<template>
	<!-- @vue-expect-error -->
	<InputText
		:type="type ?? 'text'"
		:id="form.html_id" :name="form.html_id"
		:modelValue="form.ui_value_in_editing"
		@update:modelValue="(v: string|undefined) => form.on_change_ui_value_in_editing(v)"
		@blur="form.on_blur($event)"
		:invalid="form.ui_is_invalid.value"
		v-tooltip.top="{ value: tooltip, showDelay: 1000 }"
		fluid
	/>
</template>
