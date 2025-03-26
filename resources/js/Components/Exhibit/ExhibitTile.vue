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
		<div class="tile exhibit-tile shadow-md dark:shadow-sm shadow-gray-400 items-center flex">
			<div class="w-[10rem] h-[6rem] flex justify-center">
				<img class="object-contain" v-if="props.exhibit.title_image"
					:src="route('ajax.image.get_thumbnail', { image_id: props.exhibit.title_image.id })"
					loading="lazy"
				>
			</div>
			<div class="w-[19rem] pl-[1rem]">
				<p class="truncate w-[18rem]" style="font-size: larger;">{{ props.exhibit.name }}</p>
				<p class="truncate w-[18rem]"> {{ "Baujahr: " + ( exhibit_manufacture_date ? exhibit_manufacture_date.format_pretty() : '' ) }}</p>
				<p class="truncate w-[18rem]"> {{ "Hersteller: " + props.exhibit.manufacturer }}</p>
				<p class="truncate w-[18rem]"> {{ "Standort: " + props.exhibit.location_name + " - " + props.exhibit.place_name }}</p>
			</div>
		</div>
	</a>
</template>

<style lang="css" scoped>
.exhibit-tile {
	width: 30rem;
	height: 8.5rem;
	border-radius: 1rem;
	padding: 1rem;
}
</style>
