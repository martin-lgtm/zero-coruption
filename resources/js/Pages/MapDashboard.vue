<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- MAP -->
    <div id="mk-map" class="h-[70vh] rounded border"></div>

    <!-- RIGHT SIDE: CHART + CARD -->
    <div class="p-4">
      <h2 class="text-2xl font-bold mb-2">Случаи на корупција по општини</h2>
      <div ref="chartEl" class="h-[55vh]"></div>

      <div v-if="activeCard" class="mt-6 rounded border p-4 shadow-sm" style="max-width: 640px">
        <h3 class="text-xl font-extrabold text-center mb-3">{{ activeCard.name }}</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between font-semibold border-b pb-1">
            <span>Вкупно случаи</span>
            <span>{{ formatNumber(activeCard.total) }}</span>
          </div>
          <div v-for="r in activeCard.sectors" :key="r.label" class="flex justify-between">
            <span>{{ r.label }}</span>
            <span class="font-semibold">{{ formatNumber(r.value) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue'
import Highcharts from 'highcharts'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

import municipalGeoFile from '../maps/mk_municipalities.json' 
import { corruptionData } from '../data/corruptionData.js'

delete L.Icon.Default.prototype._getIconUrl

const NAME_TO_CODE = {
  Gevgelija: 'MK-801',
  Bitola: 'MK-705',
  Bogdanci: 'MK-802',
}


function colorForCases(n) {
  if (n == null) return '#e5e7eb'          // no data
  if (n === 0) return '#eef2ff'            // 0
  if (n <= 5) return '#c7d2fe'             // 1–5
  if (n <= 10) return '#93c5fd'            // 6–10
  if (n <= 20) return '#60a5fa'            // 11–20
  if (n <= 40) return '#3b82f6'            // 21–40
  return '#1d4ed8'                          // >40
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
      const code = feature.properties?.code

      layer.on('mouseover', () => {
        layer.setStyle(highlightStyleFor(code))
        layer.bringToFront()
        const s = seriesFromMunicipality(code)
        if (s) {
          activeCard.value = { ...s.data }
          buildChart(s.series, 'Процент по сектор')
        } else {
          activeCard.value = null
          buildChart([], '')
        }
      })

      layer.on('mouseout', () => layer.setStyle(baseStyleFor(code)))

      // Tooltip: name + total cases (or “Нема податоци”)
      const rec = corruptionData[code]
      const tip = rec
        ? `${rec.name}: вкупно ${formatNumber(rec.sectors.reduce((t, x) => t + x.value, 0))} случаи`
        : (feature.properties?.shapeName || '—') + ': нема податоци'
      layer.bindTooltip(tip, { sticky: true })

      layer.on('add', () => layer.getElement()?.classList.add('cursor-pointer'))
    },
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
#mk-map { background: #fff; }
.cursor-pointer { cursor: pointer; }
</style>
