<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
</script>

<template>
	<div class="flex">
		
		<NavBar/>
		
		<!-- Page Content -->
		<div class="flex-1">
			<div class="flex bg-white h-16">
				<!-- Page Heading -->
				<header class="bg-white h-fit min-h-16 shadow dark:bg-gray-800" v-if="$slots.header">
					<div class="pl-4 h-full items-center flex">
						<slot name="header" />
					</div>
				</header>
			</div>
			
			<!-- <main class="w-full"> -->
			<main class="p-4">
				<slot />
			</main>
		</div>
	</div>
</template>
