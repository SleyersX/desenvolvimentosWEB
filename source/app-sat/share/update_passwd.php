<?php
    session_start();
    require_once("/var/www/html/source/app-sat/security/seguranca.php");
    protegePagina();
    require_once("/var/www/html/source/app-sat/security/connect.php");
?>
<form name="frm_senha" action="/source/app-sat/processo/salva_senha.php" method="POST">
    <div class="modal fade" id="editaSenha" tabindex="-1" role="dialog" aria-labelledby="editaSenhaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alteração de Senha</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" class="form-control" maxlength="50" name="nova-senha" placeholder="Nova Senha" id="nova-senha">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-eye" id="show_password" style="border: none; background-color: transparent ;"></span>
                                    </div>
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" class="form-control" maxlength="50" name="conf-senha" placeholder="Confirme Senha" id="conf-senha" oninput="check_passwd('conf-senha','nova-senha')">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <span id="span-passwd" style="color:#ff0000;"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-outline-primary">Confirma</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    function check_passwd(passwd_new,passwd_check) {
        var msgpasswd = 'Senhas não conferem';
        var textpasswd = msgpasswd.bold();
        var passwd_1 = document.getElementById(passwd_check).value;
        var passwd_2 = document.getElementById(passwd_new).value;

        if(passwd_1 != "" && passwd_2 != "" && passwd_1 === passwd_2){
            document.getElementById(passwd_new).style.border = '1px solid green';
            document.getElementById('span-passwd').innerHTML = '';
        }else{
            document.getElementById('span-passwd').innerHTML = textpasswd;
            document.getElementById(passwd_new).style.border = '1px solid red';
            document.getElementById(passwd_new).focus();
        }
    }
</script>
