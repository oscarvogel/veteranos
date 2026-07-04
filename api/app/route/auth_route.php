<?php
use App\Model\AuthModel;

$app->group('/auth', function () {
    $this->post('/login', function ($req, $res, $args) {
        $data     = (array)$req->getParsedBody();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            $r = new \App\Lib\Response();
            $r->SetResponse(false, 'Usuario y contraseña son requeridos');
            $res->getBody()->write(json_encode($r));
            return $res->withHeader('Content-Type', 'application/json');
        }

        $model = new AuthModel();
        $result = $model->ValidarCredenciales($username, $password);
        $res->getBody()->write(json_encode($result));
        return $res->withHeader('Content-Type', 'application/json');
    });
});
