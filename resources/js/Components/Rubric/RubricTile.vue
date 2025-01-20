<script setup lang="ts">
import type { IRubricForTile } from '@/types/meik/models';

// (interne) Attribute der Komponente
const props = defineProps<{
	rubric: {id: string, name: string};
}>();
const rubric = props.rubric;

const name: string = rubric.name;

</script>

<template>
	<a :href="route('exhibit.overview', {rubric: rubric.id})">
		<div class="rubric-tile">
			<p>{{ rubric.name }}</p>
		</div>
	</a>
</template>

<style lang="css" scoped>
.rubric-tile {
	width: 300px !important;
	height: 100px;
	border-radius: 20px;
	padding: 20px;
	margin: 10px;
	color: black;
	background-color: #808080;
}
</style>
