<template>
  <Modal :show="true" :closeable="true" @close="$emit('close')">
    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-left">
      <h2 class="text-lg font-bold text-gray-800 text-center">Политика за приватност</h2>

      <p class="text-sm text-gray-700 leading-relaxed">
        Вашата приватност е важна за нас. Со оваа политика се објаснува како се прибираат, обработуваат и чуваат вашите податоци.
      </p>

      <ul class="list-disc list-inside text-sm text-gray-700 space-y-2">
        <li>При поднесување на пријава, ние не прибираеме лични податоци како име или презиме.</li>
        <li>Податоците што ги доставувате се користат исклучиво за статистички и аналитички цели.</li>
        <li>Вашата приказна ќе биде објавена јавно само доколку дадете согласност за тоа.</li>
        <li>Сите информации се чуваат безбедно и не се споделуваат со трети страни.</li>
      </ul>



      <div class="flex justify-center mt-6">
        <button
          class="px-4 py-2 rounded bg-yellow-500 text-black text-sm font-medium hover:bg-yellow-400"
          @click="$emit('close')"
        >
          Затвори
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'

// Declare the event
defineEmits(['close'])
</script>
