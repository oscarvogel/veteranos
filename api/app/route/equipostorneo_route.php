<?php
use App\Model\EquipostorneoModel;

$app->group('/equipostorneo', function () {

    $this->get('/', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $model  = new EquipostorneoModel();
        if (!empty($params['torneo'])) {
            $result = $model->GetByTorneo((int)$params['torneo']);
        } else {
            $result = $model->GetAll();
        }
        $res->getBody()->write(json_encode($result));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new EquipostorneoModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new EquipostorneoModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new EquipostorneoModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
