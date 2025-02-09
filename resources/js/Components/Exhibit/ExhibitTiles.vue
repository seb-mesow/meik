<script lang="ts" setup>
import { route } from 'ziggy-js';
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { ref } from 'vue';
import { AxiosRequestConfig, AxiosResponse } from 'axios';
import axios from 'axios';
import { IExhibitTilesMainProps } from '@/types/page_props/exhibit_tiles';
import ExhibitTile from './ExhibitTile.vue';
import Button from 'primevue/button';

const props = defineProps<{
	rubric_id?: string,
	main_props: IExhibitTilesMainProps,
}>();

const exhibits = ref(props.main_props.exhibit_tiles);

// This site was loaded with page_number 0.
// So for the first AJAX the page_number is 1.
let page_number = 1;

let more_exhibits_exist = true;

let is_loading = false;

async function load_exhibits(): Promise<void> {
	if (is_loading) {
		return; // Verhindert mehrere Anfragen gleichzeitig
	}
	if (!more_exhibits_exist) {
		console.log(`loaded ${exhibits.value.length} exhibits overall`);
		return;
	}
	is_loading = true;
	return ajax_get_paginated();
}

async function ajax_get_paginated(): Promise<void> {
	const query_params: ExhibitAJAX.GetTilesPaginated.IQueryParams = {
		page_number: page_number,
	};
	if (props.rubric_id) {
		query_params.rubric_id = props.rubric_id;
	}
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.exhibit.get_tiles_paginated'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<ExhibitAJAX.GetTilesPaginated.I200ResponseData>) => {
			exhibits.value.push(...response.data)
			page_number++;
			more_exhibits_exist = response.data.length >= props.main_props.count_per_page;
			is_loading = false;
		}
	);
}

function handleScroll(event: Event): void {
	const container = event.target as HTMLElement;
	// console.log(`trigger: scrollHeight == ${container.scrollHeight}, diff == ${container.scrollTop + container.clientHeight}`);
	const diff: number = container.scrollHeight * 1.0 
						- container.scrollTop * 1.0 
						- container.clientHeight * 1.0;
	// console.log(`trigger: overall diff == ${diff}`);
	const bottom_reached = diff <= 1; // a difference of zero is to restrictive
	if (bottom_reached && !is_loading) {
		// console.log(`trigger: bottom_reached == ${bottom_reached}, is_loading == ${is_loading}`);
		load_exhibits();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
	}
}
</script>

<template>
	<div class="flex h-full">
		<div class="overflow-y-auto flex flex-wrap flex-grow content-start" @scroll="handleScroll($event)">
			<ExhibitTile v-for="exhibit in exhibits" :key="exhibit.id" :exhibit="exhibit" />
		</div>
		
		<div class="fixed bottom-4 right-4">
			<Button severity="info" as="a" :href="route('exhibit.new', { rubric_id: props.rubric_id })" icon="pi pi-plus" />
		</div>
	</div>
</template>
