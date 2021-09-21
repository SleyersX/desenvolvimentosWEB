<?php /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */

namespace models;
include "config.php";

interface DBInterface
{
    public function newConnection(String $db, String $us, String $dbPass, String $dbHost);
    public function setBindValues(array $args);
    public function executeSql(String $sql, array $args, bool $isKeyValue);
    public function setSqlInsert(String $table, array $args, bool $isKeyValue);
    public function insert(String $table, array $args, bool $isKeyValue);

}

class DB implements DBInterface
{


    const DBHOST = DATA_CONFIG["host"];
    const DBUSER = DATA_CONFIG["username"];
    const DBPASS = DATA_CONFIG["passwd"];
    const DBNAME = DATA_CONFIG["dbname"];

    public $conn;
    public $sql;
    public $stmt;

    public function __construct($dbName = DB::DBNAME, $dbUser = DB::DBUSER, $dbPass = DB::DBPASS, $dbHost = DB::DBHOST)
    {
        $this->newConnection($dbName, $dbUser, $dbPass, $dbHost);
    }

    public function newConnection($dbName = DB::DBNAME, $dbUser = DB::DBUSER, $dbPass = DB::DBPASS, $dbHost = DB::DBHOST)
    {
        try {
            $this->conn = new \PDO("mysql:dbname=" . $dbName . ";charset=utf8;host=" . $dbHost,
                $dbUser,
                $dbPass,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
            );

            $this->conn->setAttribute(
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION
            );

            $this->conn->setAttribute(
                \PDO::ATTR_DEFAULT_FETCH_MODE,
                \PDO::FETCH_ASSOC
            );
        } catch (PDOException $e) {
            echo 'Não foi possível se conecytar com a base de dados';
            exit;
        }
    }

    public function setBindValues(array $args)
    {

        foreach ($args as $key => $value) {
            $this->stmt->bindValue(':' . $key, $value);
        }

    }

    public function executeSql(String $sql, array $args, bool $isKeyValue)
    {

        $this->sql = $sql;

        if (count($args) > 0) {
            $this->stmt = $this->conn->prepare($this->sql);

            if ($isKeyValue) {
                $this->setBindValues($args);

            } else {
                $this->stmt->execute($args);
            }
        } else {
            $this->stmt = $this->conn->query($this->sql);
        }

        return $this->stmt;
    }


    public function setSqlInsert(String $table, array $args, bool $isKeyValue)
    {
        if ($isKeyValue) {
            $keys = array_keys($args);
            $this->sql = "INSERT INTO $table (" . implode(',', $keys) . ")VALUES(:" . implode(',:', $keys) . ");";
        } else {
            $this->sql = "INSERT INTO $table VALUES(?" . str_repeat(',?', count($args) - 1) . ")";
        }
        return $this->sql;
    }

    public function insert(String $table, array $args, bool $isKeyValue)
    {

        $this->setSqlInsert($table, $args, $isKeyValue);

        $this->stmt = $this->conn->prepare($this->sql);

        if ($isKeyValue) {
            $this->setBindValues($args);
        } else {
            $this->stmt->execute($args);
        }

        return $this->stmt;
    }

}