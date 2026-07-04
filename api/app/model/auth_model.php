<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class AuthModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::StartUp();
    }

    /**
     * Verifica credenciales contra la tabla cruge_user (hash MD5 como usaba el sistema Yii/Cruge).
     * Devuelve datos del usuario si son válidas, false en caso contrario.
     */
    public function ValidarCredenciales($username, $password)
    {
        $r = new Response();
        try {
            $hash = md5($password);
            $stm  = $this->db->prepare(
                "SELECT id, username, email, superuser
                 FROM cruge_user
                 WHERE username = ? AND passwordhash = ? AND state = 1
                 LIMIT 1"
            );
            $stm->execute([$username, $hash]);
            $usuario = $stm->fetch(PDO::FETCH_OBJ);

            if (!$usuario) {
                $r->SetResponse(false, 'Usuario o contraseña incorrectos');
                return $r;
            }

            // Generar JWT simple (header.payload.signature)
            $secret  = getenv('JWT_SECRET') ?: 'veteranos_secret_key_2025';
            $payload = [
                'iss'  => 'veteranos-api',
                'iat'  => time(),
                'exp'  => time() + 86400, // 24 horas
                'uid'  => $usuario->id,
                'user' => $usuario->username,
                'su'   => (int)$usuario->superuser,
            ];

            $token = $this->GenerarJWT($payload, $secret);

            $r->result = [
                'token'    => $token,
                'username' => $usuario->username,
                'email'    => $usuario->email,
                'superuser'=> (int)$usuario->superuser,
            ];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /**
     * Genera un JWT sin dependencias externas (HS256).
     */
    private function GenerarJWT(array $payload, string $secret): string
    {
        $header  = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($payload));
        $header  = str_replace(['+', '/', '='], ['-', '_', ''], $header);
        $payload = str_replace(['+', '/', '='], ['-', '_', ''], $payload);
        $sig     = hash_hmac('sha256', "{$header}.{$payload}", $secret, true);
        $sig     = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($sig));
        return "{$header}.{$payload}.{$sig}";
    }

    /**
     * Verifica y decodifica un JWT.
     */
    public static function VerificarJWT(string $token): ?array
    {
        $secret = getenv('JWT_SECRET') ?: 'veteranos_secret_key_2025';
        $parts  = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payload, $sig] = $parts;
        $expectedSig = hash_hmac('sha256', "{$header}.{$payload}", $secret, true);
        $expectedSig = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSig));

        if (!hash_equals($expectedSig, $sig)) return null;

        $data = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
        if (!$data || $data['exp'] < time()) return null;

        return $data;
    }
}
