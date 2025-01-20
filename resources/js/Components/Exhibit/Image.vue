<script setup lang="ts">
import { IImageForm } from '@/form/imageform';
import InputField from '../Form/InputField.vue';
import Button from 'primevue/button';
import ToggleButton from 'primevue/togglebutton';
import { onMounted, reactive, watch } from 'vue';
import { formToJSON } from 'axios';
import InputTextField2 from '../Form/InputTextField2.vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: Readonly<IImageForm>
}>();
onMounted(() => {
	props.form.on_mounted();
})
console.log(`Image.vue: props.form ==`);
console.log(props.form);
watch(props.form, (form) => {
	console.log(`form.has_changes == ${form.has_changes}`);
})
</script>

<template>
	<div class="page">
		<img :id="`image-zone-${form.ui_id}`" class="image"
			:src="form.file_url"
			draggable="true"
		>
		<!-- TODO use v-show -->
		<div hidden :id="`drop-zone-${form.ui_id}`" class="drop-zone">
			<div class="drop-zone-text">
				<i class="drop-zone-icon pi pi-upload"/>
				<p>Bild hierein ziehen</p>
				<p>oder klicken</p>
				
			</div>
		</div>
		<div class="inputs">
			<InputTextField2 class="description-field" label="Beschreibung"
				:form="form.description"
			/>
			<ToggleButton class="w-28" onLabel='öffentlich' offLabel="intern"
				:modelValue="form.is_public.val_in_editing"
				@update:modelValue="(v) => form.is_public.on_change_val_in_editing(v)"
			/>
		</div>
		<div class="buttons">
			<p>{{ form.has_changes }}</p>
			<Button @click="form.click_save()" label="Speichern"
				:loading="form.is_save_button_loading"
				/>
				<!-- :disabled="!form.has_changes" -->
			<Button @click="form.click_delete()" label="Löschen" severity="danger"
				:loading="form.is_delete_button_loading"
			/>
		</div>
	</div>
</template>

<style lang="css" scoped>
.drop-zone {
	margin-left: auto;
	margin-right: auto;
	width: 32rem;
	height: 18rem;
	border: 0.2rem dashed;
	border-color: black;
	display: flex;
	justify-content: center;
	align-items: center;
}
.p-dark .drop-zone {
	border-color: white;
}
.drop-zone-text {
	text-align: center;
}
.drop-zone-icon {
	font-size: 300%;
	margin-bottom: 1rem;
}
.page {
	/* overflow: hidden; */
	/* height: 2rem; */
	/* width: 20rem;  */
	/* background-color: lightcoral; */
}
.image {
	margin-left: auto;
	margin-right: auto;
	padding: 1rem
}
.inputs {
	margin-top: 1em;
	display: flex;
	justify-content: space-between;
	gap: 1rem;
}
.description-field {
	flex: 1;
}
.buttons {
	margin-top: 1em;
	/* width: 90%; */
	display: flex;
	justify-content: space-between;
}
</style>
