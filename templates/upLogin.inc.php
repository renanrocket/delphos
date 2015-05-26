<?php
header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");
// Expired already
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
// good for HTTP/1.1
header("Pragma: no-cache");
//header("Cache: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="shortcut icon" href="img/ico.jpg">
<!--  META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
<meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script type='text/javascript' src='js/funcoes.js'></script> <!-- Funções que criei no java script -->
<script type='text/javascript' src='js/mascara.js'></script> <!-- Mascaras de filtros para tratamento de formularios -->
<script type='text/javascript' src='plugins/ckeditor/ckeditor.js'></script> <!-- puglin do texto -->

<script type='text/javascript' src='js/jquery-1.9.1.js'></script> <!-- biblioteca jquery com o datepicker -->
<script type='text/javascript' src='plugins/TooltipMenu/js/modernizr.custom.js'></script> <!-- menu responsivo (existe a outra parte no css)-->
<script type='text/javascript' src='js/jquery.dimensions.js'></script> <!-- dimensionar imagens -->
<script type='text/javascript' src='js/chili-1.7.pack.js'></script> <!-- complementação do dimensionamento de imagens -->

<script type='text/javascript' src='js/jquery.js'></script> <!-- biblioteca jquery 1.9.1 -->
<!--<script src="http://code.jquery.com/jquery-1.8.2.min.js" type="text/javascript"></script> <!-- chat e biblioteca jquery -->
<script type='text/javascript' src='js/jquery-ui.js'></script> <!-- tentativa de implementar o datepicker -->


<style type="text/css">
	@import url(css/tagsLogin.css);
	@import url(css/classLogin.css);
	@import url(css/idLogin.css);
	@import url(css/menu.css);
	@import url(css/jquery-ui.css);/* layout do plugin datepiker (ferramenta de data) */
	@import url(plugins/TooltipMenu/css/component.css); /* layout do plugin TooltipMenu (menu responsivo) */
	@import url(plugins/ResponsiveMultiColumnForm/css/component.css);
</style>


<script type="text/javascript">

	//evitar que o usuario feche a pagina antes dela ter sido carregada totalmente.
    function confirmSair(message) {
      this.message = message;
      this.confirm = true;

      var myself = this;

      window.onbeforeunload = function() {
         if (myself.confirm) {
           return myself.message;
         }
       }
    }
    var sairPagina = new confirmSair("Existe processos pendentes na página.\n Seu fechamento antecipado pode acarretar em problemas.");

	$(function() {
		
		if (navigator.appName == 'Microsoft Internet Explorer') {
			alert('Desculpe pelo transtorno, mas seu navegador é inapropriado!\n Aconselhamos utilizar o Mozila Firefox ou Google Chrome para utilização do sistema.\n Caso contrário muitas das funções do sistema não estarão disponíveis.');
		}
		
		$('a').tooltip({
			track : true,
			delay : 0,
			showURL : false,
			showBody : " - ",
			fade : 250
		});
		$( 'a' ).tooltip( "option", "position", { my: "left+30 center", at: "right center" } );
		

		$(".inputValor").attr("readonly", "true");
		$(".valor").removeAttr("readonly");
		
		

		var intervaloAlerta = setInterval(function(){
			if($(".alerta").attr("id")=="alerta_red" || $(".alerta").attr("id")==undefined){
				$(".alerta").attr("id", "alerta_red1");
			}
			
			if($(".alerta").attr("id")=="alerta_white"){
				$(".alerta").attr("id", "alerta_red");
			}
			if($(".alerta").attr("id")=="alerta_red1"){
				$(".alerta").attr("id", "alerta_white");
			}
			
			
		}, 400);

		$('#info').draggable();

	});

	function infoApagar() {
		$(".info").hide();
		$(".infoBack").hide();
	}


	
