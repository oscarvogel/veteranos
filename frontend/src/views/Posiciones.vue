<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Fixture</h1>
          <p class="mt-1 text-sm text-gray-500">Partidos programados del torneo</p>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center gap-2 text-gray-400">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span class="text-sm">Cargando torneos...</span>
        </div>
      </div>
      <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ error }}</div>

      <div v-else>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Torneo</label>
            <select v-model="selectedTorneo" @change="loadNFechas" class="select-modern">
              <option v-for="t in torneos" :key="t.id" :value="t.id">{{ t.nombre }}</option>
            </select>
          </div>

          <div v-if="nfechas.length >= 0" class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Fecha</label>
            <select v-model.number="selectedNFecha" @change="loadFixture" class="select-modern">
              <option :value="0">Todas las fechas</option>
              <option v-for="nf in nfechas" :key="nf.nfecha" :value="nf.nfecha">N° {{ nf.nfecha }} - {{ nf.fecha }}</option>
            </select>
          </div>
        </div>

        <div v-if="fixture.length === 0" class="text-center py-12 text-gray-400 text-sm">No hay partidos para este torneo.</div>

        <div v-else class="table-card">
          <div class="grid grid-cols-12 gap-0">
            <div class="col-span-3 table-header-cell">Fecha</div>
            <div class="col-span-3 table-header-cell">Local</div>
            <div class="col-span-3 table-header-cell">Visitante</div>
            <div class="col-span-3 table-header-cell">Resultado</div>
          </div>
          <div>
            <div v-for="m in fixture" :key="m.id" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
              <div class="col-span-3 table-cell text-gray-500">{{ m.fecha }}</div>
              <div class="col-span-3 table-cell font-semibold text-gray-800">{{ m.local }}</div>
              <div class="col-span-3 table-cell font-semibold text-gray-800">{{ m.visitante }}</div>
              <div class="col-span-3 table-cell">
                <span class="badge-score bg-indigo-50 text-indigo-700">{{ m.goles_local }} - {{ m.goles_visitante }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-2">
            <button @click="prevPage" :disabled="!meta || meta.page <= 1" class="btn-ghost text-sm">Anterior</button>
            <button @click="nextPage" :disabled="!meta || meta.page >= meta.total_pages" class="btn-ghost text-sm">Siguiente</button>
          </div>

          <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500">Por página</label>
            <select v-model.number="perPage" @change="changePerPage" class="select-modern !py-1.5 text-sm">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
            </select>
          </div>

          <div v-if="meta" class="text-sm text-gray-400">
            Página {{ meta.page }} de {{ meta.total_pages }} &mdash; {{ meta.total }} partidos
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api, { getApiErrorMessage, getResponseErrorMessage, isModernApiSuccess } from '../services/api'

export default {
  data() {
    return {
      torneos: [],
      selectedTorneo: null,
      nfechas: [],
      selectedNFecha: 0,
      fixture: [],
      loading: true,
      error: '',
      // pagination
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
        if (isModernApiSuccess(res.data)) {
          this.torneos = res.data.data.map(t => ({
            id: t.id ?? t.idTorneo ?? t.id_torneo ?? t.id_torneos ?? null,
            nombre: t.nombre ?? t.Nombre ?? t.NombreTorneo ?? t.nombre_torneo ?? 'Torneo',
            estado: t.estado ?? t.Estado ?? null
          }))
          if (this.torneos.length > 0) {
            this.selectedTorneo = this.torneos[0].id
            await this.loadNFechas()
          }
        } else if (res.data) {
          this.error = getResponseErrorMessage(res.data)
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },

    async loadNFechas() {
      if (!this.selectedTorneo) return
      this.error = ''
      try {
        const res = await api.get('/fechas', { params: { torneo_id: this.selectedTorneo } })
        if (isModernApiSuccess(res.data)) {
          this.nfechas = res.data.data.map(n => ({ nfecha: n.nfecha ?? n.NFecha ?? 0, fecha: n.fecha ?? n.Fecha ?? null }))
          this.page = 1
          this.meta = null
          if (this.nfechas.length > 0) {
            this.selectedNFecha = 0 // default to Todas
            await this.loadFixture()
          } else {
            this.selectedNFecha = 0
            this.fixture = []
          }
        } else if (res.data) {
          this.error = getResponseErrorMessage(res.data)
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },

    async loadFixture() {
      if (!this.selectedTorneo) return
      this.fixture = []
      this.error = ''
      try {
        const params = { torneo_id: this.selectedTorneo, page: this.page, per_page: this.perPage }
        if (this.selectedNFecha && this.selectedNFecha !== 0) params.nfecha = this.selectedNFecha
        const res = await api.get('/fixture', { params })
        if (res.data) {
          if (isModernApiSuccess(res.data)) {
            this.meta = res.data.meta ?? null
            this.fixture = res.data.data.map(m => {
              const rawDate = m.fecha ?? m.Fecha ?? m.date ?? ''
              let formatted = rawDate
              if (/^\d{4}-\d{2}-\d{2}$/.test(rawDate)) {
                const parts = rawDate.split('-')
                formatted = `${parts[2]}-${parts[1]}-${parts[0]}`
              }
              return {
                id: m.id ?? m.idFixture ?? m.id_partido ?? null,
                fecha: formatted,
                local: m.local ?? m.local_id ?? m.Local ?? m.equipo_local ?? m.local,
                visitante: m.visitante ?? m.visitante_id ?? m.Visitante ?? m.equipo_visitante ?? m.visitante,
                goles_local: m.goles_local ?? m.gollocal ?? m.gol_local ?? m.goles_local ?? 0,
                goles_visitante: m.goles_visitante ?? m.golvisitante ?? m.gol_visitante ?? m.goles_visitante ?? 0,
              }
            })
          } else if (res.data) {
            this.error = getResponseErrorMessage(res.data)
          }
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },

    async prevPage() {
      if (!this.meta) return
      if (this.meta.page > 1) {
        this.page = this.meta.page - 1
        await this.loadFixture()
      }
    },
    async nextPage() {
      if (!this.meta) return
      if (this.meta.page < this.meta.total_pages) {
        this.page = this.meta.page + 1
        await this.loadFixture()
      }
    },
    async changePerPage() {
      this.page = 1
      await this.loadFixture()
    }
  }
}
</script>
