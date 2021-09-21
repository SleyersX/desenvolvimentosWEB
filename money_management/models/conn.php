<?php

    use models\DB;
    require_once "connection.php";

    $db = new DB();
    //$stmt = $db->executeSql("SELECT * FROM tb_entries",[],false);
    $stmt = $db->conn->prepare("SELECT * FROM tb_entries");
    $stmt->execute();
    print_r($stmt->fetch());