<?php
use App\Model\PosicionesModel;

$app->group('/posiciones', function () {

    $this->get('/', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetro torneo requerido');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new PosicionesModel();
        $res->getBody()->write(json_encode($model->GetTabla((int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/torneos', function ($req, $res, $args) {
        $model = new PosicionesModel();
        $res->getBody()->write(json_encode($model->GetTorneos()));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
