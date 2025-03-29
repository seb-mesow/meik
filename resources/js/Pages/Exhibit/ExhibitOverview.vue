<script lang="ts" setup>
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from 'primevue/breadcrumb';
import ExhibitTiles from '@/Components/Exhibit/ExhibitTiles.vue';
import { IExhibitTilesMainProps } from '@/types/page_props/exhibit_tiles';
import { ref } from 'vue';
import InputText from 'primevue/inputtext';

const props = defineProps<IExhibitTilesMainProps>();

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

const query = ref('')
</script>

<template>
	<AuthenticatedLayout :disable_overflow="true">
		
		<template #header>
			<Breadcrumb class="!bg-gray-100 dark:!bg-gray-800" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>

		<template #searchbar>
			<div class="pr-8">
				<InputText placeholder="Exponate durchsuchen" type="text" v-model="query" />
			</div>
		</template>
		
		<ExhibitTiles :main_props="props" :query="query" />
		
	</AuthenticatedLayout>
</template>
