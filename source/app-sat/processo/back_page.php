<?php
    require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");
	
    $token = $_SESSION['tokenLogonDashSAT'];
    $nivel = $_SESSION['usuarioNivelDashSAT'];
    if(isset($_GET['str'],$_GET['id'])){
        $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        $str = filter_input(INPUT_GET,'str',FILTER_SANITIZE_STRING);
    }
    if(isset($_GET['search'])){
        $search = filter_input(INPUT_GET,'search',FILTER_SANITIZE_STRING);
    }
    $idPage = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);


    if(!empty($nivel)){
        if($nivel == 1){
            if($idPage == 'f9dbbb417e1282002a8aecd54173b1a4'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/estrutura.php?token='.$token.'">';
            }elseif ($idPage == 'ebbbf5a356760e53d4313296b7a42709') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/getdashsat.php?token='.$token.'&id='.$id.'&str='.$str.'">';
            }elseif ($idPage == '874409f0e07176e9ca2e8fe4877f13cb') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/tables/table.php?token='.$token.'">';
            }elseif ($idPage == 'd57ea0157f4fb499069258c46c5e258d') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/tables/tb-comun-sefaz.php?token='.$token.'">';
            }elseif ($idPage == '90ddb39c86c34511f23b26c39448a4e4') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/tables/tb-erro-config.php?token='.$token.'">';
            }elseif ($idPage == '44e7662969b1f0014ec5feba3cdb776b') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/tables/tb-sats-falha.php?token='.$token.'">';
            }elseif ($idPage == 'a7e01f4f5af6c3a7d169f07f098023d5') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/tables/tb-xml-presos.php?token='.$token.'">';
            }elseif ($idPage == '790aee404dc63df1744bf9cc59c12535') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/getlojas.php?token='.$token.'&id='.$id.'&str='.$str.'&page='.$idPage.'">';
            }elseif($idPage == '98b1ead80be34c0c6320b921adc83368'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';
            }elseif($idPage == '4c11ce1f483bac29acd478620c8185ba'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/search.php?token='.$token.'&search='.$search.'">';
            }
        }elseif ($nivel == 2) {
            if($idPage == 'f9dbbb417e1282002a8aecd54173b1a4'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/estrutura.php?token='.$token.'">';
            }elseif ($idPage == 'ebbbf5a356760e53d4313296b7a42709') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/getdashsat.php?token='.$token.'&id='.$id.'&str='.$str.'">';
            }elseif ($idPage == '874409f0e07176e9ca2e8fe4877f13cb') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/tables/table.php?token='.$token.'">';
            }elseif ($idPage == 'd57ea0157f4fb499069258c46c5e258d') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/tables/tb-comun-sefaz.php?token='.$token.'">';
            }elseif ($idPage == '90ddb39c86c34511f23b26c39448a4e4') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/tables/tb-erro-config.php?token='.$token.'">';
            }elseif ($idPage == '44e7662969b1f0014ec5feba3cdb776b') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/tables/tb-sats-falha.php?token='.$token.'">';
            }elseif ($idPage == 'a7e01f4f5af6c3a7d169f07f098023d5') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/tables/tb-xml-presos.php?token='.$token.'">';
            }elseif ($idPage == '790aee404dc63df1744bf9cc59c12535') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/getlojas.php?token='.$token.'&id='.$id.'&str='.$str.'&page='.$idPage.'">';
            }elseif($idPage == '98b1ead80be34c0c6320b921adc83368'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
            }elseif($idPage == '4c11ce1f483bac29acd478620c8185ba'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/search.php?token='.$token.'&search='.$search.'">';
            }
        }elseif ($nivel == 3) {
            if($idPage == 'f9dbbb417e1282002a8aecd54173b1a4'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/estrutura.php?token='.$token.'">';
            }elseif ($idPage == 'ebbbf5a356760e53d4313296b7a42709') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/getdashsat.php?token='.$token.'&id='.$id.'&str='.$str.'">';
            }elseif ($idPage == '874409f0e07176e9ca2e8fe4877f13cb') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/tables/table.php?token='.$token.'">';
            }elseif ($idPage == 'd57ea0157f4fb499069258c46c5e258d') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/tables/tb-comun-sefaz.php?token='.$token.'">';
            }elseif ($idPage == '90ddb39c86c34511f23b26c39448a4e4') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/tables/tb-erro-config.php?token='.$token.'">';
            }elseif ($idPage == '44e7662969b1f0014ec5feba3cdb776b') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/tables/tb-sats-falha.php?token='.$token.'">';
            }elseif ($idPage == 'a7e01f4f5af6c3a7d169f07f098023d5') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/tables/tb-xml-presos.php?token='.$token.'">';
            }elseif ($idPage == '790aee404dc63df1744bf9cc59c12535') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/getlojas.php?token='.$token.'&id='.$id.'&str='.$str.'&page='.$idPage.'">';
            }elseif($idPage == '98b1ead80be34c0c6320b921adc83368'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }elseif($idPage == '4c11ce1f483bac29acd478620c8185ba'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/search.php?token='.$token.'&search='.$search.'">';
            }
        }elseif ($nivel == 4) {
            if($idPage == 'f9dbbb417e1282002a8aecd54173b1a4'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/estrutura.php?token='.$token.'">';
            }elseif ($idPage == 'ebbbf5a356760e53d4313296b7a42709') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/getdashsat.php?token='.$token.'&id='.$id.'&str='.$str.'">';
            }elseif ($idPage == '874409f0e07176e9ca2e8fe4877f13cb') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/tables/table.php?token='.$token.'">';
            }elseif ($idPage == 'd57ea0157f4fb499069258c46c5e258d') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/tables/tb-comun-sefaz.php?token='.$token.'">';
            }elseif ($idPage == '90ddb39c86c34511f23b26c39448a4e4') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/tables/tb-erro-config.php?token='.$token.'">';
            }elseif ($idPage == '44e7662969b1f0014ec5feba3cdb776b') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/tables/tb-sats-falha.php?token='.$token.'">';
            }elseif ($idPage == 'a7e01f4f5af6c3a7d169f07f098023d5') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/tables/tb-xml-presos.php?token='.$token.'">';
            }elseif ($idPage == '790aee404dc63df1744bf9cc59c12535') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/getlojas.php?token='.$token.'&id='.$id.'&str='.$str.'&page='.$idPage.'">';
            }elseif($idPage == '98b1ead80be34c0c6320b921adc83368'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
            }elseif($idPage == '4c11ce1f483bac29acd478620c8185ba'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/search.php?token='.$token.'&search='.$search.'">';
            }
        }elseif ($nivel == 5) {
            if($idPage == 'f9dbbb417e1282002a8aecd54173b1a4'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/estrutura.php?token='.$token.'">';
            }elseif ($idPage == 'ebbbf5a356760e53d4313296b7a42709') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/getdashsat.php?token='.$token.'&id='.$id.'&str='.$str.'">';
            }elseif ($idPage == '874409f0e07176e9ca2e8fe4877f13cb') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/tables/table.php?token='.$token.'">';
            }elseif ($idPage == 'd57ea0157f4fb499069258c46c5e258d') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/tables/tb-comun-sefaz.php?token='.$token.'">';
            }elseif ($idPage == '90ddb39c86c34511f23b26c39448a4e4') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/tables/tb-erro-config.php?token='.$token.'">';
            }elseif ($idPage == '44e7662969b1f0014ec5feba3cdb776b') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/tables/tb-sats-falha.php?token='.$token.'">';
            }elseif ($idPage == 'a7e01f4f5af6c3a7d169f07f098023d5') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/tables/tb-xml-presos.php?token='.$token.'">';
            }elseif ($idPage == '790aee404dc63df1744bf9cc59c12535') {
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/getlojas.php?token='.$token.'&id='.$id.'&str='.$str.'&page='.$idPage.'">';
            }elseif($idPage == '98b1ead80be34c0c6320b921adc83368'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
            }elseif($idPage == '4c11ce1f483bac29acd478620c8185ba'){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/search.php?token='.$token.'&search='.$search.'">';
            }
        }
    }
?>