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

$conn = TConnection::open(ALCUNHA);
$criterio = new TCriteria;
$criterio->add(new TFilter('tipo_imovel', 'like', '%'.$tipo.'%'));

$sql = new TSqlSelect;
$sql->setEntity('imovel');
$sql->addColumn('tipo_imovel');
$sql->setCriteria($criterio);

$result= $conn->query($sql->getInstruction());
$cod = '<ul>';
if($result->rowCount()){
	for($i=0; $i<$result->rowCount(); $i++){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		$cod.= '<li>';
		$cod.= '<a href=\'#\' onclick="preencher(\''.$nome.'\');">'.$nome.'</a>';
		$cod.= '</li>';
	}
}
$cod.= '<ul>';

echo $cod;

mysqli_close($conexao);


?>