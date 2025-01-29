<script setup lang="ts">
import { route } from 'ziggy-js';

import Button from 'primevue/button';
import ExhibitTile from '@/Components/Exhibit/ExhibitTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IExhibitOverviewInitPageProps } from '@/types/page_props/exhibit_overview';
import Breadcrumb from 'primevue/breadcrumb';
import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { ref } from 'vue';

const props = defineProps<{
	init_props: IExhibitOverviewInitPageProps,
	rubric: any,
}>();
console.log(props.init_props)

let page = ref(0);
const pageSize = ref(50);
let isLoading = ref(false);

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};

let items = [
	{
		label: 'Exponate',
		url: route('exhibit.overview'),
	}
];

if (props.rubric) {
	items = [
		{
			label: 'Kategorien',
			url: route('category.overview')
		},
		{
			label: props.rubric.category,
			url: route('rubric.overview', { category: props.rubric.category ?? '' })
		},
		{
			label: props.rubric.name,
			url: route('exhibit.overview', { rubric: props.rubric.id }),
		}
	];
}

const load_exhibits = (): void => {
	if (isLoading.value) return;  // Verhindert mehrere Anfragen gleichzeitig
	isLoading.value = true;

	ajax_get_paginated()
}

const ajax_get_paginated = (): Promise<void> => {
	const request_config: AxiosRequestConfig = {
		method: "get",
		url: route('ajax.exhibit.get_paginated'),
		params: {
			rubric: props.rubric.id,
			page: page.value,
			page_size: pageSize.value
		}
	};
	return axios.request(request_config).then(
		(response: AxiosResponse) => {
			isLoading.value = false;
			if (page.value === 0) {
				exhibits.value = []
				exhibits.value = response.data.exhibits
			} else {
				exhibits.value.push(...response.data.exhibits)
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

const exhibits = ref(props.init_props.exhibits);
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
