<?php
include "funcoes.inc.php";
extract($_POST);

$email = new enviar_email($remetente, $destinatario, $assunto, $corpo);
if($email->enviarEmail()){
	echo "<label>E-mail enviado.</label>";
}else{
	echo "<label>Falha ao enviar o e-mail</label>";
}

?>