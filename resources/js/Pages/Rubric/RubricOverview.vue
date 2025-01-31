<script setup lang="ts">
import Button from 'primevue/button';
import RubricTile from '@/Components/Rubric/RubricTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IRubricForTile } from '@/types/meik/models';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { IRubricTileProps } from '@/types/page_props/rubric_overview';
const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	category: {
		id: string,
		name: string,
	},
	rubrics: IRubricTileProps[],
	// total_count: number
}>();

const dialog = useDialog();

let rubricArray = ref(props.rubrics)
let page = ref(0);
const pageSize = ref(50);
let isLoading = ref(false);
// let total_count = ref(props.total_count)

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
const items = [
	{
		label: props.category.name,
		// url: route('rubric.overview', { category_id: props.category.id }),
	}
];

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
			category: props.category.id,
			on_created: (tile: IRubricTileProps): void => append_tile(tile),
		},
		onClose: (options) => {
			const data = options?.data;
			if (data) {
				reload()
			}
		}
	});
}

const append_tile = (tile: IRubricTileProps) => {
	rubricArray.value.push(tile);
}

const update_tile = (tile: IRubricTileProps) => {
	for (const rubric of rubricArray.value) {
		if (rubric.id === tile.id) {
			rubric.name = tile.name
			return;
		}
	}
}

const delete_tile = (id: string) => {
	rubricArray.value = rubricArray.value.filter((rubric_tile: IRubricTileProps): boolean => rubric_tile.id !== id);
}

const reload = () => {
	page.value = 0
	load_rubrics()
}

const load_rubrics = (): void => {
	if (isLoading.value) return;  // Verhindert mehrere Anfragen gleichzeitig
	isLoading.value = true;

	ajax_get_paginated()
}

const ajax_get_paginated = (): Promise<void> => {
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.rubric.get_paginated'),
		params: {
			category: props.category.id,
			page: page.value,
			page_size: pageSize.value
		}
	};
	return axios.request(request_config).then(
		(response: AxiosResponse) => {
			isLoading.value = false;
			if (page.value === 0) {
				rubricArray.value = []
				rubricArray.value = response.data.rubrics
				// total_count = response.data.total_count
				console.log('replace', rubricArray.value)
			} else {
				rubricArray.value.push(...response.data.rubrics)
				// total_count = response.data.total_count
				console.log('append')
			}
		}
	);
}

const handleScroll = (event: Event) => {
	const container = event.target as HTMLElement;
	const bottomReached = container.scrollHeight === container.scrollTop + container.clientHeight;
	console.log('trigger', bottomReached, !isLoading.value)
	if (bottomReached && !isLoading.value) {

		page.value = page.value + 1;
		load_rubrics();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
	}
}

</script>
<!-- TODO: Hier muss noch ein Infinite Scroll + nachladen rein -->
<template>

	<Head title="Kategorien" />
	<AuthenticatedLayout :disable_overflow="true">

		<template #header>
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		<div class="flex h-full">
			<!-- Wrapper für den Scroll-Bereich -->
			<div class="overflow-y-auto flex flex-wrap flex-grow content-start" @scroll="handleScroll($event)">
				<RubricTile v-for="rubric in rubricArray" :key="rubric.id" 	
					:rubric="rubric"
					:category="props.category.id"
					@delete_tile="delete_tile"
				/>
			</div>
			<div class="absolute bottom-4 right-4">
				<Button @click="create">Neu</Button>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
