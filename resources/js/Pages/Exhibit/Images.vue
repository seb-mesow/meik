<script setup lang="ts">
import { route } from 'ziggy-js';
import Image from '@/Components/Exhibit/Image.vue';
import { IImagesForm, ImagesForm } from '@/form/special/overview/images-form';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IImagesInitPageProps } from '@/types/page_props/images';
import Breadcrumb from 'primevue/breadcrumb';
import Carousel from 'primevue/carousel';
import ImageOrder from '@/Components/Exhibit/ImageOrder.vue';

const props = defineProps<{
	name: string,
	init_props: IImagesInitPageProps,
	category: {
		id: string,
		name: string
	},
	rubric: {
		id: string,
		name: string
	}
}>();

const home = {
	icon: 'pi pi-home',
	url: route('category.overview'),
};
let items: { label: string, url?: string }[] = [];
items.push({
	label: props.category.name,
	url: route('category.details', { category_id: props.category.id })
});
items.push({
	label: props.rubric.name,
	url: route('rubric.details', { rubric_id: props.rubric.id })
});
items.push({
	label: props.name,
	url: route('exhibit.details', { exhibit_id: props.init_props.exhibit_id })
});
items.push({
	label: 'Bilder'
});

const form: IImagesForm = new ImagesForm({
	data: {
		exhibit_id: props.init_props.exhibit_id,
		images: props.init_props.images.map((_prop) => {
			return {
				id: _prop.id,
				description: _prop.description,
				is_public: _prop.is_public,
			};
		}),
	},
});
// const children = form.children
// watch(children, (children) => {
// 	const new_order: string = children.reduce((acc, cur) => {
// 		return acc + ", " + cur.ui_id;
// 	}, '');
// 	console.log(`watch: new_order == ${new_order}`);
// });
</script>
<template>
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb class="border border-white !overflow-x-visible" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<div class="w-[100%] flex flex-wrap gap-x-3">
			<!-- <div class="flex-2 max-w-4xl min-w-2xs border-green-500 border">
				<Carousel class=""
					:value="form.children.value"
					>
					<template #item="{ data }">
						<Image :form="data"/>
					</template>
				</Carousel>
				<div class="m-auto">
					jfsdjkfhsdj
				</div>
			</div>
			<ImageOrder class="flex-1 max-w-2xl min-w-sm" :form="form" />
			<ImageOrder class="flex-1 max-w-2xl min-w-2xs" :form="form" /> -->
		</div>
	</AuthenticatedLayout>
</template>
<style lang="css" scoped>
._images {
	display: flex;
	flex-wrap: wrap;
}
._image {
	flex: 14rem;
	object-fit: inherit;
}
</style>
