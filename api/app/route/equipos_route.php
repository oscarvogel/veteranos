<?php
use App\Model\EquiposModel;

$app->group('/equipos', function () {

    $this->get('/', function ($req, $res, $args) {
        $params   = $req->getQueryParams();
        $model    = new EquiposModel();
        if (!empty($params['torneo'])) {
            $result = $model->GetPorTorneo((int)$params['torneo']);
        } else {
            $result = $model->GetAll();
        }
        $res->getBody()->write(json_encode($result));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/autocomplete', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $q      = trim($params['q'] ?? '');
        $model  = new EquiposModel();
        $res->getBody()->write(json_encode($model->Autocomplete($q)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/buenafe', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetro torneo requerido');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new EquiposModel();
        $res->getBody()->write(json_encode($model->GetListaBuenaFe((int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new EquiposModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new EquiposModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new EquiposModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
