<?php
//header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");        // Expired already 
//header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//header("Cache-Control: no-cache, must-revalidate");      // good for HTTP/1.1 
//header("Pragma: no-cache"); 
include_once "../inc/funcoes.inc.php";
if(isset($_COOKIE["id_empresa"]) or isset($_GET["empresa"])){
	if(isset($_GET["empresa"])){
		$id_empresa = base64_decode($_GET["empresa"]);
	}else{
		$id_empresa = $_COOKIE["id_empresa"];
	}

	$conn = TConnection::open("gestor");

	$criterio = new TCriteria;
	$criterio->add(new TFilter("id", "=", $id_empresa));

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
	//includes do php para todas as paginas
	file_exists("../conecta.php") ?
	include_once "../conecta.php" : $conexao = null;
	$sql = query("select conectasrc, alcunha from cliente where id='$id_empresa'");
	extract(mysqli_fetch_assoc($sql));
	define("END_ARQ", CONST_ARQ."/".$alcunha."/");
	include_once "../".$conectasrc;
	//fim

}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}

?>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="shortcut icon" href="img/ico.jpg">
		<!-- META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
		<meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
		<!--<script type='text/javascript' src="js/jquery-1.9.1(dragAndDrop).js"></script>
		<script type='text/javascript' src="js/jquery-ui(dragAndDrop).js"></script>-->
		<script type='text/javascript' src='../js/funcoes.js'></script>
		<script type='text/javascript' src='../js/mascara.js'></script>
		<script type='text/javascript' src='../js/jquery.js'></script>
		<script type='text/javascript' src='../js/jquery.cookie.js'></script>
		<script type='text/javascript' src='../js/jquery.dimensions.js'></script>
		<script type='text/javascript' src='../js/chili-1.7.pack.js'></script>
		<style type="text/css">
			@import url(../css/tagsLogin.css);
			@import url(../css/classLogin.css);
			@import url(../css/idLogin.css);
		</style>
		<!--<script type="text/javascript">
			$(function() {
				var tamanho = parseInt($.cookie("fonte"));
				alert(tamanho);
				fontSet2(tamanho);
			});
			
			function fontSet2(tamanho){
				$("html").attr("style", "font-size: "+ tamanho +"px;");
				$("td").attr("style", "font-size: "+ tamanho +"px;");
				$("th").attr("style", "font-size: "+ tamanho +"px;");
				$("input").attr("style", "font-size: "+ tamanho +"px;");
			}
		</script>-->
	</head>
	<link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />
	<body style='margin:0px; padding:0px; background-color: #47a3da;'>
		<center>
