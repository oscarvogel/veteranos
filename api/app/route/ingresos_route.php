<?php
use App\Model\IngresosModel;

$app->group('/ingresos', function () {

    $this->get('/', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $model = new IngresosModel();
        if (!empty($params['equipo'])) {
            $data = $model->GetByEquipo((int)$params['equipo']);
        } else {
            $data = $model->GetAll();
        }
        $res->getBody()->write(json_encode($data));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new IngresosModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $body = $req->getParsedBody();
        $model = new IngresosModel();
        $res->getBody()->write(json_encode($model->Save($body)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new IngresosModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
