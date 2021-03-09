<?php
    
    include "../security/connect.php";

    if(isset($_POST['shop'])){
        $sql = "SELECT id, shop, sat, pos, ip, mask, gateway, dns_primary, dns_secondary, used_disk, firmware, layout, numbers_cfes_memory, fail, status_sat FROM cn_closed_shops_sat WHERE shop = '".$_POST['shop']."'";
        $query = mysqli_query($conn,$sql);
        while($data = mysqli_fetch_assoc($query)){
            $vetor[] = array_map('utf8_encode',$data);
        }
        echo json_encode($vetor);
    }else{
        echo json_encode("SHOP NULL");
    }
    
?>