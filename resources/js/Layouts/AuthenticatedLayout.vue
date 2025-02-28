<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
import DarkMode from '@/util/dark-mode';
import { onBeforeMount } from 'vue';
import Button from 'primevue/button';

const props = defineProps<{
	disable_overflow?: boolean
}>();
const disable_overflow: boolean = props.disable_overflow ?? false;

let dark_mode: DarkMode;
onBeforeMount(() => {
	dark_mode = new DarkMode();
});
</script>

<template>
	<!-- Hier keine Popups, Toasts, DynamicDialog usw. einfügen! -->
	<!-- Stattdessen diese nur in solchen Seiten einfügen, wo sie auch benötigt werden -->
	<div class="w-full flex flex-no-wrap">
		
		<NavBar />
		
		<!-- Page Content -->
		<div class="grow-1 border border-red-500 overflow-hidden">
		<!-- <div class="grow-1"> -->
			
			<!-- Page Heading -->
			<header class="bg-white shadow dark:bg-gray-800 items-center w-full flex flex-no-wrap overflow-auto justify-between border border-yellow-500">
					<slot name="header"/>
					<!-- Darkmode-Toogle: neue Platzierung-->
					<Button class="pl-4 pr-4 py-2 px-2 shadow-md postion: relative" @click="dark_mode?.toggle()">
						<i id="dark_mode_icon" class="pi pi-sun"/>
					</Button>
			</header>
			
			<main class="border border-green-500 p-4">
				<slot />
			</main>
			
		</div>
	</div>
</template>
<style lang="css">
.p-breadcrumb {
	background: var(--p-breadcrumb-background);
  	padding: var(--p-breadcrumb-padding);
}
</style>
