<script setup lang="ts">
defineProps<{
	method: string;
	action: string;
}>();

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
</script>

<template>
	<form :method :action>
		<input type="hidden" name="_token" :value="token" />
		<slot/>
	</form>
</template>
