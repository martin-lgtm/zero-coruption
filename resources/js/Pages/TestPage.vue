<script setup>
const props = defineProps({ message: String })
</script>

<template>
  <div class="p-6 text-center">
    <h1 class="text-2xl font-bold">Vue + Inertia Test</h1>
    <p class="mt-4">{{ message }}</p>
  </div>
</template>
