<?php
    
    include_once "../Conn.php";

    $id=1;
    //if(isset($_POST['dateSearch'])){
        //if($id == 1){    
            $sql = "SELECT shop, pos, data_abertura, hora_abertura, matricula, valor_abertura, data_atualizacao FROM tb_monitoramento_abertura_de_caixas";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        //}
        echo json_encode("DATA NULL");
    //}
    
?>