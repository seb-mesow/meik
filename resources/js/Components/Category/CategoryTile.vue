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
		<div class="category-tile">
			<p>{{ props.category.name }}</p>
		</div>
	</a>
</template>

<style lang="css" scoped>
.category-tile {
	width: 35rem;
	height: auto;
	border-radius: 1rem;
	padding: 1rem;
	color: black;
	background-color: #808080;
}
</style>
