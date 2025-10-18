<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- HEADER -->
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

    <!-- MAIN -->
    <main class="flex px-4 py-6 lg:py-10">
      <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 w-full">
        <!-- MAP -->
        <div class="bg-white rounded-xl shadow-lg border flex-1">
          <div class="p-3">
            <div id="mk-map" class="h-[40vh] sm:h-[50vh] lg:h-[66vh] rounded-lg border"></div>
          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="flex-1 flex flex-col space-y-6">
          <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800">
              Случаи на корупција по општини
            </h2>
            <p class="text-gray-500 text-sm">Визуелизација на податоците по региони и сектори</p>
          </div>

          <div class="bg-white rounded-xl border p-4 flex flex-col lg:flex-row w-full gap-4">
            <!-- Donut -->
            <div ref="chartEl" class="h-[30vh] sm:h-[40vh] w-full lg:w-1/2"></div>

            <!-- Card -->
            <div v-if="activeCard"
              class="bg-white rounded-xl border shadow-lg p-5 hover:shadow-md self-center w-full lg:w-1/2 max-h-[40vh] overflow-y-auto">
              <h3 class="text-lg sm:text-xl font-bold text-center text-gray-800 mb-4">
                {{ activeCard.name }}
              </h3>

              <div class="space-y-4 text-sm text-gray-700">
                <div class="flex justify-between font-semibold border-b pb-2 text-gray-900">
                  <span>Вкупно случаи</span>
                  <span>{{ formatNumber(activeCard.total) }}</span>
                </div>

                <!-- Sector list with percentages + comments under each sector -->
                <div v-for="s in activeCard.sectors" :key="s.label">
<div class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded border">
                    <span class="font-semibold text-red-400">{{ s.label }}</span>
                    <span class="text-gray-700">
                      {{ formatNumber(s.count) }} • {{ s.pct.toFixed(2) }} %
                    </span>
                  </div>

                  <!-- Scrollable comments per sector -->
                  <ul v-if="s.comments && s.comments.length" class="mt-2 space-y-2 max-h-[250px] overflow-y-auto pr-1">
                    <li v-for="(c, idx) in s.comments" :key="idx"
                      class="bg-white border rounded p-2 text-sm text-gray-700">
                      <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>👤</span>
                        <span>{{ new Date(c.created_at).toLocaleString('mk-MK') }}</span>
                      </div>
                      <p>{{ c.text }}</p>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- /Card -->
          </div>
        </div> <!-- /RIGHT PANEL -->
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 mt-8">
      <div
        class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm space-y-2 md:space-y-0">
        <p>© 2025 Multus. All rights reserved.</p>
        <a target="_blank" href="https://www.facebook.com/MultusCentar/">
          <img src="../../images/facebook.png" class="w-5" alt="" />
        </a>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue'
import Highcharts from 'highcharts'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import municipalGeoFile from '../maps/mk_municipalities.json'
import { Link, usePage } from '@inertiajs/vue3'

// Fix Leaflet default icon urls under Vite
delete L.Icon.Default.prototype._getIconUrl

// If your GeoJSON lacks properties.code for some features,
// we derive it from shapeName here:
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
}

const isOpen = ref(false)
const chartEl = ref(null)
const activeCard = ref(null)
let chart, map, geoLayer

const page = usePage()
const municipalities = page.props.municipalities || []

function formatNumber(n) {
  return new Intl.NumberFormat('mk-MK').format(n)
}

// Build dataset consumed by the rest of the component
const corruptionData = Object.fromEntries(
  municipalities.map(m => {
    const total = Object.values(m.stats || {}).reduce((a, b) => a + b, 0)
    const sectors = Object.entries(m.stats || {}).map(([label, count]) => {
      const pct = total ? (count / total) * 100 : 0
      // Attach comments for this sector (array) coming from controller
      const commentsForSector = (m.comments && m.comments[label]) ? m.comments[label] : []
      return { label, count, pct, comments: commentsForSector }
    })
    return [
      m.key,
      { name: m.name, total, sectors }
    ]
  })
)

