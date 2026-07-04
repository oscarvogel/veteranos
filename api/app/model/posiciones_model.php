<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class PosicionesModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    /**
     * Calcula la tabla de posiciones en tiempo real a partir de resultados en fixture.
     */
    public function GetTabla($idTorneo)
    {
        $r = new Response();
        try {
            // Partidos jugados (con resultado cargado)
            $stm = $this->db->prepare(
                "SELECT f.Local, f.Visitante, f.GolLocal, f.GolVisitante,
                        f.PuntosLocal, f.PuntosVisitante, f.SumaPuntos, f.PostTemporada
                 FROM fixture f
                 WHERE f.idTorneo = ?
                   AND f.GolLocal IS NOT NULL AND f.GolLocal != ''
                   AND f.GolVisitante IS NOT NULL AND f.GolVisitante != ''
                   AND f.PostTemporada = 0"
            );
            $stm->execute([(int)$idTorneo]);
            $partidos = $stm->fetchAll(PDO::FETCH_OBJ);

            // Equipos del torneo
            $stmEq = $this->db->prepare(
                "SELECT e.idEquipo, e.Nombre
                 FROM equipostorneo et JOIN equipos e ON et.idEquipo = e.idEquipo
                 WHERE et.idTorneo = ?"
            );
            $stmEq->execute([(int)$idTorneo]);
            $equipos = $stmEq->fetchAll(PDO::FETCH_OBJ);

            $tabla = [];
            foreach ($equipos as $eq) {
                $tabla[$eq->idEquipo] = [
                    'idEquipo' => $eq->idEquipo,
                    'Nombre'   => $eq->Nombre,
                    'PJ' => 0, 'PG' => 0, 'PE' => 0, 'PP' => 0,
                    'GF' => 0, 'GC' => 0, 'DIF' => 0, 'Pts' => 0,
                ];
            }

            foreach ($partidos as $p) {
                $ptsL = is_numeric($p->PuntosLocal)    ? (int)$p->PuntosLocal    : ($p->GolLocal > $p->GolVisitante ? 3 : ($p->GolLocal == $p->GolVisitante ? 1 : 0));
                $ptsV = is_numeric($p->PuntosVisitante) ? (int)$p->PuntosVisitante : ($p->GolVisitante > $p->GolLocal ? 3 : ($p->GolLocal == $p->GolVisitante ? 1 : 0));

                if (isset($tabla[$p->Local])) {
                    $tabla[$p->Local]['PJ']++;
                    $tabla[$p->Local]['GF'] += (int)$p->GolLocal;
                    $tabla[$p->Local]['GC'] += (int)$p->GolVisitante;
                    $tabla[$p->Local]['Pts'] += $ptsL;
                    if ($ptsL == 3) $tabla[$p->Local]['PG']++;
                    elseif ($ptsL == 1) $tabla[$p->Local]['PE']++;
                    else $tabla[$p->Local]['PP']++;
                }

                if (isset($tabla[$p->Visitante])) {
                    $tabla[$p->Visitante]['PJ']++;
                    $tabla[$p->Visitante]['GF'] += (int)$p->GolVisitante;
                    $tabla[$p->Visitante]['GC'] += (int)$p->GolLocal;
                    $tabla[$p->Visitante]['Pts'] += $ptsV;
                    if ($ptsV == 3) $tabla[$p->Visitante]['PG']++;
                    elseif ($ptsV == 1) $tabla[$p->Visitante]['PE']++;
                    else $tabla[$p->Visitante]['PP']++;
                }
            }

            foreach ($tabla as &$t) {
                $t['DIF'] = $t['GF'] - $t['GC'];
            }

            usort($tabla, function ($a, $b) {
                if ($b['Pts'] !== $a['Pts']) return $b['Pts'] - $a['Pts'];
                if ($b['DIF'] !== $a['DIF']) return $b['DIF'] - $a['DIF'];
                return $b['GF'] - $a['GF'];
            });

            $r->result = array_values($tabla);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Lista todos los torneos con posiciones cargadas */
    public function GetTorneos()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT t.idTorneo, t.Nombre, t.Estado, t.Inicio
                 FROM torneo t
                 WHERE t.Estado IN ('A','I','F')
                 ORDER BY t.idTorneo DESC"
            );
            $stm->execute();
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }
}
