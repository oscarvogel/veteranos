<?php
namespace App\Lib;

class PlanillasApiGuard
{
    public static function authorized($request)
    {
        $expected = (string)getenv('API_PLANILLAS_KEY');
        if ($expected === '') {
            return false;
        }

        $provided = trim((string)$request->getHeaderLine('X-Planillas-Key'));
        if ($provided === '') {
            $auth = trim((string)$request->getHeaderLine('Authorization'));
            if (stripos($auth, 'Bearer ') === 0) {
                $provided = trim(substr($auth, 7));
            }
        }

        if (function_exists('hash_equals')) {
            return hash_equals($expected, $provided);
        }
        return $expected === $provided;
    }
}
