<script setup lang="ts">
const props = defineProps<{
	form: any,
	method: string;
	action: string;
}>();
const emit = defineEmits<{
	finish: [],
}>()

const submit = () => {
	/**
	 * Dies sendet eine AJAX-Request mittels Axios.
	 * Und Axios sendet den CSRF-Token im Header X-XSRF-TOKEN mit.
	 * (sowie zusätzlich nochmal als Anfrage-Cookie XSRF-TOKEN, was von Laravel aber nicht ausgewertet wird.)
	 */
	props.form.post(props.action, {
		onFinish: () => {
			emit('finish');
		},
	});
};
</script>

<template>
	<form :method :action @submit.prevent="submit">
		<slot/>
	</form>
</template>
