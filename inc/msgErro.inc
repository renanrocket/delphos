<?php

$motivo = $debug = null;
$php = "Versão do Php e Sistema operacional: " . PHP_VERSION . " " . PHP_OS . "<br>";
if (isset($errno)) {
	$motivo .= "Motivo php: <b style='color:red;'>" . $errstr . "</b><br>";
	$debug .= "Debug php: <b style='color:red;'>Linha " . $errline . " no Arquivo " . $errfile . "</b><br>";
}
if (mysqli_error($conexao)) {
	$motivo .= "Motivo Query: <b style='color:red;'>" . mysqli_error($conexao) . "</b><br>";
	if (isset($instrucao)) {
		$debug .= "Debug Query: <b style='color:red;'>" . $instrucao . "</b><br>";
		//variavel instrucao esta no arquivo q inclui esse arquivo
	}
}
if (mysqli_error($conexaoMaster)) {
	$motivo .= "Motivo Query: <b style='color:red;'>" . mysqli_error($conexaoMaster) . "</b><br>";
	if (isset($instrucao)) {
		$debug .= "Debug Query: <b style='color:red;'>" . $instrucao . "</b><br>";
		//variavel instrucao esta no arquivo q inclui esse arquivo
	}
}
$debug .= debugVariaveis()."<br>";
if(!isset($dbmaster)){
	$sql = query("select * from empresa where usarTimbrado='1'");
	if (mysqli_num_rows($sql)) {
		extract(mysqli_fetch_assoc($sql));
	
		$corpo = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
		$corpo .= "<html>";
		$corpo .= "<head>";
		$corpo .= "<meta http-equiv=\"content-Type\" content=\"text/html; charset=UTF-8\" />";
		$corpo .= "<title>Delphos</title>";
		$corpo .= "</head>";
		$corpo .= "<body>";
		$corpo .= "<div style=\"";
		$corpo .= "font-size:18px;";
		$corpo .= "font-family: Arial, Helvetica, sans-serif;";
		$corpo .= "border: solid 2px #4F0E11;";
		$corpo .= "padding: 10px;";
		$corpo .= "border-radius: 10px;";
		$corpo .= "\" >";
		$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/boneco_duvida.png' style='float: right;'>";
		$corpo .= "<a href='http://www.rocketsolution.com.br'>";
		$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/logo.png' style='margin-right: 30px; margin-bottom: 20px;'><br>";
		$corpo .= "</a>";
		$corpo .= "<h3>Houston we have a problem!</h3><br>";
		$corpo .= "Identificamos um bug em um de nossos sistemas!<br>";
		$corpo .= "Empresa: <b style='color:red;'>$nome</b><br>";
		$corpo .= "Telefone: <b style='color:red;'>$fone1 $fone2 $fone3</b><br>";
		$corpo .= "E-mail: <b style='color:red;'>$email</b><br>";
		$corpo .= "Utilizador Nome: <b style='color:red;'>" . getNomeCookieLogin($_COOKIE["login"]) . "</b><br>";
		$corpo .= "Utilizador telefone: <b style='color:red;'>" . registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "telefone1") . "</b><br>";
		$corpo .= "Utilizador e-email: <b style='color:red;'>" . registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "email") . "</b><br>";
		$corpo .= "Hora da ocorrência: <b style='color:red;'>" . date("d/m/Y H:i:s") . "</b><br>";
		$corpo .= "Página em que foi encontrado o bug: <b style='color:red;'>" . $_SERVER["HTTP_REFERER"] . "</b><br>";
		$corpo .= $php;
		$corpo .= $motivo;
		$corpo .= $debug;
		$corpo .= "I.A. Delphos";
		$corpo .= "</div>";
		$corpo .= "</body>";
		$corpo .= "</html>";
	
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= 'From: falecom@rocketsolution.com.br' . "\r\n";
		$headers .= 'Reply-To: falecom@rocketsolution.com.br' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
	
		if (mail("falecom@rocketsolution.com.br", "Delphos - Suporte $nome", $corpo, $headers)) {
			//se for um problema de banco de dados exibir mensagem na tela solicitando suporte
			if (strstr($motivo, "Motivo Query:")) {
				$msg .= "<h1>Aconteceu algo errado!</h1>";
				$pagina = explode("/", $_SERVER['PHP_SELF']);
				$include = "";
				for ($i = 3; $i < count($pagina); $i++) {
					$include .= "../";
				}
				$include .= "img/robo.png";
				$msg .= "<img src='$include' style='float:right; margin-left:1em;'>";
				$msg .= "Sentimos muito, mas não foi possivel completar essa operação.<br>";
				$msg .= "Um e-mail foi enviado para os programadores da <b style='color:red;'>Rocket Solution</b>, e em breve ";
				$msg .= "entraremos em contato com a <b>$nome</b>, através dos telefones: <b>$fone1</b> ";
				if ($email) {
					$msg .= "ou atravês do e-mail: <b>$email</b>.";
				}
	
				
				info($msg);
				die();
			}
		} else {
			//if ($errno == E_USER_ERROR or $errno == E_USER_WARNING or mysqli_error($conexao)) {
				$msg .= "<h1>Aconteceu algo errado!</h1>";
				$pagina = explode("/", $_SERVER['PHP_SELF']);
				$include = "";
				for ($i = 3; $i < count($pagina); $i++) {
					$include .= "../";
				}
				$include .= "img/robo.png";
				$msg .= "<img src='$include' style='float:right; margin-left:1em;'>";
				$msg .= "Sentimos muito, mas não foi possivel completar essa operação.<br>";
				$msg .= "Como o seu sistema não está na internet, contate o nosso suporte e informe o seguinte problema:<br>";
				$msg .= $php . $motivo . $debug;
				
				info($msg);
			//}
			
		}
	}
}

?>