<script setup lang="ts">
import { route } from 'ziggy-js';
import Image from '@/Components/Exhibit/Image.vue';
import { IImageFormConstructorArgs, IImagesForm, ImagesForm } from '@/form/special/overview/images-form';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { IImageInitPageProps, IImagesInitPageProps } from '@/types/page_props/images';
import Breadcrumb from 'primevue/breadcrumb';
import Carousel from 'primevue/carousel';
import { reactive, Reactive, ShallowReactive, shallowReactive, ShallowRef, shallowRef, watch } from 'vue';

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
	url: route('rubric.overview', { category_id: props.category.id })
});
items.push({
	label: props.rubric.name,
	url: route('exhibit.overview', { rubric: props.rubric.id })
});
items.push({
	label: props.name,
	url: route('exhibit.details', { exhibit_id: props.init_props.exhibit_id })
});
items.push({
	label: 'Bilder'
});

const images: IImageFormConstructorArgs[] = props.init_props.images.map((_props: IImageInitPageProps): IImageFormConstructorArgs => {
	return {
		id: _props.id,
		description: { 
			val: _props.description,
			errs: [],
		},
		is_public:{ 
			val: _props.is_public,
			errs: [],
		},
	};
});
images.push({});

const form: ShallowReactive<IImagesForm> = shallowReactive(new ImagesForm({
	exhibit_id: props.init_props.exhibit_id,
	images: images,
}));
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
			<Breadcrumb :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		<div class="carousel">
			<Carousel
				:value="form.children.value"
				>
				<template #item="{ data }">
					<Image :form="data"/>
				</template>
			</Carousel>
		</div>
		<!-- <div class="_images">
			<div class="_image" v-for="image_id in init_props.images">
				<img
					:src="route('ajax.image.get_file', { image_id: image_id.id })"
				>
			</div>
		</div> -->
	</AuthenticatedLayout>
</template>
<style lang="css" scoped>
.carousel {
	margin-left: auto;
	margin-right: auto;
	width: 40rem;
}
._images {
	display: flex;
	flex-wrap: wrap;
}
._image {
	flex: 14rem;
	object-fit: inherit;
}
</style>
