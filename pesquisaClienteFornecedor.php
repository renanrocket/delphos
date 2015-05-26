<?php
include "templates/upLogin.inc.php";

extract($_GET);
$array=null;

if(!isset($_GET["formato"])){
	
	echo "<form method='get' action='pesquisaClienteFornecedor.php' enctype='multipart/form-data'>";
		echo "Buscar:<br>";
		echo "<select name='formato' style='width:auto;'>";
			echo "<option value='cliente'>Cliente</option>";
			echo "<option value='fornecedor'>Fornecedor</option>";
		echo "</select><br>";
		echo "Nome<br>";
		echo "<input type='text' name='nome' style='width:auto;'><br>";
		echo "<input type='submit' class='btnEnviar' valu='Enviar'>";
	echo "</form>";
	
}else{
	$Nome = $nome;
	
	$sql = query("select * from cliente_fornecedor where formato='$formato' and nome like '%$nome%' order by nome");
	
	for($i = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='cadastrarClienteFornecedor.php' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id_cliente_fornecedor' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = showImagemClienteFornecedor($id, 3, 3);
		$array[] = $nome;
		$array[] = $fone1;
		$array[] = $fone2;
		$array[] = $email;
		
	}
	
	
	$formato=="cliente" ? $formato="Cliente" : $formato="Fornecedor";
	
	$tag = array("ID", "Imagem" ,$formato, "Telefone 1", "Telefone 2", 'E-mail');
	
	$tabela = new tabela;
	$tabela->setTitulo("Resultado para busca de $formato $Nome");
	$tabela->setTag($tag);
	$tabela->setValores($array);
	echo $tabela->showTabela();	
	
	
}

include "templates/downLogin.inc.php";
?>

