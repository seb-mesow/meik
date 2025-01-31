<script setup lang="ts">
import NavBar from '@/Components/NavBar.vue';
import ConfirmPopup from 'primevue/confirmpopup';
import DynamicDialog from 'primevue/dynamicdialog';
import Toast from 'primevue/toast';
import DarkMode from '@/util/dark-mode';
import { onBeforeMount } from 'vue';
import Button from 'primevue/button';
import DarkMode from '@/util/dark-mode';
import { onBeforeMount } from 'vue';
import Button from 'primevue/button';

const props = defineProps<{
	disable_overflow?: boolean
}>();
const disable_overflow: boolean = props.disable_overflow ?? false;
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
					<div class="items-center flex">
						<slot name="header"/>
						<!-- Darkmode-Toogle: neue Platzierung-->
						<Button class="pl-4 pr-4 py-2 px-2 shadow-md" @click="dark_mode?.toggle()"> <!-- TODO: Funktion aus Navbar.vue noch übertragen -->
							<i id="dark_mode_icon" class="pi pi-sun"/>
						</Button>
					</div>
				</header>
			</div>
			<main class="p-4 h-full" :class="{ 'overflow-y-hidden': disable_overflow }">
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
