<script setup lang="ts">
import { route } from 'ziggy-js';
import CategoryTile from '@/Components/Category/CategoryTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from 'primevue/breadcrumb';
import { ICategoryTileProps } from '@/types/page_props/category_overview';

const props = defineProps<{
	categories: ICategoryTileProps[]
}>();

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
const items:{ label: string, url?: string }[] = [];
</script>

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
			<!-- <div class="border-black border-solid border-2 w-[20%] min-w-[20rem]" v-for="category in categorys"> -->
			<CategoryTile v-for="category in props.categories" :category="category" />
			<!-- </div> -->
		</div>
	</AuthenticatedLayout>
</template>
