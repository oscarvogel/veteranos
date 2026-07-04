# Plantilla: PHP Model (`api/app/model/<modulo>_model.php`)

El proyecto ya tiene `App\Lib\Database` (`Database::StartUp()` devuelve PDO) y `App\Lib\Response`.
Las credenciales se leen desde `api/.env` automáticamente (cargado en `api/public/index.php`).

```php
<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use PDOException;

class <Modulo>Model {

    private $db;

    public function __construct() {
        $this->db = Database::StartUp();
    }

    /**
     * Retorna todos los registros.
     * Usar JOINs aquí para resolver relaciones del modelo Yii (relations()).
     */
    public function GetAll() {
        $r = new Response();
        try {
            $stmt = $this->db->prepare("SELECT * FROM <tabla> ORDER BY id DESC");
            $stmt->execute();
            $r->result   = $stmt->fetchAll(PDO::FETCH_OBJ);
            $r->response = true;
            $r->message  = '';
        } catch (PDOException $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /**
     * Retorna un registro por ID.
     */
    public function Get($id) {
        $r = new Response();
        try {
            $stmt = $this->db->prepare("SELECT * FROM <tabla> WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$data) {
                $r->SetResponse(false, 'Registro no encontrado');
            } else {
                $r->result   = $data;
                $r->response = true;
                $r->message  = '';
            }
        } catch (PDOException $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /**
     * Inserta o actualiza. $data proviene del body JSON del request.
     */
    public function Save($data) {
        $r = new Response();
        try {
            // Validar campos requeridos
            $required = ['campo1', 'campo2']; // ajustar según reglas() del modelo Yii
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $r->SetResponse(false, "El campo '{$field}' es requerido");
                    return $r;
                }
            }

            if (!empty($data['id'])) {
                // UPDATE
                $stmt = $this->db->prepare(
                    "UPDATE <tabla> SET campo1 = :campo1, campo2 = :campo2 WHERE id = :id"
                );
                $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);
            } else {
                // INSERT
                $stmt = $this->db->prepare(
                    "INSERT INTO <tabla> (campo1, campo2) VALUES (:campo1, :campo2)"
                );
            }

            $stmt->bindValue(':campo1', htmlspecialchars(strip_tags($data['campo1'])));
            $stmt->bindValue(':campo2', htmlspecialchars(strip_tags($data['campo2'])));
            $stmt->execute();

            $r->result   = ['id' => empty($data['id']) ? $this->db->lastInsertId() : $data['id']];
            $r->response = true;
            $r->message  = '';
        } catch (PDOException $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /**
     * Elimina un registro por ID.
     */
    public function Delete($id) {
        $r = new Response();
        try {
            $stmt = $this->db->prepare("DELETE FROM <tabla> WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            $r->result   = ['deleted' => $stmt->rowCount() > 0];
            $r->response = true;
            $r->message  = '';
        } catch (PDOException $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
```

## Notas

- Namespace: `App\Model\<Modulo>Model` — el autoloader de `app_loader.php` lo incluye automáticamente.
- `Database::StartUp()` lee las credenciales desde `api/.env` vía `getenv()`. No hardcodear credenciales.
- **Nunca** interpolar variables en SQL — siempre `bindValue()` con tipo explícito.
- `htmlspecialchars(strip_tags(...))` en campos de texto libre para sanitizar inputs.
- Las relaciones del modelo Yii (`relations()`) se resuelven como JOINs en el SELECT.
- Reemplazar `<tabla>` con el nombre real de la tabla (ver `tableName()` en el modelo Yii original).
