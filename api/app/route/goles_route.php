<?php
use App\Model\GolesModel;

$app->group('/goles', function () {

    $this->get('/', function ($req, $res, $args) {
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->GetAll()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/goleadores', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetro torneo requerido');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->GetGoleadores((int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/jugador/{id}', function ($req, $res, $args) {
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->GetByJugador((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new GolesModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
