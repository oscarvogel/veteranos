<?php
namespace App\Lib;

use PDO;

class Database
{
    public static function StartUp()
    {
        $host   = getenv('DB_HOST')   ?: 'localhost';
        $dbname = getenv('DB_NAME')   ?: 'veteranos';
        $user   = getenv('DB_USER')   ?: 'root';
        $pass   = getenv('DB_PASS')   ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8';

        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset={$charset}", $user, $pass);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        return $pdo;
    }
}