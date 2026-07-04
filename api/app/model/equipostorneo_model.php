<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class EquipostorneoModel
{
    private $db;
    private $table = 'equipostorneo';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT et.*, e.Nombre AS equipo, t.Nombre AS torneo
                 FROM {$this->table} et
                 JOIN equipos e ON et.idEquipo = e.idEquipo
                 JOIN torneo t ON et.idTorneo = t.idTorneo
                 ORDER BY t.idTorneo DESC, e.Nombre ASC"
            );
            $stm->execute();
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function GetByTorneo($idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT et.*, e.Nombre AS equipo
                 FROM {$this->table} et
                 JOIN equipos e ON et.idEquipo = e.idEquipo
                 WHERE et.idTorneo = ?
                 ORDER BY e.Nombre ASC"
            );
            $stm->execute([(int)$idTorneo]);
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
                "SELECT et.*, e.Nombre AS equipo, t.Nombre AS torneo
                 FROM {$this->table} et
                 JOIN equipos e ON et.idEquipo = e.idEquipo
                 JOIN torneo t ON et.idTorneo = t.idTorneo
                 WHERE et.idEquTorneo = ?"
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
            if (empty($data['idEquipo']) || empty($data['idTorneo'])) {
                $r->SetResponse(false, 'idEquipo e idTorneo son requeridos');
                return $r;
            }
            $lista = $data['lista'] ?? '';

            if (!empty($data['idEquTorneo'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET idEquipo=?, idTorneo=?, lista=? WHERE idEquTorneo=?"
                );
                $stm->execute([(int)$data['idEquipo'], (int)$data['idTorneo'], $lista, (int)$data['idEquTorneo']]);
                $r->result = ['idEquTorneo' => (int)$data['idEquTorneo']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (idEquipo, idTorneo, lista) VALUES (?,?,?)"
                );
                $stm->execute([(int)$data['idEquipo'], (int)$data['idTorneo'], $lista]);
                $r->result = ['idEquTorneo' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idEquTorneo = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
