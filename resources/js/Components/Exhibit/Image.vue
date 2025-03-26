<script setup lang="ts">
import { UIImageForm } from '@/form/special/multiple/image-form';
import Button from 'primevue/button';
import ToggleButton from 'primevue/togglebutton';
import InputText from '@/Components/Form/Wrapper/InputText.vue';
import DarkMode from '@/util/dark-mode';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: Readonly<UIImageForm>
}>();

// Vue.js Lessons Learned:
//
// Wenn man Daten für eine Array von Komponenten verändert, dann bleiben die GUI-Komponenten als solcher erhalten
// Sie erhalten aber neue Daten und werden nur "geupdated".
// - Dabei wird die Logik des Templates neu ausgeführt.
// - Werte/Objekte die keine Properties sind bleiben gleich! Weil die setup()-Funktion nicht nochmal ausgeführt wird.
//
// EventListener immer besser über den @-Shortcut setzen als über .addEventListener()

</script>

<template>
	<div>
		
		<div class="w-full aspect-[16/9]">
			<img v-if="form.file_url.value"
				class="h-full w-full object-contain"
				:src="form.file_url.value"
				draggable="true"
				@dragover="form.on_dragover"
				@drop="form.on_drop"
			>
			<div v-else
				class="h-full w-full border-4 border-dashed border-black dark:border-white grid justify-items-center items-center"
				@dragover="form.on_dragover"
				@drop="form.on_drop"
			>
				<div class="text-center">
					<i class="drop-zone-icon pi pi-upload"/>
					<p>Bild hierein ziehen</p>
					<p>oder klicken</p>
				</div>
			</div>
		</div>
		
		<div class="ps-1 pb-1 pe-1">
		<div class="pt-3 flex gap-x-3">
			<div class="flex-grow">
				<label :for="form.description.html_id">Beschreibung</label>
				<InputText :form="form.description" />
			</div>
			<ToggleButton
				raised
				class="w-28 is_public-button"
				:class="{
					'is_public-button--public': form.is_public.ui_value_in_editing.value,
					'is_public-button--internal': !form.is_public.ui_value_in_editing.value
				}"
				style="grid-area: 3 / 2 / 3 / 2;"
				onLabel='öffentlich'
				offLabel='intern'
				:modelValue="form.is_public.ui_value_in_editing.value"
				@update:modelValue="(v: boolean) => form.is_public.on_change_ui_value_in_editing(v)"
			/>
		</div>
		
		<div class="buttons">
			<Button
				@click="form.click_save()"
				:disabled="!(form.has_changes.value)"
				:loading="form.is_save_button_loading.value"
				severity="primary"
				label="Speichern"
				raised
			/>
			<Button
				@click="form.click_delete()"
				:loading="form.is_delete_button_loading.value"
				label="Löschen"
				severity="danger"
				raised
			/>
		</div>
		</div>
		
	</div>
</template>

<style lang="css" scoped>
.drop-zone {
	border: 0.2rem dashed;
	border-color: rgb(0, 0, 0);
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
