<script setup lang="ts">
import InputErrors from './InputErrors.vue';
import InputText from 'primevue/inputtext';
import type { IForm } from '@/util/form';

defineProps<{
	label: string,
	form_value: IForm<string|number, any>
}>();
</script>

<template>
	<div>
		<p><label :for="form_value.id.toString()">{{ label }}</label></p>
		<InputText type=text
			v-model="form_value.val"
			:name="form_value.id.toString()"
			:id="form_value.id.toString()"
		/>
		<InputErrors :errors="form_value.errs"/>
	</div>
</template>
