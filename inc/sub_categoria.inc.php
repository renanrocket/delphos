<?php
include "funcoes.inc.php";
if(isset($_COOKIE["id_empresa"])){
	//includes do php para todas as paginas
	$conn = TConnection::open("gestor");

	$criterio = new TCriteria;
	$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

	$sql = new TSqlSelect;
	$sql->setEntity("cliente");
	$sql->addColumn('alcunha');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		define("ALCUNHA", $alcunha);
	}

	//deletar esse codigo apois migração do app.ado
	file_exists("../conecta.php") ?
	include "../conecta.php" : $conexao = null;
	$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
	extract(mysqli_fetch_assoc($sql));
	include "../".$conectasrc;
	//fim
}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}

extract($_GET);
echo "Sub Categoria<a href='#' ".pop("subCategoriaProduto.php")." title='Cadastrar nova sub categoria'><img class='imgHelp' src='img/mais.png'></a><br>";
if ($categoria ==""){
	echo "<select name='SUBCATEGORIA' disabled>";
}else{
	echo "<select name='SUBCATEGORIA' onchange='mudaSubCat(this.value)'>";
	echo "<option value=''>--</option>";
	$sql= query("select id, nome from sub_categoria where id_categoria='$categoria' order by nome");
	while($row= mysqli_fetch_assoc($sql)){
		extract($row);
		echo "<option value='$id'>$nome</option>";
	}
}
echo "</select>";

mysqli_close($conexao);
?>
