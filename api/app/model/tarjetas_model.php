<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class TarjetasModel
{
    private $db;
    private $table = 'tarjetas';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT t.*,
                        j.Nombre AS jugador, e.Nombre AS equipo,
                        el.Nombre AS local, ev.Nombre AS visitante,
                        f.NFecha, f.Fecha AS fecha_partido
                 FROM {$this->table} t
                 JOIN jugador j   ON t.idJugador = j.idJugador
                 LEFT JOIN equipos e  ON t.idEquipo  = e.idEquipo
                 JOIN fixture f   ON t.idFixture = f.idFixture
                 LEFT JOIN equipos el ON f.Local       = el.idEquipo
                 LEFT JOIN equipos ev ON f.Visitante   = ev.idEquipo
                 ORDER BY f.Fecha DESC"
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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idTarjeta = ?");
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Tarjetas vigentes (suspensiones activas) */
    public function GetVigentes()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT t.*, j.Nombre AS jugador, e.Nombre AS equipo,
                        f.NFecha AS desde_fecha, t.HastaFecha AS hasta_fecha
                 FROM {$this->table} t
                 JOIN jugador j  ON t.idJugador = j.idJugador
                 JOIN fixture f  ON t.idFixture = f.idFixture
                 LEFT JOIN equipos e ON t.idEquipo = e.idEquipo
                 WHERE t.Roja > 0
                 ORDER BY j.Nombre ASC"
            );
            $stm->execute();
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Tabla fair play por equipo en un torneo */
    public function GetFairPlay($idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT e.idEquipo, e.Nombre AS equipo,
                        SUM(t.Amarilla) AS amarillas, SUM(t.Roja) AS rojas
                 FROM {$this->table} t
                 JOIN jugador j  ON t.idJugador = j.idJugador
                 JOIN equipos e  ON j.idEquipo  = e.idEquipo
                 JOIN fixture f  ON t.idFixture = f.idFixture
                 WHERE f.idTorneo = ?
                 GROUP BY e.idEquipo, e.Nombre
                 ORDER BY rojas ASC, amarillas ASC"
            );
            $stm->execute([(int)$idTorneo]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Tarjetas por equipo en un torneo */
    public function GetByEquipoTorneo($idEquipo, $idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT t.*, j.Nombre AS jugador, f.NFecha, f.Fecha AS fecha_partido
                 FROM {$this->table} t
                 JOIN jugador j ON t.idJugador = j.idJugador
                 JOIN fixture f ON t.idFixture = f.idFixture
                 WHERE j.idEquipo = ? AND f.idTorneo = ?
                 ORDER BY f.NFecha ASC"
            );
            $stm->execute([(int)$idEquipo, (int)$idTorneo]);
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
            if (empty($data['idJugador']) || empty($data['idFixture'])) {
                $r->SetResponse(false, 'idJugador e idFixture son requeridos');
                return $r;
            }
            $fields = [
                'idFixture'   => (int)$data['idFixture'],
                'idJugador'   => (int)$data['idJugador'],
                'Amarilla'    => isset($data['Amarilla'])  ? (int)$data['Amarilla']  : 0,
                'Roja'        => isset($data['Roja'])      ? (int)$data['Roja']      : 0,
                'DesdeFecha'  => $data['DesdeFecha']   ?? null,
                'HastaFecha'  => $data['HastaFecha']   ?? null,
                'Motivo'      => $data['Motivo']        ?? '',
                'idEquipo'    => isset($data['idEquipo']) ? (int)$data['idEquipo'] : null,
            ];

            if (!empty($data['idTarjeta'])) {
                $keys = implode('=?, ', array_keys($fields)) . '=?';
                $stm  = $this->db->prepare(
                    "UPDATE {$this->table} SET {$keys} WHERE idTarjeta=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idTarjeta']]);
                $r->result = ['idTarjeta' => (int)$data['idTarjeta']];
            } else {
                $cols = implode(',', array_keys($fields));
                $vals = implode(',', array_fill(0, count($fields), '?'));
                $stm  = $this->db->prepare(
                    "INSERT INTO {$this->table} ({$cols}) VALUES ({$vals})"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idTarjeta' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idTarjeta = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
