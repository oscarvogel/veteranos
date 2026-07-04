<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class CanchasModel
{
    private $db;
    private $table = 'canchas';

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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idCancha = ?");
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
            $titular  = htmlspecialchars(strip_tags($data['Titular'] ?? ''));
            $telefono = htmlspecialchars(strip_tags($data['Telefono'] ?? ''));

            if (!empty($data['idCancha'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET Nombre=?, Titular=?, Telefono=? WHERE idCancha=?"
                );
                $stm->execute([$nombre, $titular, $telefono, (int)$data['idCancha']]);
                $r->result = ['idCancha' => (int)$data['idCancha']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (Nombre, Titular, Telefono) VALUES (?,?,?)"
                );
                $stm->execute([$nombre, $titular, $telefono]);
                $r->result = ['idCancha' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idCancha = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
