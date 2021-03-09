<?php 
    session_start();
    require_once("/var/www/html/source/app-sat/security/seguranca.php");
    protegePagina();
    require_once("/var/www/html/source/app-sat/security/connect.php"); 
?>
<ul class="navbar-nav ml-auto">
    <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge"><?php print $_SESSION['total_notificacoes'];?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header"><?php print $_SESSION['total_notificacoes'];?> Notificações</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-satellite-dish mr-2"></i><?php print $_SESSION['total_reg_sat_s_comun_sefaz'];?> S@Ts s/ comunicar com a SEFAZ
                <!--<span class="float-right text-muted text-sm">5 mins</span>-->
                <span class="float-right text-muted text-sm">5 mins</span>
            </a>
            <a href="#" class="dropdown-item">
                <i class="fas fa-satellite-dish mr-2"></i><?php print $_SESSION['total_reg_sat_s_transm_sefaz'];?> S@Ts s/ transmitir a SEFAZ
                <!--<span class="float-right text-muted text-sm">5 mins</span>-->
                <span class="float-right text-muted text-sm">5 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-hdd mr-2"></i><?php print $_SESSION['total_reg_sat_xml_presos'];?> S@Ts com XMLs em memória
                <!--<span class="float-right text-muted text-sm">5 mins</span>-->
                <span class="float-right text-muted text-sm">5 mins</span>
            </a>
            <a href="#" class="dropdown-item">
                <i class="fas fa-exclamation-circle mr-2"></i><?php print $_SESSION['total_reg_sat_falha'];?> S@Ts com falha
                <!--<span class="float-right text-muted text-sm">5 mins</span>-->
                <span class="float-right text-muted text-sm">5 mins</span>
            </a>
        </div>
    </li>
</ul>