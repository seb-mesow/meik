<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
import ConfirmPopup from 'primevue/confirmpopup';
import DynamicDialog from 'primevue/dynamicdialog';
import Toast from 'primevue/toast';
const props = withDefaults(defineProps<{
	disable_overflow: boolean
}>(), { disable_overflow: false })
</script>

<template>
	<DynamicDialog />
	<ConfirmPopup />
	<Toast />
	<div class="page">
		<NavBar />
		<!-- Page Content -->
		<div class="content flex flex-col h-screen">
			<div class="bg-white h-16">
				<!-- Page Heading -->
				<header class="bg-white h-fit min-h-16 shadow dark:bg-gray-800" v-if="$slots.header">
					<div class="pl-4 items-center flex">
						<slot name="header" />
					</div>
				</header>
			</div>
			<main class="p-4 h-full" :class="{ 'overflow-y-hidden': props.disable_overflow }">
				<slot />
			</main>
		</div>
	</div>
</template>
<style lang="css" scoped>
.page {
	display: flex;
}

.content {
	flex: 1;
	/* background-color: lightcoral; */
}
</style>