function colorForCases(n) {
  if (n == null) return '#e5e7eb'
  if (n === 0) return '#eef2ff'
  if (n <= 5) return '#c7d2fe'
  if (n <= 10) return '#93c5fd'
  if (n <= 20) return '#60a5fa'
  if (n <= 40) return '#3b82f6'
  return '#1d4ed8'
}

function seriesFromMunicipality(code) {
  const rec = corruptionData[code]
  if (!rec) return null
  const series = rec.sectors.map(s => ({ name: s.label, y: s.pct }))
  return { title: rec.name, data: rec, series }
}

function buildChart(seriesData, title = '') {
  if (!chart) {
    chart = Highcharts.chart(chartEl.value, {
      chart: { type: 'pie' },
      title: { text: title || 'Структура по сектори' },
      accessibility: { enabled: false },
      plotOptions: {
        pie: {
          innerSize: '55%',
          dataLabels: { enabled: true, format: '{point.percentage:.2f} %' },
          showInLegend: false,
        },
      },
      series: [{ name: 'Случаи', data: seriesData }],
      credits: { enabled: false },
    })
  } else {
    chart.setTitle({ text: title || 'Структура по сектори' })
    chart.series[0].setData(seriesData, true)
  }
}

function baseStyleFor(code) {
  const n = corruptionData[code]?.total
  return {
    color: '#ffffff',
    weight: 1,
    fillColor: colorForCases(n),
    fillOpacity: 0.95,
  }
}
function highlightStyleFor(code) {
  return { ...baseStyleFor(code), weight: 2, fillOpacity: 1 }
}

onMounted(() => {
  // Ensure each feature has a usable `properties.code`
  const municipalGeo = {
    ...municipalGeoFile,
    features: municipalGeoFile.features.map(f => {
      const code = f.properties?.code || NAME_TO_CODE[f.properties?.shapeName]
      return { ...f, properties: { ...f.properties, code } }
    }),
  }

  map = L.map('mk-map', { zoomControl: true, attributionControl: false })
    .setView([41.6, 21.7], 8)

  geoLayer = L.geoJSON(municipalGeo, {
    style: f => baseStyleFor(f.properties.code),
    onEachFeature: (feature, layer) => {
      const code = feature.properties.code

      // Hover effects
      layer.on('mouseover', () => {
        layer.setStyle(highlightStyleFor(code))
        layer.bringToFront()
      })
      layer.on('mouseout', () => {
        if (activeCard.value?.code !== code) {
          layer.setStyle(baseStyleFor(code))
        }
      })

      // Click -> set selection and update donut + card
      layer.on('click', () => {
        geoLayer.eachLayer(l => l.setStyle(baseStyleFor(l.feature.properties.code)))
        layer.setStyle(highlightStyleFor(code))

        const s = seriesFromMunicipality(code)
        if (s) {
          activeCard.value = { ...s.data, code }
          buildChart(s.series, 'Процент по сектор')
        } else {
          activeCard.value = null
          buildChart([], '')
        }
      })

      // Tooltip
      const rec = corruptionData[code]
      const tip = rec ? `${rec.name}: ${formatNumber(rec.total)} случаи` : 'Нема податоци'
      layer.bindTooltip(tip, { sticky: true })

      layer.on('add', () => layer.getElement()?.classList.add('cursor-pointer'))
    },
  }).addTo(map)

  // Fit + initial selection (prefer MK-801 if present)
  if (geoLayer.getLayers().length) {
    map.fitBounds(geoLayer.getBounds(), { padding: [20, 20] })
  }
  map.whenReady(() => map.invalidateSize())

  const codesWithData = Object.keys(corruptionData)
  const initialCode = codesWithData.includes('MK-801') ? 'MK-801' : codesWithData[0]
  const init = initialCode ? seriesFromMunicipality(initialCode) : null
  if (init) {
    activeCard.value = { ...init.data, code: initialCode }
    buildChart(init.series, 'Процент по сектор')
    geoLayer.eachLayer(l => {
      if (l.feature?.properties?.code === initialCode) {
        l.setStyle(highlightStyleFor(initialCode))
        l.bringToFront()
      }
    })
  } else {
    buildChart([], '')
  }
})

onBeforeUnmount(() => {
  if (map) map.remove()
  if (chart) chart.destroy()
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
