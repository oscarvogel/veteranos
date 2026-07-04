# Plantilla: PHP Route (`api/app/route/<modulo>_route.php`)

> El proyecto usa **Slim 3** — la sintaxis de grupos usa `$this->get(...)` (no `$group->get(...)`).

```php
<?php
use App\Model\<Modulo>Model;

$app->group('/<modulo>', function () {

    // GET /api/<modulo>/ — lista (público o protegido según módulo)
    $this->get('/', function ($req, $res, $args) {
        $model = new <Modulo>Model();
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($model->GetAll()));
    });

    // GET /api/<modulo>/{id} — detalle
    $this->get('/{id}', function ($req, $res, $args) {
        $model = new <Modulo>Model();
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($model->Get((int)$args['id'])));
    });

    // POST /api/<modulo>/save — crear o actualizar (requiere autenticación)
    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new <Modulo>Model();
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($model->Save($data)));
    });

    // POST /api/<modulo>/delete/{id} — eliminar (requiere autenticación)
    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new <Modulo>Model();
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($model->Delete((int)$args['id'])));
    });

});
```

## Registrar en `api/src/routes.php`

Agregar al final del archivo:

```php
require __DIR__ . '/../app/route/<modulo>_route.php';
```

## Respuesta estándar (`App\Lib\Response`)

Los modelos devuelven una instancia de `App\Lib\Response` con:

| Propiedad | Tipo | Descripción |
|---|---|---|
| `response` | bool | `true` si tuvo éxito |
| `result` | mixed | datos devueltos |
| `message` | string | mensaje de error si `response = false` |

El frontend Vue chequea `data.response === true`.

## CORS (frontend Vue en `frontend/` en mismo repo)

Si el dev server de Vite corre en `localhost:5173` y la API en otra ruta/puerto, agregar en `api/public/index.php`:

```php
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});
```

En producción no se necesita CORS: el frontend compilado se sirve desde el mismo dominio.

## Notas de seguridad

- Castear siempre los IDs a `(int)` antes de pasar al modelo.
- Las rutas de escritura (save, delete) deben requerir autenticación (JWT middleware cuando se implemente).
- No retornar stack traces al cliente en producción (configurar en `api/src/settings.php`).
