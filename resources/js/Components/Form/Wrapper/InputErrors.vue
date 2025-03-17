<script setup lang="ts">
defineProps<{
	errs: string[];
}>();
</script>

<template>
	<div v-if="errs.length > 0">
		<p v-for="error in errs" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
	</div>
</template>
