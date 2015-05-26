<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
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
	
	extract($_GET);
	
	if($id!="0"){
		$instrucao = "and id<>'$id'";
	}else{
		$instrucao = "";
	}
	
	$sqlServico = query("select nome as nomeCheck from servico where nome='$nome' ". $instrucao);
	$linha = mysqli_num_rows($sqlServico);
	$sqlProduto = query("select nome as nomeCheck from produto where nome='$nome'");
	$linha += mysqli_num_rows($sqlProduto);
	if($linha>0){
		echo "false";
	}else{
		echo "true";
	}
	
	
	mysqli_close($conexao);
?>