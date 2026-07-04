<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Lista de buena fe</h1>
          <p class="mt-1 text-sm text-gray-500">Jugadores y documentación presentada por equipo</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Torneo</label>
            <select v-model="selectedTorneo" @change="loadEquipos" class="select-modern">
              <option v-for="t in torneos" :key="t.id" :value="t.id">{{ t.nombre }}</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Equipo</label>
            <select v-model="equipoId" @change="loadJugadores" class="select-modern min-w-48">
              <option :value="null">Seleccionar</option>
              <option v-for="e in equipos" :key="e.id" :value="e.id">{{ e.nombre }}</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center gap-2 text-gray-400">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span class="text-sm">Cargando jugadores...</span>
        </div>
      </div>
      <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ error }}</div>
      <div v-else-if="rows.length === 0" class="text-center py-12 text-gray-400 text-sm">Seleccione un equipo para consultar la lista.</div>

      <div v-else class="table-card">
        <div class="grid grid-cols-12 gap-0">
          <div class="col-span-4 table-header-cell">Jugador</div>
          <div class="col-span-2 table-header-cell">Clase</div>
          <div class="col-span-2 table-header-cell">DNI</div>
          <div class="col-span-4 table-header-cell">Documentación</div>
        </div>
        <div>
          <div v-for="row in rows" :key="row.idJugador" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
            <div class="col-span-4 table-cell font-semibold text-gray-800">{{ row.nombre }}</div>
            <div class="col-span-2 table-cell text-gray-600">{{ row.clase }}</div>
            <div class="col-span-2 table-cell text-gray-600 font-mono text-xs">{{ row.dni }}</div>
            <div class="col-span-4 table-cell">
              <div class="flex flex-wrap gap-1.5">
                <span :class="row.certificado ? 'badge-doc bg-emerald-50 text-emerald-700' : 'badge-doc bg-gray-50 text-gray-400'">Certificado</span>
                <span :class="row.firma_lista ? 'badge-doc bg-emerald-50 text-emerald-700' : 'badge-doc bg-gray-50 text-gray-400'">Firma</span>
                <span :class="row.fotocopia_dni ? 'badge-doc bg-emerald-50 text-emerald-700' : 'badge-doc bg-gray-50 text-gray-400'">DNI</span>
                <span :class="row.dec_jurada ? 'badge-doc bg-emerald-50 text-emerald-700' : 'badge-doc bg-gray-50 text-gray-400'">DDJJ</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="meta" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
          <button @click="prevPage" :disabled="meta.page <= 1" class="btn-ghost text-sm">Anterior</button>
          <button @click="nextPage" :disabled="meta.page >= meta.total_pages" class="btn-ghost text-sm">Siguiente</button>
        </div>
        <div class="text-sm text-gray-400">Página {{ meta.page }} de {{ meta.total_pages }} &mdash; {{ meta.total }} jugadores</div>
      </div>
    </div>
  </div>
</template>

<script>
import api, { getApiErrorMessage } from '../services/api'

export default {
  data() {
    return {
      equipoId: 1,
      torneos: [],
      selectedTorneo: null,
      equipos: [],
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
    getErrorMessage(e, fallback = 'No se pudo cargar la informacion.') {
      return getApiErrorMessage(e, fallback)
    },
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
            await this.loadEquipos()
          }
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = this.getErrorMessage(e)
      }
    },
    async loadEquipos() {
      this.equipos = []
      this.equipoId = null
      this.rows = []
      this.meta = null
      this.error = ''
      if (!this.selectedTorneo) return

      try {
        const res = await api.get('/equipos', { params: { torneo_id: this.selectedTorneo, per_page: 100 } })
        if (res.data && res.data.success) {
          this.equipos = res.data.data.map(e => ({
            id: e.id ?? e.idEquipo ?? null,
            nombre: e.nombre ?? e.Nombre ?? 'Equipo',
          }))
          if (this.equipos.length > 0) {
            this.equipoId = this.equipos[0].id
            await this.loadJugadores()
          }
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = this.getErrorMessage(e)
      }
    },
    async loadJugadores() {
      if (!this.equipoId) {
        this.error = 'Seleccione un equipo.'
        return
      }
      this.loading = true
      this.error = ''
      this.rows = []
      this.meta = null
      try {
        const res = await api.get('/lista-buena-fe', {
          params: { equipo_id: this.equipoId, page: this.page, per_page: 20 },
        })
        if (res.data && res.data.success) {
          this.rows = res.data.data || []
          this.meta = res.data.meta || null
        } else if (res.data && res.data.error) {
          this.error = res.data.error.message
        }
      } catch (e) {
        this.error = this.getErrorMessage(e)
      } finally {
        this.loading = false
      }
    },
    badgeClass(value) {
      return value
        ? 'bg-green-100 px-2 py-1 text-xs font-semibold text-green-800'
        : 'bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-500'
    },
    async prevPage() {
      if (this.meta && this.meta.page > 1) {
        this.page = this.meta.page - 1
        await this.loadJugadores()
      }
    },
    async nextPage() {
      if (this.meta && this.meta.page < this.meta.total_pages) {
        this.page = this.meta.page + 1
        await this.loadJugadores()
      }
    },
  },
}
</script>
