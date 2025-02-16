<script setup lang="ts" generic="ChildType = string, ParentType = ChildType">
import AutoComplete from 'primevue/autocomplete';

import { IGroupSelectForm } from '@/form/groupselectform';

const props = defineProps<{
	label: string,
	form: IGroupSelectForm<ChildType, ParentType>,
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
			optionGroupLabel="children"
			optionGroupChildren="children"
		>
			<template #optiongroup="{ option }">
				<slot name="optiongroup" v-bind="option"></slot>
			</template>
			<template #option="{ option }">
				<slot name="option" v-bind="option"></slot>
			</template>
		</AutoComplete>
		<div v-show="form.ui_errs">
			<p v-for="error in form.ui_errs">{{ error }}</p>
		</div>
	</div>
</template>
