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

#filtro
$valida = "true";

if (strlen($doc) < 14 and $tipo == "f") {
	$valida = "false";
}
if (strlen($doc) < 18 and $tipo == "j") {
	$valida = "false";
}
if (!validaCNPJ($doc) and $tipo == "j") {
	$valida = "false";
}
if (!validaCPF($doc) and $tipo == "f") {
	$valida = "false";
}

if($id) {
	$instrucao = "select * from cliente_fornecedor where cpf_cnpj='$doc' and id<>'$id'";
}else{
	$instrucao = "select * from cliente_fornecedor where cpf_cnpj='$doc'";
}
$sql = query($instrucao);
mysqli_num_rows($sql) > 0 ? $valida = "false" : $valida;

echo $valida;

mysqli_close($conexao);
?>