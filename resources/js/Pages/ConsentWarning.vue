<!-- resources/js/Pages/ConsentWarning.vue -->
<template>
  <Modal :show="true" :closeable="false">
    <div class="p-6 space-y-4 text-center">
      <h2 class="text-lg font-bold text-gray-800">
        Потврда за согласност
      </h2>

      <p class="text-sm text-gray-700 leading-relaxed">
        За да ја испратите пријавата, мора да потврдите дека се согласувате со
        обработката и користењето на вашите податоци согласно со
        <Link href="/privacy" class="text-blue-600 hover:underline" target="_blank">
          Политиката за приватност
        </Link>.
      </p>

      <div class="flex justify-center gap-3 mt-6">
        <button
          class="px-4 py-2 rounded bg-yellow-500 text-black text-sm font-medium"
          @click="$emit('accept')"
        >
          Потврди согласност
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import { Link } from '@inertiajs/vue3'

defineEmits(['accept'])
</script>
