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
	<div
		:id="'image-tile-' + form.ui_id"
		draggable="true"
		@dragstart="form.on_tile_dragstart"
		@dragend="form.on_tile_dragend"
		@click="form.on_tile_click"
		class="image-tile md:h-30 sm:h-22 h-15 md:p-3 p-2 my-3 flex items-center gap-x-3 cursor-grab text-default-text-color-light dark:text-default-text-color-dark bg-gray-200 dark:bg-gray-700 shadow-md dark:shadow-sm shadow-gray-400 hover:bg-blue-100 dark:hover:bg-slate-600"
	>
		<div>
			<i class="m-auto ms-[-.25rem] pi pi-bars"/>
		</div>
		
		<div class="h-full aspect-[4/3]">
			<img v-if="form.file_url.value"
				:src="form.file_url.value"
				class="h-full w-full object-contain"
			>
		</div>
		
		<div class="grow max-h-full overflow-hidden">
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
