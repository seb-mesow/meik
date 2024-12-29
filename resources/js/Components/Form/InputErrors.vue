<script setup lang="ts">
defineProps<{
	errors: readonly string[];
}>();
</script>

<template>
	<div v-show="errors">
		<p v-for="error in errors"
			class="text-sm text-red-600 dark:text-red-400"
		>
			{{ error }}
		</p>
	</div>
</template>
