<!-- resources/js/Pages/PublishWarning.vue -->
<template>
  <!-- Breeze Modal usually needs :show -->
  <Modal :show="true" :closeable="true" @close="$emit('proceed')">
    <div class="p-6 space-y-4 text-center">
      <h2 class="text-lg font-bold text-gray-800">
        Објавување на приказната
      </h2>

      <p class="text-sm text-gray-700 leading-relaxed">
        Доколку сакате вашата приказна да биде споделена јавно, треба да го
        штиклирате полето „Се согласувам мојата споделена приказна да биде
        објавена јавно...“.
      </p>

      <div class="flex justify-center gap-3 mt-6">
        <button
          class="px-4 py-2 rounded bg-gray-200 text-gray-800 text-sm"
          @click="$emit('proceed')"
        >
          Продолжи без објавување
        </button>

        <button
          class="px-4 py-2 rounded bg-yellow-500 text-black text-sm"
          @click="$emit('allow')"
        >
          Дозволи објавување
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'

// declare events so Vue won’t warn
defineEmits(['proceed', 'allow'])
</script>
