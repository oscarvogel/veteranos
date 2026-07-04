<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class GolesModel
{
    private $db;
    private $table = 'goles';

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT g.*, j.Nombre AS jugador, e.Nombre AS equipo,
                        el.Nombre AS local, ev.Nombre AS visitante, f.NFecha
                 FROM {$this->table} g
                 JOIN jugador j   ON g.idJugador = j.idJugador
                 LEFT JOIN equipos e  ON j.idEquipo   = e.idEquipo
                 JOIN fixture f   ON g.idFixture = f.idFixture
                 LEFT JOIN equipos el ON f.Local       = el.idEquipo
                 LEFT JOIN equipos ev ON f.Visitante   = ev.idEquipo
                 ORDER BY g.idFixture ASC"
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
            $stm = $this->db->prepare("SELECT * FROM {$this->table} WHERE idGol = ?");
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Tabla de goleadores por torneo */
    public function GetGoleadores($idTorneo)
    {
        $r = new Response();
        try {
            $fixtureStmt = $this->db->prepare("SELECT idFixture FROM fixture WHERE idTorneo = ?");
            $fixtureStmt->execute([(int)$idTorneo]);
            $fixtureIds = array_map('intval', $fixtureStmt->fetchAll(PDO::FETCH_COLUMN));

            if (count($fixtureIds) === 0) {
                $r->result = [];
                $r->SetResponse(true, '');
                return $r;
            }

            $fixtureLookup = array_fill_keys($fixtureIds, true);
            $goalsStmt = $this->db->prepare("SELECT idJugador, idFixture, Cantidad FROM {$this->table}");
            $goalsStmt->execute();

            $totals = [];
            while ($goal = $goalsStmt->fetch(PDO::FETCH_OBJ)) {
                $fixtureId = (int)$goal->idFixture;
                if (!isset($fixtureLookup[$fixtureId])) {
                    continue;
                }

                $playerId = (int)$goal->idJugador;
                if (!isset($totals[$playerId])) {
                    $totals[$playerId] = 0;
                }
                $totals[$playerId] += is_numeric($goal->Cantidad) ? (int)$goal->Cantidad : 1;
            }

            if (count($totals) === 0) {
                $r->result = [];
                $r->SetResponse(true, '');
                return $r;
            }

            $playerIds = array_keys($totals);
            $placeholders = implode(',', array_fill(0, count($playerIds), '?'));
            $playersStmt = $this->db->prepare(
                "SELECT j.idJugador, j.Nombre AS jugador, e.Nombre AS equipo
                 FROM jugador j
                 LEFT JOIN equipos e ON j.idEquipo = e.idEquipo
                 WHERE j.idJugador IN ({$placeholders})"
            );
            $playersStmt->execute($playerIds);

            $players = [];
            while ($player = $playersStmt->fetch(PDO::FETCH_OBJ)) {
                $players[(int)$player->idJugador] = $player;
            }

            $rows = [];
            foreach ($totals as $playerId => $totalGoals) {
                if (!isset($players[$playerId])) {
                    continue;
                }
                $row = new \stdClass();
                $row->idJugador = $playerId;
                $row->jugador = $players[$playerId]->jugador;
                $row->equipo = $players[$playerId]->equipo;
                $row->total_goles = $totalGoals;
                $rows[] = $row;
            }

            usort($rows, function ($a, $b) {
                if ($b->total_goles !== $a->total_goles) {
                    return $b->total_goles - $a->total_goles;
                }
                return strcmp($a->jugador, $b->jugador);
            });

            $r->result = $rows;
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Goles de un jugador */
    public function GetByJugador($idJugador, $idTorneo = null, $nFecha = 0)
    {
        $r = new Response();
        try {
            $fixtureWhere = [];
            $fixtureParams = [];
            if ($idTorneo !== null && (int)$idTorneo > 0) {
                $fixtureWhere[] = 'f.idTorneo = ?';
                $fixtureParams[] = (int)$idTorneo;
            }
            if ((int)$nFecha > 0) {
                $fixtureWhere[] = 'f.NFecha = ?';
                $fixtureParams[] = (int)$nFecha;
            }
            $whereSql = count($fixtureWhere) > 0 ? 'WHERE ' . implode(' AND ', $fixtureWhere) : '';

            $fixtureStmt = $this->db->prepare(
                "SELECT f.idFixture, f.NFecha, f.Fecha,
                        el.Nombre AS local, ev.Nombre AS visitante, t.Nombre AS torneo
                 FROM fixture f
                 LEFT JOIN equipos el ON f.Local = el.idEquipo
                 LEFT JOIN equipos ev ON f.Visitante = ev.idEquipo
                 LEFT JOIN torneo t ON f.idTorneo = t.idTorneo
                 {$whereSql}"
            );
            $fixtureStmt->execute($fixtureParams);

            $fixtures = [];
            while ($fixture = $fixtureStmt->fetch(PDO::FETCH_OBJ)) {
                $fixtures[(int)$fixture->idFixture] = $fixture;
            }

            if (count($fixtures) === 0) {
                $r->result = [];
                $r->SetResponse(true, '');
                return $r;
            }

            $goalsStmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE idJugador = ?");
            $goalsStmt->execute([(int)$idJugador]);

            $rows = [];
            while ($goal = $goalsStmt->fetch(PDO::FETCH_OBJ)) {
                $fixtureId = (int)$goal->idFixture;
                if (!isset($fixtures[$fixtureId])) {
                    continue;
                }

                $fixture = $fixtures[$fixtureId];
                $goal->NFecha = $fixture->NFecha;
                $goal->Fecha = $fixture->Fecha;
                $goal->local = $fixture->local;
                $goal->visitante = $fixture->visitante;
                $goal->torneo = $fixture->torneo;
                $rows[] = $goal;
            }

            usort($rows, function ($a, $b) {
                return strcmp((string)$a->Fecha, (string)$b->Fecha);
            });

            $r->result = $rows;
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
            $cantidad = isset($data['Cantidad']) ? (int)$data['Cantidad'] : 1;

            if (!empty($data['idGol'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET idJugador=?, idFixture=?, Cantidad=? WHERE idGol=?"
                );
                $stm->execute([(int)$data['idJugador'], (int)$data['idFixture'], $cantidad, (int)$data['idGol']]);
                $r->result = ['idGol' => (int)$data['idGol']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table} (idJugador, idFixture, Cantidad) VALUES (?,?,?)"
                );
                $stm->execute([(int)$data['idJugador'], (int)$data['idFixture'], $cantidad]);
                $r->result = ['idGol' => $this->db->lastInsertId()];
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
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idGol = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
