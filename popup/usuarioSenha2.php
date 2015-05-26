
<?php
	
	include_once "../templates/upLoginImp.inc.php";

	extract($_POST);
	$valida = true;
	$antiga = md5($antiga);
	$cod = "";
	
	if (strlen($nova) < 5) {
		$cod .= "Desculpe, mas senha nova muito curta.<br>";
		$cod .= historico("-1");
		$valida = false;
	}
	$sql = query("select * from usuario where id='$id' and senha='$antiga'");
	if (mysqli_num_rows($sql) <= 0) {
		$cod .= "Desculpe, mas senha antiga incorreta.<br>";
		$cod .= historico("-1");
		$valida = false;
	}
	if ($nova <> $repitenova) {
		$cod .= "Desculpe, mas você repetiu a senha incorretamente.<br>";
		$cod .= historico("-1");
		$valida = false;
	}
	if ($valida) {
	
		$nova = md5($nova);
	
		$instrucao = "update usuario set senha='$nova' where id='$id'";
		$sql = query($instrucao);
	
		if (mysqli_affected_rows($conexao) > 0) {
			$cod .= "Senha alterada com sucesso.<br>";
			$cod .= "<script type='text/javascript'>
				window.onload = function() {
					window.setTimeout('window.close()', 2000);
				}
			</script>";
		} else {
			include_once "inc/msgErro.inc";
		}
	
	}
	
	
	echo $cod;
	include_once "../templates/downLoginImp.inc.php";
?>