</script>
<title>
<?php
include "inc/funcoes.inc.php";
if(isset($_COOKIE["id_empresa"])){
	//includes do php para todas as paginas
	$connGestor = TConnection::open("gestor");

	$criterio = new TCriteria;
	$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

	$sql = new TSqlSelect;
	$sql->setEntity("cliente");
	$sql->addColumn('alcunha');
	$sql->setCriteria($criterio);
	$result = $connGestor->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		define("ALCUNHA", $alcunha);
	}

	//deletar esse codigo apois migração do app.ado
	file_exists("conecta.php") ? include "conecta.php" : $conexaoMaster = null;
	
	$sql = query("select conectasrc, alcunha from cliente where id='".$_COOKIE["id_empresa"]."'");
	extract(mysqli_fetch_assoc($sql));
	define("END_ARQ", CONST_ARQ."/".$alcunha."/");
	include $conectasrc;
	//fim
}else{
	echo "<body>";
	echo "Impossível se conectar com o banco de dados.";
	echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=index.php?end=".$_SERVER['REQUEST_URI']."'>";
	echo "</body></html>";
	die;
}


//caso não haja conexão retornar para index principal
if (!$conexao) {
	echo "</title></head>";
	echo "<body>";
	include "inc/msgErro.inc";
	echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=index.php?end=".$_SERVER['REQUEST_URI']."'>";
	echo "</body></html>";
	die ;
}
/*
// error handler function

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		case E_USER_ERROR :
			include_once "inc/msgErro.inc";
			exit(1);
			break;

		case E_USER_WARNING :
			include_once "inc/msgErro.inc";
			break;

		case E_USER_NOTICE :
			include_once "inc/msgErro.inc";
			break;

		default :
			include_once "inc/msgErro.inc";
			break;
	}

	//Don't execute PHP internal error handler
	return true;
}

// set to the user defined error handler

$old_error_handler = set_error_handler("myErrorHandler");

error_reporting(0);
*/

$titulo = explode("/", $_SERVER['PHP_SELF']);
count($titulo) > 0 ? $linha = (count($titulo) - 1) : $linha = 0;

$sql = query("select nome as Titulo, ferramenta as Img from ferramentas where ferramenta='" . $titulo[$linha] . "'");
if (mysqli_num_rows($sql) == 0) {
	$Titulo = "Delphos";
	$codImg = "";
} else {
	extract(mysqli_fetch_assoc($sql));
	$Img = explode(".php", $Img);
	$codImg = "<img id='imgMenuBg' src='img/icones/" . $Img[0] . ".png'>";
}

echo $Titulo;

//set hora certa
date_default_timezone_set('America/Sao_Paulo');

//verificando se existe credencial
$pagina = explode("2", $titulo[$linha]);
$exception = array("indexUsuario.php", "implantacao.php");
if (!isset($_COOKIE["login"]) and !in_array($pagina[0], $exception)/* $pagina[0] != "indexUsuario.php"*/) {
	$_COOKIE["login"] = "";
} elseif (!isset($_COOKIE["login"]) and in_array($pagina[0], $exception) /*$pagina[0] == "indexUsuario.php"*/) {
	echo "</title></head>";
	include "redirecionamento.php";
	die ;
}

if (!getCredencialUsuario($pagina[0]) and !in_array($pagina[0], $exception)) {
	echo "</title></head>";
	include "redirecionamento.php";
	die ;
}

//verificando validade do sistema
$sql = query("select valor, email_aviso from registro where id=(select max(id) from registro)");
if (mysqli_num_rows($sql) > 0) {
	extract(mysqli_fetch_assoc($sql));
	$chave = new chave;
	$chave -> valor = $valor;
	if ($chave -> verificar_valor_valido()) {
		$chave -> decodificar_atribuir();
		if (!($chave -> verificar_validade())) {
			echo "</title></head>";
			include "inserirChave.php";
			die ;
		}
	} else {
		echo "</title></head>";
		include "inserirChave.php";
		die ;
	}
} else {
	echo "</title></head>";
	include "inserirChave.php";
	die ;
}

