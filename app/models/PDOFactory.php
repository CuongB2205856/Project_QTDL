<?php

namespace App\Models; 
class PDOFactory
{
    // Hàm tạo đối tượng PDO
    public function create(array $config): \PDO
    {
        [
            'dbhost' => $dbhost,
            'dbname' => $dbname,
            'dbuser' => $dbuser,
            'dbpass' => $dbpass
        ] = $config;

        $dsn = "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4";

        return new \PDO($dsn, $dbuser, $dbpass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }
}
?>