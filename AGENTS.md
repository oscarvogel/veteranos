# AGENTS.md — veteranos.ar

## Contexto del proyecto
- Sitio público de la **Asociación de Fútbol Veteranos Ldor. Gral. San Martín** (Misiones, Argentina).
- Usuario (Oscar Vogel) es **secretario de la liga** y **administra el sitio**. Es el cliente + el operador.
- URL prod: `http://www.veteranos.ar` (también `http://veteranos.ar`).
- Hosting: Ferozo (`ye000174.ferozo.com`).

## Stack
- **Yii 1.1** (PHP 5.x o 7.x en prod — NO PHP 8, hay incompatibilidades con `foreach(null)` en AR).
- **Bootstrap 3.4.1** cargado por CDN en `themes/classic/views/layouts/main.php`.
- Theme activo: `themes/classic` (tiene el menú). El theme `blackboot` también existe pero no se usa.
- **NO usar `bootstrap.widgets.TbActiveForm` / `TbButton`** — son del extension de Yii para bootstrap 2, incompatible con el CSS de bootstrap 3 que carga el theme. El form se rompe (labels flotando a la derecha). Usar siempre HTML crudo con clases BS3 (`form-horizontal`, `row`, `col-md-*`, `form-group`, `form-control`, `btn btn-primary`, `btn btn-success`).
- DB prod: `ye000174_veteranos` en Ferozo. DB local dev: `veteranos` en MariaDB local (credenciales en `protected/config/db-local.php`).

## Deploy
- Credenciales FTP en `datos.ftp.txt` (root del proyecto). `Host=ye000174.ferozo.com`, user `codex@veteranos.ar`.
- FTP **requiere SSL/TLS** (error 550 si se usa `FTP` plano). Usar `ftplib.FTP_TLS` + `ftp.prot_p()`.
- **Subir solo los archivos cambiados**, no todo el repo. Verificar con MD5 después de cada upload.
- Archivos típicos: `protected/controllers/*.php`, `protected/views/**/*.php`, `themes/classic/views/layouts/*.php`.

## Modelo de datos clave
- Tabla `torneo`: `idTorneo`, `Nombre`, `Inicio`, `Estado` (A=Activo, B=Baja, S=Suspendido, I=Iniciado, F=Finalizado).
- Tabla `fixture`: `idFixture`, `idTorneo`, `NFecha` (int 1..N), `Fecha` (date), `Local` (FK a `equipos`), `Visitante` (FK a `equipos`, 0=libre), `idCancha`, `idArbitro`, `idLinea1`, `idLinea2`, `Hora`, `GolLocal`, `GolVisitante`.
- `Fixture::model()->ConsultaFixture($idTorneo)` devuelve los partidos ordenados por NFecha + Visitante desc.
- `Torneo::getListTorneo('I')` devuelve `CHtml::listData` filtrado por estado (usado por `ConsultaFixture` admin).

## Rutas clave
- `r=site/index` — home pública (artículos + tabla de posiciones de torneos I).
- `r=site/fixture` — **nueva página pública de fixture** (refactor de 2026-07-25). Dropdown torneo + dropdown fecha, tabla solo Local+Visitante, botones "Exportar a PDF" y "Compartir por WhatsApp" (wa.me).
- `r=site/fixturePdf` — genera PDF del fixture (filtro `&fecha=N` opcional).
- `r=site/fixtureSuper` — **redirect** al último Super Veteranos I/A (compat con link viejo, no borrar la ruta).
- `r=fixture/ConsultaFixture` — **admin vieja** con todos los árbitros y export a Excel. NO la usa el público.
- `r=fixture/ConsultaAsignaciones` — admin canchas y árbitros.

## Gotchas recurrentes
- `db-local.php` está en `.gitignore` (no se sube) y suele apuntar a DB que no existe. Para correr local con la DB real, overridear `$config['components']['db']` en scripts standalone.
- Smoke test local con PHP 8.2 explota por bug de Yii 1.x con `foreach(null)` en `_table->primaryKey`. No es problema del código, es de la versión de PHP. En prod (PHP 5/7) anda.
- El extension `ePdf` (mPDF) está preconfigurado en `main.php` como `Yii::app()->ePdf`. Usar `$pdf = Yii::app()->ePdf->mpdf('', 'A4', 0, '', 12, 12, 14, 14, 9, 9, 'P')` (formato, márgenes, orientación). Output `'I'` = inline en browser, `'D'` = download.
- `Fixture` tiene relaciones lowercase: `$partido->local`, `$partido->visitante` (NO `Local`/`Visitante` que son las columnas FK).
- Las relaciones `local`/`visitante`/`Cancha`/`Arbitro`/`Linea1`/`Linea2` son `BELONGS_TO`, devuelven `null` si la FK no apunta a nada. Usar `if ($partido->local)` antes de acceder a `->Nombre`.

## Historial reciente
- 2026-07-25: refactor de la página de fixture pública. Creado `site/fixture` (genérico, todos los torneos I/A, filtro por fecha, share WhatsApp, export PDF) + modificado menú. Subido al FTP.
