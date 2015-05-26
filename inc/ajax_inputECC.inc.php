<?php
include_once 'funcoes.inc.php';
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

try{
	$conn = TConnection::open(ALCUNHA);
	
	$criterio = new TCriteria;
	if($op=="cidade"){
		$criterio->add(new TFilter("estados_cod_estados", "=", $valor));
	}elseif($op=="cep"){
		$criterio->add(new TFilter("cod_cidades", "=", $valor));
	}
	$criterio->setProperty("order", "nome");
	
	
	$sql = new TSqlSelect;
	$sql->setEntity("cidades");
	$sql->addColumn("*");
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	if($result){
		$linha = $result->rowCount(); //quantidade de linhas do resultado
		for($i=0; $i<$linha; $i++){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
			if($op=="cidade"){
				if($i==0){
					echo "<select name='$nomeInput' onchange=\"mudaECC(this.value, 'cep', '$nomeInputCep', 'null')\">";
				}
				echo "<option value='$cod_cidades'>$nome</option>";
				if($i+1>$linha){
					echo "</select>";
				}
			}elseif($op=="cep"){
				$cep = $cep{0}.$cep{1}.$cep{2}.$cep{3}.$cep{4}."-".$cep{5}.$cep{6}.$cep{7};
				echo "<input type='text' name='$nomeInput' value='$cep' ".mascara("Cep", 9).">";
			}
		}
	}
	
}catch(PDOException $e){
	echo "Erro!: " . $e->getMessage() . "<br>";
	die();
}








?>