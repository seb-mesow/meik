<script setup lang="ts">
import DarkMode from '@/util/dark-mode';
import Button from 'primevue/button';
import { onBeforeMount } from 'vue';

onBeforeMount(() => {
	DarkMode.init_on_before_mounted();
})
</script>
<template>
	<Button
		severity="primary" raised
		@click="DarkMode.toggle()"
		class="pl-4 pr-4 py-2 px-2 shadow-md"
	>
		<div v-if="DarkMode.is_system.value"
			class="pi relative h-[1lh] w-[1lh]">
			<i class="pi pi-sun absolute top-[-.25lh] right-[-.25lh]"/>
			<i class="pi pi-moon absolute bottom-[-.25lh] left-[-.25lh]"/>
		</div>
		<i v-else class="pi" :class="{
			'pi-sun': !DarkMode.is_dark.value,
			'pi-moon': DarkMode.is_dark.value,
		}"/>
	</Button>
</template>
