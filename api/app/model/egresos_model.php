<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class EgresosModel
{
    private $db;
    private $table = 'egresos';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT eg.*, c.Nombre AS concepto
                 FROM {$this->table} eg
                 JOIN conceptos c ON eg.idConcepto = c.idConcepto
                 ORDER BY eg.Fecha DESC"
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
                "SELECT eg.*, c.Nombre AS concepto
                 FROM {$this->table} eg
                 JOIN conceptos c ON eg.idConcepto = c.idConcepto
                 WHERE eg.idEgreso = ?"
            );
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
            if (empty($data['idConcepto'])) {
                $r->SetResponse(false, 'idConcepto es requerido');
                return $r;
            }
            $fields = [
                'idConcepto' => (int)$data['idConcepto'],
                'Detalle'    => $data['Detalle'] ?? '',
                'Fecha'      => $data['Fecha']   ?? date('Y-m-d'),
                'Monto'      => $data['Monto']   ?? null,
            ];

            if (!empty($data['idEgreso'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET idConcepto=?, Detalle=?, Fecha=?, Monto=? WHERE idEgreso=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idEgreso']]);
                $r->result = ['idEgreso' => (int)$data['idEgreso']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (idConcepto, Detalle, Fecha, Monto) VALUES (?,?,?,?)"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idEgreso' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idEgreso = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
