<?php

namespace src\models;

use components\Db;
use PDO;

/**
 * Class Country
 * @package src\models
 */
class Country
{

    public function getInfo(string $phone_number, $continent_code): bool
    {
        $db = Db::getConnection();

        $query = $db->prepare("SELECT * FROM country WHERE (continent = :continent)");
        $query->execute(array(":continent" => $continent_code));

        $list = $query->fetchAll(PDO::FETCH_OBJ);
        
        // possible bug with range phone code

        if (!empty($list)) {
            $phone = (string)preg_replace('/[^0-9]/', '', $phone_number);
            // its not optimised
            foreach ($list as $item) {
                $phone_code = (string)preg_replace('/[^0-9]/', '', $item->phone);

                if (
                    !empty($phone_code) &&
                    str_starts_with($phone, $phone_code)
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}
