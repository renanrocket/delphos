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

$link = "pesquisaConta.php?buscarPor=$buscarPor&tipo=$tipo&empresa=$empresa&data1=$data1&data2=$data2&quitacao=$quitacao&pesquisa=$pesquisa&entidade=$entidade&referido=$referido&doc=$doc&planoConta=$planoConta&numeroConta=$numeroConta";
$cod = null;
for($i=($pag-6); $i<=$pag-1; $i++){
	while($i<0){
		$i++;	
	}
	if($pag!=$i){
		$instrucao2 = " limit ".($i*10).",10";
		$sql = query($instrucao.$instrucao2);
		if(mysqli_num_rows($sql)>0){
			$cod.= "<a href='$link&pag=$i' class='aSubmit'>$i</a>";
		}
	}
}
$cod.= "<a href='#' class='aSubmit'>Atual</a>";
for($i=($pag+1); $i<$pag+6; $i++){
	if($pag!=$i){
		$instrucao2 = " limit ".($i*10).",10";
		$sql = query($instrucao.$instrucao2);

		if(mysqli_num_rows($sql)>1){
			$cod.= "<a href='$link&pag=$i' class='aSubmit'>$i</a>";
		}
	}
}

echo $cod;

mysqli_close($conexao);
?>