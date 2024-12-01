<script setup lang="ts">
import type { IFreeText } from '@/types/meik/models';
import { IValueForm } from '@/util/form';
import FreeTextField from './FreeTextField.vue';
import Button from 'primevue/button';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: IValueForm<'free_texts', IFreeText[]>;
}>();
</script>

<template>
	<div>
		<FreeTextField v-for="free_text_form in props.form.val" :form="free_text_form"/>
	</div>
	<Button label="Abschnitt hinzufügen" :href="route('user.new')"/>
</template>

<style lang="css" scoped>
</style>
