<script setup lang="ts">

// (interne) Attribute der Komponente
const props = defineProps<{
	category: string;
}>();
const category = props.category;
// const manufacturer: string = '';
// const site: string = '';
// const place: string = '';

</script>

<template>
	<a :href="route('rubric.overview', category)">
		<div class="category-tile">
			<p>{{ category }}</p>
		</div>
	</a>
</template>

<style lang="css" scoped>
.category-tile {
	width: 300px !important;
	height: 100px;
	border-radius: 20px;
	padding: 20px;
	margin: 10px;
	color: black;
	background-color: #808080;
}
</style>
