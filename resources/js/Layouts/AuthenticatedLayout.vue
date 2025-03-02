<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';

const props = defineProps<{
	disable_overflow?: boolean
}>();
const disable_overflow: boolean = props.disable_overflow ?? false;
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
				<DarkModeToggle/>
			</header>
			
			<main class="overflow-auto lg:p-4 p-2 border-yellow-800 border-2">
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
