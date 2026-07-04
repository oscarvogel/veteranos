<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class ConceptosModel
{
    private $db;
    private $table = 'conceptos';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY Nombre ASC");
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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idConcepto = ?");
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Movimientos (ingresos + egresos) por concepto */
    public function GetMovimientos($idConcepto)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT 'ingreso' AS tipo, i.Fecha, i.Monto, i.Detalle, e.Nombre AS equipo
                 FROM ingresos i
                 JOIN equipos e ON i.idEquipo = e.idEquipo
                 WHERE i.idConcepto = ?
                 UNION ALL
                 SELECT 'egreso' AS tipo, eg.Fecha, eg.Monto, eg.Detalle, '' AS equipo
                 FROM egresos eg
                 WHERE eg.idConcepto = ?
                 ORDER BY Fecha DESC"
            );
            $stm->execute([(int)$idConcepto, (int)$idConcepto]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
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
            if (empty($data['Nombre'])) {
                $r->SetResponse(false, 'El nombre es requerido');
                return $r;
            }
            $nombre = htmlspecialchars(strip_tags($data['Nombre']));
            $monto  = $data['Monto'] ?? null;

            if (!empty($data['idConcepto'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET Nombre=?, Monto=? WHERE idConcepto=?"
                );
                $stm->execute([$nombre, $monto, (int)$data['idConcepto']]);
                $r->result = ['idConcepto' => (int)$data['idConcepto']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (Nombre, Monto) VALUES (?,?)"
                );
                $stm->execute([$nombre, $monto]);
                $r->result = ['idConcepto' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idConcepto = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
