<?php
use App\Model\JugadorModel;

$app->group('/jugador', function () {

    $this->get('/', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $model  = new JugadorModel();
        if (!empty($params['equipo'])) {
            $result = $model->GetByEquipo((int)$params['equipo']);
        } else {
            $result = $model->GetAll();
        }
        $res->getBody()->write(json_encode($result));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/autocomplete', function ($req, $res, $args) {
        $params = $req->getQueryParams();
        $q      = trim($params['q'] ?? '');
        $model  = new JugadorModel();
        $res->getBody()->write(json_encode($model->Autocomplete($q)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}', function ($req, $res, $args) {
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->Get((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->get('/{id}/historia', function ($req, $res, $args) {
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->GetHistoria((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/save', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->Save($data)));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/liberar/{id}', function ($req, $res, $args) {
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->Liberar((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/asignar', function ($req, $res, $args) {
        $data  = (array)$req->getParsedBody();
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->Asignar((int)$data['idJugador'], (int)$data['idEquipo'])));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/importar-lista-buena-fe', function ($req, $res, $args) {
        $data = (array)$req->getParsedBody();
        $idEquipo = (int)($data['idEquipo'] ?? 0);
        $files = $req->getUploadedFiles();
        $file = $files['archivo'] ?? ($files['file'] ?? null);

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Debe subir un archivo CSV o XLSX.');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'lista_buena_fe_');
        $file->moveTo($tmp);

        $model = new JugadorModel();
        $result = $model->ImportarListaBuenaFeArchivo($tmp, $idEquipo, $file->getClientFilename());
        @unlink($tmp);

        $res->getBody()->write(json_encode($result));
        return $res->withHeader('Content-Type', 'application/json');
    });

    $this->post('/delete/{id}', function ($req, $res, $args) {
        $model = new JugadorModel();
        $res->getBody()->write(json_encode($model->Delete((int)$args['id'])));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
