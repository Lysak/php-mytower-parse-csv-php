<?php

namespace src\models;

use components\Db;
use PDOStatement;

/**
 * Class Ip
 * @package src\models
 */
class Ip {

    /**
     * @param string $ip
     * @param string $json
     * @return false|PDOStatement
     */
    public function create(string $ip, string $json): bool|PDOStatement
    {
        $db = Db::getConnection();
        $sql = "INSERT INTO ip
        (ip, params) 
        VALUES('$ip', '$json')";

        return $db->query($sql);
    }

    public function getInfo(string $ip): Object {
        $db = Db::getConnection();

        $stmt = $db->prepare("SELECT * FROM ip WHERE ip = :ip");
        $stmt->execute(array(":ip" => $ip));
        return $stmt->fetchObject();
    }
}

