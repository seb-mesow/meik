<script setup lang="ts">
import { onMounted } from 'vue';
import ImageTile from './ImageTile.vue';
import { UIImagesForm } from '@/form/special/overview/images-form';
import Button from 'primevue/button';

const props = defineProps<{
	form: UIImagesForm;
}>();
onMounted(() => {
	props.form.on_mounted();
});
</script>

<template>
	<div>
		<div id="image-tile-container"
			@dragover="form.on_tile_container_dragover"
		>
			<ImageTile v-for="image in form.children_in_editing.value" :key="image.ui_id" :form="image" />
		</div>
		
		<div class="mt-6 flex justify-between">
			<Button severity="primary" raised @click="form.click_add()" icon="pi pi-plus"/>
			
			<Button
				@click="form.click_image_order_rollback()"
				:disabled="!form.ui_has_changes.value"
				severity="primary" raised
				label="Zurücksetzen"
			/>
		</div>
		
		<Button
			@click="form.click_image_order_save()"
			:disabled="!form.ui_has_changes.value"
			label="Reihenfolge speichern"
			severity="primary"
			raised
			class="mt-3"
		/>
			
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
