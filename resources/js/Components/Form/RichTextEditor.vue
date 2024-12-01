<script setup lang="ts">
// https://github.com/primefaces/primevue/issues/5606#issuecomment-2198442124
import Editor from "primevue/editor";
import Quill from "quill";

const props = defineProps<{
	modelValue: string
}>();

const emit = defineEmits<{
	'update:modelValue': [string]
}>();

function onLoad(params: { instance: Quill }) {
	params.instance.setContents(params.instance.clipboard.convert({
		html: props.modelValue
	}))
}

function onChange(v: string) {
	emit('update:modelValue', v);
}
</script>

<template>
	<Editor @load="onLoad" @update:modelValue="onChange" />
</template>
