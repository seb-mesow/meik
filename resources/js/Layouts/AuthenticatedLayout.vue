<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Card from 'primevue/card';
import { Head } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>


<style lang="scss">
@import 'primeicons/primeicons.css';
</style>

<template>

    <Head>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    </Head>
    <div class="flex flex-col h-screen">
        <div class="h-full flex bg-gray-100 dark:bg-gray-900">
            <div
                class=" flex gap-8 shadow w-40
                 flex-col justify-between border-b h-full border-gray-100 dark:border-gray-700 dark:bg-gray-800 bg-[#003a6a]">

                 <div class="p-1">
                    <img src="resources/graphic/Logo_isc_300x122.jpg">
                 </div>

                <div>
                <a :href="route('exhibit.overview')" class="flex justify-between items-center p-4 text-white">
                    <span class="material-symbols-outlined pr-2">
                        archive
                    </span>
                    <span>Exponate</span>
                </a>


                <a :href="route('users.all')" class="flex justify-between items-center p-4 text-white">
                    <span class="material-symbols-outlined pr-2">
                        person
                    </span>
                    <span>Benutzer</span>
                </a>


                <a :href="route('locations.all')" class="flex justify-between items-center p-4 text-white">
                    <span class="material-symbols-outlined pr-2">
                        location_on
                    </span>
                    <span>Location</span>
                </a>

                </div>

                <div class="justify-self-end p-1">
                    <img src="resources/graphic/FV_ISC.jpg">
                 </div>

            </div>
            <!-- Page Content -->
            <div class="flex flex-col w-full">
                <div class="w-full flex bg-white h-16">
                    <!-- Page Heading -->
                    <header class="bg-white w-full h-fit min-h-16 shadow dark:bg-gray-800" v-if="$slots.header">
                        <div class="pl-8 h-full items-center flex">
                            <slot name="header" />
                        </div>
                    </header>
                </div>
                <main class="w-full">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>