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

extract($_POST);

echo "<li class='li1'><a href='#' onclick=\"preencher('0', 'Em branco', '$inputId');\">Em branco</a></li>";

$sql = query("select * from produto where nome like '%$queryString%' or modelo like '%$queryString%' or cod_barra='$queryString'");
for($i=0, $li='li1'; $i<mysqli_num_rows($sql); $i++){
	extract(mysqli_fetch_assoc($sql));
	if($li=="li1"){
		$li = "li2";
	}else{
		$li = "li1";
	}
	echo "<li class='$li'><a href='#tr_produto_$inputId' onclick=\"preencher('$id', '$nome', '$inputId');\">$nome</a></li>";
}

?>