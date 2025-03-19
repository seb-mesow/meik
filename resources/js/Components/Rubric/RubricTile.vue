<script setup lang="ts">
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import Button from 'primevue/button';
import { defineAsyncComponent, ref } from 'vue';
const RubricDialog = defineAsyncComponent(() => import('./RubricDialog.vue'));
import axios, { AxiosRequestConfig } from "axios";
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { ICloseOptions, IRubricFormConstructorArgs } from '@/form/special/multiple/rubric-form';
import { IRubricTileProps } from '@/types/page_props/rubric_tiles';

const confirm_service = useConfirm();
const emit = defineEmits({
	delete_tile: (id: string) => {},
});

// (interne) Attribute der Komponente
const props = defineProps<{
	rubric: {
		id: string,
		name: string,
		category_id: string,
	},
}>();
const rubric = props.rubric;
const rubric_id = ref(rubric.id);
const rubric_name = ref(rubric.name);
const rubric_category_id = ref(rubric.category_id);
	
console.log(`rubric_id == ${rubric_id.value}`);
console.log(`rubric_name == ${rubric_name.value}`);
	
const dialog = useDialog();
const toast_service = useToast();

function create_dialog() {
	const form_args: Omit<IRubricFormConstructorArgs,'dialog_ref'|'selectable_categories'> = {
		data: {
			id: rubric_id.value,
			name: rubric_name.value,
			category_id: rubric_category_id.value,
		},
		on_rubric_updated: (tile: IRubricTileProps) => {
			rubric_id.value = tile.id;
			rubric_name.value = tile.name;
		},
	};
	
	dialog.open(RubricDialog, {
		props: {
			header: 'Rubrik bearbeiten',
			style: {
				width: '50vw',
			},
			breakpoints: {
				'960px': '75vw',
				'640px': '90vw'
			},
			modal: true,
		},
		data: form_args,
		onClose: (opts: ICloseOptions) => {
			const data = opts.data;
			if (data) {
				rubric_id.value = data.id;
				rubric_name.value = data.name;
				rubric_category_id.value = data.category_id;
			}
		}
	});
}

const delete_rubric = (event: any): Promise<void> => {
	return new Promise((resolve: () => void, reject: () => void) => {
		confirm_service.require({
			target: event.currentTarget,
			message: "Sind Sie sicher das Sie die Rubrik löschen wollen?",
			icon: 'pi pi-exclamation-triangle',
			rejectProps: {
				label: 'Abbrechen',
				severity: 'secondary',
				outlined: true
			},
			acceptProps: {
				label: 'Bestätigen'
			},
			accept: () => {
				accept_delete().then(resolve, reject)
			},
			reject: reject
		});
	});
};

const accept_delete = (): Promise<void> => {
	return new Promise((resolve: () => void, reject: () => void) => {
		ajax_delete().then(
			() => {
				toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Die Rubrik wurde erfolgreich gelöscht.', life: 3000 });
				emit('delete_tile', rubric_id.value);
				resolve();
			},
			() => {
				toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Die Rubrik konnte nicht gelöscht werden.', life: 3000 });
				reject();
			}
		);
	});
}

const ajax_delete = (): Promise<void> => {
	// if (props.rubric.id) {
		// throw new Error("undefined id");
	// }
	const request_config: AxiosRequestConfig<any> = {
		method: "delete",
		url: route('ajax.rubric.delete', { rubric_id: rubric_id.value })
	}
	return axios.request(request_config);
}
</script>

<template>
	<div class="h-fit">

		<a :href="route('rubric.details', { rubric_id: rubric_id })">
			<div class="tile rubric-tile shadow-md shadow-gray-400 flex justify-between items-center">
				<span class="text-2xl">{{ rubric_name }}</span>
				<div class="flex flex-col gap-3">
					<Button severity="primary" raised class="!p-1" @click.prevent="create_dialog">
						<span class="material-symbols-outlined">edit</span>
					</Button>
					<Button raised class="!p-1" severity="danger" @click.prevent="delete_rubric($event)">
						<span class="material-symbols-outlined">delete</span>
					</Button>
				</div>
			</div>
		</a>
	</div>
</template>

<style lang="css" scoped>
.rubric-tile {
	width: 30em;
	height: 8.5rem;
	border-radius: 1rem;
	padding: 1rem;
}
</style>
