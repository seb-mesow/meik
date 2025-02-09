<script setup lang="ts">
import Button from 'primevue/button';
import RubricTiles from '@/Components/Rubric/RubricTiles.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { IRubricProps } from '@/types/page_props/rubric_overview';
import * as RubricAJAX from '@/types/ajax/rubric';
const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	category: {
		id: string,
		name: string,
	},
	rubric_tiles_main_props: {
		rubric_tiles: IRubricProps[],
		count_per_page: number,
	},
}>();
console.log(`Category/Category.vue: props ==`)
console.log(props);

const dialog = useDialog();

let rubricArray = ref(props.rubric_tiles_main_props.rubric_tiles);

let page = ref(0);
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
	rubricArray.value.push(tile);
}

const update_tile = (tile: IRubricProps) => {
	for (const rubric of rubricArray.value) {
		if (rubric.id === tile.id) {
			rubric.name = tile.name
			return;
		}
	}
}

const delete_tile = (id: string) => {
	rubricArray.value = rubricArray.value.filter((rubric_tile: IRubricProps): boolean => rubric_tile.id !== id);
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
	const query_params: RubricAJAX.GetPaginated.IQueryParams = {
		category_id: props.category.id,
		page_number: page.value
	};
	
	const request_config: AxiosRequestConfig<RubricAJAX.GetPaginated.IRequestData> = {
		method: "get",
		url: route('ajax.rubric.get_paginated'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<RubricAJAX.GetPaginated.I200ResponseData>) => {
			isLoading.value = false;
			if (page.value === 0) {
				rubricArray.value = []
				rubricArray.value = response.data
				// total_count = response.data.total_count
				console.log('replace', rubricArray.value)
			} else {
				rubricArray.value.push(...response.data)
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
		
		<RubricTiles :main_props="props.rubric_tiles_main_props" :category_id="props.category.id" />
		
	</AuthenticatedLayout>
</template>
