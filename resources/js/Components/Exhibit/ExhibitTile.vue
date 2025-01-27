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
		<div class="exhibit-tile flex justify-between">
			<div class="w-48 h-28 flex">
				<img v-if="exhibit.title_image" 
					:src="route('ajax.image.get_thumbnail', { image_id: exhibit.title_image.id })"
				>
			</div>
			<div class="flex flex-col pl-4">
				<p style="font-size: x-large;">{{ "" + exhibit.name, exhibit.inventory_number}}</p>
				<p>{{ "Baujahr: " + exhibit.year_of_manufacture }}</p>
				<p>{{ "Hersteller: " + exhibit.manufacturer }}</p>
				<p>{{ "Standort: " + exhibit.location_name, exhibit.place_name }}</p>
			</div>
		</div>
	</a>
</template>

<style lang="css" scoped>
.exhibit-tile {
	min-width:20rem;
	height: auto;
	border-radius: 20px;
	padding: 20px;
	margin: 10px;
	color: black;
	background-color: #808080;
}
</style>
