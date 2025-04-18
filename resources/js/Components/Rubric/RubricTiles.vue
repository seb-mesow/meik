<script setup lang="ts">
import Button from 'primevue/button';
import RubricTile from '@/Components/Rubric/RubricTile.vue';
import { defineAsyncComponent, onBeforeUnmount, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import * as RubricAJAX from '@/types/ajax/rubric';
import { IRubricTileProps, IRubricTilesMainProps } from '@/types/page_props/rubric_tiles';
import { IRubricFormConstructorArgs } from '@/form/special/multiple/rubric-form';

const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	main_props: IRubricTilesMainProps,
	category_id?: string,
}>();

const rubrics = ref(props.main_props.rubric_tiles);



// ----- Dialog -------------------------------------------------------------------

const dialog = useDialog();

function create_dialog() {
	let preset: any = undefined;
	if (props.category_id) {
		preset = preset ?? {};
		preset.category_id = props.category_id;
	};
	const form_args: Omit<IRubricFormConstructorArgs, 'dialog_ref' | 'selectable_categories'> = {
		preset: preset,
		on_rubric_created: (tile: IRubricTileProps): void => append_tile(tile),
	};

	dialog.open(RubricDialog, {
		props: {
			header: 'Rubrik anlegen',
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
		onClose: (options) => {
			// const data = options?.data;
			// if (data) {
			// 	reload()
			// }
		}
	});
}



// ----- Tiles-Verwaltung --------------------------------------------------------

function append_tile(tile: IRubricTileProps): void {
	rubrics.value.push(tile);
}

function update_tile(tile: IRubricTileProps): void {
	for (const rubric of rubrics.value) {
		if (rubric.id === tile.id) {
			rubric.name = tile.name
			return;
		}
	}
}

const delete_tile = (id: string) => {
	rubrics.value = rubrics.value.filter((rubric_tile: IRubricTileProps): boolean => rubric_tile.id !== id);
}



// ----- Infinite Scrolling ------------------------------------------------------------

// This site was loaded with page_number 0.
// So for the first AJAX the page_number is 1.
let page_number = 1;
let more_exist = true;
let is_loading = false;

async function load_rubrics(): Promise<void> {
	if (is_loading) {
		console.log("is_loading");
		return; // Verhindert mehrere Anfragen gleichzeitig
	}
	if (!more_exist) {
		console.log(`loaded ${rubrics.value.length} rubrics overall`);
		return;
	}
	is_loading = true;
	return ajax_get_paginated();
}

async function ajax_get_paginated(): Promise<void> {
	const query_params: RubricAJAX.Query.IQueryParams = {
		page_number: page_number,
		count_per_page: props.main_props.count_per_page,
	};
	if (props.category_id) {
		query_params.category_id = props.category_id;
	}
	const request_config: AxiosRequestConfig<never> = {
		method: "get",
		url: route('ajax.rubric.query'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<RubricAJAX.Query.I200ResponseData>) => {
			rubrics.value.push(...response.data.rubrics);
			page_number++;
			more_exist = response.data.rubrics.length >= props.main_props.count_per_page;
			is_loading = false;
		}
	);
}

const handleScroll = (event: Event) => {
	console.log("handleScroll");
	const container = event.target as HTMLElement;
	const diff: number = document.body.scrollHeight - (window.innerHeight + window.scrollY)
	// console.log(`trigger: overall diff == ${diff}`);
	const bottom_reached = diff <= 1; // a difference of zero is to restrictive
	if (bottom_reached && !is_loading) {
		console.log(`trigger: bottom_reached == ${bottom_reached}, is_loading == ${is_loading}`);
		load_rubrics();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
	}
}

onMounted(() => {
	window.addEventListener('scroll', handleScroll);
	window.addEventListener('resize', handleScroll);
});
onBeforeUnmount(() => {
	window.removeEventListener('scroll', handleScroll);
	window.removeEventListener('resize', handleScroll);
});
</script>

<template>
	<!-- Hier keine Card oder andere (sichtbare) Umrandung drum! -->
	<!-- Irgendwas mit overflow ist hier nicht nötig. -->
	<div class="flex flex-col">
		<span class="pb-4 pl-4 text-3xl">Rubriken</span>
		<div class="tile-container" @scroll="handleScroll($event)">
			<RubricTile v-for="rubric in rubrics" :key="rubric.id" :rubric="rubric" @delete_tile="delete_tile" />
		</div>
	</div>
	<div class="fixed bottom-4 right-4">
		<Button severity="primary" raised @click="create_dialog" icon="pi pi-plus" />
	</div>
</template>
