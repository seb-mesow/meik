<script lang="ts" setup>
import { IRubricForm, RubricForm } from "@/form/rubricform";
import Button from "primevue/button";
import { inject, reactive, Reactive } from "vue";
import InputTextField2 from "../Form/InputTextField2.vue";

const dialogRef: any = inject('dialogRef');

const params = dialogRef.value.data;

const form: Reactive<IRubricForm> = reactive(new RubricForm({
	id: params.rubric?.id,
	name: {
		val: params.rubric?.name,
		errs: [],
	},
	category_id: params.category_id,
	on_created: params.on_created,
	dialog_ref: dialogRef
}));
</script>

<template>
	<div>
		<InputTextField2 :form="form.name" label="Name" />
		<div class="flex justify-between mt-3">
			<Button @click="form.save()" label="Speichern"/>
		</div>
	</div>
</template>
