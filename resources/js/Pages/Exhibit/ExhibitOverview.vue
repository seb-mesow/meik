<script setup lang="ts">
import { route } from 'ziggy-js';

import Button from 'primevue/button';
import ExhibitTile from '@/Components/Exhibit/ExhibitTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IExhibitOverviewPageProps } from '@/types/page_props/exhibit_overview';
import Breadcrumb from 'primevue/breadcrumb';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { ref } from 'vue';

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
console.log(props.main)

let page = ref(0);
const pageSize = ref(50);
let isLoading = ref(false);

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

const load_exhibits = (): void => {
	if (isLoading.value) return;  // Verhindert mehrere Anfragen gleichzeitig
	isLoading.value = true;

	ajax_get_paginated()
}

const ajax_get_paginated = (): Promise<void> => {
	const query_params: { page: number, page_size: number, rubric?: string } = {
		page: page.value,
		page_size: pageSize.value,
	};
	if (props.main.rubric) {
		query_params.rubric = props.main.rubric.id;
	}
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.exhibit.get_paginated'),
		params: query_params
	};
	return axios.request(request_config).then(
		(response: AxiosResponse) => {
			isLoading.value = false;
			if (page.value === 0) {
				exhibits.value = []
				exhibits.value = response.data
			} else {
				exhibits.value.push(...response.data)
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
		load_exhibits();  // Lädt die nächste Seite, wenn der Benutzer den unteren Rand erreicht
	}
}

const exhibits = ref(props.main.exhibits);
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
				</template>Route
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
