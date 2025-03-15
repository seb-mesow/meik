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
		<div>
			<i class="m-auto ms-[-.25rem] pi pi-bars"/>
		</div>
		
		<div class="md:h-24 h-11 aspect-[4/3]">
			<img v-if="form.file_url.value"
				:src="form.file_url.value"
				class="h-full w-full object-contain"
			>
		</div>
		
		<div>
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
