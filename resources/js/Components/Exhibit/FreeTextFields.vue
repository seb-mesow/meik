<script setup lang="ts">
import FreeTextField from './FreeTextField.vue';
import Button from 'primevue/button';
import { FreeTextsForm, IFreeTextsForm, IFreeTextFormConstructorArgs } from '@/form/special/overview/freetexts-form';
import { IFreeTextsProps } from '@/types/page_props/freetexts';
import { IFreeTextProps } from '@/types/page_props/freetext';
import { reactive } from 'vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit_id: number,
	init_props: IFreeTextsProps;
}>();
// console.log(`FreeTextFields.vue: props.init_props ==`);
// console.log(props.init_props);
const form: IFreeTextsForm = reactive(new FreeTextsForm({
	exhibit_id: props.exhibit_id,
	val: props.init_props.map((_init_props: IFreeTextProps): IFreeTextFormConstructorArgs => {
		return {
			id: _init_props.id,
			heading: _init_props.heading,
			html: _init_props.html,
			is_public: _init_props.is_public,
		};
	}),
	errs: [],
}));
</script>

<template>
	
	<div class="gap-2 pt-4 flex flex-col">
		<!-- Die Reaktivität von form erstreckt sich auch auf seine Properties (deep reactive) -->
		<div v-for="free_text_form in form.children" :key="free_text_form.ui_id">
			<div class="!bg-gray-100 dark:!bg-gray-900 !border-gray-300 dark:!border-gray-800 !border-2 p-4 rounded-md">
				<!-- <hr class="my-3"> -->
				<FreeTextField :form="free_text_form" />
			</div>
			<!-- <hr class="my-3"> -->
		</div>
	</div>
	
	<div class="flex w-full justify-end py-4">
		<Button severity="primary" raised @click="form.append_form()" label="Abschnitt hinzufügen" />
	</div>
	
</template>

<style lang="css" scoped></style>
