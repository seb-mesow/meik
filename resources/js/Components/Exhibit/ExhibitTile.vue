<script lang="ts" setup>
import { route } from 'ziggy-js';
import { IExhibitOverviewExhibitTileInitPageProps } from '@/types/page_props/exhibit_overview';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit: IExhibitOverviewExhibitTileInitPageProps;
}>();
const exhibit: IExhibitOverviewExhibitTileInitPageProps = props.exhibit;
if (exhibit.title_image) {
	console.log(`thumbnail: ${exhibit.title_image.thumbnail_width} x ${exhibit.title_image.thumbnail_height}`);
}
</script>

<template>
	<a :href="route('exhibit.details', exhibit.id)">
		<div class="exhibit-tile">
			<img v-if="exhibit.title_image" 
				:src="route('ajax.image.get_thumbnail', { image_id: exhibit.title_image.id })"
				:width="exhibit.title_image.thumbnail_width"
				:height="exhibit.title_image.thumbnail_height"
			>
			<p>{{ exhibit.name }}</p>
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
