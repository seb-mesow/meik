<script setup lang="ts">
import RubricTiles from '@/Components/Rubric/RubricTiles.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { provide, ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';
import { route } from 'ziggy-js';
import DynamicDialog from 'primevue/dynamicdialog';
import { IRubricTilesMainProps } from '@/types/page_props/rubric_tiles';
import { ICategory } from '@/form/special/multiple/rubric-form';
import ConfirmDialog from 'primevue/confirmdialog';

const props = defineProps<{
	selectable_categories: ICategory[],
	rubric_tiles_main_props: IRubricTilesMainProps,
	category: {
		id: string,
		name: string,
	},
}>();

console.log(`Category/Category.vue: props ==`)
console.log(props);

provide('selectable_categories', props.selectable_categories);

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

</script>

<template>
	<DynamicDialog />
	<ConfirmDialog :draggable="false"/>
	
	<Head title="Kategorien" />
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
		
		<RubricTiles :main_props="props.rubric_tiles_main_props" :category_id="props.category.id" />
		
	</AuthenticatedLayout>
</template>
