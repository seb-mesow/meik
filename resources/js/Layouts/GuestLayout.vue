<script setup lang="ts">
import fv_logo_url from '../../images/FV_ISC.jpg'; // Pfad zum Bild 1
import isc_log_url from '../../images/Logo_isc_300x122.jpg'; // Pfad zum Bild 2
</script>

<template>
	<!-- <div class="flex min-h-screen flex-col items-center bg-gray-300 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-100"> -->
	<img :src="fv_logo_url" class="z-[-1] fixed bottom-3 left-3 max-h-35">
	<div class="z-[-2] fixed bottom-3 right-3 max-w-50 p-2 bg-white border-0 border-gray-400">
		<img :src="isc_log_url" class="">
	</div>
	<slot/>
</template>

<style lang="css">
body {
	background-color: var(--color-gray-100);
}
</style>
