<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
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

	$instrucao = "select valor, id, referido from conta where tabela_referido='pdv' and status<>'4' and referido = any (select id from pdv where nome='$pdv' or nome like '% $pdv')";

	$SQL = query($instrucao);
	$qtd = mysqli_num_rows($SQL);
	for($i=0; $i<$qtd; $i++){
		extract(mysqli_fetch_assoc($SQL));
		$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		$valorParcial = round($valorParcial, 2);
		if($valorParcial<$valor){
			
			if($qtd and $op=="novo"){
				$id = registro($id, "conta", "referido");
				echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=cadastrarPDV.php?pdv=".base64_encode($id)."'>";
			}
		
		}
	}
	if($qtd==0 and $op=="editar"){
		echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=cadastrarPDV.php?pdvSelected=$pdv'>";
	}
?>