# Estructura Vue 3 recomendada para el proyecto Veteranos

## Setup inicial (una sola vez)

```bash
# Desde la raíz del repo (o:\veteranos\)
npm create vue@latest frontend
cd frontend
npm install axios pinia vue-router
```

Configurar `frontend/vite.config.js` para proxy hacia la API (evita CORS en desarrollo):

```js
export default {
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost/veteranos/api/public',
        rewrite: path => path.replace(/^\/api/, '')
      }
    }
  }
}
```

En producción el build de Vue (`frontend/dist/`) se sirve estáticamente; no hace falta CORS porque comparten dominio.

---

## Estructura de carpetas recomendada

```
frontend/src/
  api/
    client.js            ← instancia axios con baseURL y JWT interceptor
  composables/
    use<Modulo>.js       ← lógica de estado + llamadas API por módulo
  stores/
    auth.js              ← sesión / token JWT (Pinia)
    <modulo>.js          ← solo si el estado es global/compartido
  views/
    <Modulo>/
      <Modulo>List.vue   ← listado (tabla + paginación)
      <Modulo>Form.vue   ← formulario crear/editar
  components/
    AppNav.vue           ← menú equivalente al de main.php Yii
    AppTable.vue         ← tabla genérica reutilizable
    AppPaginator.vue     ← paginación
  router/
    index.js             ← definición de rutas
  App.vue
  main.js
```

---

## Plantilla: `src/api/client.js`

```js
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const client = axios.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' }
})

client.interceptors.request.use(config => {
  const auth = useAuthStore()
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

export default client
```

## Respuesta esperada del backend

Todos los endpoints devuelven `App\Lib\Response` serializado como JSON:

```json
{ "response": true,  "result": [...], "message": "" }
{ "response": false, "result": null,  "message": "Error description" }
```

En los composables chequear `data.response === true` (no `data.success`).

---

## Plantilla: `src/composables/use<Modulo>.js`

```js
import { ref } from 'vue'
import client from '@/api/client'

export function use<Modulo>() {
  const items   = ref([])
  const item    = ref(null)
  const loading = ref(false)
  const error   = ref(null)

  async function fetchAll(page = 1, perPage = 50) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await client.get('/<modulo>/', { params: { page, per_page: perPage } })
      if (data.response) {
        items.value = data.result
      } else {
        error.value = data.message
      }
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function fetchOne(id) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await client.get(`/<modulo>/${id}`)
      if (data.response) item.value = data.result
      else error.value = data.message
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function save(payload) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await client.post('/<modulo>/save', payload)
      if (!data.response) error.value = data.message
      return data.response
    } catch (e) {
      error.value = e.message
      return false
    } finally {
      loading.value = false
    }
  }

  async function remove(id) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await client.post(`/<modulo>/delete/${id}`)
      if (!data.response) error.value = data.message
      return data.response
    } catch (e) {
      error.value = e.message
      return false
    } finally {
      loading.value = false
    }
  }

  return { items, item, loading, error, fetchAll, fetchOne, save, remove }
}
```

---

## Plantilla: `<Modulo>List.vue`

```vue
<template>
  <div>
    <h2><Modulo></h2>
    <div v-if="loading">Cargando...</div>
    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
    <table v-else class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Campo 1</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in items" :key="row.id">
          <td>{{ row.id }}</td>
          <td>{{ row.campo1 }}</td>
          <td>
            <router-link :to="`/<modulo>/${row.id}/editar`">Editar</router-link>
            <button @click="handleDelete(row.id)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
    <router-link to="/<modulo>/nuevo">Nuevo</router-link>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { use<Modulo> } from '@/composables/use<Modulo>'

const { items, loading, error, fetchAll, remove } = use<Modulo>()

onMounted(() => fetchAll())

async function handleDelete(id) {
  if (!confirm('¿Eliminar registro?')) return
  await remove(id)
  fetchAll()
}
</script>
```

---

## Plantilla: `<Modulo>Form.vue`

```vue
<template>
  <div>
    <h2>{{ isEdit ? 'Editar' : 'Nuevo' }} <Modulo></h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label>Campo 1</label>
        <input v-model="form.campo1" class="form-control" required />
      </div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Guardando...' : 'Guardar' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { use<Modulo> } from '@/composables/use<Modulo>'

const route  = useRoute()
const router = useRouter()
const { item, loading, error, fetchOne, save } = use<Modulo>()

const isEdit = computed(() => !!route.params.id)

const form = reactive({ id: null, campo1: '' })

onMounted(async () => {
  if (isEdit.value) {
    await fetchOne(route.params.id)
    Object.assign(form, item.value)
  }
})

async function handleSubmit() {
  const ok = await save({ ...form })
  if (ok) router.push('/<modulo>')
}
</script>
```

---

## Equivalencia menú Yii → Vue Router

| Ítem menú Yii | Ruta Vue |
|---|---|
| Posiciones | `/posiciones` |
| Resultados | `/posiciones/resultados` |
| Tarjetas | `/tarjetas` |
| Goleadores | `/goles/goleadores` |
| Fixture | `/fixture` |
| Resoluciones | `/resoluciones` |
| Listas de Buena Fe | `/equipos/buena-fe` |
| Admin: Torneo | `/admin/torneo` |
| Admin: Equipos | `/admin/equipos` |
| (etc.) | (etc.) |

El componente `AppNav.vue` reemplaza el `<nav>` de [themes/classic/views/layouts/main.php](../../../themes/classic/views/layouts/main.php).
