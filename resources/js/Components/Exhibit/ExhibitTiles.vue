<script lang="ts" setup>
import { route } from 'ziggy-js';
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { AxiosRequestConfig, AxiosResponse } from 'axios';
import axios from 'axios';
import { IExhibitTilesMainProps } from '@/types/page_props/exhibit_tiles';
import ExhibitTile from './ExhibitTile.vue';
import Button from 'primevue/button';

const props = defineProps<{
	rubric_id?: string,
	main_props: IExhibitTilesMainProps,
	query: string
}>();

const exhibits = ref(props.main_props.exhibit_tiles);
let query = props.query
let timeout: any = null;


// ----- Infinite Scrolling -----------------------------------------------

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
	const query_params: ExhibitAJAX.TilesQuery.IQueryParams = {
		page_number: page_number,
		count_per_page: props.main_props.count_per_page,
	};
	if (props.rubric_id) {
		query_params.rubric_id = props.rubric_id;
	}

	if (query) {
		query_params.query = query
	}

	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.exhibit.tiles.query'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<ExhibitAJAX.TilesQuery.I200ResponseData>) => {
			exhibits.value.push(...response.data);
			page_number++;
			more_exhibits_exist = response.data.length >= props.main_props.count_per_page;
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
		load_exhibits();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
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

watch(() => props.query, (newValue) => {
	clearTimeout(timeout); // Vorheriges Timeout abbrechen
	timeout = setTimeout(() => {
		query = newValue
		exhibits.value = []
		page_number = 0
		more_exhibits_exist = true;
		load_exhibits();
	}, 300); // 300ms Verzögerung

});


</script>

<template>
	<!-- Hier keine Card oder andere (sichtbare) Umrandung drum! -->
	<!-- Irgendwas mit overflow ist hier nicht nötig. -->
	<div class="flex flex-col">
		<span class="pb-4 pl-4 text-3xl">Exponate</span>
		<div class="tile-container" @scroll="handleScroll($event)">
			<ExhibitTile v-for="exhibit in exhibits" :key="exhibit.id" :exhibit="exhibit" />
		</div>
	</div>
	<div class="fixed bottom-4 right-4">
		<Button as="a" :href="route('exhibit.new', { rubric_id: props.rubric_id })" severity="primary" raised
			icon="pi pi-plus" />
	</div>
</template>
