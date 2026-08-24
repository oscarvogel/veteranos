<?php
use App\Lib\PlanillasApiGuard;
use App\Model\PlanillaImportModel;

$app->group('/api/planillas', function () {
    $this->post('/preview', function ($req, $res) {
        if (!PlanillasApiGuard::authorized($req)) {
            return $res->withJson(['success' => false, 'error' => 'No autorizado'], 401);
        }

        $payload = json_decode((string)$req->getBody(), true);
        if (!is_array($payload)) {
            $payload = (array)$req->getParsedBody();
        }

        try {
            $model = new PlanillaImportModel();
            $result = $model->Preview($payload ?: []);
            return $res->withJson($result, $result['success'] ? 200 : 422);
        } catch (Exception $e) {
            return $res->withJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    });

    $this->post('/confirmar', function ($req, $res) {
        if (!PlanillasApiGuard::authorized($req)) {
            return $res->withJson(['success' => false, 'error' => 'No autorizado'], 401);
        }

        $body = json_decode((string)$req->getBody(), true);
        if (!is_array($body)) {
            $body = (array)$req->getParsedBody();
        }
        $payload = isset($body['payload']) && is_array($body['payload']) ? $body['payload'] : [];
        $token = isset($body['confirm_token']) ? (string)$body['confirm_token'] : '';

        try {
            $model = new PlanillaImportModel();
            return $res->withJson($model->Confirm($payload, $token), 200);
        } catch (Exception $e) {
            return $res->withJson(['success' => false, 'error' => $e->getMessage()], 422);
        }
    });
});
