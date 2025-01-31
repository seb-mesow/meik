<script setup lang="ts">
import { onMounted } from 'vue';

defineProps<{
	method: string;
	action: string;
}>();

// let token: string|null|undefined = null;
// onMounted(() => {
	// token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	// });
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
</script>

<template>
	<form :method :action>
		<input type="hidden" name="_token" :value="token"/>
		<slot/>
	</form>
</template>
