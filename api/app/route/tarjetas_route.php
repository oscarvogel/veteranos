<?php
use App\Model\TarjetasModel;

$app->group('/tarjetas', function () {

    $this->get('/', function ($req, $res, $args) {
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->GetAll()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/vigentes', function ($req, $res, $args) {
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->GetVigentes()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/fairplay', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetro torneo requerido');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->GetFairPlay((int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/equipo', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['equipo']) || empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetros equipo y torneo requeridos');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->GetByEquipoTorneo((int)$params['equipo'], (int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new TarjetasModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
