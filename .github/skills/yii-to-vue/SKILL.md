---
name: yii-to-vue
description: 'Migrar aplicacion Yii a backend PHP API REST + frontend Vue 3. Usar para: rediseñar módulos del sistema Veteranos, crear endpoints REST en Slim 4, construir componentes Vue 3 con Pinia y Vue Router, reemplazar vistas Yii/PHP por SPA. Triggers: migrar módulo, crear endpoint, rediseñar vista, nueva api, vue frontend, slim api, refactor yii.'
argument-hint: 'Nombre del módulo a migrar (ej: equipos, jugador, fixture, posiciones)'
---

# Migración Yii → PHP API REST + Vue 3

## Contexto del Proyecto

**Sistema:** Veteranos Ldor Gral San Martin — gestión de torneos de fútbol veterano.  
**Stack actual:** Yii 1.x (MVC monolítico), MySQL `ye000174_veteranos`, autenticación Cruge (MD5).  
**Stack destino:** Backend Slim 4 API REST (`api/`) + Frontend Vue 3 (Composition API, Pinia, Vue Router).  
**API base existente:** `api/` — estructura Slim 3, patrón `app/route/<nombre>_route.php` + `app/model/<nombre>_model.php`.

### Módulos del sistema (20 controladores)
`arbitros`, `articulos`, `canchas`, `conceptos`, `conexiones`, `egresos`, `equipos`, `equipostorneo`,
`fixture`, `goles`, `ingresos`, `jugador`, `noticiascel`, `planillas`, `posiciones`, `posicionestorneo`,
`resoluciones`, `site`, `tarjetas`, `torneo`

### Estructura API existente
```
api/
  app/
    route/   ← agregar <modulo>_route.php
    model/   ← agregar <modulo>_model.php
    lib/     ← clases compartidas (Response, DB, Auth)
  src/
    routes.php   ← registrar grupos de rutas nuevas
```

---

## Procedimiento por Módulo

### FASE 1 — Análisis del módulo Yii

1. Leer el controlador `protected/controllers/<Modulo>Controller.php`:
   - Identificar acciones públicas (`actionXxx`)
   - Marcar cuáles son solo lectura (GET) y cuáles mutan datos (POST)
   - Detectar filtros de acceso (`accessRules`)
2. Leer el modelo `protected/models/<Modulo>.php`:
   - Identificar la tabla (`tableName()`), relaciones (`relations()`), validaciones (`rules()`)
   - Detectar scopes o named scopes usados
3. Revisar la vista más relevante en `protected/views/<modulo>/` para entender qué datos consume el frontend

**Salida esperada:** tabla de endpoints a crear (método, path, fuente Yii, autenticación requerida)

---

### FASE 2 — Backend: crear endpoint en `api/`

Seguir el patrón ya establecido en `api/app/`:

1. Crear `api/app/model/<modulo>_model.php` — ver [plantilla model](./references/model-template.md)
   - Clase `<Modulo>Model` con namespace `App\Model`
   - Métodos `GetAll()`, `Get($id)`, `Save($data)`, `Delete($id)` según necesidad
   - Usar `Database::StartUp()` (ya existe en `App\Lib\Database`)
   - Devolver siempre una instancia de `App\Lib\Response`
   - Las credenciales se leen desde `api/.env` automáticamente
2. Crear `api/app/route/<modulo>_route.php` — ver [plantilla route](./references/route-template.md)
   - Grupo `$app->group('/<modulo>', function(RouteCollectorProxyInterface $group) {...})`
   - Rutas: GET `/`, GET `/{id}`, POST `/save`, POST `/delete/{id}`
   - Aplicar middleware de autenticación JWT en rutas protegidas
3. Registrar el grupo en `api/src/routes.php`:
   ```php
   require __DIR__ . '/../app/route/<modulo>_route.php';
   ```
4. Probar endpoint con: `GET /api/<modulo>` y verificar respuesta JSON

**Criterio de calidad:** respuesta JSON `{ "response": true, "result": [...] }` o `{ "response": false, "message": "..." }`

---

