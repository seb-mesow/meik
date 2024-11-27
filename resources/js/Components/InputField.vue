<script setup lang="ts">
import InputErrors from './InputError.vue';
import InputText from 'primevue/inputtext';
import type { FormValue } from '@/types/meik';

defineProps<{
	label: string,
	form_value: FormValue<string, any>
}>();
</script>
<template>
	<div>
		<label :for="form_value.id">{{ label }}</label>
		<InputText type=text :name="form_value.id" v-model="form_value.value"/>
		<InputErrors :errors="form_value.errors"/>
	</div>
</template>
