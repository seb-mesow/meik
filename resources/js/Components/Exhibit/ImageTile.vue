<script setup lang="ts">
import { UIImageForm } from '@/form/special/multiple/image-form';

defineProps<{
	form: UIImageForm,
}>();

</script>

<template>
	<div class="image-tile flex items-center gap-x-3">
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
	height: 8rem;
	border-radius: 1rem;
	padding: 1rem;
	margin: 10px;
	color: black;
	background-color: #808080;
	display: flex;
}
</style>
