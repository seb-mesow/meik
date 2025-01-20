<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MEIK_HeadLine from '@/Components/MEIK_HeadLine.vue';
</script>

<template>
	<div
		class="flex min-h-screen flex-col items-center bg-gray-300 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-100"
	>
		<div>
			<MEIK_HeadLine class="h-200 w-200 fill-current text-blue-500" />
			
		</div>

		<div

			class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-400"
		>
			<slot />
		</div>

		<div>
			<ApplicationLogo class="h-20 w-20 fill-current text-blue-500" />
		</div>
	</div>
</template>
