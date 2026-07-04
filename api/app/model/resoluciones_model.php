<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class ResolucionesModel
{
    private $db;
    private $table = 'resoluciones';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY Fecha DESC");
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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idResolucion = ?");
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
            $url     = htmlspecialchars(strip_tags($data['URL'] ?? ''));
            $detalle = $data['Detalle'] ?? '';
            $fecha   = $data['Fecha'] ?? null;

            if (!empty($data['idResolucion'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET Fecha=?, URL=?, Detalle=? WHERE idResolucion=?"
                );
                $stm->execute([$fecha, $url, $detalle, (int)$data['idResolucion']]);
                $r->result = ['idResolucion' => (int)$data['idResolucion']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (Fecha, URL, Detalle) VALUES (?,?,?)"
                );
                $stm->execute([$fecha, $url, $detalle]);
                $r->result = ['idResolucion' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idResolucion = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
