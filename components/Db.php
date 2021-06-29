<?php

namespace components;

use PDO;

class Db
{
    public static function getConnection(): PDO
    {
        $charset = "utf8";
        $paramsPath = PROJECT_ROOT . '/config/db_params.php';
        $params = include($paramsPath);
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']};charset=$charset";
        return new PDO($dsn, $params['user'], $params['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function getSalt()
    {
        $paramsPath = PROJECT_ROOT . '/config/db_params.php';
        $params = include($paramsPath);
        return $params['salt'];
    }
}
