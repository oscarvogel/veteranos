<?php
use App\Model\EgresosModel;

$app->group('/egresos', function () {

    $this->get('/', function ($req, $res, $args) {
        $model = new EgresosModel();
        $res->getBody()->write(json_encode($model->GetAll()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new EgresosModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $body = $req->getParsedBody();
        $model = new EgresosModel();
        $res->getBody()->write(json_encode($model->Save($body)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new EgresosModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
