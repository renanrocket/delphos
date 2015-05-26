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


$conn = TConnection::open(ALCUNHA);
$criterio = new TCriteria;
$criterio->add(new TFilter('id','>=','1'));
$criterio->setProperty('order', 'tag');
$criterio->setProperty('group', 'tag');

$sql = new TSqlSelect;
$sql->setEntity('imovel_caracteristicas');
$sql->addColumn('tag');
$sql->setCriteria($criterio);


$result = $conn->query($sql->getInstruction());

$retorno = array();
if($result->rowCount()){
	for($i=0;$i<$result->rowCount(); $i++){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		$retorno[] = $tag;
		

	}
}else{
	$retorno[] = "";
}




$retorno = json_encode($retorno);
echo $retorno;


mysqli_close($conexao);






?>