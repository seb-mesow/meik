<script setup lang="ts">
import { route } from 'ziggy-js';

import Button from 'primevue/button';
import ExhibitTile from '@/Components/Exhibit/ExhibitTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IExhibitOverviewInitPageProps } from '@/types/page_props/exhibit_overview';
import Breadcrumb from 'primevue/breadcrumb';

const props = defineProps<{
	init_props: IExhibitOverviewInitPageProps,
}>();

const home = {
	icon: 'pi pi-home',
	url: route('exhibit.overview'),
};
const items = [
	{
		label: 'Exponate',
		url: route('exhibit.overview'),
	}
];
const exhibits = props.init_props.exhibits;
</script>

<template>
	<AuthenticatedLayout>
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

		<div class="fixed bottom-4 right-4">
			<Button severity="info" as="a" :href="route('exhibit.new')" icon="pi pi-plus"/>
		</div>
		
		<div class="flex flex-wrap">
			<!-- <div class="border-black border-solid border-2 w-[20%] min-w-[20rem]" v-for="exhibit in exhibits"> -->
			<ExhibitTile v-for="exhibit in exhibits" :exhibit="exhibit" />
			<!-- </div> -->
		</div>
	</AuthenticatedLayout>
</template>
