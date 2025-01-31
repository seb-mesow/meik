<script lang="ts" setup>
import { IRubricForm, RubricForm } from "@/form/rubricform";
import Button from "primevue/button";
import { inject, reactive, Reactive } from "vue";
import InputField from "../Form/InputField.vue";

const dialogRef: any = inject('dialogRef');

const params = dialogRef.value.data;

const form: Reactive<IRubricForm> = reactive(new RubricForm({
	id: params.rubric?.id,
	name: {
		val: params.rubric?.name,
		errs: [],
	},
	category: params.category,
	on_created: params.on_created,
	dialog_ref: dialogRef
}));
</script>

<template>
	<div>
		<InputField label="Name" :form="form.name"/>
		<div class="flex justify-between mt-3">
			<Button @click="form.save()" label="Speichern"/>
		</div>
	</div>
</template>
