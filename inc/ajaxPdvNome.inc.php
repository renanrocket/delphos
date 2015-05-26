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

if($tipo=="nome"){
	$SQL = query("select valor, id, referido from conta where tabela_referido='pdv' and status<>'4' order by id desc limit 2000");//4 igual a cancelado
	for($i=0, $valida = true; $i<mysqli_num_rows($SQL); $i++){
		extract(mysqli_fetch_assoc($SQL));
		$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		$valorParcial = round($valorParcial, 2);
		if($valorParcial<$valor and strtolower(registro($referido, "pdv", "nome"))==strtolower($nome) and $identidade!=$referido){
			$valida = false;
			$pdv = $referido;
		}
	}
	if(!$valida){
		$cod = "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=cadastrarPDV.php?pdv=".base64_encode($pdv)."'>";
		info("Já existe um pdv com esse nome ($nome) aberto.".$cod, "red");
	}else{
		if($identidade){
			$sql = query("update pdv set $tipo='$nome' where id='$identidade'");	
		}
	}
}else{
	$sql = query("update pdv set $tipo='$nome' where id='$identidade'");
}



mysqli_close($conexao);
?>