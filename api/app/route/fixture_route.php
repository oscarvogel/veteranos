<?php
use App\Model\FixtureModel;

$app->group('/fixture', function () {

    $this->get('/', function ($req, $res, $args) {
        $params   = $req->getQueryParams();
        $model    = new FixtureModel();
        $idTorneo = !empty($params['torneo']) ? (int)$params['torneo'] : null;
        $res->getBody()->write(json_encode($model->GetAll($idTorneo)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/resultados', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetro torneo requerido');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new FixtureModel();
        $res->getBody()->write(json_encode($model->GetResultados((int)$params['torneo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/fecha', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        if (empty($params['torneo']) || !isset($params['nfecha'])) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Parámetros torneo y nfecha requeridos');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $model = new FixtureModel();
        $res->getBody()->write(json_encode($model->GetFecha((int)$params['torneo'], (int)$params['nfecha'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new FixtureModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new FixtureModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new FixtureModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
