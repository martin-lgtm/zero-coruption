<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">
    <header class="bg-gray-900 text-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="../../images/multus.png" class="w-14 sm:w-16" alt="Multus Logo" />
          <h1 class="text-xl font-bold tracking-wide">Multus</h1>
        </div>

        <button @click="isOpen = !isOpen"
          class="sm:hidden focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <nav class="hidden sm:flex gap-6 text-sm font-medium">
          <Link :href="route('map')" class="hover:text-yellow-400 transition">Мапа</Link>
          <Link :href="route('about')" class="hover:text-yellow-400 transition">За нас</Link>
          <Link href="/report" class="hover:text-yellow-400 transition">Пријави корупција</Link>
        </nav>
      </div>

      <div v-if="isOpen" class="sm:hidden px-4 pb-4 space-y-2">
        <Link :href="route('map')" class="block hover:text-yellow-400 transition">Мапа</Link>
        <Link :href="route('about')" class="block hover:text-yellow-400 transition">За нас</Link>
        <Link href="/report" class="block hover:text-yellow-400 transition">Пријави корупција</Link>
      </div>
    </header>

    <main class="flex px-4 py-6 lg:py-10">
      <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 w-full">
        <div class="bg-white rounded-xl shadow-lg border w-full lg:w-[50%] sm:h-[75vh] h-[53vh]">
          <div class="p-3">
            <div id="mk-map" class="h-[40vh] sm:h-[50vh] lg:h-[66vh] rounded-lg border"></div>

            <div class="mt-3 px-2">
              <div class="text-xs text-gray-500 font-medium mb-1">Легенда (вкупно случаи)</div>
              <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#eef2ff"></span> 0</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#c7d2fe"></span> 1–5</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#93c5fd"></span> 6–10</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#60a5fa"></span> 11–20</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#3b82f6"></span> 21–40</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block w-4 h-4 rounded"
                    style="background:#1d4ed8"></span> 41+</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex-1 flex flex-col space-y-6">
          <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800">Случаи на корупција по општини</h2>
            <p class="text-gray-500 text-sm">Визуелизација на податоците по региони и сектори</p>
          </div>

          <div v-if="hasData" class="bg-white rounded-xl border p-4 space-y-4">
            <div v-if="activeCard" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <h3 class="text-lg sm:text-xl font-bold text-gray-800">{{ activeCard.name }}</h3>
              <div class="text-sm text-gray-600">
                Вкупно случаи: <span class="font-semibold text-gray-900">{{ formatNumber(activeCard.total) }}</span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieSectorEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieGoodsEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieReasonsEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieAgeEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieBribeRequestedEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieBribeOfferedEl" class="w-full h-full"></div>
              </div>
              <div class="rounded-xl border p-3 flex items-center justify-center h-[260px]">
                <div ref="pieWouldReportEl" class="w-full h-full"></div>
              </div>

            </div>

          </div>

          <div v-else class="bg-white rounded-xl border p-6 text-center text-gray-500">
            Нема податоци за прикажување.
          </div>

          <section class="pb-10">
            <div class="max-w-7xl mx-auto bg-white rounded-xl border p-6">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                  Коментари за: <span v-if="activeCard" class="text-gray-900">{{ activeCard.name }}</span>
                  <span v-else class="text-gray-500">—</span>
                </h3>
                <span class="text-xs text-gray-500">{{ formatNumber(activeCard?.comments?.length || 0) }}</span>
              </div>

              <ul v-if="activeCard && activeCard.comments && activeCard.comments.length"
                class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
                <li v-for="(c, idx) in activeCard.comments" :key="idx"
                  class="bg-white border rounded p-3 text-sm text-gray-700">
                  <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>👤</span><span>{{ formatDate(c.created_at) }}</span>
                  </div>
                  <p>{{ c.text }}</p>
                </li>
              </ul>
              <p v-else class="text-sm text-gray-500">Нема коментари за прикажување.</p>
            </div>
          </section>
        </div>
      </div>
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
import { onMounted, onBeforeUnmount, ref, computed, nextTick } from 'vue'
import Highcharts from 'highcharts'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import municipalGeoFile from '../maps/mk_municipalities.json'
import { Link, usePage } from '@inertiajs/vue3'

delete L.Icon.Default.prototype._getIconUrl

const NAME_TO_CODE = {
  Gevgelija: 'MK-801',
  Bogdanci: 'MK-802',
  Bosilovo: 'MK-803',
  Valandovo: 'MK-804',
  Vasilevo: 'MK-805',
  Dojran: 'MK-806',
  Strumica: 'MK-807',
  Konce: 'MK-808',
  NovoSelo: 'MK-809',
  Radovis: 'MK-810',

  Aerodrom: 'MK-SK',
  Centar: 'MK-SK',
  Karpos: 'MK-SK',
  GaziBaba: 'MK-SK',
  Cair: 'MK-SK',
  GjorcePetrov: 'MK-SK',
  KiselaVoda: 'MK-SK',
  Butel: 'MK-SK',
  SutoOrizari: 'MK-SK',
  Saraj: 'MK-SK',
}


const isOpen = ref(false)
const pieSectorEl = ref(null)
const pieGoodsEl = ref(null)
const pieReasonsEl = ref(null)
const pieAgeEl = ref(null)
const pieBribeRequestedEl = ref(null)
const pieBribeOfferedEl = ref(null)
const pieWouldReportEl = ref(null)


const pies = {
  sector: null,
  goods: null,
  reasons: null,
  ages: null,
  bribe_requested: null,
  bribe_offered: null,
  would_report: null,
}
let map, geoLayer
const page = usePage()
const municipalities = computed(() => page.props.municipalities || [])
const hasData = computed(() => Array.isArray(municipalities.value) && municipalities.value.length > 0)

