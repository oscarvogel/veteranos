<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class TorneoModel
{
    private $db;
    private $table = 'torneo';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY idTorneo DESC");
            $stm->execute();
            $r->result   = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function GetActivos()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE Estado IN ('A','I') ORDER BY idTorneo DESC");
            $stm->execute();
            $r->result   = $stm->fetchAll(PDO::FETCH_OBJ);
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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idTorneo = ?");
            $stm->execute([(int)$id]);
            $r->result   = $stm->fetch(PDO::FETCH_OBJ);
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
            if (!empty($data['idTorneo'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET Nombre=?, Inicio=?, Estado=?, InicioTorneo=? WHERE idTorneo=?"
                );
                $stm->execute([
                    htmlspecialchars(strip_tags($data['Nombre'])),
                    $data['Inicio'] ?? null,
                    $data['Estado'] ?? 'A',
                    $data['InicioTorneo'] ?? 0,
                    (int)$data['idTorneo'],
                ]);
                $r->result = ['idTorneo' => (int)$data['idTorneo']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (Nombre, Inicio, Estado, InicioTorneo) VALUES (?,?,?,?)"
                );
                $stm->execute([
                    htmlspecialchars(strip_tags($data['Nombre'])),
                    $data['Inicio'] ?? null,
                    $data['Estado'] ?? 'A',
                    $data['InicioTorneo'] ?? 0,
                ]);
                $r->result = ['idTorneo' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idTorneo = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
