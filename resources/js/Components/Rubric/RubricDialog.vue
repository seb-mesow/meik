<script lang="ts" setup>
import { IRubricForm, RubricForm } from "@/form/rubricform";
import Button from "primevue/button";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { inject, onMounted, reactive, Reactive } from "vue";
import InputField from "../Form/InputField.vue";

const dialogRef: any = inject('dialogRef');

const confirm_service = useConfirm();
const toast_service = useToast();

const params = dialogRef.value.data;

const form: Reactive<IRubricForm> = reactive(new RubricForm({
    id: params.rubric?.id,
	name: params.rubric?.name,
    category: params.category,
	toast_service: toast_service,
	confirm_service: confirm_service,
	dialog_ref: dialogRef
	}));

</script>

<template>
    <div>
		<InputField label="Name" :form="form.name"/>
		<div class="flex justify-between mt-3">
			<Button @click="form.save()" label="Speichern" />
		</div>
	</div>
</template>
