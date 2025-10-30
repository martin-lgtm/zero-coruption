<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- HEADER (same as yours) -->
    <header class="bg-gray-900 text-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="../../images/multus.png" class="w-12 sm:w-14" alt="Multus Logo">
          <h1 class="text-lg sm:text-xl font-bold tracking-wide">Multus</h1>
        </div>
        <nav class="hidden sm:flex gap-6 text-sm font-medium">
          <Link :href="route('map')" class="hover:text-yellow-400 transition">Мапа</Link>
          <Link :href="route('about')" class="hover:text-yellow-400 transition">За нас</Link>
          <Link :href="route('report.create')" class="hover:text-yellow-400 transition">Пријави корупција</Link>
        </nav>
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

      <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 sm:p-6 space-y-8">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Во која општина се одвивало коруптивното
            дејство?</label>
          <select v-model="form.municipality_id" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери општина —</option>
            <option v-for="m in municipalities" :key="m.id" :value="m.id">
              {{ m.name }}
            </option>
          </select>
          <p v-if="form.errors.municipality_id" class="text-sm text-red-600 mt-1">
            {{ form.errors.municipality_id }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Пол</label>
          <select v-model="form.gender" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери —</option>
            <option value="Женски">Женски</option>
            <option value="Машки">Машки</option>
          </select>
          <p v-if="form.errors.gender" class="text-sm text-red-600 mt-1">
            {{ form.errors.gender }}
          </p>
        </div>






        <!-- AGE RANGE -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Возраст</label>
          <select v-model="form.age_range" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери возраст —</option>
            <option value="18-25">18–24</option>
            <option value="26-35">25–34</option>
            <option value="36-45">35–44</option>
            <option value="46-55">45–54</option>
            <option value="65+">55+</option>
          </select>
          <p v-if="form.errors.age_range" class="text-sm text-red-600 mt-1">
            {{ form.errors.age_range }}
          </p>
        </div>


        <!-- 3) МИТО -->
        <!-- 1. Дали сте биле во ситуација каде што некој ви побарал мито? -->
        <div>
          <label class="font-semibold">Дали сте биле во ситуација каде што некој ви побарал мито?</label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.bribe_requested" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.bribe_requested" /> Не</label>
          </div>
          <p v-if="form.errors.bribe_requested" class="text-sm text-red-600 mt-1">
            {{ form.errors.bribe_requested }}
          </p>
        </div>

        <!-- 2. Дали сте биле во ситуација каде што сами сте понудиле мито? -->
        <div>
          <label class="font-semibold">Дали сте биле во ситуација каде што сами сте понудиле мито?</label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.bribe_offered" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.bribe_offered" /> Не</label>
          </div>
          <p v-if="form.errors.bribe_offered" class="text-sm text-red-600 mt-1">
            {{ form.errors.bribe_offered }}
          </p>
        </div>

        <!-- Би пријавиле ако е безбедно? -->
        <div>
          <label class="font-semibold"> Доколку има безбеден начин за пријавување корупција или знаете дека случајот би
            бил процесиран, дали би пријавиле?
          </label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.would_report_if_safe" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.would_report_if_safe" /> Не</label>
          </div>
          <p v-if="form.errors.would_report_if_safe" class="text-sm text-red-600 mt-1">
            {{ form.errors.would_report_if_safe }}
          </p>
        </div>



        <!-- 4) СЕКТОР (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Одберете сектор во кој се одвивало коруптивното
            дејство“</label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="s in sectors" :key="s.id" class="flex items-start gap-2">
              <input type="checkbox" :value="s.id" v-model="form.sector_ids" class="mt-1">
              <span class="text-sm text-gray-800">{{ s.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.sector_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.sector_ids }}
          </p>
        </div>

        <!-- 5) ДОБРА (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Во каков вид на добра?</label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="g in goods" :key="g.id" class="flex items-start gap-2">
              <input type="checkbox" :value="g.id" v-model="form.good_ids" class="mt-1">
              <span class="text-sm text-gray-800">{{ g.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.good_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.good_ids }}
          </p>
        </div>

        <!-- 6) ПРИЧИНА (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Причина за мито</label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="r in reasons" :key="r.id" class="flex items-start gap-2">
              <input type="checkbox" :value="r.id" v-model="form.reason_ids" class="mt-1">
              <span class="text-sm text-gray-800">{{ r.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.reason_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.reason_ids }}
          </p>
        </div>

        <!-- 7) ИЗБОР -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Дали чувствувавте дека имате избор?</label>
          <select v-model="form.felt_choice" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери —</option>
            <option value="Да">Да</option>
            <option value="Не">Не</option>
            <option value="Преферирам да не одговорам">Преферирам да не одговорам</option>
          </select>
          <p v-if="form.errors.felt_choice" class="text-sm text-red-600 mt-1">
            {{ form.errors.felt_choice }}
          </p>
        </div>

        <!-- 8) РАНГ -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Од кој пол и ранг беше административецот?</label>
          <select v-model="form.admin_rank" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери —</option>
            <option value="Машки - висок ранг (раководна или позиција на одговорно лице)">Машки - висок ранг (раководна
              или позиција на одговорно лице)</option>
            <option value="Женски - висок ранг (раководна или позиција на одговорно лице)">Женски - висок ранг
              (раководна или позиција на одговорно лице)</option>
            <option value="Машки - низок ранг (вработен во јавна администрација на секоја друга позиција)">Машки - низок
              ранг (вработен во јавна администрација на секоја друга позиција)</option>
            <option value="Женски - низок ранг (вработена во јавна администрација на секоја друга позиција)">Женски -
              низок ранг (вработена во јавна администрација на секоја друга позиција)</option>
            <option value="Преферирам да не одговорам">Преферирам да не одговорам</option>
          </select>
          <p v-if="form.errors.admin_rank" class="text-sm text-red-600 mt-1">
            {{ form.errors.admin_rank }}
          </p>
        </div>

        <!-- 9) ПРИКАЗНА -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Сподели ја твојата приказна</label>
          <label class="block text-sm font-medium text-gray-700 mb-1">Опишете ја ситуацијата - која била причината за
            побараниот или понудениот поткуп?</label>
          <textarea v-model="form.story" rows="4" class="w-full rounded border-gray-300"></textarea>
          <p v-if="form.errors.story" class="text-sm text-red-600 mt-1">
            {{ form.errors.story }}
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

    <!-- FOOTER (same as yours) -->
    <footer class="bg-gray-900 text-gray-400 mt-8">
      <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm">
        <p>Оваа активност се спроведува во рамки на проектот „Под маса: претпродба на
          интегритетот“ кој го спроведува Центар за едукација, култура и активизам Мултус.
          Проектот е дел од програмата за мали грантови на СЕЛДИ која се спроведува во
          рамки на проектот „Граѓанско општество за добро владеење и антикорупција во
          Југоисточна Европа: Градење на капацитети за застапување врз база на докази,
          влијание врз политики и граѓански ангажман (СЕЛДИ.нет)“, финансиран од
          Европската Унија. Оваа содржина е единствена одговорност на Центар за
          едукација, култура и активизам Мултус и не нужно ги одразува ставовите на
          Евопската Унија и СЕЛДИ.</p>


      </div>
      <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center">
        <img src="../../images/Funded_by_EU-removebg-preview.png" alt="">
        <img class="w-44" src="../../images/eu with you copy.png" alt="">
        <img class="w-44" src="../../images/Seldi_Logo-removebg-preview.png" alt="">
        <img class="w-44" src="../../images/MULTUS_final_Artboard_11_copy_2-removebg-preview.png" alt="">
      </div>
      <div class="max-w-7xl mx-auto px-4 py-4 border-t border-gray-700 text-center text-sm">
        <p>© 2025 Multus. All rights reserved.</p>

      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  municipalities: Array,
  sectors: Array,
  goods: Array,
  reasons: Array,
})

const form = useForm({
  municipality_id: null,
  gender: null,
  age_range: null,
  bribe_requested: null,
  bribe_offered: null,
  felt_choice: null,
  admin_rank: null,
  story: '',

  sector_ids: [],
  good_ids: [],
  reason_ids: [],
  would_report_if_safe: null,
})


const submit = () => {
  form.post(route('report.store'))
}
</script>
