<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class IngresosModel
{
    private $db;
    private $table = 'ingresos';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT i.*, e.Nombre AS equipo, c.Nombre AS concepto
                 FROM {$this->table} i
                 JOIN equipos e   ON i.idEquipo  = e.idEquipo
                 JOIN conceptos c ON i.idConcepto = c.idConcepto
                 ORDER BY i.Fecha DESC"
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
            $stm = $this->db->prepare(
                "SELECT i.*, e.Nombre AS equipo, c.Nombre AS concepto
                 FROM {$this->table} i
                 JOIN equipos e   ON i.idEquipo  = e.idEquipo
                 JOIN conceptos c ON i.idConcepto = c.idConcepto
                 WHERE i.idIngreso = ?"
            );
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function GetByEquipo($idEquipo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT i.*, c.Nombre AS concepto
                 FROM {$this->table} i
                 JOIN conceptos c ON i.idConcepto = c.idConcepto
                 WHERE i.idEquipo = ?
                 ORDER BY i.Fecha DESC"
            );
            $stm->execute([(int)$idEquipo]);
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
            if (empty($data['idEquipo']) || empty($data['idConcepto'])) {
                $r->SetResponse(false, 'idEquipo e idConcepto son requeridos');
                return $r;
            }
            $fields = [
                'idEquipo'   => (int)$data['idEquipo'],
                'NFecha'     => isset($data['NFecha']) ? (int)$data['NFecha'] : null,
                'Fecha'      => $data['Fecha'] ?? date('Y-m-d'),
                'Hora'       => $data['Hora']  ?? null,
                'Monto'      => $data['Monto'] ?? null,
                'idConcepto' => (int)$data['idConcepto'],
                'Detalle'    => $data['Detalle'] ?? '',
            ];

            if (!empty($data['idIngreso'])) {
                $keys = implode('=?, ', array_keys($fields)) . '=?';
                $stm  = $this->db->prepare(
                    "UPDATE {$this->table} SET {$keys} WHERE idIngreso=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idIngreso']]);
                $r->result = ['idIngreso' => (int)$data['idIngreso']];
            } else {
                $cols = implode(',', array_keys($fields));
                $vals = implode(',', array_fill(0, count($fields), '?'));
                $stm  = $this->db->prepare(
                    "INSERT INTO {$this->table} ({$cols}) VALUES ({$vals})"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idIngreso' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idIngreso = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
