<script setup lang="ts">
import { Button } from "primevue";
import { route } from "ziggy-js";
import { useForm } from '@inertiajs/vue3';
import Form from '@/Components/Form/Form.vue';

const form = useForm({});
</script>

<template>
	<Form id="logout" method="post" :action="route('logout')" :form="form">
		<Button class="logout-button" raised type="submit"> <!--Todo: andere Farbe für Logout-->
			<i class="pi pi-sign-out"/>
		</Button>
	</Form>
</template>
