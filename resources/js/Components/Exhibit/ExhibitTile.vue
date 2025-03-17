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
		<div class="exhibit-tile bg-gray-100 dark:bg-gray-500 flex justify-start items-center shadow-md shadow-gray-400 hover:bg-[#249cda]/12">
			<div class="flex w-[10rem] h-[6rem] justify-center">
				<img class="object-contain" v-if="props.exhibit.title_image"
					:src="route('ajax.image.get_thumbnail', { image_id: props.exhibit.title_image.id })"
					loading="lazy"
				>
			</div>
			<div class="flex flex-col pl-4">
				<p class="truncate" style="font-size: larger;">{{ props.exhibit.name + " (" + props.exhibit.inventory_number + ")" }}</p>
				<p class="truncate"> {{ "Baujahr: " + ( exhibit_manufacture_date ? exhibit_manufacture_date.format_pretty() : '' ) }}</p>
				<p class="truncate"> {{ "Hersteller: " + props.exhibit.manufacturer }}</p>
				<p class="truncate"> {{ "Standort: " + props.exhibit.location_name + " - " + props.exhibit.place_name }}</p>
			</div>
		</div>
	</a>
</template>

<style lang="css" scoped>
.exhibit-tile {
	width: 35rem;
	height: auto;
	border-radius: 1rem;
	padding: 1rem;
}
</style>
