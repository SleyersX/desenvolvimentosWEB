<?php
    use models\DB;
    require_once "../models/connection.php";


    function consulta($query){
        $db = new DB();
        $sql=$query;
        $stmt = $db->conn->prepare($sql);
        $stmt->execute();

        if ($stmt == 0) {
            throw new Exception('Erro ao realizar consulta no banco de dados.');
        }

        $response = array();

        while($data = $stmt->fetch()){
            $response[] = array_map( null,$data);
        }

        return array("data"=>$response,"rowCount"=>count($response));
    }

    if(isset($_GET["query"])){


        try {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode(consulta($_GET["query"]));
        }catch (Exception $e) {
            $errors = array(
                "mensagem" => $e->getMessage()
            );

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode($errors);
        }

    }else{
        $errors = array(
            "mensagem"=>"Error"
        );
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode($errors);
    }
