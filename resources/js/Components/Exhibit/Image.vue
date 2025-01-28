<script setup lang="ts">
import { IImageForm } from '@/form/imageform';
import Button from 'primevue/button';
import ToggleButton from 'primevue/togglebutton';
import { onBeforeMount, onBeforeUnmount, onBeforeUpdate, onMounted, onUpdated, reactive, shallowReactive, shallowRef, useTemplateRef, watch } from 'vue';
import InputTextField2 from '../Form/InputTextField2.vue';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: Readonly<IImageForm>
}>();

// Vue.js Lessons Learned:
//
// Wenn man Daten für eine Array von Komponenten verändert, dann bleiben die GUI-Komponenten als solcher erhalten
// Sie erhalten aber neue Daten und werden nur "geupdated".
// - Dabei wird die Logik des Templates neu ausgeführt.
// - Werte/Objekte die keine Properties sind bleiben gleich! Weil die setup()-Funktion nicht nochmal ausgeführt wird.
//
// EventListener immer besser über den @-Shortcut setzen als über .addEventListener()
// 

</script>

<template>
	<div class="page">
		<img v-if="props.form.file_url.value"
			class="image"
			:src="props.form.file_url.value"
			draggable="true"
			@dragover="props.form.on_dragover"
			@drop="props.form.on_drop"
		>
		<div v-else
			class="drop-zone"
			@dragover="props.form.on_dragover"
			@drop="props.form.on_drop"
		>
			<div class="drop-zone-text">
				<i class="drop-zone-icon pi pi-upload"/>
				<p>Bild hierein ziehen</p>
				<p>oder klicken</p>
			</div>
		</div>
		<div class="inputs">
			<InputTextField2 class="description-field" label="Beschreibung"
				:form="props.form.description"
			/>
			<ToggleButton class="w-28" onLabel='öffentlich' offLabel="intern"
				:modelValue="props.form.is_public.val_in_editing"
				@update:modelValue="(v) => props.form.is_public.on_change_val_in_editing(v)"
			/>
		</div>
		<div class="buttons">
			<p>{{ props.form.description.val }}</p>
			<p>{{ props.form.has_changes }}</p>
			<Button @click="props.form.click_save()" label="Speichern"
				:loading="props.form.is_save_button_loading.value"
				:disabled="!(props.form.has_changes.value)"
				/>
			<Button @click="props.form.click_delete()" label="Löschen" severity="danger"
				:loading="props.form.is_delete_button_loading.value"
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
	border-color: rgb(0, 0, 0);
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
