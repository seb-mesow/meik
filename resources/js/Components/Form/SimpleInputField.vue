<script setup lang="ts">
import InputErrors from './InputErrors.vue';
import InputText from 'primevue/inputtext';
import type { IValueForm } from '@/util/form';

defineProps<{
	label: string,
	form_value: IValueForm<string|number, any>
}>();
</script>

<template>
	<div>
		<p><label :for="form_value.id.toString()">{{ label }}</label></p>
		<InputText type=text :id="form_value.id.toString()" v-model="form_value.val"/>
		<InputErrors :errors="form_value.errs"/>
	</div>
</template>
