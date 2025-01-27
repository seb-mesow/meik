<script setup lang="ts">
import Button from 'primevue/button';
import RubricTile from '@/Components/Rubric/RubricTile.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { IRubricForTile } from '@/types/meik/models';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue';
import Breadcrumb from 'primevue/breadcrumb';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
const RubricDialog = defineAsyncComponent(() => import('../../Components/Rubric/RubricDialog.vue'));

const props = defineProps<{
	rubrics: IRubricForTile[],
	category_name: string
}>();

const dialog = useDialog();

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
const items = [
	{
		label: 'Kategorien',
		url: route('category.overview')
	},
	{
		label: props.category_name,
		url: route('rubric.overview', props.category_name),
	}
];

const create = () => {
    const dialogRef = dialog.open(RubricDialog, {
        props: {
            header: 'Rubrik anlegen',
            style: {
                width: '50vw',
            },
            breakpoints:{
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true,
        },
		data: {
			rubric: null,
			category: props.category_name	
		},
        onClose: (options) => {
            const data = options?.data;
            if (data) {
            //    TODO: Reload einfügen
            }
        }
    });
}

</script>
<!-- TODO: Hier muss noch ein Infinite Scroll + nachladen rein -->
<template>

	<Head title="Kategorien" />
	<AuthenticatedLayout>

		<template #header>
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<div class="flex flex-wrap">
			<RubricTile v-for="rubric in rubrics" :category="category_name" :rubric="rubric" />
		</div>

		<div class="absolute bottom-4 right-4">
			<Button @click="create">Neu</Button>
		</div>
	</AuthenticatedLayout>
</template>
