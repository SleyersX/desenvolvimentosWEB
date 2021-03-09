<?php

namespace Source\Models;

use PDO;
use PDOException;


class Connection
{
    /** @var PDO */
    private static $instanceI;

    /** @var PDOException */
    private static $errorI;

    /**
     * @return PDO
     */

    public static function getInstanceI(): ?PDO
    {
        if (empty(self::$instanceI)) {
            try {
                self::$instanceI = new PDO(
                    DATA_CONFIG["driver"] . ":host=" . DATA_CONFIG["host"] . ";dbname=" . DATA_CONFIG["dbname"] . ";port=" . DATA_CONFIG["port"],
                    DATA_CONFIG["username"],
                    DATA_CONFIG["passwd"],
                    DATA_CONFIG["options"]
                );
            } catch (PDOException $exceptionI) {
                self::$errorI = $exceptionI;
            }
        }

        return self::$instanceI;
    }


    /**
     * @return PDOException|null
     */
    public static function getErrorI(): ?PDOException
    {
        return self::$errorI;
    }

}