?>
</title>
</head>
<link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
<link rel="shortcut icon" href="img/ico.png" />
<body style="background-color:#47A3DA;">
		<?php

		function menu($buscarMain, $idMax = 31) {
			$count = count($buscarMain);
			for ($i = 0; $i < $count; $i++) {

				if (!is_array($buscarMain[$i])) {
					$instrucao = "select ferramenta from credenciais where ";
					$instrucao .= "ferramenta='$buscarMain[$i]' ";
					$instrucao .= "and ferramenta = any(select ferramenta from ferramentas where id<=$idMax)";
					$instrucao .= "and id_usuario='" . registro($_COOKIE["login"], "usuario", "id", "login") . "' group by ferramenta";
					$sqlMain = query($instrucao);
					if (mysqli_num_rows($sqlMain)) {
						extract(mysqli_fetch_assoc($sqlMain));
						$img = explode(".", $ferramenta);

						echo "<li><a style='line-height: 20px; vertical-align: middle;' href='$ferramenta'>";
						echo "<img src='img/icones/$img[0].png' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
						echo registro($ferramenta, "ferramentas", "nome", "ferramenta") . "</a>";
						echo "</li>";
					}

				} else {

					$instrucao = "select ferramenta from credenciais where ";
					$instrucao .= "ferramenta='" . $buscarMain[$i][0] . "' ";
					$instrucao .= "and ferramenta = any(select ferramenta from ferramentas where id<=$idMax)";
					$instrucao .= "and id_usuario='" . registro($_COOKIE["login"], "usuario", "id", "login") . "' group by ferramenta";
					$sqlMain = query($instrucao);
					if (mysqli_num_rows($sqlMain)) {
						extract(mysqli_fetch_assoc($sqlMain));
						$img = explode(".", $ferramenta);

						echo "<li><a style='line-height: 20px; vertical-align: middle;' href='$ferramenta'>";
						echo "<img src='img/icones/$img[0].png' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
						echo registro($ferramenta, "ferramentas", "nome", "ferramenta") . "</a></li>";
						

						for ($j = 1; $j < count($buscarMain[$i]); $j++) {
							if ($buscarMain[$i][0] == "relatorioMapaProducao.php") {
								$statusOS = explode(", ", $buscarMain[$i][$j]);
								for ($l = 0; $l < count($statusOS); $l++) {
										
									echo "<li><a style='line-height: 20px; vertical-align: middle; ";
									echo "background-color: " . registro($statusOS[$l], "ordem_servico_status", "cor_bg");
									echo "; color: " . registro($statusOS[$l], "ordem_servico_status", "cor_font") . "' ";
									echo "href='relatorioMapaProducao.php?statusSelect=" . base64_encode($statusOS[$l]) . "'>";
									echo registro($statusOS[$l], "ordem_servico_status", "nome");
									echo "</a></li>";
								}

							} else {

								$instrucao = "select ferramenta from credenciais where ";
								$instrucao .= "ferramenta='" . $buscarMain[$i][$j] . "' ";
								$instrucao .= "and ferramenta = any(select ferramenta from ferramentas where id<=$idMax)";
								$instrucao .= "and id_usuario='" . registro($_COOKIE["login"], "usuario", "id", "login") . "' group by ferramenta";
								$sqlMain = query($instrucao);
								if (mysqli_num_rows($sqlMain)) {
									extract(mysqli_fetch_assoc($sqlMain));
									$img = explode(".", $ferramenta);
									if ($i - 1 == count($buscarMain[$i])) {
										$class = "class='last'";
									} else {
										$class = "";
									}
									echo "<li><a style='line-height: 20px; vertical-align: middle;' href='$ferramenta'>";
									echo "<img src='img/icones/$img[0].png' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
									echo registro($ferramenta, "ferramentas", "nome", "ferramenta");
									echo "</a></li>";
								}
							}
						}
					}
				}
			}

		}

		echo "<div>";

		//verifição do modulo para poder exibir o menu ordem de serviço caso exista
		if ($chave -> modulo <> "basico") {
			$idMax = 100;
		} else {
			$idMax = 32;
		}

		echo "<ul id='cbp-tm-menu' class='cbp-tm-menu'>";
		$sql = query("select nome as Nome, id as idEmpresa, imgsrc from empresa where usarTimbrado='1'");
		if (mysqli_num_rows($sql) == 1) {

			echo "<li>";
			extract(mysqli_fetch_array($sql));
			echo "<a href='indexUsuario.php'>";
			//echo "<img style='height:2em; margin:2px;' src='$imgsrc'>";
			echo "Inicio";
			echo "</a>";
			echo "</li>";

			$main = array( array("cadastrarOrcamento.php", "pesquisaOrcamento.php"), 
				array("cadastrarPDV.php", "pesquisaPDV.php"), 
				array("cadastrarTrocaPonto.php", "pesquisaTrocaPonto.php"), 
				array("cadastrarOrdemServico.php", "pesquisaOrdemServico.php"), 
				array("cadastrarMatricula.php", "pesquisaMatricula.php"),
				array("cadastrarVendaAluga.php", "pesquisaVendaAluga.php"));
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>Comercial</a>";
				echo "<ul class='cbp-tm-submenu'>";
				menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}
			
			$main = array( array("cadastrarCaixa.php", "pesquisaCaixa.php"), 
				array("cadastrarConta.php", "pesquisaConta.php"));
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>Finanças</a>";
				echo "<ul class='cbp-tm-submenu'>";
					menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}

			$main = array( array("cadastrarClienteFornecedor.php", "pesquisaClienteFornecedor.php"), 
				array("cadastrarEmpresa.php", "pesquisaEmpresa.php"),
				 array("cadastrarProduto.php", "pesquisaProduto.php"), 
				 array("cadastrarMProduto.php", "pesquisaMProduto.php"), 
				 array("cadastrarPlanoAssinatura.php", "pesquisaPlanoAssinatura.php"), 
				 array("cadastrarExercicio.php", "pesquisaExercicio.php"), 
				 array("cadastrarServico.php", "pesquisaServico.php"), 
				 array("cadastrarUsuario.php", "pesquisaUsuario.php"),
				 array("cadastrarImovel.php", "pesquisaImovel.php"));
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>Ferramentas</a>";
				echo "<ul class='cbp-tm-submenu'>";
				menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}

			$mapas = null;
			if ($chave -> modulo == "ordemServico") {
				$sql = query("select id as Mapas from ordem_servico_status where status='1' and id<>'2' and id<>'3'");
				for ($j = 0; $j < mysqli_num_rows($sql); $j++) {
					if ($j != 0) {
						$mapas .= ", ";
					}
					extract(mysqli_fetch_assoc($sql));
					$mapas .= $Mapas;
				}
			}

			$main = array(array("relatorioMapaProducao.php", $mapas));
			if (getCredencialUsuario("relatorioMapaProducao.php")){
				echo "<li>";
				echo "<a href='#'>Mapas</a>";
				echo "<ul class='cbp-tm-submenu'>";
				menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}
			
			$main = array("relatorioMovimentoCaixa.php", "relatorioHistoricoVendas.php", "relatorioEstoque.php", "relatorioFrequenciaCliente.php", "relatorioBalancoConta.php", "relatorioProducaoUsuario.php", "relatorioProducaoEmpresa.php");
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>Relatório</a>";
				echo "<ul class='cbp-tm-submenu'>";
				menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}
			
			/*
			$main = array("rhFolhaPagamento.php", "rhFrequenciaUsuario.php", "rhProcedimentos.php", "rhFormularios.php");
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>RH</a>";
				echo "<ul class='cbp-tm-submenu'>";
				menu($main, $idMax);
				echo "</ul>";
				echo "</li>";
			}*/

			$main = array("administrativoToken.php", "administrativoPagamento.php", 
				"administrativoServicoStatus.php", "administrativoOrdemServicoAtributoPadrao.php", 
				"administrativoAnamneseAtributoPadrao.php", "administrativoEtiqueta.php", 
				"administrativoSuporte.php");
			if (getCredencialUsuario($main)) {
				echo "<li>";
				echo "<a href='#'>Administrativo</a>";
				echo "<ul class='cbp-tm-submenu'>";
				if (getCredencialUsuario("administrativoToken.php")) {
					echo "<li>";
					echo "<a href='cadastrarEmpresa.php?id=$idEmpresa&op=visualizar'>";
					echo "<img src='$imgsrc' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
					echo "Editar dados da Empresa</a>";
					echo "</li>";
				}
				menu($main, $idMax);
				
				echo "</ul>";
				echo "</li>";
			}

		} else {
			echo "<li>";
			echo "<a href='cadastrarEmpresa.php' title='Por favor cadastre sua empresa para que você possa acessar todas as funcionalidades do sistema.'>";
			echo "Cadastre sua Empresa";
			echo "</a></li>";
		}

		echo "<li>";
		echo "<a href='#'>" . getNomeCookieLogin($_COOKIE["login"], false) . "</a>";
		echo "<ul class='cbp-tm-submenu'>";
		echo "<li>";
		echo "<a href='cadastrarUsuario.php?op=visualizar&ID=";
		echo base64_encode(getIdCookieLogin($_COOKIE["login"])) . "'>";
		echo "<img src='img/icones/pesquisaUsuario.png' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
		echo "Editar seus dados";
		echo "</a>";
		echo "</li>";

		echo "<li>";
		echo "<a href='logout.php'>";
		echo "<img src='img/icones/sair.png' class='imgFerramenta' style='height:1em; display:block;margin:0px;padding:0px;'>";
		echo "Sair</a>";
		echo "</li>";
		echo "</ul>";
		echo "</li>";

		echo "<li>";
		
		if ($chave -> verificar_validade_aviso() == "aviso1") {
			echo "<a href='inserirChave.php?op=novaChave' title='Sua chave está prestes a expirar. Insira uma nova!'>Aviso! <img id='alerta' src=\"img/exclamacao.png\" style='width:25px;'></a>";
			extract(mysqli_fetch_assoc(query("select valor, email_aviso from registro where id=(select max(id) from registro)")));
			if(!$email_aviso){
				
				//variaveis para serem substituidas nos emails de envio
				$empresaNome = registro(1, "empresa", "nome");
				$empresaEmail = registro(1, "empresa", "email");
				$adminNome = registro(2, "usuario", "nome");
				$adminEmail = registro(2, "usuario", "email");
				$urlServidor = $_SERVER['HTTP_HOST'];

				//variaveis do email
				$sql = query("select * from email where id='1'", $conexaoMaster);
				extract(mysqli_fetch_assoc($sql));
				$assunto1 = $assunto;
				$sql2= query("select * from email_variaveis where id_email='1'", $conexaoMaster);
				for($i=0; $i<mysqli_num_rows($sql2); $i++){
					extract(mysqli_fetch_assoc($sql2));
					$email = str_replace($buscar, $$trocar, $email);
				}
				$email1 = $email;
				$sql = query("select * from email where id='2'", $conexaoMaster);
				extract(mysqli_fetch_assoc($sql));
				$assunto2 = $assunto;
				$sql2= query("select * from email_variaveis where id_email='2'", $conexaoMaster);
				for($i=0; $i<mysqli_num_rows($sql2); $i++){
					extract(mysqli_fetch_assoc($sql2));
					$email = str_replace($buscar, $$trocar, $email);	
				}
				$email2 = $email;
				
				//marcando destinatario
				$destinatario = "";
				if($empresaEmail){
					$destinatario .= $empresaEmail;
				}
				if($empresaEmail!= $adminEmail){
					if($empresaEmail and $adminEmail){
						$destinatario .= ",";
					}
					if($adminEmail){
						$destinatario .= $adminEmail;
					}
				}
				//enviando os email
				$email = new enviar_email(EMAIL_ROCKET, $destinatario, $assunto1, $email1);
				if($email->enviarEmail()){
					$valida = true;
				}else{
					$valida = false;
				}
				

				if($valida){
					$email = new enviar_email(EMAIL_ROCKET, EMAIL_ROCKET, $assunto2, $email2);
					$email->enviarEmail();
					extract(mysqli_fetch_assoc(query("select max(id) as idRegistro from registro")));
					$sql = query("update registro set email_aviso='1' where id='$idRegistro'");
				}
				
			}
		}elseif($chave -> verificar_validade_aviso() == "aviso2"){
			$dias = $chave->contar_dias() + $chave->dias;
			echo "<a href='inserirChave.php?op=novaChave' class='alerta' title='Você tem $dias dia(s) de acesso. Por favor, renove sua chave!'>Sua chave expirou!</a>";

			extract(mysqli_fetch_assoc(query("select valor, email_aviso from registro where id=(select max(id) from registro)")));
			if($email_aviso<2){
				
				//variaveis para serem substituidas nos emails de envio
				$empresaNome = registro(1, "empresa", "nome");
				$empresaEmail = registro(1, "empresa", "email");
				$adminNome = registro(2, "usuario", "nome");
				$adminEmail = registro(2, "usuario", "email");
				$urlServidor = $_SERVER['HTTP_HOST'];

				//variaveis do email
				$sql = query("select * from email where id='3'", $conexaoMaster);
				extract(mysqli_fetch_assoc($sql));
				$assunto1 = $assunto;
				$sql2= query("select * from email_variaveis where id_email='3'", $conexaoMaster);
				for($i=0; $i<mysqli_num_rows($sql2); $i++){
					extract(mysqli_fetch_assoc($sql2));
					$email = str_replace($buscar, $$trocar, $email);
				}
				$email1 = $email;
				$sql = query("select * from email where id='4'", $conexaoMaster);
				extract(mysqli_fetch_assoc($sql));
				$assunto2 = $assunto;
				$sql2= query("select * from email_variaveis where id_email='4'", $conexaoMaster);
				for($i=0; $i<mysqli_num_rows($sql2); $i++){
					extract(mysqli_fetch_assoc($sql2));
					$email = str_replace($buscar, $$trocar, $email);	
				}
				$email2 = $email;
				
				//marcando destinatario
				$destinatario = "";
				if($empresaEmail){
					$destinatario .= $empresaEmail;
				}
				if($empresaEmail!= $adminEmail){
					if($empresaEmail and $adminEmail){
						$destinatario .= ",";
					}
					if($adminEmail){
						$destinatario .= $adminEmail;
					}
				}
				//enviando os email
				$email = new enviar_email(EMAIL_ROCKET, $destinatario, $assunto1, $email1);
				if($email->enviarEmail()){
					$valida = true;
				}else{
					$valida = false;
				}
				

				if($valida){
					$email = new enviar_email(EMAIL_ROCKET, EMAIL_ROCKET, $assunto2, $email2);
					$email->enviarEmail();
					extract(mysqli_fetch_assoc(query("select max(id) as idRegistro from registro")));
					$sql = query("update registro set email_aviso='2' where id='$idRegistro'");
				}
				
			}
		}
		echo "</li>";
		echo "</ul>";
		echo "</div>";
		
	?>

<script type='text/javascript' src='plugins/TooltipMenu/js/cbpTooltipMenu.min.js'></script> <!-- menu responsivo (existe a outra parte no css)-->
<script>var menu = new cbpTooltipMenu(document.getElementById('cbp-tm-menu'));</script>
<center>





