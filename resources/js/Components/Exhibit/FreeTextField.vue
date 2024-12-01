<script setup lang="ts">
import { IFreeText } from '@/types/meik/models';
import { IValueForm } from '@/util/form';

// (interne) Attribute der Komponente
const props = defineProps<{
	form: IValueForm<number, IFreeText>;
}>();
// const name: string = exhibit.name;
// const manufacturer: string = '';
// const site: string = '';
// const place: string = '';
</script>

<template>
	<div>
		<div>#{{ props.form.id }}</div>
		<div>{{ props.form.val.heading.val }}</div>
		<div>{{ props.form.val.html.val }}</div>
		<div>{{ props.form.val.is_public.val ? 'öffentlich' : 'intern' }}</div>
	</div>
</template>

<style lang="css" scoped>
</style>
