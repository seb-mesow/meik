<script setup lang="ts">
import { route } from 'ziggy-js';
import Image from '@/Components/Exhibit/Image.vue';
import { UIImagesForm, ImagesForm } from '@/form/special/overview/images-form';
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

const form: UIImagesForm = new ImagesForm({
	data: {
		exhibit_id: props.init_props.exhibit_id,
		images: props.init_props.images,
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
			<Breadcrumb class="!overflow-x-visible border-white border-1" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<div class="w-[100%] lg:flex lg:flex-wrap gap-3">
			<div class="lg:flex-2 lg:min-w-1xl min-w-xs border-green-500 border-2">
				<Carousel class=""
					:value="form.children_in_editing.value"
					>
					<template #item="{ data }">
						<Image :form="data"/>
					</template>
				</Carousel>
			</div>
			<ImageOrder class="lg:flex-1 lg:min-w-xs min-w-xs lg:mt-0 mt-3 border-white border-2" :form="form" />
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
