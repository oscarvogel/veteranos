<?php
namespace App\Model;

use App\Lib\Database;
use PDO;
use Exception;

class PlanillaImportModel
{
    private $db;
    private $secret;

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?: Database::StartUp();
        $this->secret = getenv('API_PLANILLAS_SECRET') ?: getenv('API_PLANILLAS_KEY');
    }

    public function Preview(array $payload)
    {
        $errors = [];
        $warnings = [];
        $resolved = [
            'version' => isset($payload['version']) ? (int)$payload['version'] : 0,
            'source_id' => isset($payload['source_id']) ? trim((string)$payload['source_id']) : '',
            'torneo' => null,
            'partidos' => [],
        ];

        if ($resolved['version'] !== 1) {
            $errors[] = $this->problem('payload.version', 'Versión de contrato no soportada; se espera version=1.');
        }
        if ($resolved['source_id'] === '') {
            $errors[] = $this->problem('payload.source_id', 'source_id es obligatorio para identificar el lote.');
        }
        if (empty($payload['torneo']) || !is_array($payload['torneo'])) {
            $errors[] = $this->problem('payload.torneo', 'torneo es obligatorio.');
        } else {
            $torneo = $this->resolveTorneo($payload['torneo']);
            if (!$torneo) {
                $errors[] = $this->problem('payload.torneo', 'No se pudo resolver el torneo.');
            } else {
                $resolved['torneo'] = $torneo;
            }
        }

        if (empty($payload['partidos']) || !is_array($payload['partidos'])) {
            $errors[] = $this->problem('payload.partidos', 'partidos debe contener al menos un elemento.');
        }

        if (count($errors) === 0) {
            $fixtureIndex = [];
            foreach ($payload['partidos'] as $index => $partido) {
                if (!is_array($partido)) {
                    $errors[] = $this->problem("partidos.$index", 'El partido debe ser un objeto.');
                    continue;
                }
                $path = "partidos.$index";
                $resolvedMatch = $this->resolvePartido($resolved['torneo'], $partido, $path, $errors, $warnings);
                if ($resolvedMatch) {
                    $fixtureId = (int)$resolvedMatch['idFixture'];
                    if (isset($fixtureIndex[$fixtureId])) {
                        $pos = $fixtureIndex[$fixtureId];
                        $resolved['partidos'][$pos] = $this->mergeResolvedMatch(
                            $resolved['partidos'][$pos],
                            $resolvedMatch,
                            $path,
                            $errors
                        );
                    } else {
                        $fixtureIndex[$fixtureId] = count($resolved['partidos']);
                        $resolved['partidos'][] = $resolvedMatch;
                    }
                }
            }
        }

        $canConfirm = count($errors) === 0 && !$this->hasUnreviewedWarnings($warnings);
        $token = null;
        if ($canConfirm && $this->secret) {
            $token = $this->sign($resolved);
        }

        return [
            'success' => count($errors) === 0,
            'can_confirm' => $canConfirm,
            'errors' => $errors,
            'warnings' => $warnings,
            'resolved' => $resolved,
            'confirm_token' => $token,
        ];
    }

    public function Confirm(array $payload, $confirmToken)
    {
        $preview = $this->Preview($payload);
        if (!$preview['success']) {
            throw new Exception('El lote contiene errores y no puede confirmarse.');
        }
        if (!$preview['can_confirm']) {
            throw new Exception('El lote tiene advertencias pendientes de revisión.');
        }
        if (!$this->secret) {
            throw new Exception('API_PLANILLAS_SECRET/API_PLANILLAS_KEY no está configurado.');
        }

        $expected = $this->sign($preview['resolved']);
        if (!$this->safeEquals($expected, (string)$confirmToken)) {
            throw new Exception('confirm_token inválido o vencido; ejecutar preview nuevamente.');
        }

        $this->db->beginTransaction();
        try {
            $summary = [
                'source_id' => $preview['resolved']['source_id'],
                'partidos_actualizados' => 0,
                'goles_actualizados' => 0,
                'tarjetas_actualizadas' => 0,
            ];

            foreach ($preview['resolved']['partidos'] as $partido) {
                $this->applyResultado($partido);
                $summary['partidos_actualizados']++;

                foreach ($partido['jugadores'] as $jugador) {
                    $this->replaceGoles($partido['idFixture'], $jugador['idJugador'], $jugador['goles']);
                    $summary['goles_actualizados'] += (int)$jugador['goles'];

                    $cards = $this->replaceTarjetas(
                        $partido['idFixture'],
                        $jugador['idJugador'],
                        $jugador['idEquipo'],
                        $jugador['amarillas'],
                        $jugador['rojas']
                    );
                    $summary['tarjetas_actualizadas'] += $cards;
                }
            }

            $this->db->commit();
            return ['success' => true, 'summary' => $summary];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    private function resolveTorneo(array $input)
    {
        if (!empty($input['id'])) {
            $stm = $this->db->prepare('SELECT idTorneo, Nombre FROM torneo WHERE idTorneo = ?');
            $stm->execute([(int)$input['id']]);
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return $row ? ['idTorneo' => (int)$row['idTorneo'], 'nombre' => $row['Nombre']] : null;
        }

        $nombre = isset($input['nombre']) ? trim((string)$input['nombre']) : '';
        if ($nombre === '') {
            return null;
        }

        $stm = $this->db->prepare('SELECT idTorneo, Nombre FROM torneo WHERE LOWER(TRIM(Nombre)) = LOWER(TRIM(?))');
        $stm->execute([$nombre]);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 1) {
            return null;
        }
        return ['idTorneo' => (int)$rows[0]['idTorneo'], 'nombre' => $rows[0]['Nombre']];
    }

    private function resolvePartido(array $torneo, array $input, $path, array &$errors, array &$warnings)
    {
        $fixture = null;
        if (!empty($input['idFixture'])) {
            $stm = $this->db->prepare(
                'SELECT f.*, el.Nombre local_nombre, ev.Nombre visitante_nombre
                 FROM fixture f
                 LEFT JOIN equipos el ON el.idEquipo=f.Local
                 LEFT JOIN equipos ev ON ev.idEquipo=f.Visitante
                 WHERE f.idFixture=? AND f.idTorneo=?'
            );
            $stm->execute([(int)$input['idFixture'], $torneo['idTorneo']]);
            $fixture = $stm->fetch(PDO::FETCH_ASSOC);
        } else {
            $equipo = $this->resolveEquipo(isset($input['equipo']) && is_array($input['equipo']) ? $input['equipo'] : []);
            $rival = $this->resolveEquipo(isset($input['rival']) && is_array($input['rival']) ? $input['rival'] : []);
            if (!$equipo) {
                $errors[] = $this->problem("$path.equipo", 'No se pudo resolver el equipo.');
            }
            if (!$rival) {
                $errors[] = $this->problem("$path.rival", 'No se pudo resolver el rival.');
            }
            if (!$equipo || !$rival) {
                return null;
            }

            $sql = 'SELECT f.*, el.Nombre local_nombre, ev.Nombre visitante_nombre
                    FROM fixture f
                    LEFT JOIN equipos el ON el.idEquipo=f.Local
                    LEFT JOIN equipos ev ON ev.idEquipo=f.Visitante
                    WHERE f.idTorneo=? AND ((f.Local=? AND f.Visitante=?) OR (f.Local=? AND f.Visitante=?))';
            $params = [$torneo['idTorneo'], $equipo['idEquipo'], $rival['idEquipo'], $rival['idEquipo'], $equipo['idEquipo']];
            if (!empty($input['nfecha'])) {
                $sql .= ' AND f.NFecha=?';
                $params[] = (int)$input['nfecha'];
            }
            $stm = $this->db->prepare($sql);
            $stm->execute($params);
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) !== 1) {
                $errors[] = $this->problem($path, count($rows) === 0 ? 'No se encontró el fixture.' : 'El fixture es ambiguo; indicar idFixture o nfecha.');
                return null;
            }
            $fixture = $rows[0];
        }

        if (!$fixture) {
            $errors[] = $this->problem($path, 'No se encontró el fixture.');
            return null;
        }

        $equipoInput = isset($input['equipo']) && is_array($input['equipo']) ? $this->resolveEquipo($input['equipo']) : null;
        if (!$equipoInput) {
            $errors[] = $this->problem("$path.equipo", 'equipo es obligatorio aun cuando se indique idFixture.');
            return null;
        }
        if ($equipoInput['idEquipo'] !== (int)$fixture['Local'] && $equipoInput['idEquipo'] !== (int)$fixture['Visitante']) {
            $errors[] = $this->problem("$path.equipo", 'El equipo informado no participa del fixture resuelto.');
            return null;
        }

        $resultado = isset($input['resultado']) && is_array($input['resultado']) ? $input['resultado'] : [];
        if (!isset($resultado['goles_equipo']) || !isset($resultado['goles_rival'])) {
            $errors[] = $this->problem("$path.resultado", 'Se requieren goles_equipo y goles_rival.');
            return null;
        }
        $golesEquipo = (int)$resultado['goles_equipo'];
        $golesRival = (int)$resultado['goles_rival'];
        if ($golesEquipo < 0 || $golesRival < 0) {
            $errors[] = $this->problem("$path.resultado", 'Los goles no pueden ser negativos.');
            return null;
        }

        $isLocal = $equipoInput['idEquipo'] === (int)$fixture['Local'];
        $jugadores = [];
        $sumGoles = 0;
        $inputJugadores = isset($input['jugadores']) && is_array($input['jugadores']) ? $input['jugadores'] : [];
        foreach ($inputJugadores as $jIndex => $jugador) {
            if (!is_array($jugador)) {
                $errors[] = $this->problem("$path.jugadores.$jIndex", 'El jugador debe ser un objeto.');
                continue;
            }
            $resolvedPlayer = $this->resolveJugador($jugador, $equipoInput['idEquipo']);
            if (!$resolvedPlayer) {
                $errors[] = $this->problem("$path.jugadores.$jIndex", 'No se pudo resolver el jugador por DNI/equipo.');
                continue;
            }

            $confidence = isset($jugador['confianza']) ? (float)$jugador['confianza'] : 1.0;
            $reviewed = !empty($jugador['revisado']);
            if ($confidence < 0.85) {
                $warnings[] = $this->problem("$path.jugadores.$jIndex", 'Confianza menor a 0.85; requiere revisión manual.', $reviewed);
            }

            $goles = isset($jugador['goles']) ? max(0, (int)$jugador['goles']) : 0;
            $amarillas = isset($jugador['amarillas']) ? max(0, (int)$jugador['amarillas']) : 0;
            $rojas = isset($jugador['rojas']) ? max(0, (int)$jugador['rojas']) : 0;
            $sumGoles += $goles;

            $jugadores[] = [
                'idJugador' => $resolvedPlayer['idJugador'],
                'idEquipo' => $equipoInput['idEquipo'],
                'dni' => $resolvedPlayer['DNI'],
                'nombre' => $resolvedPlayer['Nombre'],
                'goles' => $goles,
                'amarillas' => $amarillas,
                'rojas' => $rojas,
                'confianza' => $confidence,
                'revisado' => $reviewed,
            ];
        }

        if ($sumGoles !== $golesEquipo) {
            $reviewedMatch = !empty($input['revisado']);
            $warnings[] = $this->problem(
                "$path.jugadores",
                "La suma de goles individuales ($sumGoles) no coincide con goles_equipo ($golesEquipo).",
                $reviewedMatch
            );
        }

        return [
            'idFixture' => (int)$fixture['idFixture'],
            'nfecha' => (int)$fixture['NFecha'],
            'fecha' => $fixture['Fecha'],
            'local' => ['idEquipo' => (int)$fixture['Local'], 'nombre' => $fixture['local_nombre']],
            'visitante' => ['idEquipo' => (int)$fixture['Visitante'], 'nombre' => $fixture['visitante_nombre']],
            'equipo_planilla' => $equipoInput,
            'GolLocal' => $isLocal ? $golesEquipo : $golesRival,
            'GolVisitante' => $isLocal ? $golesRival : $golesEquipo,
            'jugadores' => $jugadores,
        ];
    }

    private function mergeResolvedMatch(array $existing, array $incoming, $path, array &$errors)
    {
        if (
            (int)$existing['GolLocal'] !== (int)$incoming['GolLocal'] ||
            (int)$existing['GolVisitante'] !== (int)$incoming['GolVisitante']
        ) {
            $errors[] = $this->problem(
                $path . '.resultado',
                'El mismo fixture aparece con resultados contradictorios entre planillas.'
            );
            return $existing;
        }

        $players = [];
        foreach ($existing['jugadores'] as $player) {
            $players[(int)$player['idJugador']] = $player;
        }

        foreach ($incoming['jugadores'] as $player) {
            $playerId = (int)$player['idJugador'];
            if (!isset($players[$playerId])) {
                $players[$playerId] = $player;
                continue;
            }

            $current = $players[$playerId];
            if (
                (int)$current['goles'] !== (int)$player['goles'] ||
                (int)$current['amarillas'] !== (int)$player['amarillas'] ||
                (int)$current['rojas'] !== (int)$player['rojas']
            ) {
                $errors[] = $this->problem(
                    $path . '.jugadores',
                    'El mismo jugador aparece con estadísticas contradictorias para el mismo fixture.'
                );
            }
        }

        $existing['jugadores'] = array_values($players);
        return $existing;
    }

    private function resolveEquipo(array $input)
    {
        if (!empty($input['id'])) {
            $stm = $this->db->prepare('SELECT idEquipo, Nombre FROM equipos WHERE idEquipo=?');
            $stm->execute([(int)$input['id']]);
        } else {
            $nombre = isset($input['nombre']) ? trim((string)$input['nombre']) : '';
            if ($nombre === '') {
                return null;
            }
            $stm = $this->db->prepare('SELECT idEquipo, Nombre FROM equipos WHERE LOWER(TRIM(Nombre))=LOWER(TRIM(?))');
            $stm->execute([$nombre]);
        }
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 1) {
            return null;
        }
        return ['idEquipo' => (int)$rows[0]['idEquipo'], 'nombre' => $rows[0]['Nombre']];
    }

    private function resolveJugador(array $input, $idEquipo)
    {
        $dni = isset($input['documento']) ? preg_replace('/\D+/', '', (string)$input['documento']) : '';
        if ($dni === '') {
            return null;
        }

        $stm = $this->db->prepare('SELECT idJugador, Nombre, DNI, idEquipo FROM jugador WHERE DNI=?');
        $stm->execute([$dni]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int)$row['idEquipo'] !== (int)$idEquipo) {
            return null;
        }
        return [
            'idJugador' => (int)$row['idJugador'],
            'Nombre' => $row['Nombre'],
            'DNI' => $row['DNI'],
            'idEquipo' => (int)$row['idEquipo'],
        ];
    }

    private function applyResultado(array $partido)
    {
        $gl = (int)$partido['GolLocal'];
        $gv = (int)$partido['GolVisitante'];
        if ($gl > $gv) {
            $pl = 3; $pv = 0;
        } elseif ($gv > $gl) {
            $pl = 0; $pv = 3;
        } else {
            $pl = 1; $pv = 1;
        }

        $stm = $this->db->prepare(
            'UPDATE fixture SET GolLocal=?, GolVisitante=?, PuntosLocal=?, PuntosVisitante=? WHERE idFixture=?'
        );
        $stm->execute([$gl, $gv, $pl, $pv, (int)$partido['idFixture']]);
    }

    private function replaceGoles($idFixture, $idJugador, $cantidad)
    {
        $del = $this->db->prepare('DELETE FROM goles WHERE idFixture=? AND idJugador=?');
        $del->execute([(int)$idFixture, (int)$idJugador]);
        if ((int)$cantidad > 0) {
            $ins = $this->db->prepare('INSERT INTO goles (idJugador,idFixture,Cantidad) VALUES (?,?,?)');
            $ins->execute([(int)$idJugador, (int)$idFixture, (int)$cantidad]);
        }
    }

    private function replaceTarjetas($idFixture, $idJugador, $idEquipo, $amarillas, $rojas)
    {
        $protected = $this->db->prepare(
            "SELECT COUNT(*) FROM tarjetas
             WHERE idFixture=? AND idJugador=? AND Roja=1
             AND (COALESCE(Motivo,'')<>'' OR (HastaFecha IS NOT NULL AND HastaFecha<>0))"
        );
        $protected->execute([(int)$idFixture, (int)$idJugador]);
        $protectedReds = (int)$protected->fetchColumn();

        $del = $this->db->prepare(
            "DELETE FROM tarjetas
             WHERE idFixture=? AND idJugador=?
             AND COALESCE(Motivo,'')=''
             AND (HastaFecha IS NULL OR HastaFecha=0)"
        );
        $del->execute([(int)$idFixture, (int)$idJugador]);

        $ins = $this->db->prepare(
            'INSERT INTO tarjetas (idFixture,idJugador,Amarilla,Roja,DesdeFecha,HastaFecha,Motivo,idEquipo)
             VALUES (?,?,?,?,NULL,NULL,?,?)'
        );
        $count = 0;
        for ($i = 0; $i < (int)$amarillas; $i++) {
            $ins->execute([(int)$idFixture, (int)$idJugador, 1, 0, '', (int)$idEquipo]);
            $count++;
        }
        $redsToInsert = max(0, (int)$rojas - $protectedReds);
        for ($i = 0; $i < $redsToInsert; $i++) {
            $ins->execute([(int)$idFixture, (int)$idJugador, 0, 1, '', (int)$idEquipo]);
            $count++;
        }
        return $count;
    }

    private function problem($path, $message, $reviewed = false)
    {
        return ['path' => $path, 'message' => $message, 'reviewed' => (bool)$reviewed];
    }

    private function hasUnreviewedWarnings(array $warnings)
    {
        foreach ($warnings as $warning) {
            if (empty($warning['reviewed'])) {
                return true;
            }
        }
        return false;
    }

    private function sign(array $resolved)
    {
        return hash_hmac('sha256', json_encode($resolved), (string)$this->secret);
    }

    private function safeEquals($expected, $actual)
    {
        if (function_exists('hash_equals')) {
            return hash_equals((string)$expected, (string)$actual);
        }
        if (strlen($expected) !== strlen($actual)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($expected); $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        return $result === 0;
    }
}
