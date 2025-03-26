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
</script>

<template>
	<AuthenticatedLayout>
		<template #header>
			<Breadcrumb class="!bg-gray-100 dark:!bg-gray-800" :home="home" :model="items">
				<template #item="{ item }">
					<a class="cursor-pointer text-2xl" :href="item.url">
						<span v-if="item.icon" :class="item.icon"></span>
						<span v-else>{{ item.label }}</span>
					</a>
				</template>
			</Breadcrumb>
		</template>
		
		<!-- Hier kommt keine äußere Card hin und kein Overflow! -->
		<div class="flex flex-wrap justify-center gap-3">
			
			<div class="flex-2 md:min-w-xl min-w-sm bg-gray-100 dark:bg-gray-900 border-[2px] border-gray-300 dark:border-gray-800 p-4 rounded-md">
				<Carousel
					:value="form.children_in_editing.value"
					:page="form.shown_page.value"
				>
					<template #item="{ data }">
						<Image :form="data" />
					</template>
				</Carousel>
			</div>
			
			<div class="flex-1 sm:min-w-sm bg-gray-50 dark:bg-gray-900 border-[2px] border-gray-300 dark:border-gray-800 p-4 rounded-md">
				<span class="text-2xl">Reihenfolge</span>
				<ImageOrder :form="form" />
			</div>
			
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
