<?php
use App\Model\TorneoModel;

$app->group('/torneo', function () {

    $this->get('/', function ($req, $res, $args) {
        $model = new TorneoModel();
        $res->getBody()->write(json_encode($model->GetAll()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/activos', function ($req, $res, $args) {
        $model = new TorneoModel();
        $res->getBody()->write(json_encode($model->GetActivos()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new TorneoModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new TorneoModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new TorneoModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
