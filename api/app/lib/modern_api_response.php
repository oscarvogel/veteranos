<?php
namespace App\Lib;

class ModernApiResponse
{
    public static function fromLegacy($legacy, $mapper = null, $meta = null)
    {
        $success = is_object($legacy) && isset($legacy->response) && $legacy->response === true;

        if (!$success) {
            $message = 'Ocurrio un error inesperado.';
            if (is_object($legacy) && isset($legacy->message) && $legacy->message !== '') {
                $message = $legacy->message;
            }
            $message = self::publicErrorMessage($message);

            return [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => $message,
                ],
            ];
        }

        $data = isset($legacy->result) ? $legacy->result : [];
        $payload = [
            'success' => true,
            'data' => self::normalizeRows($data, $mapper),
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        return $payload;
    }

    public static function paginate($items, $params)
    {
        $rows = self::normalizeRows($items);
        $total = count($rows);
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $perPage = isset($params['per_page']) ? (int)$params['per_page'] : 20;
        $perPage = max(1, min(100, $perPage));
        $totalPages = max(1, (int)ceil($total / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        return [
            'data' => array_slice($rows, ($page - 1) * $perPage, $perPage),
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'page' => $page,
                'total_pages' => $totalPages,
            ],
        ];
    }

    public static function json($response, $payload, $status = 200)
    {
        $response->getBody()->write(json_encode($payload));

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public static function normalizeRows($items, $mapper = null)
    {
        if ($items === null || $items === false) {
            return [];
        }

        if (!is_array($items)) {
            $items = [$items];
        }

        $rows = [];
        foreach ($items as $item) {
            $row = (is_object($item) || is_array($item)) ? (is_object($item) ? get_object_vars($item) : $item) : $item;
            $rows[] = $mapper ? call_user_func($mapper, $row) : $row;
        }

        return $rows;
    }

    public static function publicErrorMessage($message, $fallback = 'Ocurrio un error inesperado.')
    {
        if (self::isDebugEnabled()) {
            return $message ?: $fallback;
        }

        if (self::looksLikeDatabaseError($message)) {
            return 'No se pudo completar la consulta.';
        }

        return $message ?: $fallback;
    }

    private static function isDebugEnabled()
    {
        $value = getenv('API_DEBUG');
        if ($value === false) {
            return false;
        }

        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }

    private static function looksLikeDatabaseError($message)
    {
        $text = strtolower((string)$message);
        foreach (['sqlstate', 'access denied for user', 'pdoexception', 'mysql', 'mariadb'] as $needle) {
            if (strpos($text, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
