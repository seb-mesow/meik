<script setup lang="ts">
import SplitBaseInputField from './SplitBaseInputField.vue';
import { UISelectForm } from '@/form/single/generic/select-form';
import { UISingleValueForm2 } from '@/form/single/generic/single-value-form2';
import InputNumber from 'primevue/inputnumber';
import AutoComplete from 'primevue/autocomplete';
import { ICurrency } from '@/form/exhibitform';

const props = defineProps<{
	form_amount: UISingleValueForm2<number>,
	form_currency: UISelectForm<ICurrency>,
	grid_col: number,
	grid_col_span?: number,
	grid_row: number,
}>();
</script>

<template>
	<SplitBaseInputField
		:form1="form_amount" label1="Originalpreis" flex1="1"
		:form2="form_currency" label2="Währung" flex2="0 0 7.5rem"
		:grid_col="grid_col" :grid_col_span="grid_col_span" :grid_row="grid_row"
	>
		<template #form1>
			<!-- @vue-expect-error -->
			<InputNumber
				:inputId="form_amount.html_id"
				:modelValue="form_amount.ui_value_in_editing"
				@update:modelValue="(v: number) => form_amount.on_change_ui_value_in_editing(v)"
				fluid
			/>
		</template>
		<template #form2>
			<!-- @vue-expect-error -->
			<AutoComplete
				:id="form_currency.html_id" :name="form_currency.html_id"
				:modelValue="form_currency.ui_value_in_editing"
				@update:modelValue="(v: U) => form_currency.on_change_ui_value_in_editing(v)"
				:invalid="form_currency.ui_is_invalid.value"
				dropdown
				:suggestions="form_currency.shown_suggestions.value"
				@complete="form_currency.on_complete($event)"
				@before-show="form_currency.on_before_show()"
				@hide="form_currency.on_hide()"
				optionLabel="id"
				fluid
				@keydown.tab="form_currency.on_tab_keydown($event)"
				:pt="{ dropdown: { tabindex: -1 } }"
			>
				<template #option="{ option }">
					<slot name="option" v-bind="option"></slot>
				</template>
			</AutoComplete>
		</template>
	</SplitBaseInputField>
</template>
