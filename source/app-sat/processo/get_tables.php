<?php
    
    include "../security/connect.php";
    include "../config/config.php";

    if(isset($_POST['idTable'])){
        if($_POST['idTable'] == 1){    
            $sql = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_inclusao, DATE_FORMAT(data_inclusao, '%Y-%m-%d') AS data_formatada FROM ". DATA_CONFIG_BD["cn_tab_sat"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 2){
            $sql = "SELECT sat FROM ". DATA_CONFIG_BD["cn_group_sats_inativo"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 3){
            $sql = "SELECT loja, DireFisc, PersFisc, CodiEstaClie, LocaFisc, CodiIden, n_tpvs_setvari FROM ". DATA_CONFIG_BD["cn_tab_list_lojas"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 4){
            $sql = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, n_dias, iploja FROM ". DATA_CONFIG_BD["cn_tab_comun_sefaz"] ." ORDER BY n_dias DESC";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 5){
            $sql = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_transm_sefaz, n_dias, iploja FROM ". DATA_CONFIG_BD["cn_tab_transm_sefaz"] ."  WHERE disco_usado != '0 Mbytes' ORDER BY n_dias DESC";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 6){
            $sql = "SELECT sat, loja, caixa, ipsat, disco_usado, iploja FROM ". DATA_CONFIG_BD["cn_tab_disk_used"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 7){
            $sql = "SELECT sat, loja, caixa, ip, IPLoja FROM ". DATA_CONFIG_BD["cn_tab_err_ip"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 8){
            $sql = "SELECT sat, loja, caixa, ipsat, disco_usado, status_wan, iploja FROM ". DATA_CONFIG_BD["cn_tab_st_wan"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 9){
            $sql = "SELECT sat, loja, caixa, ipsat, modelo_sat, status_wan, iploja FROM ". DATA_CONFIG_BD["cn_tab_sats_falha"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 10){
            $sql = "SELECT sat, loja, caixa, ipsat, modelo_sat, status_wan, descricao_bloqueio, iploja FROM ". DATA_CONFIG_BD["cn_tab_sats_bloq"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 11){
            $sql = "SELECT sat, loja, caixa, ipsat, modelo_sat, status_wan, descricao_aviso, iploja FROM ". DATA_CONFIG_BD["cn_tab_avisos_sefaz"] ."";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 12){
            $sql = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_atualizacao, data_hora_atual, diff_minutos, iploja FROM ". DATA_CONFIG_BD["cn_tab_diff_dt_hora"] ." WHERE diff_minutos > 0 OR diff_minutos < 0";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }elseif($_POST['idTable'] == 13){
            $sql = "SELECT shop, n_pos, tpvs, maior_n_dias, perc, status_loja  FROM ". DATA_CONFIG_BD["cn_group_lojas_comunicar_sefaz"] ." WHERE status_loja = 1";
            $query = mysqli_query($conn,$sql);
            while($data = mysqli_fetch_assoc($query)){
                $vetor[] = array_map('utf8_encode',$data);
            }
            echo json_encode($vetor);
        }
    }else{
        echo json_encode("DATA NULL");
    }
    
?>