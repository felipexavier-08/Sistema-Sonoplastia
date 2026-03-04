<?php

declare(strict_types=1);

class Conexao
{
    private static ?PDO $instancia = null;

    public static function getInstancia(): PDO
    {
        if (self::$instancia === null) {
            $host = '127.0.0.1';
            $db = 'sistema_sonoplastia';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

            self::$instancia = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$instancia;
    }
}
