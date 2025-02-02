<script setup lang="ts">
import { route } from 'ziggy-js';

import Button from 'primevue/button';
import ExhibitTile from '@/Components/Exhibit/ExhibitTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IExhibitOverviewPageProps } from '@/types/page_props/exhibit_overview';
import Breadcrumb from 'primevue/breadcrumb';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { ref } from 'vue';
import { IGetExhibitsPaginated200ResponseData, IGetExhibitsPaginatedQueryParams } from '@/types/ajax/exhibit';

const props = defineProps<{
	breadcrumb: {
		category?: {
			id: string,
			name: string,
		},
		rubric?: {
			id: string,
			name: string,
		}
	},
	main: IExhibitOverviewPageProps,
}>();

const exhibits = ref(props.main.exhibits);

// This site was loaded with page_number 0.
// So for the first AJAX the page_number is 1.
let page_number = 1;

let more_exhibits_exist = true;

let is_loading = false;

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};

let items: { label: string, url?: string }[] = [
	{
		label: 'Exponate',
		url: route('exhibit.overview'),
	}
];

if (props.breadcrumb.category) {
	items = [];
	items.push({
		label: props.breadcrumb.category.name,
		url: route('rubric.overview', { category_id: props.breadcrumb.category.id })
	});
}
if (props.breadcrumb.rubric) {
	items.push({
		label: props.breadcrumb.rubric.name,
		// url: route('exhibit.overview', { rubric: props.breadcrumb.rubric.id }),
	});
}

function load_exhibits(): void {
	if (is_loading) {
		return; // Verhindert mehrere Anfragen gleichzeitig
	}
	if (!more_exhibits_exist) {
		console.log(`loaded ${exhibits.value.length} exhibits overall`);
		return;
	}
	is_loading = true;
	ajax_get_paginated();
}

async function ajax_get_paginated(): Promise<void> {
	const query_params: IGetExhibitsPaginatedQueryParams = {
		page_number: page_number,
		count_per_page: props.main.count_per_page,
	};
	if (props.main.rubric) {
		query_params.rubric_id = props.main.rubric.id;
	}
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.exhibit.get_paginated'),
		params: query_params,
	};
	return axios.request(request_config).then(
		(response: AxiosResponse<IGetExhibitsPaginated200ResponseData>) => {
			exhibits.value.push(...response.data)
			page_number++;
			more_exhibits_exist = response.data.length >= props.main.count_per_page;
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
			<div class="fixed bottom-4 right-4">
				<Button severity="info" as="a" :href="route('exhibit.new')" icon="pi pi-plus" />
			</div>

			<div class="overflow-y-auto flex flex-wrap flex-grow content-start" @scroll="handleScroll($event)">
				<ExhibitTile v-for="exhibit in exhibits" :key="exhibit.id" :exhibit="exhibit" />
			</div>
		</div>
	</AuthenticatedLayout>
</template>
