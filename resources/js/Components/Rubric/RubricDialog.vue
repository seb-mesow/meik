<script lang="ts" setup>
import { ICategory, IRubricForm, IRubricFormConstructorArgs, RubricForm } from "@/form/rubricform";
import Button from "primevue/button";
import { inject } from "vue";
import InputTextField2 from "../Form/InputTextField2.vue";
import SelectField from "../Form/SelectField.vue";

const dialogRef: any = inject('dialogRef');

const selectable_categories: ICategory[]|undefined = inject('selectable_categories');
if (selectable_categories === undefined) {
	throw new Error('Assertation failed: selectable_categories not provide()-d')
}

const params: Omit<IRubricFormConstructorArgs,'dialog_ref'|'selectable_categories'> = dialogRef.value.data;

const form: IRubricForm = new RubricForm({
	data: params.data,
	preset: params.preset,
	selectable_categories: selectable_categories,
	on_rubric_created: params.on_rubric_created,
	on_rubric_updated: params.on_rubric_updated,
	dialog_ref: dialogRef,
});
</script>

<template>
	<div class="grid grid-cols-1 gap-x-3">
		<SelectField :form="form.category" label="Kategorie" :grid_col="1" :grid_row="1"/>
		<InputTextField2 :form="form.name" label="Name" :grid_col="1" :grid_row="1"/>
		<div class="justify-between mt-3">
			<Button @click="form.click_save()" label="Speichern"/>
		</div>
	</div>
</template>
