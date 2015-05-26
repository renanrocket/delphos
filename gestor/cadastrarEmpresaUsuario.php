<?php

$cod = "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>";
$cod .= "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'";
$cod .= "'http://www.w3.org/TR/html4/loose.dtd'>";

$cod .= "<html>";
	$cod .= "<head>";
	    $cod .= "<!--   META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->";
        $cod .= "<meta http-equiv='content-Type' content='text/html; charset=UTF-8' />";
        $cod .= "<script type='text/javascript' src='../js/funcoes.js'></script> <!-- Funções que criei no java script -->";
		$cod .= "<script type='text/javascript' src='../js/mascara.js'></script> <!-- Mascaras de filtros para tratamento de formularios -->";
		$cod .= "<script type='text/javascript' src='../plugins/ckeditor/ckeditor.js'></script> <!-- puglin do texto -->";
        
		$cod .= "<script type='text/javascript' src='../js/jquery-1.9.1.js'></script> <!-- biblioteca jquery com o datepicker -->";
		$cod .= "<script type='text/javascript' src='../plugins/TooltipMenu/js/modernizr.custom.js'></script> <!-- menu responsivo (existe a outra parte no css)-->";
		$cod .= "<script type='text/javascript' src='../js/jquery.dimensions.js'></script> <!-- dimensionar imagens -->";
		$cod .= "<script type='text/javascript' src='../js/chili-1.7.pack.js'></script> <!-- complementação do dimensionamento de imagens -->";
        
		$cod .= "<script type='text/javascript' src='../js/jquery.js'></script> <!-- biblioteca jquery 1.9.1 -->";
		$cod .= "<script type='text/javascript' src='../js/jquery-ui.js'></script> <!-- tentativa de implementar o datepicker -->";
        $cod .= "<style type='text/css'>";
            $cod .= "@import url(../css/tagsLogin.css);";
			$cod .= "@import url(../css/classLogin.css);";
			$cod .= "@import url(../css/idLogin.css);";
			$cod .= "@import url(../css/menu.css);";
			$cod .= "@import url(../css/jquery-ui.css);/* layout do plugin datepiker (ferramenta de data) */";
			$cod .= "@import url(../plugins/TooltipMenu/css/component.css); /* layout do plugin TooltipMenu (menu responsivo) */";
			$cod .= "@import url(../plugins/ResponsiveMultiColumnForm/css/component.css);";
        $cod .= "</style>";
		$cod .= "<link rel='shortcut icon' href='../img/ico.png'>";
		$cod .= "<title>Delphos :: Cadastro de empresa</title>";
	$cod .= "</head>";
	$cod .= "<body id='bodyLogin' style='padding-top:1em; text-align: center;'>";
	$cod .= "<img id='imgindex' src='../img/ico_white.png'><br>";
	


	$cod2 = "</body>";
	$cod2 .= "</html>";


	echo $cod;
	include "../inc/funcoes.inc.php";
	
	extract($_POST);
	extract($_GET);
	
	!isset($estagio)?$estagio=1:false;
	
	if($estagio == 1){
		
		echo "<form method='post' action='cadastrarEmpresaUsuario.php' name='form' class='form' enctype='multipart/form-data'>";
		echo "<div class='column'>";
		echo "<h2 style='white-space:nowrap;'>Para cadastrar sua empresa,<br>precisamos que preencha os<br>seguintes dados:</h2>";
		echo "<label for='usuarioNome'>Seu nome completo</label>";
		echo "<input type='text' name='usuarioNome' id='usuarioNome'>";
		echo "<label for='usuarioEmail'>E-mail</label>";
		echo "<input type='text' name='usuarioEmail' id='usuarioEmail'>";
		
		echo "</div>";
		echo "</form>";
		
	}elseif ($estagio ==2){
		$empresa = new empresa;
		$empresa->getForm();
	}elseif ($estagio == 3){
		
	}
	
	
	echo $cod2;

?>