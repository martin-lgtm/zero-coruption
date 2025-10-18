<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">

    <!-- HEADER -->
    <header class="bg-gray-900 text-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

        <!-- Logo -->
        <div class="flex items-center gap-3">
          <img src="../../images/multus.png" class="w-12 sm:w-14" alt="Multus Logo">
          <h1 class="text-lg sm:text-xl font-bold tracking-wide">Multus</h1>
        </div>

        <!-- Hamburger Button -->
        <button @click="isOpen = !isOpen"
          class="sm:hidden focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Desktop Nav -->
        <nav class="hidden sm:flex gap-6 text-sm font-medium">
          <Link :href="route('map')" class="hover:text-yellow-400 transition">Мапа</Link>
          <Link :href="route('about')" class="hover:text-yellow-400 transition">За нас</Link>
          <Link href="/report" class="hover:text-yellow-400 transition">Пријави корупција</Link>
        </nav>
      </div>

      <!-- Mobile Dropdown -->
      <div v-if="isOpen" class="sm:hidden px-4 pb-4 space-y-2">
        <Link :href="route('map')" class="block hover:text-yellow-400 transition">Мапа</Link>
        <Link :href="route('about')" class="block hover:text-yellow-400 transition">За нас</Link>
        <Link href="/report" class="block hover:text-yellow-400 transition">Пријави корупција</Link>
      </div>
    </header>

    <!-- MAIN -->
    <main class="flex-1 w-full sm:w-4/5 lg:w-1/2 mx-auto py-8 sm:py-10 px-4 sm:px-6">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-6">
        Пријави ја твојата приказна
      </h2>

      <div v-if="$page.props.flash?.success"
        class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
        {{ $page.props.flash.success }}
      </div>

      <!-- FORM -->
      <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 sm:p-6 space-y-5">

        <!-- Municipality -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Општина</label>
          <select v-model="form.municipality_name" class="w-full rounded border-gray-300">
            <option value="" disabled>— Одбери општина —</option>
            <option v-for="m in municipalities" :key="m.name" :value="m.name">
              {{ m.name }}
            </option>
          </select>
          <p v-if="form.errors.municipality_name" class="text-sm text-red-600 mt-1">
            {{ form.errors.municipality_name }}
          </p>
        </div>


        <!-- Sector -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Сектор</label>
          <select v-model="form.sector" class="w-full rounded border-gray-300">
            <option value="" disabled>— Одбери сектор —</option>
            <option v-for="s in sectors" :key="s" :value="s">{{ s }}</option>
          </select>
          <p v-if="form.errors.sector" class="text-sm text-red-600 mt-1">
            {{ form.errors.sector }}
          </p>
        </div>

        <!-- Gender -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Пол</label>
          <select v-model="form.gender" class="w-full rounded border-gray-300">
            <option value="" disabled>— Вашиот пол —</option>
            <option value="male">Машки</option>
            <option value="female">Женски</option>
            <option value="other">Друго</option>
          </select>
          <p v-if="form.errors.gender" class="text-sm text-red-600 mt-1">
            {{ form.errors.gender }}
          </p>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Опис (опционално)</label>
          <textarea v-model="form.description" rows="4" class="w-full rounded border-gray-300"></textarea>
          <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Evidence -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Линк до доказ (опционално)</label>
          <input v-model="form.evidence_url" type="url" class="w-full rounded border-gray-300"
            placeholder="https://...">
          <p v-if="form.errors.evidence_url" class="text-sm text-red-600 mt-1">
            {{ form.errors.evidence_url }}
          </p>
        </div>

        <!-- Submit -->
        <div class="pt-2">
          <button type="submit" :disabled="form.processing"
            class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-5 py-2.5 font-semibold text-black hover:bg-yellow-400 disabled:opacity-50 w-full sm:w-auto">
            Испрати пријава
          </button>
        </div>
      </form>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 mt-8">
      <div
        class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm space-y-4 md:space-y-0">
        <p>© 2025 Multus. All rights reserved.</p>

        <div class="flex gap-4">
          <a target="_blank" href="https://www.facebook.com/MultusCentar/">
            <img src="../../images/facebook.png" class="w-5" alt="">
          </a>
          <a target="_blank" href="https://www.instagram.com/centar.multus/">
            <img src="../../images/instagram.png" class="w-5" alt="">
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>


<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
const isOpen = ref(false);




const props = defineProps({
  sectors: Array,
  municipalities: Array,
})



const form = useForm({
  municipality_name: '',
  sector: '',
  description: '',
  evidence_url: '',
  lat: null,
  lng: null,
})



const submit = () => {
  form.post(route('report.store'))
}
</script>
