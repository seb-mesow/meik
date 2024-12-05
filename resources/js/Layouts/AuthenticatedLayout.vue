<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Card from 'primevue/card';

const showingNavigationDropdown = ref(false);
</script>

<style lang="scss">
@import 'primeicons/primeicons.css';
</style>

<template>
    <div class="flex flex-col h-screen">
        <div class="w-full flex bg-white h-16">
            <!-- Page Heading -->
            <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
        </div>
        <div class="h-full flex bg-gray-100 dark:bg-gray-900">
            <div
                class=" flex gap-4 py-4 flex-col justify-start border-b h-full min-w-32 border-gray-100 dark:border-gray-700 dark:bg-gray-800 bg-gray-200">
                <Card>
                    <template #content>
                        <span class="pi pi-database"></span>
                        <span>Exponate</span>
                    </template>
                </Card>

                <Card>
                    <template #content>
                        <span class="pi pi-user"></span>
                        <span>Benutzer</span>
                    </template>
                </Card>

                <Card>
                    <template #content>
                        <span class="pi pi-box"></span>
                        <span>Location</span>
                    </template>
                </Card>

            </div>
            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>