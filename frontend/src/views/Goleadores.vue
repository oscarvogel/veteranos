<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Goleadores</h1>
          <p class="mt-1 text-sm text-gray-500">Tabla de goleadores del torneo</p>
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
            <select v-model.number="selectedNFecha" @change="loadGoleadores" class="select-modern">
              <option :value="0">Todas las fechas</option>
              <option v-for="nf in nfechas" :key="nf.nfecha" :value="nf.nfecha">N° {{ nf.nfecha }} - {{ nf.fecha }}</option>
            </select>
          </div>
        </div>

        <div v-if="goleadores.length === 0" class="text-center py-12 text-gray-400 text-sm">No hay registros de goles para este torneo.</div>

        <div v-else class="table-card">
          <div class="grid grid-cols-12 gap-0">
            <div class="col-span-6 table-header-cell">Jugador / Equipo</div>
            <div class="col-span-3 table-header-cell">Goles</div>
            <div class="col-span-3 table-header-cell">Detalle</div>
          </div>
          <div>
            <div v-for="g in goleadores" :key="(g.jugador||'') + '::' + (g.equipo||'')" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
              <div class="col-span-6 table-cell">
                <span class="font-medium text-gray-800">{{ g.jugador || '—' }}</span>
                <span v-if="g.equipo" class="text-gray-400 text-xs ml-1.5">/ {{ g.equipo }}</span>
              </div>
              <div class="col-span-3 table-cell">
                <span class="badge-score bg-rose-50 text-rose-700">{{ g.goles }}</span>
              </div>
              <div class="col-span-3 table-cell">
                <button @click="openDetalle(g)" class="btn-ghost text-xs !px-3 !py-1.5">Ver detalle</button>
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
            Página {{ meta.page }} de {{ meta.total_pages }} &mdash; {{ meta.total }} registros
          </div>
        </div>

        <!-- Detalle Modal -->
        <div v-if="detalleVisible" class="modal-content">
          <div class="modal-overlay" @click="closeDetalle"></div>
          <div class="modal-panel">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900">Detalle de goles &mdash; {{ detalleTitle }}</h3>
              <button @click="closeDetalle" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <div class="p-6">
              <div v-if="detalleLoading" class="flex items-center justify-center py-8">
                <div class="flex items-center gap-2 text-gray-400">
                  <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  <span class="text-sm">Cargando...</span>
                </div>
              </div>
              <div v-else>
                <div class="table-card !border-0 !shadow-none">
                  <div class="grid grid-cols-12 gap-0 border-b border-gray-100">
                    <div class="col-span-4 table-header-cell !bg-transparent">Fecha</div>
                    <div class="col-span-5 table-header-cell !bg-transparent">Oponente</div>
                    <div class="col-span-3 table-header-cell !bg-transparent">Cantidad</div>
                  </div>
                  <div v-for="d in detalleRows" :key="d.idFixture + '::' + d.fecha" class="grid grid-cols-12 gap-0 items-center table-row hover:bg-gray-50">
                    <div class="col-span-4 table-cell text-gray-500">{{ formatDate(d.fecha) }}</div>
                    <div class="col-span-5 table-cell text-gray-700">{{ d.oponente }}</div>
                    <div class="col-span-3 table-cell">
                      <span class="badge-score bg-indigo-50 text-indigo-700">{{ d.cantidad }}</span>
                    </div>
                  </div>
                </div>
                <div v-if="detalleRows.length === 0" class="text-center py-6 text-sm text-gray-400">No se encontraron registros.</div>
              </div>
            </div>
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
      goleadores: [],
      loading: true,
      error: '',
      // pagination
      page: 1,
      perPage: 20,
      meta: null,
      // detalle modal
      detalleVisible: false,
      detalleRows: [],
      detalleLoading: false,
      detalleTitle: '',
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
          this.torneos = res.data.data.map(t => ({ id: t.id ?? t.idTorneo ?? t.id_torneo ?? null, nombre: t.nombre ?? t.Nombre ?? 'Torneo' }))
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
            this.selectedNFecha = 0
            await this.loadGoleadores()
          } else {
            this.selectedNFecha = 0
            this.goleadores = []
          }
        } else if (res.data) {
          this.error = getResponseErrorMessage(res.data)
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },

    async loadGoleadores() {
      if (!this.selectedTorneo) return
      this.goleadores = []
      this.error = ''
      try {
        const params = { torneo_id: this.selectedTorneo, page: this.page, per_page: this.perPage }
        if (this.selectedNFecha && this.selectedNFecha !== 0) params.nfecha = this.selectedNFecha
        const res = await api.get('/goleadores', { params })
        if (isModernApiSuccess(res.data)) {
          this.meta = res.data.meta ?? null
          this.goleadores = res.data.data
        } else if (res.data) {
          this.error = getResponseErrorMessage(res.data)
        }
      } catch (e) {
        this.error = getApiErrorMessage(e)
      }
    },

    async prevPage() {
      if (!this.meta) return
      if (this.meta.page > 1) {
        this.page = this.meta.page - 1
        await this.loadGoleadores()
      }
    },
    async nextPage() {
      if (!this.meta) return
      if (this.meta.page < this.meta.total_pages) {
        this.page = this.meta.page + 1
        await this.loadGoleadores()
      }
    },
    async changePerPage() {
      this.page = 1
      await this.loadGoleadores()
    }
    ,
    openDetalle(g) {
      this.detalleTitle = g.jugador || 'Jugador';
      this.detalleVisible = true;
      this.detalleRows = [];
      this.detalleLoading = true;
      this.loadDetalle(g).finally(() => { this.detalleLoading = false });
    },
    closeDetalle() {
      this.detalleVisible = false;
      this.detalleRows = [];
    },
    async loadDetalle(g) {
      try {
        const params = { torneo_id: this.selectedTorneo, player_key: g.player_key };
        if (this.selectedNFecha && this.selectedNFecha !== 0) params.nfecha = this.selectedNFecha;
        const res = await api.get('/goleadores/detalle', { params });
        if (isModernApiSuccess(res.data)) {
          this.detalleRows = res.data.data || [];
        } else if (res.data) {
          this.error = getResponseErrorMessage(res.data);
          this.detalleRows = [];
        }
      } catch (e) {
        this.error = getApiErrorMessage(e);
        this.detalleRows = [];
      }
    },
    formatDate(s) {
      if (!s) return '';
      const d = new Date(s);
      if (isNaN(d)) return s;
      return d.toLocaleDateString();
    }
  }
}
</script>
