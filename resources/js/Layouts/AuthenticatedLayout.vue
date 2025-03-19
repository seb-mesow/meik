<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import LogoutButton from '@/Components/Control/LogoutButton.vue';

const props = defineProps<{
	disable_overflow?: boolean
}>();
const disable_overflow: boolean = props.disable_overflow ?? false;
</script>

<template>
	<!-- Hier keine Popups, Toasts, DynamicDialog usw. einfügen! -->
	<!-- Stattdessen diese nur in solchen Seiten einfügen, wo sie auch benötigt werden -->
	<div class="w-full flex h-screen flex-no-wrap">
		
		<NavBar />
		
		<!-- Page Content -->
		<div class="grow-1 h-full flex flex-col">
		<!-- <div class="grow-1"> -->
			
			<!-- Page Heading -->
			<header class="bg-white shadow dark:bg-gray-800 items-center w-full flex flex-no-wrap justify-between px-4">
				<slot name="header"/>
				<div>
					<DarkModeToggle class="inline-block me-3"/>
					<LogoutButton class="inline-block"/>
				</div>
			</header>
			
			<main class="overflow-auto flex flex-col bg-gray-100 dark:bg-gray-600 h-full w-full lg:p-4 p-2">
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
