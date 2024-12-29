<script setup lang="ts">
import { IFreeTextsInitPageProps } from '@/types/page_props/freetexts';
import FreeTextField from './FreeTextField.vue';
import Button from 'primevue/button';
import { FreeTextsForm, IFreeTextsForm, IFreeTextFormConstructorArgs } from '@/form/freetextsform';
import { IFreeTextInitPageProps } from '@/types/page_props/freetext';
import { reactive } from 'vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit_id: number,
	init_props: IFreeTextsInitPageProps;
}>();
console.log(`FreeTextFields.vue: props.init_props ==`);
console.log(props.init_props);
const form: IFreeTextsForm = reactive(new FreeTextsForm({
	exhibit_id: props.exhibit_id,
	val: props.init_props.val.map((_init_props: IFreeTextInitPageProps): IFreeTextFormConstructorArgs => {
		return {
			id: _init_props.id,
			heading: _init_props.val.heading,
			html: _init_props.val.html,
			is_public: _init_props.val.is_public,
		};
	}),
	errs: props.init_props.errs,
}));
console.log(`FreeTextFields.vue: form ==`);
console.log(form);
</script>

<template>
	<!-- Die Reaktivität von form erstreckt sich auch auf seine Properties (deep reactive) -->
	<div v-for="free_text_form in form.children" :key="free_text_form.ui_id">
		<hr class="my-3">
		<FreeTextField :form="free_text_form" />
	</div>
	<hr class="my-3">
	<Button @click="form.append_form()" label="Abschnitt hinzufügen"/>
</template>

<style lang="css" scoped>
</style>
