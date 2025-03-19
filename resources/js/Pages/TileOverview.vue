<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
        Kachelübersicht
      </h1>
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="card in cards"
          :key="card.id"
          class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 hover:shadow-lg"
        >
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ card.title }}
          </h2>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            {{ card.description }}
          </p>
          <button
          severity="primary" raised class="mt-4 px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
          >
            Mehr erfahren
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { PropType } from "vue";
  
  // Props: Karten können dynamisch übergeben werden
  const props = defineProps({
    cards: {
      type: Array as PropType<Array<{ id: number; title: string; description: string }>>,
      required: true,
    },
  });
  </script>
  
  <style scoped>
  /* Optional: Zusätzliche Styles */
  
  </style>
  