<script setup lang="ts">
import CheckBox from 'primevue/checkbox';
import { UISingleValueForm2 } from '@/form/generic/single/single-value-form2';

defineProps<{
	form: UISingleValueForm2<boolean>,
	tooltip?: string,
}>();
</script>

<template>
	<CheckBox
		:id="form.html_id" :name="form.html_id"
		:modelValue="form.ui_value_in_editing"
		@update:modelValue="(v: boolean) => form.on_change_ui_value_in_editing(v)"
		@blur="form.on_blur($event)"
		:invalid="form.ui_is_invalid.value"
		binary
	/>
	<!-- v-tooltip.top="{ value: tooltip, showDelay: 1000 }" -->
</template>
