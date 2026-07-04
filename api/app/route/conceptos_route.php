<?php
use App\Model\ConceptosModel;

$app->group('/conceptos', function () {

    $this->get('/', function ($req, $res, $args) {
        $model = new ConceptosModel();
        $res->getBody()->write(json_encode($model->GetAll()));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new ConceptosModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}/movimientos', function ($req, $res, $args) {
        $model = new ConceptosModel();
        $res->getBody()->write(json_encode($model->GetMovimientos((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new ConceptosModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new ConceptosModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
