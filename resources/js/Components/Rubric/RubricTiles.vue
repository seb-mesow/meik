<script setup lang="ts">
import Button from 'primevue/button';
import RubricTile from '@/Components/Rubric/RubricTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { IRubricProps } from '@/types/page_props/rubric_overview';
import * as RubricAJAX from '@/types/ajax/rubric';
import { IRubricTilesMainProps } from '@/types/page_props/rubric_tiles';
const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	category_id: string,
	main_props: IRubricTilesMainProps,
}>();
const dialog = useDialog();

const rubrics = ref(props.main_props.rubric_tiles);

// This site was loaded with page_number 0.
// So for the first AJAX the page_number is 1.
let page_number = 1;

let more_exist = true;

let is_loading = false;

const create = () => {
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
		data: {
			rubric: null,
			category: props.category_id,
			on_created: (tile: IRubricProps): void => append_tile(tile),
		},
		onClose: (options) => {
			const data = options?.data;
			if (data) {
				reload()
			}
		}
	});
}

const append_tile = (tile: IRubricProps) => {
	rubrics.value.push(tile);
}

const update_tile = (tile: IRubricProps) => {
	for (const rubric of rubrics.value) {
		if (rubric.id === tile.id) {
			rubric.name = tile.name
			return;
		}
	}
}

const delete_tile = (id: string) => {
	rubrics.value = rubrics.value.filter((rubric_tile: IRubricProps): boolean => rubric_tile.id !== id);
}

const reload = () => {
	page_number = 0;
	load_rubrics();
}

async function load_rubrics(): Promise<void> {
	if (is_loading) {
		return; // Verhindert mehrere Anfragen gleichzeitig
	}
	if (!more_exist) {
		console.log(`loaded ${rubrics.value.length} rubrics overall`);
		return;
	}
	is_loading = true;
	return ajax_get_paginated();
}

const ajax_get_paginated = (): Promise<void> => {
	const query_params: RubricAJAX.GetPaginated.IQueryParams = {
		page_number: page_number,
	};
	if (props.category_id) {
		query_params.category_id = props.category_id;
	}
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.rubric.get_paginated'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<RubricAJAX.GetPaginated.I200ResponseData>) => {
			rubrics.value.push(...response.data)
			page_number++;
			more_exist = response.data.length >= props.main_props.count_per_page;
			is_loading = false;
		}
	);
}

const handleScroll = (event: Event) => {
	const container = event.target as HTMLElement;
	// console.log(`trigger: scrollHeight == ${container.scrollHeight}, diff == ${container.scrollTop + container.clientHeight}`);
	const diff: number = container.scrollHeight * 1.0 
						- container.scrollTop * 1.0 
						- container.clientHeight * 1.0;
	// console.log(`trigger: overall diff == ${diff}`);
	const bottom_reached = diff <= 1; // a difference of zero is to restrictive
	if (bottom_reached && !is_loading) {
		// console.log(`trigger: bottom_reached == ${bottom_reached}, is_loading == ${is_loading}`);
		load_rubrics();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
	}
}

</script>

<template>
	<div class="flex h-full">
		<!-- Wrapper für den Scroll-Bereich -->
		<div class="overflow-y-auto flex flex-wrap flex-grow content-start" @scroll="handleScroll($event)">
			<RubricTile v-for="rubric in rubrics" :key="rubric.id" 	
				:rubric="rubric"
				:category_id="props.category_id"
				@delete_tile="delete_tile"
			/>
		</div>
		
		<div class="absolute bottom-4 right-4">
			<Button @click="create">Neu</Button>
		</div>
	</div>
</template>
