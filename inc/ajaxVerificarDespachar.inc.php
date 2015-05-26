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
extract($_GET);

$conn = TConnection::open(ALCUNHA);


$criterio = new TCriteria;
$criterio->add(new TFilter('id_conta', '=', $conta));

$sql = new TSqlSelect;
$sql->setEntity('conta_itens');
$sql->addColumn('sum(valor)');
$sql->setCriteria($criterio);
$instrucao = '('.$sql->getInstruction().')';

$criterio = new TCriteria;
$criterio->add(new TFilter('id', '=', $conta));
$criterio->add(new TFilter('valor', '<=', $instrucao));

$sql = new TSqlSelect;
$sql->setEntity('conta');
$sql->addColumn('*');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
if($result->rowCount()){
	$row = $result->fetch(PDO::FETCH_ASSOC);
	extract($row);

	$criterio = new TCriteria;
	$criterio->add(new TFilter('id_pdv', '=', $referido));

	$sql = new TSqlUpdate;
	$sql->setEntity('pdv_itens');
	$sql->setRowData('despachado', 1);
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());


	$historico_msg = "Despachou os itens do pdv ".registro($referido, 'pdv', 'nome');
	$historico = new historico(null, 'pdv_itens', $referido, $historico_msg);
	$historico->update();
}






mysqli_close($conexao);
?>