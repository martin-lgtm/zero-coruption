<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- HEADER -->
    <header class="bg-gray-900 text-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="../../images/multus.png" class="w-12 sm:w-14" alt="Multus Logo" />
          <h1 class="text-lg sm:text-xl font-bold tracking-wide">Multus</h1>
        </div>

        <button @click="isOpen = !isOpen"
          class="sm:hidden focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <!-- burger -->
            <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <!-- X -->
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <nav class="hidden sm:flex gap-6 text-sm font-medium">
          <Link :href="route('map')" class="hover:text-yellow-400 transition">Мапа</Link>
          <Link :href="route('about')" class="hover:text-yellow-400 transition">За нас</Link>
          <Link :href="route('report.create')" class="hover:text-yellow-400 transition">Сподели приказна</Link>
        </nav>
      </div>

      <div v-if="isOpen" class="sm:hidden px-4 pb-4 space-y-2">
        <Link :href="route('map')" class="block hover:text-yellow-400 transition">Мапа</Link>
        <Link :href="route('about')" class="block hover:text-yellow-400 transition">За нас</Link>
        <Link :href="route('report.create')" class="block hover:text-yellow-400 transition">Сподели приказна</Link>
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
        <!-- 1) Општина -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Во која општина се одвивало коруптивното дејство?
          </label>
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

        <!-- 2) Пол -->
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

        <!-- 3) Возраст -->
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

        <!-- 4) Поткуп - побаран -->
        <div>
          <label class="font-semibold">Дали сте биле во ситуација каде што некој ви побарал поткуп?</label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.bribe_requested" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.bribe_requested" /> Не</label>
          </div>
          <p v-if="form.errors.bribe_requested" class="text-sm text-red-600 mt-1">
            {{ form.errors.bribe_requested }}
          </p>
        </div>

        <!-- 5) Поткуп - понуден -->
        <div>
          <label class="font-semibold">Дали сте биле во ситуација каде што сами сте понудиле поткуп?</label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.bribe_offered" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.bribe_offered" /> Не</label>
          </div>
          <p v-if="form.errors.bribe_offered" class="text-sm text-red-600 mt-1">
            {{ form.errors.bribe_offered }}
          </p>
        </div>

        <!-- 6) Би пријавиле ако е безбедно -->
        <div>
          <label class="font-semibold">
            Доколку има безбеден начин за пријавување корупција или знаете дека случајот би бил процесиран, дали би
            пријавиле?
          </label>
          <div class="flex items-center gap-4 mt-1">
            <label><input type="radio" value="Да" v-model="form.would_report_if_safe" /> Да</label>
            <label><input type="radio" value="Не" v-model="form.would_report_if_safe" /> Не</label>
          </div>
          <p v-if="form.errors.would_report_if_safe" class="text-sm text-red-600 mt-1">
            {{ form.errors.would_report_if_safe }}
          </p>
        </div>

        <!-- 7) Сектор (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Одберете сектор во кој се одвивало коруптивното дејство
          </label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="s in sectors" :key="s.id" class="flex items-start gap-2">
              <input type="checkbox" :value="s.id" v-model="form.sector_ids" class="mt-1" />
              <span class="text-sm text-gray-800">{{ s.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.sector_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.sector_ids }}
          </p>
        </div>

        <!-- 8) Добра (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Каков вид на добра се користени за поткуп?</label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="g in goods" :key="g.id" class="flex items-start gap-2">
              <input type="checkbox" :value="g.id" v-model="form.good_ids" class="mt-1" />
              <span class="text-sm text-gray-800">{{ g.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.good_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.good_ids }}
          </p>
        </div>

        <!-- 9) Причина (multi) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Причина за поткуп</label>
          <div class="grid sm:grid-cols-2 gap-2">
            <label v-for="r in reasons" :key="r.id" class="flex items-start gap-2">
              <input type="checkbox" :value="r.id" v-model="form.reason_ids" class="mt-1" />
              <span class="text-sm text-gray-800">{{ r.name }}</span>
            </label>
          </div>
          <p v-if="form.errors.reason_ids" class="text-sm text-red-600 mt-1">
            {{ form.errors.reason_ids }}
          </p>
        </div>

        <!-- 10) Избор -->
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

        <!-- 11) Ранг -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Од кој пол и ранг беше административецот?</label>
          <select v-model="form.admin_rank" class="w-full rounded border-gray-300">
            <option :value="null" disabled>— Одбери —</option>
            <option value="Машки - висок ранг (раководна или позиција на одговорно лице)">
              Машки - висок ранг (раководна или позиција на одговорно лице)
            </option>
            <option value="Женски - висок ранг (раководна или позиција на одговорно лице)">
              Женски - висок ранг (раководна или позиција на одговорно лице)
            </option>
            <option value="Машки - низок ранг (вработен во јавна администрација на секоја друга позиција)">
              Машки - низок ранг (вработен во јавна администрација на секоја друга позиција)
            </option>
            <option value="Женски - низок ранг (вработена во јавна администрација на секоја друга позиција)">
              Женски - низок ранг (вработена во јавна администрација на секоја друга позиција)
            </option>
            <option value="Преферирам да не одговорам">Преферирам да не одговорам</option>
          </select>
          <p v-if="form.errors.admin_rank" class="text-sm text-red-600 mt-1">
            {{ form.errors.admin_rank }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Сподели ја твојата приказна
          </label>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Опишете ја ситуацијата - која била причината за побараниот или понудениот поткуп?
          </label>
          <textarea v-model="form.story" rows="4" class="w-full rounded border-gray-300"></textarea>
          <p v-if="form.errors.story" class="text-sm text-red-600 mt-1">
            {{ form.errors.story }}
          </p>

          <label class="flex items-start gap-3 mt-3">
            <input v-model="form.consent_publish" type="checkbox"
              class="mt-1 h-4 w-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400" />
            <span class="text-sm text-gray-700 leading-relaxed">
              Се согласувам мојата споделена приказна да биде објавена јавно на оваа веб страна.
            </span>
          </label>
        </div>

        <!-- 13) General consent -->
        <label class="flex items-start gap-3">
          <input type="checkbox" v-model="form.consent"
            class="mt-1 h-4 w-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400" />
          <span class="text-sm text-gray-700 leading-relaxed">
            Потврдувам дека сум согласен/а со обработката и користењето на доставените податоци согласно со
            <button type="button" class="text-blue-600 hover:underline" @click="showPrivacyPolicy = true">
              Политиката за приватност
            </button>.
          </span>
        </label>


        <!-- Submit -->
        <div class="pt-2">
          <button type="submit" :disabled="form.processing"
            class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-5 py-2.5 font-semibold text-black hover:bg-yellow-400 disabled:opacity-50 w-full sm:w-auto">
            Испрати пријава
          </button>
        </div>
      </form>
    </main>

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
        <img class="w-48" src="../../images/eu with you copy.png" alt="">
        <img class="w-72" src="../../images/Seldi_Logo-removebg-preview.png" alt="">
        <img class="w-[380px]" src="../../images/MULTUS_final_Artboard_11_copy_2-removebg-preview.png" alt="">
      </div>
      <div class="max-w-7xl mx-auto px-4 py-4 border-t border-gray-700 text-center text-sm">
        <p>© 2025 Multus. All rights reserved.</p>
      </div>
    </footer>

    <PublishWarning v-if="showPublishWarning" @proceed="submitWithoutPublishing" @allow="allowPublishing" />

    <ConsentWarning v-if="showConsentWarning" @accept="acceptConsent" />
    <PrivacyPolicyModal v-if="showPrivacyPolicy" @close="showPrivacyPolicy = false" />

  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import PublishWarning from '@/Pages/PublishWarning.vue'
import ConsentWarning from '@/Pages/ConsentWarning.vue'
import PrivacyPolicyModal from '@/Pages/PrivacyPolicyModal.vue'

const props = defineProps({
  municipalities: Array,
  sectors: Array,
  goods: Array,
  reasons: Array,
})

const showPrivacyPolicy = ref(false)

const isOpen = ref(false)   

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
  consent: false,
  consent_publish: false,
})

const showPublishWarning = ref(false)
const showConsentWarning = ref(false)

const submit = () => {
  // 1) must accept main consent
  if (!form.consent) {
    showConsentWarning.value = true
    return
  }

  // 2) story exists but no publish consent → show publish modal
  if (form.story && !form.consent_publish) {
    showPublishWarning.value = true
    return
  }

  form.post(route('report.store'))
}

// called when user clicks "Потврди согласност" in modal
const acceptConsent = () => {
  form.consent = true        // ✅ auto-check the checkbox
  showConsentWarning.value = false
  // user can now press submit again
}

const submitWithoutPublishing = () => {
  showPublishWarning.value = false
  form.post(route('report.store'))
}

const allowPublishing = () => {
  form.consent_publish = true
  showPublishWarning.value = false
  // optionally submit here too
  // form.post(route('report.store'))
}
</script>