const corruptionData = computed(() => {
  const out = {}
  for (const m of (municipalities.value || [])) {
    out[m.key] = {
      name: m.name,
      total: m.total || 0,
      comments: m.comments || [],
      charts: m.charts || { sectors: {}, goods: {}, reasons: {}, ages: {}, would_report: {} },
    }
  }
  return out
})

const activeCard = ref(null)

function formatNumber(n) { return new Intl.NumberFormat('mk-MK').format(n || 0) }
function formatDate(iso) { return iso ? new Date(iso).toLocaleString('mk-MK') : '' }

function toPieSeries(obj) {
  const entries = Object.entries(obj || {})
  const sum = entries.reduce((a, [, v]) => a + (v || 0), 0)
  if (!sum) return []
  return entries.map(([name, count]) => ({ name, y: (count / sum) * 100, raw: count }))
}

function renderPie(targetRef, chartKey, title, dataset) {
  const series = toPieSeries(dataset)
  const el = targetRef.value
  if (!el) return
  if (!pies[chartKey]) {
    pies[chartKey] = Highcharts.chart(el, {
      chart: { type: 'pie' },
      title: { text: title },
      accessibility: { enabled: false },
      plotOptions: {
        pie: { innerSize: '55%', dataLabels: { enabled: true, format: '{point.percentage:.2f} %' }, showInLegend: false },
      },
      tooltip: { pointFormat: '<b>{point.raw}</b>  ({point.percentage:.2f}%)' },
      series: [{ name: 'Случаи', data: series }],
      credits: { enabled: false },
    })
  } else {
    pies[chartKey].setTitle({ text: title })
    pies[chartKey].series[0].setData(series, true)
  }
}

function refreshPies(code) {
  const rec = corruptionData.value[code]
  if (!rec) return
  nextTick(() => {
    renderPie(pieSectorEl, 'sector', 'Процент по сектор', rec.charts.sectors)
    renderPie(pieGoodsEl, 'goods', 'Вид на добра', rec.charts.goods)
    renderPie(pieReasonsEl, 'reasons', 'Причина за мито', rec.charts.reasons)
    renderPie(pieAgeEl, 'ages', 'Возрасни групи', rec.charts.ages)
    renderPie(pieBribeRequestedEl, 'bribe_requested', 'Побарување на мито', rec.charts.bribe_requested)
    renderPie(pieBribeOfferedEl, 'bribe_offered', 'Понудување на мито', rec.charts.bribe_offered)
    renderPie(pieWouldReportEl, 'would_report', 'Би пријавиле ако е безбедно?', rec.charts.would_report)

  })
}


function colorForCases(n) {
  if (n == null) return '#e5e7eb'
  if (n === 0) return '#eef2ff'
  if (n <= 5) return '#c7d2fe'
  if (n <= 10) return '#93c5fd'
  if (n <= 20) return '#60a5fa'
  if (n <= 40) return '#3b82f6'
  return '#1d4ed8'
}
function baseStyleFor(code) {
  const n = corruptionData.value[code]?.total
  return { color: '#fff', weight: 1, fillColor: colorForCases(n), fillOpacity: 0.95 }
}
function highlightStyleFor(code) { return { ...baseStyleFor(code), weight: 2, fillOpacity: 1 } }

onMounted(() => {
  const municipalGeo = {
    ...municipalGeoFile,
    features: municipalGeoFile.features.map(f => {
      const code = f.properties?.code || NAME_TO_CODE[f.properties?.shapeName]
      return { ...f, properties: { ...f.properties, code } }
    }),
  }

  const mapOpts = { zoomControl: true, attributionControl: false }
  const center = [41.6, 21.7]
  map = L.map('mk-map', mapOpts).setView(center, 8)

  geoLayer = L.geoJSON(municipalGeo, {
    style: f => baseStyleFor(f.properties.code),
    onEachFeature: (feature, layer) => {
      const code = feature.properties.code
      layer.on('mouseover', () => { layer.setStyle(highlightStyleFor(code)); layer.bringToFront() })
      layer.on('mouseout', () => { if (activeCard.value?.code !== code) layer.setStyle(baseStyleFor(code)) })
      layer.on('click', () => {
        geoLayer.eachLayer(l => l.setStyle(baseStyleFor(l.feature.properties.code)))
        layer.setStyle(highlightStyleFor(code))
        const rec = corruptionData.value[code]
        activeCard.value = rec ? { ...rec, code } : null
        if (rec) refreshPies(code)
      })
      const rec = corruptionData.value[code]
      layer.bindTooltip(rec ? `${rec.name}: ${formatNumber(rec.total)} случаи` : 'Нема податоци', { sticky: true })
      layer.on('add', () => layer.getElement()?.classList.add('cursor-pointer'))
    },
  }).addTo(map)

  if (geoLayer.getLayers().length) map.fitBounds(geoLayer.getBounds(), { padding: [20, 20] })
  map.whenReady(() => map.invalidateSize())

  const codes = Object.keys(corruptionData.value)
  const initial = codes.includes('MK-801') ? 'MK-801' : codes[0]
  if (initial) {
    const rec = corruptionData.value[initial]
    activeCard.value = { ...rec, code: initial }
    refreshPies(initial)
    geoLayer.eachLayer(l => {
      if (l.feature?.properties?.code === initial) {
        l.setStyle(highlightStyleFor(initial))
        l.bringToFront()
      }
    })
  }
})

onBeforeUnmount(() => {
  if (map) map.remove()
  Object.values(pies).forEach(p => p?.destroy())
})
</script>


<style scoped>
#mk-map {
  background: #fff;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
