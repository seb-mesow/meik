<script setup lang="ts">
import { nextTick, ref } from 'vue';
import Popover from 'primevue/popover';
import Button from 'primevue/button';
import axios from 'axios';
import { AxiosRequestConfig } from 'axios';

// versch. Interface für typsicheres Programmieren
export interface AJAXConfirmationPopupCallbacks {
	fullfied?: () => void;
	rejected?:() => void;
}

// (interne) Attribute der Komponente
const popover = ref();
const popover_dismissable = ref(true);
const is_buttons = ref(false);
const is_loading = ref(false);
const is_success = ref(false);
const is_error = ref(false);

let target: EventTarget|null = null;
let _request_cfg: AxiosRequestConfig = {};
let _callbacks: AJAXConfirmationPopupCallbacks = {};

// so etwas wie der Konstruktor dieser Komponente
async function show(event: Event, request_cfg: AxiosRequestConfig, callbacks: AJAXConfirmationPopupCallbacks) {
	// Es kann immer nur ein Popup auf einem Target angezeigt werden
	if(is_visible()) {
		const previous_target = get_target();
		// sichtbar & anderes Target -> vorher Popup für altes Target schließen wie beim Abbrechen
		reject_by_button();
		if(event.target === previous_target) {
			// sichtbar & identisches Target -> NUR einfach schließen wie beim Abbrechen
			return;
		}
	}
	
	target = event.target;
	_request_cfg = request_cfg;
	_callbacks = callbacks;
	
	is_buttons.value = true;
	is_loading.value = false;
	is_success.value = false;
	is_error.value = false;
	
	await nextTick();
	popover.value.show(event);
}

// so etwas wie der Destruktor dieser Komponente
function destruct() {
	target = null;
	_callbacks = {};
	_request_cfg= {};
	
	is_buttons.value = false;
	is_loading.value = false;
	is_success.value = false;
	is_error.value = false;
}

function is_visible() {
	return is_buttons.value || is_loading.value || is_success.value || is_error.value;
}

function get_target(): EventTarget|null {
	return target;
}

async function call_ajax() {
	try {
		is_loading.value = true;
		
		console.log(_request_cfg);
		await axios.request(_request_cfg);
		
		is_loading.value = false;
		is_buttons.value = false;
		is_success.value = true;
		
		_callbacks.rejected = undefined;
		if (_callbacks.fullfied) {
			_callbacks.fullfied();
			_callbacks.fullfied = undefined;
		}
		
		setTimeout(() => {
			destruct();
			popover.value.hide();
		}, 1000); // Erfolgreich-Meldung 1 Sekunde lang anzeigen
		
	} catch(err) {
		console.log(err);
		
		is_loading.value = false;
		is_buttons.value = false;
		is_error.value = true;
		_callbacks = {}; // In und nach dem Fehlerzustand keine Callbacks ausführen
		
		setTimeout(() => {
			destruct();
			popover.value.hide();
		}, 3000); // Fehler-Meldung 3 Sekunden lang anzeigen
	}
}

// ausgelöst durch Klicken auf "Abbrechen"-Button
function reject_by_button() {
	on_hide_event();
	popover.value.hide();
}

// ausgelöst durch Klicken außerhalb des Popups
function on_hide_event() {
	// bereits popover.value.hide() ausgeführt
	_callbacks.fullfied = undefined;
	if (_callbacks.rejected) {
		_callbacks.rejected();
		_callbacks.rejected = undefined;
	} // else Reject nicht mehr möglich, da AJAX bereits erfolgreich
	destruct();
}
// öffentliche Methoden der Komponente deklarieren
defineExpose({
	show,
	hide: reject_by_button,
	is_visible,
	get_target,
});

</script>

<template>
	<div v-if="is_loading" class="popup-background"/>
	<Popover ref="popover"
		:dismissable="popover_dismissable"
		@hide="on_hide_event"
	>
		<div class="flex flex-col gap-4 w-[25rem]">
			<div v-if="is_buttons">
				<slot name="question">
					<span>Sicher?</span>
				</slot>
			</div>
			<div class="flex justify-evenly" v-if="is_buttons">
				<Button label="Ja" :loading="is_loading" @click="call_ajax"/>
				<Button label="Abbrechen" @click="reject_by_button"/>
			</div>
			<div v-if="is_success">
				<slot name="success">
					<span>erfolgreich</span>
				</slot>
			</div>
			<div v-if="is_error">
				<slot name="error">
					<span>Leider ist ein Fehler aufgetreten.</span>
				</slot>
			</div>
		</div>
	</Popover>
	<!-- Der unsichtbare Overlay verhindert, dass während des Ladens etwas geklickt werden kann. -->
	<div v-if="is_loading" class="popup-overlay" @click.stop/>
</template>

<style lang="css">
.popup-background {
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	background-color: rgba(0,0,0,0.5);
}
.popup-overlay {
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	z-index: 90000;
	background-color: transparent;
}
</style>
