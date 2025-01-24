<script lang="ts" setup>
import { route } from 'ziggy-js';
import { IExhibitOverviewExhibitTileInitPageProps } from '@/types/page_props/exhibit_overview';

// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit: IExhibitOverviewExhibitTileInitPageProps;
}>();
const exhibit: IExhibitOverviewExhibitTileInitPageProps = props.exhibit;
</script>

<template>
	<a :href="route('exhibit.details', exhibit.id)">
		<div class="exhibit-tile">
			<img v-if="exhibit.title_image" :src="route('ajax.image.get_file', { image_id: exhibit.title_image.id })">
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
