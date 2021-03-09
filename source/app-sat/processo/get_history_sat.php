<?php
    
    include "../security/connect.php";

    if(isset($_POST['sat'])){
        $sql = "SELECT id,sat, shop, pos, data_inclusao, data_atualizacao, status_atual FROM cn_hml_sats_historico_inativos WHERE sat = '".$_POST['sat']."'";
        $query = mysqli_query($conn,$sql);
        while($data = mysqli_fetch_assoc($query)){
            $vetor[] = array_map('utf8_encode',$data);
        }
        echo json_encode($vetor);
    }else{
        echo json_encode("SAT NULL");
    }
    
?>