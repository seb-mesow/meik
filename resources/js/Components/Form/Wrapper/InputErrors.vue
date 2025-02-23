<script setup lang="ts">
defineProps<{
	errs: string[];
}>();
</script>

<template>
	<p v-for="error in errs" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
</template>
