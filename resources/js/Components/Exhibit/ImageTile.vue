<script setup lang="ts">
import { UIImageForm } from '@/form/special/multiple/image-form';
import { onMounted } from 'vue';

const props = defineProps<{
	form: UIImageForm,
}>();
onMounted(() => {
	props.form.on_mounted();
});
</script>

<template>
	<div class="image-tile md:h-30 h-15 md:p-3 p-2 my-3 flex items-center gap-x-3 cursor-grab"
		:id="'image-tile-' + form.ui_id"
		draggable="true"
		@dragstart="form.on_tile_dragstart"
		@dragend="form.on_tile_dragend"
	>
		<div class="border border-black">
			<i class="m-auto ms-[-.5rem] pi pi-bars"/>
		</div>
		<div class="h-full aspect-video border border-black">
			<img class="h-full w-full object-contain" v-if="form.file_url.value"
				:src="form.file_url.value"
			>
		</div>
		<div class="border border-black">
			<p>{{ form.description.ui_value_in_editing }}</p>
		</div>
	</div>
</template>

<style lang="css" scoped>
.image-tile {
	border-radius: 1rem;
	color: black;
	background-color: #808080;
	display: flex;
}
.image-tile-dragging {
	cursor: grapping;
}
</style>
