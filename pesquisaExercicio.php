<?php
include "templates/upLogin.inc.php";

extract($_GET);
$array=null;

if(!isset($_GET["nome"])){
	
	echo "<form method='get' action='pesquisaExercicio.php' enctype='multipart/form-data'>";
		echo "Nome do exercício:<br>";
		echo "<input type='text' name='nome' style='width:auto;'><br>";
		echo "<input type='submit' class='btnEnviar' valu='Enviar'>";
	echo "</form>";
	
}else{
	$Nome = $nome;
	
	$sql = query("select * from exercicio where nome like '%$nome%' order by nome");
	
	for($i = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='cadastrarExercicio.php' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = showImagemExercicio($id, 3, 3);
		$array[] = $nome;
		$array[] = $beneficios;
		
	}
	
	
	
	$tag = array("ID", "Imagem" , "Nome", "Benefícios");
	
	$tabela = new tabela;
	$tabela->setTitulo("Resultado para busca de $Nome");
	$tabela->setTag($tag);
	$tabela->setValores($array);
	echo $tabela->showTabela();	
	
	
}

include "templates/downLogin.inc.php";
?>

