<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps(['exhibits'])
console.log(props.exhibits)

</script>

<template>
    <Head title="Exhibits" />
	<AuthenticatedLayout>
		<div class="flex flex-wrap">
			<div class="border-black border-solid border-2 w-[20%] min-w-[20rem]" v-for="exhibit in exhibits">
				Exhibit {{ exhibit.designation }}
			</div>
		</div>
	</AuthenticatedLayout>
</template>
