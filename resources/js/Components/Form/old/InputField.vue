<script setup lang="ts">
import InputText from 'primevue/inputtext';
import { ISingleValueForm } from '@/form/generic/single/single-value-form';

const props = defineProps<{
	label: string,
	form: ISingleValueForm<any>,
}>();

const form = props.form;
</script>

<template>
	<div>
		<p><label :for="form.id">{{ props.label }}</label></p>
		<div v-show="form.errs">
			<p v-for="error in form.errs">{{ error }}</p>
		</div>
		<InputText
			class="w-full"
			type=text :id="form.id" :name="form.id" v-model="form.val"
		/>
	</div>
</template>
