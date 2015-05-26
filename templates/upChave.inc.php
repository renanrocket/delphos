<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="shortcut icon" href="img/ico.jpg">
		<!--  META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
		<meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
		<script type='text/javascript' src='js/jquery.js'></script>
		<style type="text/css">
			@import url(css/tagsLogin.css);
			@import url(css/classLogin.css);
			@import url(css/idLogin.css);
			@import url(css/menu.css);
			@import url(plugins/ResponsiveMultiColumnForm/css/component.css);
		</style>
		<title>
		<?php

        //includes do php para todas as paginas
        include_once "inc/funcoes.inc.php";
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
			file_exists("conecta.php") ?
			include_once "conecta.php" : $conexao = null;
			$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
			extract(mysqli_fetch_assoc($sql));
			include_once "".$conectasrc;
			//set hora certa
	        date_default_timezone_set('America/Sao_Paulo');
?>
		Delphos - Chave
	</title>
</head>
<?php
		}else{
?>
		Delphos - Chave
	</title>
</head>
<body id='bodyLogin' style='padding-top:1em; text-align: center;'>
<?php
			echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=index.php?end=".$_SERVER['REQUEST_URI']."'>";
			echo "<div class='form'><div class='column'><label>Impossível se conectar com o banco de dados.</label></div></div>";
			die;
		}
?>
        

