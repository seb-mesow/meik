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
	<div class="image-tile text-default-text-color-light dark:text-default-text-color-dark bg-gray-100 dark:bg-gray-700 shadow-md dark:shadow-sm shadow-gray-400 hover:bg-blue-100 dark:hover:bg-slate-600 md:h-30 h-15 md:p-3 p-2 my-3 flex items-center gap-x-3 cursor-grab"
		:id="'image-tile-' + form.ui_id"
		draggable="true"
		@dragstart="form.on_tile_dragstart"
		@dragend="form.on_tile_dragend"
		@click="form.on_tile_click"
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
	display: flex;
}
.image-tile-dragging {
	cursor: grapping;
}
</style>
