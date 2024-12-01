<script setup lang="ts">
import { IFreeText } from '@/types/meik/models';
import { IValueForm } from '@/util/form';
import Editor from 'primevue/editor';
import { ref } from 'vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: IValueForm<number, IFreeText>;
}>();
const form = props.form;
const html = ref(form.val.html.val);

</script>

<template>
	<div>
		<div>#{{ props.form.id }}</div>
		<div>{{ props.form.val.heading.val }}</div>
		<Editor v-model="html" />
		<div>{{ props.form.val.is_public.val ? 'öffentlich' : 'intern' }}</div>
	</div>
</template>

<style lang="css" scoped>
</style>
