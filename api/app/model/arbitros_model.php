<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class ArbitrosModel
{
    private $db;
    private $table = 'arbitros';

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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idArbitro = ?");
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
            if (empty($data['Nombre'])) {
                $r->SetResponse(false, 'El nombre es requerido');
                return $r;
            }
            $nombre   = htmlspecialchars(strip_tags($data['Nombre']));
            $telefono = htmlspecialchars(strip_tags($data['Telefono'] ?? ''));
            $correo   = htmlspecialchars(strip_tags($data['Correo'] ?? ''));

            if (!empty($data['idArbitro'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET Nombre=?, Telefono=?, Correo=? WHERE idArbitro=?"
                );
                $stm->execute([$nombre, $telefono, $correo, (int)$data['idArbitro']]);
                $r->result = ['idArbitro' => (int)$data['idArbitro']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (Nombre, Telefono, Correo) VALUES (?,?,?)"
                );
                $stm->execute([$nombre, $telefono, $correo]);
                $r->result = ['idArbitro' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idArbitro = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
