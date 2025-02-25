<script setup lang="ts">
import { onMounted } from 'vue';
import ImageTile from './ImageTile.vue';
import { IImagesForm } from '@/form/special/overview/images-form';
import Form from '../Form/Form.vue';

const props = defineProps<{
	form: IImagesForm;
}>();
onMounted(() => {
	props.form.on_mounted();
});
</script>

<template>
	<div id="image-tile-container" @dragover="form.on_tile_container_dragover" >
		<ImageTile v-for="image in form.children.value" :key="image.ui_id" :form="image" />
	</div>
</template>

<style lang="css">
.tile {
	background-color: var(--p-sky-400);
	color: black;
	text-align: center;
	align-content: center;
	border-radius: 1rem;
	width: 15rem;
	height: 5rem;
	margin-top: .5rem;
	margin-bottom: .5rem;
	cursor: move;	
}
.tile.dragging {
	opacity: .5;
}
.slot {
	width: 15rem;
	height: 5rem;
	border-width: .15rem;
	border-style: dotted; 
	border-color: white;
	border-radius: 1rem;
	margin-top: .5rem;
	margin-bottom: .5rem;
	padding: 0;
}
.slot.target-slot {
	background-color: green;
}
</style>
