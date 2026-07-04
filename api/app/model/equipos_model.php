<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class EquiposModel
{
    private $db;
    private $table = 'equipos';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT e.*, c.Nombre AS NombreCategoria
                 FROM {$this->table} e
                 LEFT JOIN categorias c ON e.idCategoria = c.idCategoria
                 ORDER BY e.Nombre ASC"
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
                "SELECT e.*, c.Nombre AS NombreCategoria
                 FROM {$this->table} e
                 LEFT JOIN categorias c ON e.idCategoria = c.idCategoria
                 WHERE e.idEquipo = ?"
            );
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Equipos inscritos en un torneo */
    public function GetPorTorneo($idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT e.*, et.idEquTorneo, et.lista
                 FROM equipostorneo et
                 JOIN {$this->table} e ON et.idEquipo = e.idEquipo
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

    /** Lista de buena fe: jugadores habilitados de los equipos de un torneo */
    public function GetListaBuenaFe($idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT e.idEquipo, e.Nombre AS equipo, et.lista,
                        j.idJugador, j.Nombre AS jugador, j.DNI, j.Clase,
                        j.certificado, j.firma_lista, j.fotocopia_dni, j.dec_jurada
                 FROM equipostorneo et
                 JOIN {$this->table} e ON et.idEquipo = e.idEquipo
                 LEFT JOIN jugador j ON j.idEquipo = e.idEquipo
                 WHERE et.idTorneo = ?
                 ORDER BY e.Nombre ASC, j.Nombre ASC"
            );
            $stm->execute([(int)$idTorneo]);
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
            if (empty($data['Nombre']) || empty($data['Delegado'])) {
                $r->SetResponse(false, 'Nombre y Delegado son requeridos');
                return $r;
            }
            $fields = [
                'Nombre'           => htmlspecialchars(strip_tags($data['Nombre'])),
                'Delegado'         => htmlspecialchars(strip_tags($data['Delegado'])),
                'idCategoria'      => $data['idCategoria'] ?? null,
                'DelegadoSuplente' => htmlspecialchars(strip_tags($data['DelegadoSuplente'] ?? '')),
                'Camiseta'         => htmlspecialchars(strip_tags($data['Camiseta'] ?? '')),
                'CamisetaSuplente' => htmlspecialchars(strip_tags($data['CamisetaSuplente'] ?? '')),
                'Cancha'           => $data['Cancha'] ?? null,
                'Correo'           => htmlspecialchars(strip_tags($data['Correo'] ?? '')),
                'Telefono'         => htmlspecialchars(strip_tags($data['Telefono'] ?? '')),
            ];

            if (!empty($data['idEquipo'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET
                        Nombre=?, Delegado=?, idCategoria=?, DelegadoSuplente=?,
                        Camiseta=?, CamisetaSuplente=?, Cancha=?, Correo=?, Telefono=?
                     WHERE idEquipo=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idEquipo']]);
                $r->result = ['idEquipo' => (int)$data['idEquipo']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table}
                        (Nombre, Delegado, idCategoria, DelegadoSuplente, Camiseta, CamisetaSuplente, Cancha, Correo, Telefono)
                     VALUES (?,?,?,?,?,?,?,?,?)"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idEquipo' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idEquipo = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Autocomplete($q)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT idEquipo AS id, Nombre AS label FROM {$this->table}
                 WHERE Nombre LIKE ? ORDER BY Nombre ASC LIMIT 10"
            );
            $stm->execute(["%{$q}%"]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
