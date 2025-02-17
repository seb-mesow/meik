<script setup lang="ts">
import DatePicker from 'primevue/datepicker';
import { UISingleValueForm2 } from '@/form/singlevalueform2';

const props = defineProps<{
	label: string,
	form: UISingleValueForm2<Date>,
	tooltip?: string,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.html_id">{{ props.label }}</label></p>
		<!-- @vue-expect-error -->
		<DatePicker
			:id="form.html_id" :name="form.html_id"
			:modelValue="form.ui_value_in_editing"
			@update:modelValue="(v: Date|undefined) => form.on_change_ui_value_in_editing(v)"
			:invalid="form.ui_is_invalid.value"
			v-tooltip.top="{ value: props.tooltip, showDelay: 1000 }"
			fluid
		/>
		<div v-show="form.errs.value.length > 0">
			<p v-for="error in form.errs.value" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
		</div>
	</div>
</template>
