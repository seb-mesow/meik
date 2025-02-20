<script lang="ts" setup>
import { route } from 'ziggy-js';
import { PartialDate } from '@/util/partial-date';
import { IExhibitTileProps } from '@/types/page_props/exhibit_tiles';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit: IExhibitTileProps;
}>();

const exhibit_manufacture_date: PartialDate|null = props.exhibit.manufacture_date === '' ? null : PartialDate.parse_iso(props.exhibit.manufacture_date);
</script>

<template>
	<a :href="route('exhibit.details', props.exhibit.id)">
		<div class="exhibit-tile flex justify-between" style="align-items: center;">
			<div class="w-48 h-28 flex">
				<img v-if="props.exhibit.title_image"
					:src="route('ajax.image.get_thumbnail', { image_id: props.exhibit.title_image.id })"
					loading="lazy"
				>
			</div>
			<div class="flex flex-col pl-4">
				<p class="truncate w-72" style="font-size: larger;">{{ props.exhibit.name + " (" + props.exhibit.inventory_number + ")" }}</p>
				<p class="truncate w-72"> {{ "Baujahr: " + ( exhibit_manufacture_date ? exhibit_manufacture_date.format_pretty() : '' ) }}</p>
				<p class="truncate w-72"> {{ "Hersteller: " + props.exhibit.manufacturer }}</p>
				<p class="truncate w-72"> {{ "Standort: " + props.exhibit.location_name + " - " + props.exhibit.place_name }}</p>
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
