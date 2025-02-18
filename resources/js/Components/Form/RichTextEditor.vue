<script setup lang="ts">
// https://github.com/primefaces/primevue/issues/5606#issuecomment-2198442124
import { ISingleValueForm } from "@/form/single/single-value-form";
import Editor from "primevue/editor";
import Quill from "quill";

const props = defineProps<{
	modelValue: ISingleValueForm<string>
}>();

const emit = defineEmits<{
	'update:modelValue': [ISingleValueForm<string>]
}>();

function onLoad(params: { instance: Quill }) {
	params.instance.setContents(params.instance.clipboard.convert({
		html: props.modelValue.val
	}));
}

function onChange(v: string) {
	props.modelValue.val = v;
	emit('update:modelValue', props.modelValue);
}
</script>

<template>
	<Editor @load="onLoad" @update:modelValue="onChange" />
</template>
