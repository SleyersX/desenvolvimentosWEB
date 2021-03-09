<?php
    include_once("source/Conn.php");
    
    if(isset($_POST['idTable']) && isset($_POST['dataInicial'])){
        if($_POST['idTable'] == 1){

            $dtInicial = array_reverse(explode("-", trim($_POST['dataInicial'])));
            $dtInicial = implode("-", $dtInicial);

            $dtFinal = array_reverse(explode("-", trim($_POST['dataFinal'])));
            $dtFinal = implode("-", $dtFinal);
            
            $sql = "SELECT shop, pos, data_abertura, hora_abertura, matricula, valor_abertura, data_atualizacao FROM tb_monitoramento_abertura_de_caixas WHERE data_abertura BETWEEN '$dtInicial' AND '$dtFinal'";
            //$sql = "SELECT shop, pos, data_abertura, hora_abertura, matricula, valor_abertura, data_atualizacao FROM tb_monitoramento_abertura_de_caixas WHERE data_abertura = DATE_FORMAT('15-10-2020', '%Y-%m-%d')";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            
            echo json_encode($vetor);

        }else{

            echo json_encode("DATA NULL");

        }
    }else{

        $vetor[] = array_map('utf8_encode',$_POST['idTable']);
        echo json_encode($vetor);      

    }
?>