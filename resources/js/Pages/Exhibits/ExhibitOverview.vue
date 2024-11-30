<script setup lang="ts">
import Button from 'primevue/button';
import ExhibitTile from '@/Components/ExhibitTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Exhibit } from '@/types/meik/models';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
	exhibits: Exhibit[]
}>();
console.log(props.exhibits);
</script>

<template>
	<Head title="Exponate"/>
	<AuthenticatedLayout>
		<Button :href="route('exhibit.new')" label="Exponat hinzufügen" as="a"/>
		<div class="flex flex-wrap">
			<!-- <div class="border-black border-solid border-2 w-[20%] min-w-[20rem]" v-for="exhibit in exhibits"> -->
				<ExhibitTile v-for="exhibit in exhibits"
					:exhibit="exhibit"
				/>
			<!-- </div> -->
		</div>
	</AuthenticatedLayout>
</template>
