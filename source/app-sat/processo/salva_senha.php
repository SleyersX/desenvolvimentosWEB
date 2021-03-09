<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];
    
    $id = $_SESSION['usuarioIDDashSAT'];
    $nivel = $_SESSION['usuarioNivelDashSAT'];
    $nome = $_SESSION['usuarioNomeDashSAT'];
    $login = $_SESSION['usuarioLoginDashSAT'];
    $nova_senha = (isset($_POST['nova-senha'])) ? $_POST['nova-senha'] : '';
    $conf_senha = (isset($_POST['conf-senha'])) ? $_POST['conf-senha'] : '';
    $data = date('Y-m-d H:i:s');

    if (!empty($nova_senha) && !empty($conf_senha)){
        if ($nova_senha != $conf_senha){
            if($nivel == 1){
                //Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Update Passwd'; 
				$msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, senhas não conferem.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
                echo "<script>alert('Senhas não coincidem!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
            }elseif($nivel == 2){
                //Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Update Passwd'; 
				$msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, senhas não conferem.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
                echo "<script>alert('Senhas não coincidem!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
            }elseif($nivel == 3){
                //Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Update Passwd'; 
				$msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, senhas não conferem.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
                echo "<script>alert('Senhas não coincidem!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }elseif($nivel == 4){
                //Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Update Passwd'; 
				$msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, senhas não conferem.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
                echo "<script>alert('Senhas não coincidem!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
            }elseif($nivel == 5){
                //Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Update Passwd'; 
				$msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, senhas não conferem.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
                echo "<script>alert('Senhas não coincidem!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
            }
        }else{
            $senha = password_hash($nova_senha, PASSWORD_DEFAULT, ['cost' => 12 ]);
            $update = "UPDATE tb_usuarios_dashsat SET senha = '$senha', data_modificacao='$data' WHERE id = '$id'";
            $query = mysqli_query($conn, $update);
            if (mysqli_affected_rows($conn)){
                if($nivel == 1){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Senha alterada com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
                }elseif($nivel == 2){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Senha alterada com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
                }elseif($nivel == 3){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Senha alterada com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
                }elseif($nivel == 4){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Senha alterada com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
                }elseif($nivel == 5){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Senha alterada com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
                }
            }else{
                if($nivel == 1){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Erro no processo!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
                }elseif($nivel == 2){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Erro no processo!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
                }elseif($nivel == 3){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Erro no processo!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
                }elseif($nivel == 4){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Erro no processo!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
                }elseif($nivel == 5){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Update Passwd'; 
                    $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    echo "<script>alert('Erro no processo!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
                }
            }
        }    
    }else{
        if($nivel == 1){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Update Passwd'; 
            $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, uma das senhas está em branco.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Senha em branco!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
        }elseif($nivel == 2){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Update Passwd'; 
            $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, uma das senhas está em branco.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Senha em branco!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
        }elseif($nivel == 3){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Update Passwd'; 
            $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, uma das senhas está em branco.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Senha em branco!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
        }elseif($nivel == 4){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Update Passwd'; 
            $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, uma das senhas está em branco.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Senha em branco!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
        }elseif($nivel == 5){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Update Passwd'; 
            $msgLog = 'Update ['.$id.']:['.$login.']:['.$nivel.'] senha, uma das senhas está em branco.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Senha em branco!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
        }
    }
?>
