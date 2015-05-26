<?php
include_once "../templates/upLoginImp.inc.php";

extract($_POST);
extract($_GET);

if ($op == "editar") {
	$sql = "UPDATE funcao SET nome='$grava_funcao' WHERE id='$grava_id'";
	$consulta = query($sql);
	if ($consulta) {
		echo "<br><br> Alterado com sucesso!";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=cadastrarFuncao.php'>";
	} else {
		echo "<br><br> Erro na altera&ccedil;&atilde;o! <br><br>" . mysqli_error();
	}
} elseif ($op == "novo") {
	$sql = "INSERT INTO funcao (nome) VALUES ('$grava_funcao')";
	$consulta = query($sql);
	if ($consulta) {
		echo "<br><br> Inserido com sucesso!";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=cadastrarFuncao.php'>";

	} else {
		echo "<br><br> Erro na inser&ccedil;&atilde;o! <br><br>" . mysqli_error();
	}
} elseif ($op == "deletar") {
	$sql = "UPDATE funcao SET status='Inativo' where id='$id_funcao'";
	$consulta = query($sql);
	if ($consulta) {
		echo "<br><br> Inativado com sucesso!";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=cadastrarFuncao.php'>";
	} else {
		echo "<br><br> Erro na altera&ccedil;&atilde;o! <br><br>" . mysqli_error();
	}
} elseif ($op == "ativar") {
	$sql = "UPDATE funcao SET status='Ativo' where id='$id_funcao'";
	$consulta = query($sql);
	if ($consulta) {
		echo "<br><br> Ativado com sucesso!";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=cadastrarFuncao.php'>";
	} else {
		echo "<br><br> Erro na altera&ccedil;&atilde;o! <br><br>" . mysqli_error();
	}
}

include_once "../templates/downLoginImp.inc.php";
?>