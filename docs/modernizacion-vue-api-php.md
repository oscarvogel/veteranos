# Modernizacion Vue + API PHP

Estado inicial de implementacion en `O:\veteranos`.

## Alcance implementado

- Se agrego una capa moderna de API JSON bajo `/api/...` sobre el backend Slim existente.
- Se mantiene la API legada sin cambiar sus rutas (`/torneo`, `/fixture`, `/goles`, etc.).
- Se agrego una vista Vue para Resultados.
- Se agregaron vistas Vue para Tarjetas y Lista de buena fe.
- La Lista de buena fe usa selector de torneo y equipo desde API moderna.
- El frontend Vue quedo copiado dentro de `O:\veteranos\frontend`.
- El frontend Vue de desarrollo apunta a `http://127.0.0.1:8017/api`.
- El build de produccion apunta a `https://veteranos.ar/nueva_web/api`.

## Endpoints modernos disponibles

- `GET /api/health`
- `GET /api/health/db`
- `GET /api/torneos?status=I`
- `GET /api/fechas?torneo_id=ID`
- `GET /api/fixture?torneo_id=ID&page=1&per_page=20`
- `GET /api/resultados?torneo_id=ID&page=1&per_page=20`
- `GET /api/posiciones?torneo_id=ID`
- `GET /api/goleadores?torneo_id=ID&page=1&per_page=20`
- `GET /api/goleadores/detalle?player_key=ID`
- `GET /api/tarjetas?tipo=fairplay&torneo_id=ID&page=1&per_page=20`
- `GET /api/tarjetas?tipo=vigentes`
- `GET /api/tarjetas?tipo=equipo&torneo_id=ID&equipo_id=ID&page=1&per_page=20`
- `GET /api/equipos?torneo_id=ID&page=1&per_page=100`
- `GET /api/lista-buena-fe?equipo_id=ID&page=1&per_page=20`

## Frontend

- `frontend/src/services/api.js` normaliza la base `VITE_API_URL`, agrega `/api` y acepta respuestas HTTP `400/500` para que las vistas lean el JSON moderno (`success:false`, `error.message`) sin perder el detalle en errores de Axios.
- `frontend/src/services/api-response.js` centraliza la deteccion de respuestas modernas y extraccion de errores para que las vistas muestren mensajes sanitizados en pantalla.
- `frontend/src/App.vue` usa `shallowRef` y `markRaw` para el ruteo hash de vistas, evitando que Vue convierta los componentes en objetos reactivos.
- `frontend/vite.config.js` fija `host=127.0.0.1` y usa watcher con polling para que `npm run dev` sea estable sobre el drive mapeado `O:`.
- `frontend/vite.config.js` tambien permite `VITE_API_PROXY_TARGET` en desarrollo para probar desde el navegador embebido usando el mismo origen del frontend y reenviando `/api` al backend local.
- Dependencias frontend actualizadas y auditadas: `axios@1.16.1`, `vite@8.0.14`, `@vitejs/plugin-vue@6.0.7`, `postcss@8.5.15`; `npm audit` queda en 0 vulnerabilidades.

## Seguridad de errores

- La API moderna no expone detalles de MySQL por defecto. Errores como `SQLSTATE`, credenciales o nombres de base se responden como `No se pudo completar la consulta.`.
- Para diagnostico local se puede definir `API_DEBUG=true` en `api\.env`; en produccion debe quedar `API_DEBUG=false`.
- Slim tambien toma `API_DEBUG` para `displayErrorDetails`, por lo que los detalles internos quedan desactivados salvo diagnostico local explicito.
- `api/app/lib/env_loader.php` carga `api\.env` aceptando valores con comillas, espacios y `=` internos, util para claves reales mas complejas.

Archivo local esperado para credenciales:

```dotenv
DB_HOST=localhost
DB_NAME=nombre_base_de_datos
DB_USER=usuario
DB_PASS=clave
DB_CHARSET=utf8
API_DEBUG=false
```

La respuesta moderna usa:

```json
{
  "success": true,
  "data": [],
  "meta": {
    "total": 0,
    "per_page": 20,
    "page": 1,
    "total_pages": 1
  }
}
```

En errores:

```json
{
  "success": false,
  "error": {
    "code": 500,
    "message": "detalle"
  }
}
```

## Verificacion local

Backend:

```powershell
cd O:\veteranos
php -S 127.0.0.1:8017 -t api\public
```

Frontend:

```powershell
cd O:\veteranos\frontend
npm install
npm test
npm run dev
npm audit
```

Pruebas:

