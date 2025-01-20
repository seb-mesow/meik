<script setup lang="ts">
import Button from 'primevue/button';
import RubricTile from '@/Components/Rubric/RubricTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IRubricForTile } from '@/types/meik/models';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';

const props = defineProps<{
	rubrics: IRubricForTile[],
	category_name: string
}>();

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
</script>
<!-- TODO: Hier muss noch ein Infinite Scroll + nachladen rein -->
<template>

	<Head title="Kategorien" />
	<AuthenticatedLayout>

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
		
		<div class="flex flex-wrap">
			<RubricTile v-for="rubric in rubrics" :rubric="rubric" />
		</div>
	</AuthenticatedLayout>
</template>
