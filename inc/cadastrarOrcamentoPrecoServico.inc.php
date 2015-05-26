<?php
header('content-type: text/html; charset=ISO-8859-1');
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

$instrucao1="select * from servico_preco where id_servico='$idServico' and limite>='$qtd' order by limite asc";
$instrucao2="select * from servico_preco where id_servico='$idServico' order by limite desc";

$sql = query($instrucao1);
if(mysqli_num_rows($sql)==0){
	$sql = query($instrucao2);
}

extract(mysqli_fetch_assoc($sql));
$js = "calcularTotal();";
echo "<input type='text' name='subTotal[]' value='".real($valor)."' class='totalValor preco' id='subTotal_".$idInput."' ";
echo mascara("Valor2", null, "autocomplete='off' onblur='$js'").">";

mysqli_close($conexao);
?>