```powershell
cd O:\veteranos
.\scripts\verify-modernizacion.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web
```

El verificador unico ejecuta pruebas PHP, pruebas frontend, `npm audit`, build de `dist\nueva_web` y smoke test de API. Tambien se pueden correr los pasos individuales:

```powershell
cd O:\veteranos
php api\tests\modern_api_response_test.php
php api\tests\env_loader_test.php
php api\tests\request_path_test.php
php api\tests\settings_test.php
php api\tests\package_script_test.php
php api\tests\staging_script_test.php
php api\tests\verify_script_test.php
php api\tests\health_http_test.php
.\scripts\test-nueva-web.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web
.\scripts\test-nueva-web.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web -CheckDatabase
.\scripts\validate-staging.ps1
```

El smoke test acepta que endpoints con DB real (`/api/fixture?torneo_id=1`, `/api/health/db`) respondan `200` si hay credenciales validas o `500` sanitizado si la DB local todavia no esta disponible. `-CheckDatabase` incluye `/api/health/db`; `-RequireDatabase` exige DB disponible y tambien valida `/api/torneos?status=I`.

Con la base local accesible, usar:

```powershell
.\scripts\test-nueva-web.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web -RequireDatabase
.\scripts\verify-modernizacion.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web -RequireDatabase
```

Ultima verificacion local estricta:

```text
.\scripts\verify-modernizacion.ps1 -BaseUrl http://127.0.0.1:8017/nueva_web -RequireDatabase
Modernizacion verification passed for O:\veteranos
Smoke checks: /api/health, /api/fixture, /api/fixture?torneo_id=1, /api/health/db y /api/torneos?status=I OK.
```

Verificacion adicional en navegador embebido:

```text
http://192.168.0.200:5185/#/goleadores
Goleadores carga 16 filas reales, sin Network Error ni estado Cargando.
El modal de detalle respeta torneo_id: Riveros Carlos Cesar muestra 1 fila de Senior Clausura.
```

Tambien se verifico que el backend acepte el prefijo de despliegue:

```powershell
Invoke-WebRequest http://127.0.0.1:8017/nueva_web/api/fixture -UseBasicParsing -SkipHttpErrorCheck
Invoke-WebRequest http://127.0.0.1:8017/nueva_web/api/health -UseBasicParsing -SkipHttpErrorCheck
Invoke-WebRequest http://127.0.0.1:8017/nueva_web/api/health/db -UseBasicParsing -SkipHttpErrorCheck
```

## Despliegue en `/nueva_web`

- El frontend se compila con `VITE_API_URL=https://veteranos.ar/nueva_web`, por lo que consume `https://veteranos.ar/nueva_web/api/...`.
- `api/public/index.php` normaliza peticiones con prefijo, por ejemplo `/nueva_web/api/fixture`, antes de que Slim resuelva las rutas.
- Usar `docs/htaccess-nueva-web.txt` como base para el `.htaccess` del directorio publico `/nueva_web`.
- La plantilla asume que el backend Slim queda publicado en `/nueva_web/api/public/index.php`.

Para generar un paquete local listo para subir:

```powershell
cd O:\veteranos
.\scripts\build-nueva-web.ps1
```

El resultado queda en `O:\veteranos\dist\nueva_web` e incluye:

- Build Vue en la raiz.
- `.htaccess` para SPA + API.
- `README_DEPLOY.txt` con pasos de publicacion y recordatorio de `api\.env`.
- Backend Slim en `api\`.
- `api\.env.example`, pero no copia `api\.env` con credenciales.
- `build-manifest.json` con conteos de archivos y origenes.

Para validar el paquete generado sin ejecutar el smoke HTTP:

```powershell
.\scripts\validate-staging.ps1
```

Para generar un ZIP listo para subir, reconstruyendo y validando antes de comprimir:

```powershell
.\scripts\package-nueva-web.ps1
```

El ZIP queda por defecto en `O:\veteranos\dist\nueva_web.zip` y el script rechaza empaquetar si detecta `api\.env` dentro del staging.

Smoke test recomendado luego de subir:

```powershell
Invoke-WebRequest https://veteranos.ar/nueva_web/api/health -UseBasicParsing
.\scripts\test-nueva-web.ps1 -BaseUrl https://veteranos.ar/nueva_web
.\scripts\test-nueva-web.ps1 -BaseUrl https://veteranos.ar/nueva_web -CheckDatabase
.\scripts\test-nueva-web.ps1 -BaseUrl https://veteranos.ar/nueva_web -RequireDatabase
```

## Pendiente

- Inicializar CodeGraph si se aprueba (`codegraph init -i`).
