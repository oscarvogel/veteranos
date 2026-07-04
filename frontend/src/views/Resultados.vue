<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Resultados</h1>
          <p class="mt-1 text-sm text-gray-500">Partidos jugados del torneo seleccionado</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Torneo</label>
            <select v-model="selectedTorneo" @change="loadResultados" class="select-modern">
              <option v-for="t in torneos" :key="t.id" :value="t.id">{{ t.nombre }}</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Por página</label>
            <select v-model.number="perPage" @change="changePerPage" class="select-modern">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center gap-2 text-gray-400">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span class="text-sm">Cargando resultados...</span>
        </div>
      </div>
      <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ error }}</div>
      <div v-else-if="resultados.length === 0" class="text-center py-12 text-gray-400 text-sm">No hay resultados cargados para este torneo.</div>

      <div v-else class="table-card">
        <div class="grid grid-cols-12 gap-0">
          <div class="col-span-2 table-header-cell">Fecha</div>
          <div class="col-span-4 table-header-cell">Local</div>
          <div class="col-span-2 table-header-cell text-center">Resultado</div>
          <div class="col-span-4 table-header-cell">Visitante</div>
        </div>

        <div v-for="m in resultados" :key="m.id" class="grid grid-cols-12 items-center table-row hover:bg-gray-50">
          <div class="col-span-2 table-cell text-gray-500">{{ formatDate(m.fecha) }}</div>
          <div class="col-span-4 table-cell font-semibold text-gray-800">{{ m.local }}</div>
          <div class="col-span-2 table-cell flex justify-center">
            <span class="badge-score bg-emerald-50 text-emerald-700">{{ m.goles_local }} - {{ m.goles_visitante }}</span>
          </div>
          <div class="col-span-4 table-cell font-semibold text-gray-800">{{ m.visitante }}</div>
        </div>
      </div>

      <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
          <button @click="prevPage" :disabled="!meta || meta.page <= 1" class="btn-ghost text-sm">Anterior</button>
          <button @click="nextPage" :disabled="!meta || meta.page >= meta.total_pages" class="btn-ghost text-sm">Siguiente</button>
        </div>
        <div v-if="meta" class="text-sm text-gray-400">
          Página {{ meta.page }} de {{ meta.total_pages }} &mdash; {{ meta.total }} resultados
        </div>
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
      resultados: [],
      loading: true,
      error: '',
      page: 1,
      perPage: 20,
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
          this.torneos = res.data.data.map(t => ({
            id: t.id ?? t.idTorneo ?? null,
            nombre: t.nombre ?? t.Nombre ?? 'Torneo',
          }))
          if (this.torneos.length > 0) {
            this.selectedTorneo = this.torneos[0].id
            await this.loadResultados()
          }
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },
    async loadResultados() {
      if (!this.selectedTorneo) return
      this.resultados = []
      this.error = ''
      try {
        const res = await api.get('/resultados', {
          params: { torneo_id: this.selectedTorneo, page: this.page, per_page: this.perPage },
        })
        if (res.data && res.data.success) {
          this.resultados = res.data.data || []
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
        await this.loadResultados()
      }
    },
    async nextPage() {
      if (this.meta && this.meta.page < this.meta.total_pages) {
        this.page = this.meta.page + 1
        await this.loadResultados()
      }
    },
    async changePerPage() {
      this.page = 1
      await this.loadResultados()
    },
    formatDate(value) {
      if (!value) return ''
      if (/^\d{4}-\d{2}-\d{2}/.test(value)) {
        const [year, month, day] = value.substring(0, 10).split('-')
        return `${day}-${month}-${year}`
      }
      return value
    },
  },
}
</script>