### FASE 3 — Autenticación API

El sistema actual usa **Cruge con MD5** (tabla `cruge_user`). En la API usar **JWT**:

1. Verificar que `api/app/lib/` tenga `Auth.php` (middleware JWT). Si no existe, crearlo.
2. El endpoint de login debe:
   - Recibir `POST /auth/login` con `{ usuario, password }`
   - Verificar contra tabla `cruge_user` (campo `username`, hash MD5)
   - Devolver token JWT con payload `{ user_id, username, rol }`
3. Rutas protegidas aplican `->add(new AuthMiddleware())`
4. Las rutas públicas (posiciones, fixture, goleadores, etc.) NO requieren JWT

---

### FASE 4 — Frontend: componente Vue 3

Ver [estructura frontend recomendada](./references/vue-structure.md).

> El frontend Vue está en `frontend/` dentro del mismo repo (`o:\veteranos\frontend\`).
> En desarrollo, Vite proxea `/api` → `api/public/`; en producción el build estático se sirve desde el mismo dominio.

1. **Composable para el módulo** (`src/composables/use<Modulo>.js`):
   - `const { items, loading, error, fetchAll, save, remove } = use<Modulo>()`
   - Llamadas con `axios` o `fetch` al endpoint `GET /api/<modulo>`
   - Estado reactivo con `ref` / `reactive`
2. **Store Pinia** (`src/stores/<modulo>.js`) — solo si el estado se comparte entre rutas
3. **Componentes** (`src/views/<Modulo>/`):
   - `<Modulo>List.vue` — tabla/listado (reemplaza vista `index.php` Yii)
   - `<Modulo>Form.vue` — formulario create/edit (reemplaza `_form.php`)
   - `<Modulo>Detail.vue` — detalle (reemplaza `view.php`) — opcional
4. **Ruta Vue Router**:
   ```js
   { path: '/<modulo>', component: () => import('./views/<Modulo>/<Modulo>List.vue') }
   { path: '/<modulo>/nuevo', component: () => import('./views/<Modulo>/<Modulo>Form.vue') }
   { path: '/<modulo>/:id/editar', component: () => import('./views/<Modulo>/<Modulo>Form.vue') }
   ```

---

### FASE 5 — Validación y migración de datos

1. Confirmar que la API devuelve los mismos datos que la vista Yii actual
2. Verificar paginación si la lista es grande (parámetros `?page=1&per_page=20`)
3. Comprobar que las relaciones (ej: `equipostorneo → equipos → torneo`) se resuelven en el modelo PHP (JOINs) o en el composable Vue (llamadas múltiples)
4. Validar formularios: las `rules()` del modelo Yii deben replicarse en el form Vue (con Vuelidate o validación manual)

---

## Prioridad de módulos sugerida

| Prioridad | Módulo | Tipo |
|---|---|---|
| 1 | `posiciones` | Solo lectura, público |
| 2 | `fixture` | Solo lectura + JSON ya existe (`actionConsultaFixtureJson`) |
| 3 | `equipos` | CRUD + autocomplete |
| 4 | `jugador` | CRUD + asignaciones |
| 5 | `tarjetas` | Consultas complejas |
| 6 | `goles` | Goleadores |
| 7 | `torneo` | Admin |
| 8 | resto | Según necesidad |

---

## Checklist de módulo completado

- [ ] Controlador Yii analizado y endpoints mapeados
- [ ] `api/app/model/<modulo>_model.php` creado y probado
- [ ] `api/app/route/<modulo>_route.php` creado y registrado
- [ ] Endpoint responde JSON correcto (GET, POST)
- [ ] Rutas protegidas con JWT donde corresponde
- [ ] Composable Vue creado
- [ ] Componente List creado
- [ ] Componente Form creado (si hay CRUD)
- [ ] Rutas Vue Router registradas
- [ ] Funcionalidad equivalente a la vista Yii original

---

## Referencias

- [Plantilla PHP Model](./references/model-template.md)
- [Plantilla PHP Route](./references/route-template.md)
- [Estructura Vue 3 recomendada](./references/vue-structure.md)
