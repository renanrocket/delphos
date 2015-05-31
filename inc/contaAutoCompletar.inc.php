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

extract($_GET);
$nome = utf8_decode($nome);
if($op == "completarEntidade"){
	$instrucao1 = "select * from cliente_fornecedor where nome like '%$nome%'";
	$instrucao2 = "select * from usuario where nome like '%$nome%'";
}elseif($op=="completarReferido"){
	$instrucao1 = "select * from conta where referido like '%$nome%' or tabela_referido like '%$nome%'";
}elseif($op=="completarContaPlano"){
	$instrucao1 = "select * from conta_plano where nome like '%$nome%'";
	
}

$sql = query($instrucao1);
$li = "li1";
for($i=0;$i<mysqli_num_rows($sql);$i++){
	extract(mysqli_fetch_assoc($sql));
	
	if($op == "completarEntidade"){
		$cod = "<li class='$li' onclick=\"showNome('$nome', 'preencherEntidade');\">";
		switch ($tipo) {
			case 'f':
				$cod .="$nome</li>";
				break;
			
			default:
				$cod .="$nome / $razao_social</li>";
				break;
		}
	}elseif($op=="completarReferido"){
		$cod = "<li class='$li' onclick=\"showNome('$referido', 'preencherReferido');\">";
		$cod.= "$referido</li>";
	}elseif($op=="completarContaPlano"){
		$cod = "<li class='$li' onclick=\"showNome('$nome', 'preencherContaPlano');\">";
		$cod.= "$nome</li>";
	}
	
	echo $cod;
	if($li=="li1"){
		$li= "li2";
	}else{
		$li="li1";
	}
}
if(isset($instrucao2)){
	$sql = query($instrucao2);
	$li = "li1";
	for($i=0;$i<mysqli_num_rows($sql);$i++){
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<li class='$li' onclick=\"showNome('$nome', 'preencherEntidade');\">";
		$cod .="$nome</li>";
		echo $cod;
		if($li=="li1"){
			$li= "li2";
		}else{
			$li="li1";
		}
	}
}


mysqli_close($conexao);
?>