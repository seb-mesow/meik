<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-300 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-100"
    >
        <div>
            <ApplicationLogo class="h-20 w-20 fill-current text-blue-500" />
            <!-- Link href="/">
                
            </Link -->
        </div>

        <div

            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-400"
        >
            <slot />
        </div>
    </div>
</template>
