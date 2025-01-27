<script setup lang="ts">
import type { IRubricForTile } from '@/types/meik/models';
import { route } from 'ziggy-js';
import { useDialog } from 'primevue/usedialog';
import DynamicDialog from 'primevue/dynamicdialog';
import Button from 'primevue/button';
import { defineAsyncComponent, reactive } from 'vue';
const RubricDialog = defineAsyncComponent(() => import('./RubricDialog.vue'));
// (interne) Attribute der Komponente
const props = defineProps<{
	rubric: { id: string, name: string };
    category: string
}>();
let rubric = reactive(props.rubric);

const dialog = useDialog();

const edit = () => {
    console.log(rubric)
    const dialogRef = dialog.open(RubricDialog, {
        props: {
            header: 'TEST',
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
			rubric: rubric,
			category: props.category	
		},
        onClose: (options) => {
            const data = options?.data;
            if (data) {
                rubric.name = data.data.name
            }
        }
    });
}



</script>

<template>
	<div>
		<Button @click="edit">Edit</Button>
		<a :href="route('exhibit.overview', { rubric: rubric.id })">
			<div class="rubric-tile">
				<p>{{ rubric.name }}</p>
			</div>
		</a>
	</div>
	<DynamicDialog />
</template>

<style lang="css" scoped>
.rubric-tile {
	width: 300px !important;
	height: 100px;
	border-radius: 20px;
	padding: 20px;
	margin: 10px;
	color: black;
	background-color: #808080;
}
</style>
