<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class ArticulosModel
{
    private $db;
    private $table = 'Articulos';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll($soloActivos = true)
    {
        $r = new Response();
        try {
            $where = $soloActivos ? "WHERE Activo = 1" : "";
            $stm = $this->db->prepare(
                "SELECT idArticulo, FechaPublicacion, Activo, Titulo, Introduccion
                 FROM {$this->table} {$where} ORDER BY FechaPublicacion DESC"
            );
            $stm->execute();
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Get($id)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idArticulo = ?");
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Save($data)
    {
        $r = new Response();
        try {
            $titulo       = htmlspecialchars(strip_tags($data['Titulo'] ?? ''));
            $introduccion = $data['Introduccion'] ?? '';
            $texto        = $data['Texto'] ?? '';
            $fecha        = $data['FechaPublicacion'] ?? date('Y-m-d');
            $activo       = isset($data['Activo']) ? (int)$data['Activo'] : 1;

            if (!empty($data['idArticulo'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET FechaPublicacion=?, Activo=?, Titulo=?, Texto=?, Introduccion=?
                     WHERE idArticulo=?"
                );
                $stm->execute([$fecha, $activo, $titulo, $texto, $introduccion, (int)$data['idArticulo']]);
                $r->result = ['idArticulo' => (int)$data['idArticulo']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (FechaPublicacion, Activo, Titulo, Texto, Introduccion)
                     VALUES (?,?,?,?,?)"
                );
                $stm->execute([$fecha, $activo, $titulo, $texto, $introduccion]);
                $r->result = ['idArticulo' => $this->db->lastInsertId()];
            }
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Delete($id)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idArticulo = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
