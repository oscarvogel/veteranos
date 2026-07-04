<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Tarjetas</h1>
          <p class="mt-1 text-sm text-gray-500">Fair play por equipo y suspensiones vigentes</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Vista</label>
            <select v-model="tipo" @change="reload" class="select-modern">
              <option value="fairplay">Fair play</option>
              <option value="vigentes">Vigentes</option>
            </select>
          </div>

          <div v-if="tipo === 'fairplay'" class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Torneo</label>
            <select v-model="selectedTorneo" @change="loadTarjetas" class="select-modern">
              <option v-for="t in torneos" :key="t.id" :value="t.id">{{ t.nombre }}</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center gap-2 text-gray-400">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span class="text-sm">Cargando tarjetas...</span>
        </div>
      </div>
      <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ error }}</div>
      <div v-else-if="rows.length === 0" class="text-center py-12 text-gray-400 text-sm">No hay registros para mostrar.</div>

      <div v-else-if="tipo === 'fairplay'" class="table-card">
        <div class="grid grid-cols-12 gap-0">
          <div class="col-span-6 table-header-cell">Equipo</div>
          <div class="col-span-3 table-header-cell text-center">Amarillas</div>
          <div class="col-span-3 table-header-cell text-center">Rojas</div>
        </div>
        <div>
          <div v-for="row in rows" :key="row.idEquipo" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
            <div class="col-span-6 table-cell font-semibold text-gray-800">{{ row.equipo }}</div>
            <div class="col-span-3 table-cell text-center">
              <span class="badge-score bg-amber-50 text-amber-700">{{ row.amarillas }}</span>
            </div>
            <div class="col-span-3 table-cell text-center">
              <span class="badge-score bg-red-50 text-red-700">{{ row.rojas }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="table-card">
        <div class="grid grid-cols-12 gap-0">
          <div class="col-span-4 table-header-cell">Jugador</div>
          <div class="col-span-3 table-header-cell">Equipo</div>
          <div class="col-span-2 table-header-cell text-center">Sanción</div>
          <div class="col-span-3 table-header-cell">Motivo</div>
        </div>
        <div>
          <div v-for="row in rows" :key="row.idTarjeta" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
            <div class="col-span-4 table-cell font-semibold text-gray-800">{{ row.jugador }}</div>
            <div class="col-span-3 table-cell text-gray-600">{{ row.equipo }}</div>
            <div class="col-span-2 table-cell text-center">
              <span class="badge-score bg-red-50 text-red-700">{{ row.rojas }} roja</span>
            </div>
            <div class="col-span-3 table-cell text-gray-600">{{ row.motivo || 'Sin detalle' }}</div>
          </div>
        </div>
      </div>

      <div v-if="meta" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
          <button @click="prevPage" :disabled="meta.page <= 1" class="btn-ghost text-sm">Anterior</button>
          <button @click="nextPage" :disabled="meta.page >= meta.total_pages" class="btn-ghost text-sm">Siguiente</button>
        </div>
        <div class="text-sm text-gray-400">Página {{ meta.page }} de {{ meta.total_pages }} &mdash; {{ meta.total }} registros</div>
      </div>
    </div>
  </div>
</template>

<script>
import api, { getApiErrorMessage } from '../services/api'

export default {
  data() {
    return {
      torneos: [],
      selectedTorneo: null,
      tipo: 'fairplay',
      rows: [],
      loading: true,
      error: '',
      page: 1,
      meta: null,
    }
  },
  async mounted() {
    await this.loadTorneos()
    this.loading = false
  },
  methods: {
    async loadTorneos() {
      this.error = ''
      try {
        const res = await api.get('/torneos', { params: { status: 'I' } })
        if (res.data && res.data.success) {
          this.torneos = res.data.data.map(t => ({ id: t.id ?? t.idTorneo ?? null, nombre: t.nombre ?? t.Nombre ?? 'Torneo' }))
          if (this.torneos.length > 0) {
            this.selectedTorneo = this.torneos[0].id
          }
          await this.loadTarjetas()
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },
    async reload() {
      this.page = 1
      await this.loadTarjetas()
    },
    async loadTarjetas() {
      this.rows = []
      this.meta = null
      this.error = ''
      try {
        const params = { tipo: this.tipo, page: this.page, per_page: 20 }
        if (this.tipo === 'fairplay') params.torneo_id = this.selectedTorneo
        const res = await api.get('/tarjetas', { params })
        if (res.data && res.data.success) {
          this.rows = res.data.data || []
          this.meta = res.data.meta || null
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },
    async prevPage() {
      if (this.meta && this.meta.page > 1) {
        this.page = this.meta.page - 1
        await this.loadTarjetas()
      }
    },
    async nextPage() {
      if (this.meta && this.meta.page < this.meta.total_pages) {
        this.page = this.meta.page + 1
        await this.loadTarjetas()
      }
    },
  },
}
</script>
