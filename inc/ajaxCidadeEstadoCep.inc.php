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


if($op=='cidades'){
	$sql= query("select * from cidades where estados_cod_estados='$variavel'");
	echo "Cidade<br><select name='cidade' onchange=\"ajax(this.value, 'cep')\"'>";
	for( $linha = mysqli_num_rows($sql); $linha>0;$linha--){
		extract(mysqli_fetch_assoc($sql));
		echo "<option value='$cod_cidades'>$nome</option>";
	}
	echo "</select>";
}else{
	$sql= query("select cep from cidades where cod_cidades='$variavel'");
	extract(mysqli_fetch_assoc($sql));
	$cep = $cep{0}.$cep{1}.$cep{2}.$cep{3}.$cep{4}."-".$cep{5}.$cep{6}.$cep{7};
	echo "CEP<br><input type='text' name='cep' value='$cep' onKeyDown='Mascara(this,Cep);' onKeyPress='Mascara(this,Cep);' onKeyUp='Mascara(this,Cep);'  maxlength='14'>";
}



mysqli_close($conexao);


?>