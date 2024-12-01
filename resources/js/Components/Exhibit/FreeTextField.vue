<script setup lang="ts">
// (interne) Attribute der Komponente
const props = defineProps<{
	exhibit: Exhibit;
}>();
const exhibit: Exhibit = props.exhibit;

const name: string = exhibit.name;
// const manufacturer: string = '';
// const site: string = '';
// const place: string = '';

</script>

<template>
	<div id="" />
</template>

<style lang="css" scoped>
</style>
