<template>
  <div class="`min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100`">

    <header class="bg-gray-900 text-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="../../images/multus.png" class="w-20" alt="">
          <h1 class="text-xl font-bold tracking-wide">Multus</h1>
        </div>
        <nav class="hidden md:flex gap-6 text-sm font-medium">
          <Link :href="route('map')" class="hover:text-yellow-400 transition">Map</Link>
          <Link :href="route('about')" class="hover:text-yellow-400 transition">About</Link>
          <Link href="/contact" class="hover:text-yellow-400 transition">Contact</Link>
        </nav>
      </div>
    </header>

    <main class="flex px-4 py-6 lg:py-10">
      <div class="flex gap-8 w-full">

        <div class="bg-white rounded-xl shadow-lg border flex-1">
          <div class="p-3">
            <div id="mk-map" class="h-[66vh] rounded-lg border"></div>
          </div>
        </div>

        <div class="flex-1 flex flex-col space-y-6">

          <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-800">
              Случаи на корупција по општини
            </h2>
            <p class="text-gray-500 text-sm">Визуелизација на податоците по региони и сектори</p>
          </div>

          <div class="bg-white rounded-xl border p-4 flex w-full">
            <div ref="chartEl" class="h-[40vh] w-full"></div>

            <div v-if="activeCard" class="bg-white rounded-xl border shadow-lg p-5 hover:shadow-md self-center w-full">
              <h3 class="text-xl font-bold text-center text-gray-800 mb-4">
                {{ activeCard.name }}
              </h3>

              <div class="space-y-3 text-sm text-gray-700">
                <div class="flex justify-between font-semibold border-b pb-2 text-gray-900">
                  <span>Вкупно случаи</span>
                  <span>{{ formatNumber(activeCard.total) }}</span>
                </div>

                <div v-for="r in activeCard.sectors" :key="r.label"
                  class="flex justify-between hover:bg-yellow-50 p-1 rounded">
                  <span>{{ r.label }}</span>
                  <span class="font-medium text-gray-900">{{ formatNumber(r.value) }}</span>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 mt-8">
      <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm">
        <p>© 2025 Multus. All rights reserved.</p>
        <a target="_blank" href="https://www.facebook.com/MultusCentar/">
          <img src="../../images/facebook.png"  class="w-5" alt="">
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
import { corruptionData } from '../data/corruptionData.js'
import { Link } from '@inertiajs/vue3'

delete L.Icon.Default.prototype._getIconUrl

const NAME_TO_CODE = {
  Gevgelija: 'MK-801',
  Bitola: 'MK-705',
  Bogdanci: 'MK-802',
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

const chartEl = ref(null)
let chart, map, geoLayer
const activeCard = ref(null)

function formatNumber(n) {
  return new Intl.NumberFormat('mk-MK').format(n)
}

// ---- Build donut series for Highcharts from a municipality record ----
function seriesFromMunicipality(code) {
  const rec = corruptionData[code]
  if (!rec) return null
  const total = rec.sectors.reduce((s, x) => s + x.value, 0)
  const pctSeries = rec.sectors.map(x => ({
    name: x.label,
    y: total ? (x.value / total) * 100 : 0,
  }))
  // What we also want to show in the card:
  return {
    title: rec.name,
    data: { name: rec.name, sectors: rec.sectors, total },
    series: pctSeries,
  }
}

// ---- Style from total cases (choropleth) ----
function totalCases(code) {
  const rec = corruptionData[code]
  return rec ? rec.sectors.reduce((s, x) => s + x.value, 0) : null
}

function baseStyleFor(code) {
  const n = totalCases(code)
  return {
    color: '#ffffff',    // white borders between municipalities
    weight: 1,
    fillColor: colorForCases(n),
    fillOpacity: 0.95,
  }
}
function highlightStyleFor(code) {
  const s = baseStyleFor(code)
  return { ...s, weight: 2, fillOpacity: 1 }
}

// ---- Donut chart ----
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

// ---- Mount ----
onMounted(() => {
  // 1) Ensure every feature has properties.code mapped from shapeName
  const municipalGeo = {
    ...municipalGeoFile,
    features: municipalGeoFile.features.map(f => {
      const code = f.properties?.code || NAME_TO_CODE[f.properties?.shapeName]
      return { ...f, properties: { ...f.properties, code } }
    }),
  }

  map = L.map('mk-map', { zoomControl: true, attributionControl: false })
    .setView([41.6, 21.7], 8)

  // 2) Draw polygons as a corruption choropleth keyed by `code`
  geoLayer = L.geoJSON(municipalGeo, {
    style: f => baseStyleFor(f.properties?.code),
    onEachFeature: (feature, layer) => {
      const code = feature.properties?.code;

      // Keep base style
      layer.on('mouseover', () => {
        layer.setStyle(highlightStyleFor(code));
        layer.bringToFront();
      });

      layer.on('mouseout', () => {
        // Only revert style if it's not the active selection
        if (activeCard.value?.code !== code) {
          layer.setStyle(baseStyleFor(code));
        }
      });

      // Click event: set active selection
      layer.on('click', () => {
        // Reset style for all layers first
        geoLayer.eachLayer(l => l.setStyle(baseStyleFor(l.feature.properties.code)));

        // Highlight this one
        layer.setStyle(highlightStyleFor(code));

        const s = seriesFromMunicipality(code);
        if (s) {
          activeCard.value = { ...s.data, code };
          buildChart(s.series, 'Процент по сектор');
        } else {
          activeCard.value = null;
          buildChart([], '');
        }
      });

      // Tooltip: name + total cases
      const rec = corruptionData[code];
      const tip = rec
        ? `${rec.name}: вкупно ${formatNumber(rec.sectors.reduce((t, x) => t + x.value, 0))} случаи`
        : (feature.properties?.shapeName || '—') + ': нема податоци';
      layer.bindTooltip(tip, { sticky: true });

      layer.on('add', () => layer.getElement()?.classList.add('cursor-pointer'));
    }

  }).addTo(map)

  if (geoLayer.getLayers().length) {
    map.fitBounds(geoLayer.getBounds(), { padding: [20, 20] })
  }
  map.whenReady(() => map.invalidateSize())

  // 3) Initial selection (Gevgelija if present)
  const initialCode = 'MK-801'
  const init = seriesFromMunicipality(initialCode)
  if (init) {
    activeCard.value = { ...init.data }
    buildChart(init.series, 'Процент по сектор')
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
