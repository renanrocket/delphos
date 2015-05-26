<?php
extract($_GET);
if($op!="valorPlano"){
?>
<style>
	div {
		text-align: left;
		font-size: 13px;
		font-weight: normal;
		color: #339;
		font-family: helvetica, arial;
	}
</style>
<?php
}
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

$sql = query("select * from plano_assinatura where id='$Plano'");
extract(mysqli_fetch_assoc($sql));
if($op=="plano"){
	$codPlano = "<div>";
	$codPlano .= "$descricao<br>";
	$codPlano .= "Possui <b>$dias_validade dias</b> de Plano<br>";
	$codPlano .= "Valor total <b>R$ " . real($valor) . "</b><br>";
	if(intval($dias_validade / 30)>1){
		$codPlano .= "Dividido em até <b>" . intval($dias_validade / 30) . " vezes</b> de <b>R$ " . real($valor / intval($dias_validade / 30)) . "</b>";	
	}
	$codPlano .= "</div>";
	echo $codPlano;	
}elseif($op=="dataTermino"){
	$data_termino = date('d/m/Y', strtotime("+ $dias_validade days"));
	echo "Data final do Plano<br> $data_termino";
}elseif($op=="valorPlano"){
	echo real($valor);
}


mysqli_close($conexao);
?>