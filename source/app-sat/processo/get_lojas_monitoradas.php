<?php
    
    include "../security/connect.php";
    include "../config/config.php";

    if(isset($_POST['get_lojas_monitoradas'])){
        $sql = "SELECT centro, total_lojas, total_monitoradas, percentual FROM ". DATA_CONFIG_BD["cn_tab_lojas_cr"] ."";
        $query = mysqli_query($conn,$sql);
        while($data = mysqli_fetch_assoc($query)){
            $vetor[] = array_map('utf8_encode',$data);
        }
        echo json_encode($vetor);
    }else{
        echo json_encode("DATA NULL");
    }
    
?>