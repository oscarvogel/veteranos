<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class FixtureModel
{
    private $db;
    private $table = 'fixture';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll($idTorneo = null)
    {
        $r = new Response();
        try {
            $where  = $idTorneo ? "WHERE f.idTorneo = ?" : "";
            $params = $idTorneo ? [(int)$idTorneo] : [];
            $stm = $this->db->prepare(
                "SELECT f.*,
                        el.Nombre AS local_nombre, ev.Nombre AS visitante_nombre,
                        c.Nombre  AS cancha_nombre,
                        a.Nombre  AS arbitro_nombre,
                        l1.Nombre AS linea1_nombre, l2.Nombre AS linea2_nombre
                 FROM {$this->table} f
                 LEFT JOIN equipos el  ON f.Local      = el.idEquipo
                 LEFT JOIN equipos ev  ON f.Visitante  = ev.idEquipo
                 LEFT JOIN canchas c   ON f.idCancha   = c.idCancha
                 LEFT JOIN arbitros a  ON f.idArbitro  = a.idArbitro
                 LEFT JOIN arbitros l1 ON f.idLinea1   = l1.idArbitro
                 LEFT JOIN arbitros l2 ON f.idLinea2   = l2.idArbitro
                 {$where}
                 ORDER BY f.NFecha ASC, f.Fecha ASC"
            );
            $stm->execute($params);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function GetFecha($idTorneo, $nFecha)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT f.*,
                        el.Nombre AS local_nombre, ev.Nombre AS visitante_nombre,
                        c.Nombre AS cancha_nombre, a.Nombre AS arbitro_nombre
                 FROM {$this->table} f
                 LEFT JOIN equipos el ON f.Local     = el.idEquipo
                 LEFT JOIN equipos ev ON f.Visitante = ev.idEquipo
                 LEFT JOIN canchas c  ON f.idCancha  = c.idCancha
                 LEFT JOIN arbitros a ON f.idArbitro = a.idArbitro
                 WHERE f.idTorneo = ? AND f.NFecha = ?
                 ORDER BY f.Fecha ASC"
            );
            $stm->execute([(int)$idTorneo, (int)$nFecha]);
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
                "SELECT f.*,
                        el.Nombre AS local_nombre, ev.Nombre AS visitante_nombre,
                        c.Nombre AS cancha_nombre, a.Nombre AS arbitro_nombre,
                        l1.Nombre AS linea1_nombre, l2.Nombre AS linea2_nombre
                 FROM {$this->table} f
                 LEFT JOIN equipos el  ON f.Local     = el.idEquipo
                 LEFT JOIN equipos ev  ON f.Visitante = ev.idEquipo
                 LEFT JOIN canchas c   ON f.idCancha  = c.idCancha
                 LEFT JOIN arbitros a  ON f.idArbitro = a.idArbitro
                 LEFT JOIN arbitros l1 ON f.idLinea1  = l1.idArbitro
                 LEFT JOIN arbitros l2 ON f.idLinea2  = l2.idArbitro
                 WHERE f.idFixture = ?"
            );
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Resultados: solo partidos jugados */
    public function GetResultados($idTorneo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT f.idFixture, f.NFecha, f.Fecha,
                        el.Nombre AS local_nombre, f.GolLocal,
                        ev.Nombre AS visitante_nombre, f.GolVisitante
                 FROM {$this->table} f
                 LEFT JOIN equipos el ON f.Local     = el.idEquipo
                 LEFT JOIN equipos ev ON f.Visitante = ev.idEquipo
                 WHERE f.idTorneo = ? AND f.GolLocal IS NOT NULL AND f.GolLocal != ''
                 ORDER BY f.NFecha ASC, f.Fecha ASC"
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
            if (empty($data['Local']) || empty($data['Visitante'])) {
                $r->SetResponse(false, 'Local y Visitante son requeridos');
                return $r;
            }
            $fields = [
                'idTorneo'       => (int)$data['idTorneo'],
                'NFecha'         => (int)($data['NFecha'] ?? 0),
                'Fecha'          => $data['Fecha'] ?? null,
                'Local'          => (int)$data['Local'],
                'Visitante'      => (int)$data['Visitante'],
                'GolLocal'       => isset($data['GolLocal'])     ? (int)$data['GolLocal']     : null,
                'GolVisitante'   => isset($data['GolVisitante']) ? (int)$data['GolVisitante'] : null,
                'PuntosLocal'    => $data['PuntosLocal']    ?? null,
                'PuntosVisitante'=> $data['PuntosVisitante'] ?? null,
                'idCancha'       => isset($data['idCancha'])   ? (int)$data['idCancha']   : null,
                'idArbitro'      => isset($data['idArbitro'])  ? (int)$data['idArbitro']  : null,
                'idLinea1'       => isset($data['idLinea1'])   ? (int)$data['idLinea1']   : null,
                'idLinea2'       => isset($data['idLinea2'])   ? (int)$data['idLinea2']   : null,
                'Hora'           => $data['Hora']          ?? null,
                'PostTemporada'  => $data['PostTemporada'] ?? 0,
                'SumaPuntos'     => $data['SumaPuntos']    ?? 1,
                'interzonal'     => $data['interzonal']    ?? 0,
            ];

            if (!empty($data['idFixture'])) {
                $keys = implode('=?, ', array_keys($fields)) . '=?';
                $stm  = $this->db->prepare(
                    "UPDATE {$this->table} SET {$keys} WHERE idFixture=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idFixture']]);
                $r->result = ['idFixture' => (int)$data['idFixture']];
            } else {
                $cols = implode(',', array_keys($fields));
                $vals = implode(',', array_fill(0, count($fields), '?'));
                $stm  = $this->db->prepare(
                    "INSERT INTO {$this->table} ({$cols}) VALUES ({$vals})"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idFixture' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idFixture = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
