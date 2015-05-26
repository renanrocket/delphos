<?php
include "templates/upLogin.inc.php";

extract($_GET);
extract($_POST);

if($_GET){
	
	$sql = query("select * from servico where nome like '%$nome%'");
	
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$script  = "<form method='get' action='cadastrarServico.php' enctype='multipart/form-data'>";
		$script .= "<input type='hidden' name='op' value='visualizar'>";
		$script .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$script .= "<input type='submit' value='$id'>";
		$script .= "</form>";
		
		$valoresArray[] = $script;
		$valoresArray[] = $nome;
		$valoresArray[] = showImagemServico($id, 3, 3);
		
	}
	
	$tabela = new tabela;
	$tabela->setTitulo("Serviços encontrados");
	$tabela->setTag(array("ID", "Nome", "Imagem"));
	$tabela->setValores($valoresArray);
	echo $tabela->showTabela();
	
	
}else{
	echo "<form method='get' action='pesquisaServico.php' enctype='multipart/form-data'>";
		echo "Digite o nome do serviço<br>";
		echo "<input type='text' name='nome' style='width:auto;'><br>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
}


include "templates/downLogin.inc.php";
?>

