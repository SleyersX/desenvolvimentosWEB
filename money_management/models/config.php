<?php

define("ini", parse_ini_file('config/config.ini', true));
$host = ini['database']['host'];
$db = ini['database']['name'];
$user = ini['database']['user'];
$passwd = ini['database']['pass'];

define("DATA_CONFIG", [
    "driver" => "mysql",
    "host" => "$host",
    "port" => "3306",
    "dbname" => "$db",
    "username" => "$user",
    "passwd" => "$passwd"
]);