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
const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	rubrics: IRubricForTile[],
	category_name: string
}>();

const dialog = useDialog();

let rubricArray = ref(props.rubrics)
let page = ref(0);
let pageSize = ref(50);
let isLoading = ref(false);

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
const items = [
	{
		label: 'Kategorien',
		url: route('category.overview')
	},
	{
		label: props.category_name,
		url: route('rubric.overview', props.category_name),
	}
];

const create = () => {
	const dialogRef = dialog.open(RubricDialog, {
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
			category: props.category_name
		},
		onClose: (options) => {
			const data = options?.data;
			if (data) {
				reload()
			}
		}
	});

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
			category: props.category_name,
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
				console.log('replace', rubricArray.value)
			} else {
				rubricArray.value.push(...response.data.rubrics)
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
			<div class="overflow-y-auto flex flex-wrap flex-grow">
				<RubricTile @reload="reload" v-for="rubric in rubricArray" :key="rubric.id" :category="category_name"
					:rubric="rubric" />
			</div>
			<div class="absolute bottom-4 right-4">
				<Button @click="create">Neu</Button>
			</div>
		</div>
	</AuthenticatedLayout>
</template>