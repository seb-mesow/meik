<script setup lang="ts">
import DarkMode from '@/util/dark-mode';
import Button from 'primevue/button';
import { onBeforeMount } from 'vue';

onBeforeMount(() => {
	DarkMode.init_on_before_mounted();
})
</script>
<template>
	<Button 
		@click="DarkMode.toggle()"
		class="pl-4 pr-4 py-2 px-2 shadow-md postion: relative"
	>
		<i class="pi" :class="{
			'pi-sun': !DarkMode.is_dark.value,
			'pi-moon': DarkMode.is_dark.value,
		}" />
	</Button>
</template>
