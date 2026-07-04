<?php
use App\Model\ArticulosModel;

$app->group('/articulos', function () {

    $this->get('/', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $todos  = isset($params['todos']) && $params['todos'] == '1';
        $model  = new ArticulosModel();
        $res->getBody()->write(json_encode($model->GetAll(!$todos)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new ArticulosModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new ArticulosModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new ArticulosModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
