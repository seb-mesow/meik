<script setup lang="ts">
import { ICategoryTileProps } from '@/types/page_props/category_overview';
import { route } from 'ziggy-js';

// (interne) Attribute der Komponente
const props = defineProps<{
	category: ICategoryTileProps;
}>();
</script>

<template>
	<a :href="route('category.details', { category_id: props.category.id })">
		<div class="tile category-tile shadow-md dark:shadow-sm shadow-gray-400 flex justify-start items-center">
			<span class="text-2xl">{{ props.category.name }}</span>
		</div>
	</a>
</template>

<style lang="css" scoped>
.category-tile {
	width: 30em;
	height: 8.5rem;
	border-radius: 1rem;
	padding: 1rem;
}
</style>
