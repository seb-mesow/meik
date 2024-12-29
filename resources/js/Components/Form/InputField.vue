<script setup lang="ts">
import InputErrors from './InputErrors.vue';
import InputText from 'primevue/inputtext';
import { ISingleValueForm } from '@/form/singlevalueform';

const props = defineProps<{
	label: string,
	form: ISingleValueForm<any>,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.id.toString()">{{ props.label }}</label></p>
		<InputText type=text :id="form.id.toString()" v-model="form.val"/>
		<InputErrors :errors="form.errs"/>
	</div>
</template>
