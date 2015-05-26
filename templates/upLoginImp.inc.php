<?php
header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");
// Expired already
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
// good for HTTP/1.1
header("Pragma: no-cache");
//header("Cache: no-cache");
if(isset($_GET["empresa"])){
	setcookie("id_empresa", base64_decode($_GET["empresa"]));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html> 
	<head>
		<title>
		<?php
        
        //includes do php para todas as paginas

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
        
        $titulo = explode("/", $_SERVER['PHP_SELF']);
        count($titulo)>0 ? $linha = (count($titulo) - 1) : $linha = 0;
        
        $sql = query("select nome as Titulo from ferramentas where ferramenta='".$titulo[$linha]."'");
        if(mysqli_num_rows($sql)==0){
            $Titulo = "Delphos";
        }else{
            extract(mysqli_fetch_assoc($sql));
        }
        echo $Titulo;
		//set hora certa
		date_default_timezone_set('America/Sao_Paulo');
		
		?>
		</title>
		<!-- 	META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
		<meta http-equiv="content-Type" content="text/html; charset=UTF-8" /> 
        <style type="text/css">
            @import url(../css/cssImp.css);
            @import url(../css/jquery-ui.css);
        </style>
        
        <script type='text/javascript' src='../js/funcoes.js'></script> <!-- Funções que criei no java script -->
		<script type='text/javascript' src='../js/mascara.js'></script> <!-- Mascaras de filtros para tratamento de formularios -->
		<script type='text/javascript' src='../plugins/ckeditor/ckeditor.js'></script> <!-- puglin do texto -->

		<script type='text/javascript' src='../js/jquery-1.9.1.js'></script> <!-- biblioteca jquery com o datepicker -->
		<script type='text/javascript' src='../plugins/TooltipMenu/js/modernizr.custom.js'></script> <!-- menu responsivo (existe a outra parte no css)-->
		<script type='text/javascript' src='../js/jquery.dimensions.js'></script> <!-- dimensionar imagens -->
		<script type='text/javascript' src='../js/chili-1.7.pack.js'></script> <!-- complementação do dimensionamento de imagens -->

		<script type='text/javascript' src='../js/jquery.js'></script> <!-- biblioteca jquery 1.9.1 -->
		<script type='text/javascript' src='../js/jquery-ui.js'></script> <!-- tentativa de implementar o datepicker -->

		<script type='text/javascript' src='../js/jquery.timer.js'></script> <!-- contador  -->
		<script type="text/javascript">
			$(function(){
				$('a').tooltip({
					track : true,
					delay : 0,
					showURL : false,
					showBody : " - ",
					fade : 250
				});
			});
		</script>
	</head>
	<body>
	<link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />