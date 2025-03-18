<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MEIK_HeadLine from '@/Components/MEIK_HeadLine.vue';
</script>

<template>
	<!-- <div class="flex min-h-screen flex-col items-center bg-gray-300 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-100"> -->
	<div class="min-h-screen grid justify-items-center items-center bg-gray-300 dark:bg-gray-100">
		<!-- <div> -->
		<!-- </div> -->
			<!-- <MEIK_HeadLine/> -->
		<slot />
			<!-- <div
			class="mt-2 w-full overflow-hidden bg-white px-6 py-4 shadow sm:max-w-md sm:rounded-lg dark:bg-gray-400"	
			>
				<ApplicationLogo class="h-20 w-20 fill-current text-blue-500" />
			</div> -->
	</div>
</template>
