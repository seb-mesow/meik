<script setup lang="ts">
import type { Exhibit } from '@/types/meik/models';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit: Exhibit;
}>();
const exhibit: Exhibit = props.exhibit;

const name: string = exhibit.name;
// const manufacturer: string = '';
// const site: string = '';
// const place: string = '';

</script>

<template>
	<a :href="route('exhibit.show', exhibit.id)">
		<div class="exhibit-tile">
			<p>{{ name }}</p>
		</div>
	</a>
</template>

<style lang="css" scoped>
.exhibit-tile {
	width: 300px !important;
	height: 100px;
	border-radius: 20px;
	padding: 20px;
	margin: 10px;
	color: black;
	background-color: #808080;
}
</style>
