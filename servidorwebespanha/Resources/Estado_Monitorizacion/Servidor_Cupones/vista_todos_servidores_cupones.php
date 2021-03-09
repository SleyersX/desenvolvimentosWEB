<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

function Pon_Status($Texto) {
	if ($Texto != NULL) {
		if (preg_match("/CORRECTO/",$Texto))
			return '<text class="ok" style="font-size:125%">'.$Texto.'</text>';
		else
			return '<text class="css3-blink" style="font-size:175%; background-color:red; color:white;">'.$Texto.'</text>';
	} else return '<text style="font-size:125%">N/A</text>';
}
$Servidores=array(
	 "ESP." => array("10.208.162.6",  1)
	,"POR." => array("10.246.64.73",  1)
	,"ARG." => array("10.94.202.121", 1)
	,"BRA." => array("10.105.186.135",1)
	,"CHI." => array("1.1.1.1",0)
);

foreach($Servidores as $k => $d) {
	list($ip, $status) = $d;
	if ($status == 1) {
// 		$cmd='sudo ssh soporte@'.$ip.' "cd /home/soporteweb/tools/servidor_cupones/; sudo ./get_oper.sh GET_STAT"';
// 		$tmp=shell_exec($cmd);
// 		echo '<pre>'.$k.$cmd.$tmp.'</pre>';
// 		$lista = explode("\n",$tmp);
// 		$Balanceador[$k] = Pon_Status(array_find("BALANCEADOR:", $lista));
// 		$Balanceador[$k] = str_replace("BALANCEADOR:","BAL.:",$Balanceador[$k]);
// 		$SC1[$k] = Pon_Status(array_find("SC1:", $lista));
// 		$SC2[$k] = Pon_Status(array_find("SC2:", $lista));
		$IMG1 = '<img src="http://'.$ip.'/tools/servidor_cupones/porhora1_b.jpg" class="img_serv_cupo" />';
		$IMG2 = '<img src="http://'.$ip.'/tools/servidor_cupones/porhora2_b.jpg" class="img_serv_cupo" />';
	}
	else 
	{
		$IMG1='<img src="/img/No_disponible.jpg" class="img_serv_cupo" />';
		$IMG2='<img src="/img/No_disponible.jpg" class="img_serv_cupo" />';
// 		$Balanceador[$k]=$SC1[$k]=$SC2[$k]="";
	}
	$FIELDSET_PAIS[$k] = utf8_decode('<fieldset><legend title="Pulse aqu&iacute; para ir a ver el servidor directamente"><img class="icono_menu" src="/img/icono_'.substr($k,0,3).'.png" /> <a href="http://'.$ip.'/Resources/Estado_Monitorizacion/monitorizacion.php?PAGINA=S.CUPONES" target="_blank">Pulse aqu&iacute; para acceder al servidor de '.$k.'</a></legend>'.$IMG1.$IMG2.'<br></fieldset>');
// '.@$Balanceador[$k].' | '.@$SC1[$k].' | '.@$SC2[$k].'
}

echo '
<table>
<tr>
	<td>'.$FIELDSET_PAIS["ESP."].'</td>
	<td>'.$FIELDSET_PAIS["POR."].'</td>
</tr>
<tr>
	<td>'.$FIELDSET_PAIS["ARG."].'</td>
	<td>'.$FIELDSET_PAIS["BRA."].'</td>
</tr>
<tr>
	<td>'.$FIELDSET_PAIS["CHI."].'</td>
	<td></td>
</tr>
</table>
';

?>
