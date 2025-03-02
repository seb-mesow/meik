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

function on_is_public_button_mouseenter(event: MouseEvent): void {
	if (props.form.is_public.ui_value_in_editing.value) {
		if (DarkMode.is_dark.value) {
			// @ts-expect-error
			event.target.style['background-color'] = 'var(--meik-is-public-bg-color-dark-hover)';
		} else {
			// @ts-expect-error
			event.target.style['background-color'] = 'var(--meik-is-public-bg-color-light-hover)';
		}
	}
}
function on_is_public_button_mouseleave(event: MouseEvent): void {
	if (DarkMode.is_dark.value) {
		// @ts-expect-error
		event.target.style['background-color'] = null;
	} else {
		// @ts-expect-error
		event.target.style['background-color'] = null;
	}
}

</script>

<template>
	<div class="page">
		<img v-if="form.file_url.value"
			class="image"
			:src="form.file_url.value"
			draggable="true"
			@dragover="form.on_dragover"
			@drop="form.on_drop"
		>
		<div v-else
			class="drop-zone"
			@dragover="form.on_dragover"
			@drop="form.on_drop"
		>
			<div class="drop-zone-text">
				<i class="drop-zone-icon pi pi-upload"/>
				<p>Bild hierein ziehen</p>
				<p>oder klicken</p>
			</div>
		</div>
		<div class="flex gap-x-3">
			<div class="flex-grow">
				<label :for="form.description.html_id">Beschreibung</label>
				<InputText :form="form.description" />
			</div>
			<ToggleButton
				class="w-28"
				style="grid-area: 3 / 2 / 3 / 2;"
				onLabel='öffentlich'
				offLabel='intern'
				:modelValue="form.is_public.ui_value_in_editing.value"
				@update:modelValue="(v: boolean) => form.is_public.on_change_ui_value_in_editing(v)"
				:dt="{ colorScheme: {
					light: {
						background: 'var(--meik-is-internal-bg-color-light)', hoverBackground: 'var(--meik-is-internal-bg-color-light-hover)', color: 'black', hoverColor: 'black',
						checkedBackground: 'var(--meik-is-public-bg-color-light)', checkedColor: 'black'
					},
					dark: {
						background: 'var(--meik-is-internal-bg-color-dark)', hoverBackground: 'var(--meik-is-internal-bg-color-dark-hover)', color: 'black', hoverColor: 'black', 
						checkedBackground: 'var(--meik-is-public-bg-color-dark)', checkedColor: 'black',
					}
				}}"
				@mouseenter="on_is_public_button_mouseenter"
				@mouseleave="on_is_public_button_mouseleave"
			/>
		</div>
		<div class="buttons">
			<Button @click="form.click_save()" label="Speichern"
			:loading="form.is_save_button_loading.value"
			:disabled="!(form.has_changes.value)"
			/>
			<Button @click="form.click_delete()" label="Löschen" severity="danger"
			:loading="form.is_delete_button_loading.value"
			/>
		</div>
		<div>
			<p>{{ form.description.ui_value_in_editing }}</p>
			<p>{{ form.has_changes }}</p>
		</div>
	</div>
</template>

<style lang="css" scoped>
.drop-zone {
	/* margin-left: auto; */
	/* margin-right: auto; */
	/* width: 32rem; */
	/* height: 18rem; */
	border: 0.2rem dashed;
	border-color: rgb(0, 0, 0);
	/* display: flex; */
	/* justify-content: center; */
	/* align-items: center; */
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
