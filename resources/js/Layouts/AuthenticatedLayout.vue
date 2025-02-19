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
	<div class="flex">
		
		<NavBar />
		
		<!-- Page Content -->
		<div class="flex-grow">
			
			<!-- Page Heading -->
			<header class="bg-white h-fit min-h-16 shadow dark:bg-gray-800 items-center flex">
				<slot name="header"/>
				<!-- Darkmode-Toogle: neue Platzierung-->
				<Button class="pl-4 pr-4 py-2 px-2 shadow-md postion: relative" @click="dark_mode?.toggle()"> <!-- TODO: Funktion aus Navbar.vue noch übertragen -->
					<i id="dark_mode_icon" class="pi pi-sun"/>
				</Button>
			</header>
			
			<main class="p-4 h-full">
				<slot />
			</main>
			
		</div>
	</div>
</template>
