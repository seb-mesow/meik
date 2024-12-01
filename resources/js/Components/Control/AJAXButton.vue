<script setup lang="ts">
import axios, { AxiosError, AxiosRequestConfig, AxiosResponse } from 'axios';
import Button from 'primevue/button';
import { ref } from 'vue';

const props = defineProps<{
	requestCfg: AxiosRequestConfig
}>();

const emit = defineEmits<{
	'success': [any, number]
	'failure': [AxiosError]
}>()
const is_loading = ref<boolean>(false);

async function exec_ajax() {
	is_loading.value = true;
	try {
		const response: AxiosResponse = await axios.request(props.requestCfg);
		emit('success', response.data, response.status);
	} catch (error) {
		if (error instanceof AxiosError) {
			emit('failure', error)
		} else {
			throw error;
		}
	} finally {
		is_loading.value = false;
	}
}
</script>

<template>
	<Button :loading="is_loading" @click="exec_ajax"/>
</template>
