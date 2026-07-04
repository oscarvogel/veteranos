<?php
use App\Lib\ModernApiResponse;
use App\Lib\Database;
use App\Model\EquiposModel;
use App\Model\FixtureModel;
use App\Model\GolesModel;
use App\Model\JugadorModel;
use App\Model\PosicionesModel;
use App\Model\TarjetasModel;
use App\Model\TorneoModel;

$app->options('/api/{routes:.+}', function ($req, $res, $args) {
    return ModernApiResponse::json($res, ['success' => true]);
});

$app->group('/api', function () {
    $this->get('/health', function ($req, $res, $args) {
        return ModernApiResponse::json($res, [
            'success' => true,
            'data' => [
                'status' => 'ok',
                'service' => 'veteranos-api',
                'time' => date('c'),
                'php' => PHP_VERSION,
            ],
        ]);
    });

    $this->get('/health/db', function ($req, $res, $args) {
        try {
            $db = Database::StartUp();
            $db->query('SELECT 1');

            return ModernApiResponse::json($res, [
                'success' => true,
                'data' => [
                    'status' => 'ok',
                    'database' => 'reachable',
                ],
            ]);
        } catch (Exception $e) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => ModernApiResponse::publicErrorMessage($e->getMessage()),
                ],
            ], 500);
        }
    });

    $this->get('/torneos', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $model = new TorneoModel();
        $legacy = $model->GetAll();

        if (is_object($legacy) && isset($legacy->response) && $legacy->response === true && isset($params['status'])) {
            $status = strtoupper(trim($params['status']));
            $legacy->result = array_values(array_filter(ModernApiResponse::normalizeRows($legacy->result), function ($row) use ($status) {
                return isset($row['Estado']) && strtoupper($row['Estado']) === $status;
            }));
        }

        $payload = ModernApiResponse::fromLegacy($legacy, function ($row) {
            return [
                'id' => isset($row['idTorneo']) ? (int)$row['idTorneo'] : null,
                'idTorneo' => isset($row['idTorneo']) ? (int)$row['idTorneo'] : null,
                'nombre' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'Nombre' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'estado' => isset($row['Estado']) ? $row['Estado'] : null,
                'Estado' => isset($row['Estado']) ? $row['Estado'] : null,
                'inicio' => isset($row['Inicio']) ? $row['Inicio'] : null,
            ];
        });

        return ModernApiResponse::json($res, $payload);
    });

    $this->get('/fechas', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new FixtureModel();
        $legacy = $model->GetAll($torneoId);
        $seen = [];
        $fechas = [];

        if (is_object($legacy) && isset($legacy->response) && $legacy->response === true) {
            foreach (ModernApiResponse::normalizeRows($legacy->result) as $row) {
                $nfecha = isset($row['NFecha']) ? (int)$row['NFecha'] : 0;
                if ($nfecha > 0 && !isset($seen[$nfecha])) {
                    $seen[$nfecha] = true;
                    $fechas[] = [
                        'nfecha' => $nfecha,
                        'NFecha' => $nfecha,
                        'fecha' => isset($row['Fecha']) ? $row['Fecha'] : null,
                        'Fecha' => isset($row['Fecha']) ? $row['Fecha'] : null,
                    ];
                }
            }
        }

        usort($fechas, function ($a, $b) {
            return $a['nfecha'] - $b['nfecha'];
        });

        return ModernApiResponse::json($res, ['success' => true, 'data' => $fechas]);
    });

    $this->get('/fixture', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);
        $nfecha = isset($params['nfecha']) ? (int)$params['nfecha'] : 0;

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new FixtureModel();
        $legacy = $nfecha > 0 ? $model->GetFecha($torneoId, $nfecha) : $model->GetAll($torneoId);

        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            return [
                'id' => isset($row['idFixture']) ? (int)$row['idFixture'] : null,
                'idFixture' => isset($row['idFixture']) ? (int)$row['idFixture'] : null,
                'nfecha' => isset($row['NFecha']) ? (int)$row['NFecha'] : null,
                'fecha' => isset($row['Fecha']) ? $row['Fecha'] : null,
                'local' => isset($row['local_nombre']) ? $row['local_nombre'] : (isset($row['Local']) ? $row['Local'] : ''),
                'visitante' => isset($row['visitante_nombre']) ? $row['visitante_nombre'] : (isset($row['Visitante']) ? $row['Visitante'] : ''),
                'goles_local' => isset($row['GolLocal']) ? $row['GolLocal'] : null,
                'goles_visitante' => isset($row['GolVisitante']) ? $row['GolVisitante'] : null,
                'cancha' => isset($row['cancha_nombre']) ? $row['cancha_nombre'] : null,
                'hora' => isset($row['Hora']) ? $row['Hora'] : null,
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/resultados', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new FixtureModel();
        $legacy = $model->GetResultados($torneoId);

        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            return [
                'id' => isset($row['idFixture']) ? (int)$row['idFixture'] : null,
                'idFixture' => isset($row['idFixture']) ? (int)$row['idFixture'] : null,
                'nfecha' => isset($row['NFecha']) ? (int)$row['NFecha'] : null,
                'fecha' => isset($row['Fecha']) ? $row['Fecha'] : null,
                'local' => isset($row['local_nombre']) ? $row['local_nombre'] : '',
                'visitante' => isset($row['visitante_nombre']) ? $row['visitante_nombre'] : '',
                'goles_local' => isset($row['GolLocal']) ? (int)$row['GolLocal'] : 0,
                'goles_visitante' => isset($row['GolVisitante']) ? (int)$row['GolVisitante'] : 0,
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/posiciones', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new PosicionesModel();
        $legacy = $model->GetTabla($torneoId);
        $payload = ModernApiResponse::fromLegacy($legacy, function ($row) {
            return [
                'idEquipo' => isset($row['idEquipo']) ? (int)$row['idEquipo'] : null,
                'equipo' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'PJ' => isset($row['PJ']) ? (int)$row['PJ'] : 0,
                'PG' => isset($row['PG']) ? (int)$row['PG'] : 0,
                'PE' => isset($row['PE']) ? (int)$row['PE'] : 0,
                'PP' => isset($row['PP']) ? (int)$row['PP'] : 0,
                'GF' => isset($row['GF']) ? (int)$row['GF'] : 0,
                'GC' => isset($row['GC']) ? (int)$row['GC'] : 0,
                'DIF' => isset($row['DIF']) ? (int)$row['DIF'] : 0,
                'Pts' => isset($row['Pts']) ? (int)$row['Pts'] : 0,
            ];
        });

        return ModernApiResponse::json($res, $payload);
    });

    $this->get('/goleadores', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new GolesModel();
        $legacy = $model->GetGoleadores($torneoId);

        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            $id = isset($row['idJugador']) ? (int)$row['idJugador'] : null;
            return [
                'idJugador' => $id,
                'player_key' => $id,
                'jugador' => isset($row['jugador']) ? $row['jugador'] : '',
                'equipo' => isset($row['equipo']) ? $row['equipo'] : '',
                'goles' => isset($row['total_goles']) ? (int)$row['total_goles'] : 0,
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/goleadores/detalle', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $playerId = isset($params['player_key']) ? (int)$params['player_key'] : (isset($params['idJugador']) ? (int)$params['idJugador'] : 0);
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);
        $nfecha = isset($params['nfecha']) ? (int)$params['nfecha'] : 0;

        if ($playerId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro player_key requerido'],
            ], 400);
        }

        $model = new GolesModel();
        $legacy = $model->GetByJugador($playerId, $torneoId, $nfecha);
        $payload = ModernApiResponse::fromLegacy($legacy, function ($row) {
            return [
                'idFixture' => isset($row['idFixture']) ? (int)$row['idFixture'] : null,
                'fecha' => isset($row['Fecha']) ? $row['Fecha'] : null,
                'oponente' => trim((isset($row['local']) ? $row['local'] : '') . ' vs ' . (isset($row['visitante']) ? $row['visitante'] : '')),
                'cantidad' => isset($row['Cantidad']) ? (int)$row['Cantidad'] : 1,
                'torneo' => isset($row['torneo']) ? $row['torneo'] : null,
            ];
        });

        return ModernApiResponse::json($res, $payload);
    });

    $this->get('/tarjetas', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);
        $equipoId = isset($params['equipo_id']) ? (int)$params['equipo_id'] : (isset($params['equipo']) ? (int)$params['equipo'] : 0);
        $tipo = isset($params['tipo']) ? strtolower(trim($params['tipo'])) : 'fairplay';

        if ($tipo === 'vigentes') {
            $model = new TarjetasModel();
            $legacy = $model->GetVigentes();
            $payload = ModernApiResponse::fromLegacy($legacy, function ($row) {
                return [
                    'idTarjeta' => isset($row['idTarjeta']) ? (int)$row['idTarjeta'] : null,
                    'jugador' => isset($row['jugador']) ? $row['jugador'] : '',
                    'equipo' => isset($row['equipo']) ? $row['equipo'] : '',
                    'amarillas' => isset($row['Amarilla']) ? (int)$row['Amarilla'] : 0,
                    'rojas' => isset($row['Roja']) ? (int)$row['Roja'] : 0,
                    'desde_fecha' => isset($row['desde_fecha']) ? $row['desde_fecha'] : null,
                    'hasta_fecha' => isset($row['hasta_fecha']) ? $row['hasta_fecha'] : null,
                    'motivo' => isset($row['Motivo']) ? $row['Motivo'] : '',
                ];
            });

            return ModernApiResponse::json($res, $payload);
        }

        if ($tipo === 'equipo') {
            if ($torneoId <= 0 || $equipoId <= 0) {
                return ModernApiResponse::json($res, [
                    'success' => false,
                    'error' => ['code' => 400, 'message' => 'Parametros torneo_id y equipo_id requeridos'],
                ], 400);
            }

            $model = new TarjetasModel();
            $legacy = $model->GetByEquipoTorneo($equipoId, $torneoId);
            if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
                return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
            }

            $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
                return [
                    'idTarjeta' => isset($row['idTarjeta']) ? (int)$row['idTarjeta'] : null,
                    'jugador' => isset($row['jugador']) ? $row['jugador'] : '',
                    'nfecha' => isset($row['NFecha']) ? (int)$row['NFecha'] : null,
                    'fecha' => isset($row['fecha_partido']) ? $row['fecha_partido'] : null,
                    'amarillas' => isset($row['Amarilla']) ? (int)$row['Amarilla'] : 0,
                    'rojas' => isset($row['Roja']) ? (int)$row['Roja'] : 0,
                    'motivo' => isset($row['Motivo']) ? $row['Motivo'] : '',
                ];
            });
            $page = ModernApiResponse::paginate($rows, $params);

            return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
        }

        if ($torneoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro torneo_id requerido'],
            ], 400);
        }

        $model = new TarjetasModel();
        $legacy = $model->GetFairPlay($torneoId);
        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            return [
                'idEquipo' => isset($row['idEquipo']) ? (int)$row['idEquipo'] : null,
                'equipo' => isset($row['equipo']) ? $row['equipo'] : '',
                'amarillas' => isset($row['amarillas']) ? (int)$row['amarillas'] : 0,
                'rojas' => isset($row['rojas']) ? (int)$row['rojas'] : 0,
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/equipos', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $torneoId = isset($params['torneo_id']) ? (int)$params['torneo_id'] : (isset($params['torneo']) ? (int)$params['torneo'] : 0);
        $model = new EquiposModel();
        $legacy = $torneoId > 0 ? $model->GetPorTorneo($torneoId) : $model->GetAll();

        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            return [
                'idEquipo' => isset($row['idEquipo']) ? (int)$row['idEquipo'] : null,
                'id' => isset($row['idEquipo']) ? (int)$row['idEquipo'] : null,
                'nombre' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'Nombre' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'categoria' => isset($row['NombreCategoria']) ? $row['NombreCategoria'] : null,
                'lista' => isset($row['lista']) ? $row['lista'] : null,
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/lista-buena-fe', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $equipoId = isset($params['equipo_id']) ? (int)$params['equipo_id'] : (isset($params['equipo']) ? (int)$params['equipo'] : 0);

        if ($equipoId <= 0) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => ['code' => 400, 'message' => 'Parametro equipo_id requerido'],
            ], 400);
        }

        $model = new JugadorModel();
        $legacy = $model->GetByEquipo($equipoId);
        if (!(is_object($legacy) && isset($legacy->response) && $legacy->response === true)) {
            return ModernApiResponse::json($res, ModernApiResponse::fromLegacy($legacy), 500);
        }

        $rows = ModernApiResponse::normalizeRows($legacy->result, function ($row) {
            return [
                'idJugador' => isset($row['idJugador']) ? (int)$row['idJugador'] : null,
                'nombre' => isset($row['Nombre']) ? $row['Nombre'] : '',
                'clase' => isset($row['Clase']) ? $row['Clase'] : '',
                'dni' => isset($row['DNI']) ? $row['DNI'] : '',
                'observacion' => isset($row['Observacion']) ? $row['Observacion'] : '',
                'certificado' => !empty($row['certificado']),
                'firma_lista' => !empty($row['firma_lista']),
                'fotocopia_dni' => !empty($row['fotocopia_dni']),
                'dec_jurada' => !empty($row['dec_jurada']),
            ];
        });
        $page = ModernApiResponse::paginate($rows, $params);

        return ModernApiResponse::json($res, ['success' => true, 'data' => $page['data'], 'meta' => $page['meta']]);
    });

    $this->get('/jugador/importar-lista-buena-fe', function ($req, $res, $args) {
        return $res
            ->withStatus(302)
            ->withHeader('Location', '/index.php?r=jugador/importarListaBuenaFe');
    });

    $this->post('/jugador/importar-lista-buena-fe', function ($req, $res, $args) {
        $data = (array)$req->getParsedBody();
        $idEquipo = (int)($data['idEquipo'] ?? $data['equipo_id'] ?? 0);
        $files = $req->getUploadedFiles();
        $file = $files['archivo'] ?? ($files['file'] ?? null);

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => [
                    'code' => 400,
                    'message' => 'Debe subir un archivo CSV o XLSX.',
                ],
            ], 400);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'lista_buena_fe_');
        $file->moveTo($tmp);

        $model = new JugadorModel();
        $legacy = $model->ImportarListaBuenaFeArchivo($tmp, $idEquipo, $file->getClientFilename());
        @unlink($tmp);

        if (!is_object($legacy) || $legacy->response !== true) {
            $message = is_object($legacy) && !empty($legacy->message) ? $legacy->message : 'No se pudo importar el archivo.';
            return ModernApiResponse::json($res, [
                'success' => false,
                'error' => [
                    'code' => 400,
                    'message' => ModernApiResponse::publicErrorMessage($message),
                ],
            ], 400);
        }

        return ModernApiResponse::json($res, [
            'success' => true,
            'data' => $legacy->result,
        ]);
    });
});
