<script setup lang="ts">
import { onMounted, onUpdated, ref } from 'vue';

defineProps<{
	method: string;
	action: string;
}>();

const token: any = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

</script>

<template>
	<form :method :action>
		<input type="hidden" name="_token" :value="token" />
		<slot/>
	</form>
</template>
