<script setup lang="ts">
import { IFreeText } from '@/types/meik/models';
import { IValueForm } from '@/util/form';
import Editor from 'primevue/editor';
import { ref } from 'vue';
import SimpleInputField from '../Form/SimpleInputField.vue';
import RichTextEditor from '../Form/RichTextEditor.vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: IValueForm<number, IFreeText>;
}>();
const form = props.form;
const html = ref<string>(form.val.html.val);
</script>

<template>
	<div>
		<div>#{{ props.form.id }}</div>
		<SimpleInputField label="Überschrift" :form_value="props.form.val.heading"/>
		<RichTextEditor v-model="html" />
		<div>{{ props.form.val.is_public.val ? 'öffentlich' : 'intern' }}</div>
	</div>
</template>

<style lang="css" scoped>
</style>
