<?php
    use models\DB;
    require_once "../models/connection.php";
    $db = new DB();

    $sql="SELECT * FROM cnt_lancamentos";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();

    while($data = $stmt->fetch()){
        //$vetor[] = array_map('utf8_encode', $data);
        $vetor[] = array_map( null, $data);
    }
    //echo json_encode($stmt->fetchAll());
    //$vetor[] = $stmt->fetch();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($vetor);