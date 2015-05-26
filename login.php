<?php
header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");        // Expired already 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");      // good for HTTP/1.1 
header("Pragma: no-cache");
define("WP_MAX_MEMORY_LIMIT", "512M");
//header("Cache: no-cache");



$cod = "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>";
$cod .= "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'";
$cod .= "'http://www.w3.org/TR/html4/loose.dtd'>";

$cod .= "<html>";
	$cod .= "<head>";
	    $cod .= "<!--   META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->";
        $cod .= "<meta http-equiv='content-Type' content='text/html; charset=UTF-8' />";
        $cod .= "<style type='text/css'>";
            $cod .= "@import url(css/tagsLogin.css);";
            $cod .= "@import url(css/classLogin.css);";
            $cod .= "@import url(css/idLogin.css);";
            $cod .= "@import url(plugins/ResponsiveMultiColumnForm/css/component.css);";
        $cod .= "</style>";
		$cod .= "<script type='text/javascript' src='js/jquery.js'></script> <!-- biblioteca jquery -->";
		$cod .= "<link rel='shortcut icon' href='img/ico.png'>";
		$cod .= "<script type='text/javascript'>";
		
			$cod .= "function verificarSugestao(valor){";

				$cod .= "if (valor == '') {";
					
					$cod .= "$('#sugestao').hide();";
					$cod .= "$('#sugestao').html();";
					
				$cod .= "} else {";
					
					$cod .= "$('#sugestao').show();";
					$cod .= "$('#sugestao').html(\"<img src='img/loading.gif' style='width:3em;'>\");";
					
					$cod .= "$.post('inc/ajaxIndex.inc.php', {";
						$cod .= "valor : \"\" + valor + \"\"";
					$cod .= "}, function(data) {";
						$cod .= "if (data.length > 0) {";
							$cod .= "$('#sugestao').html(data);";
							$cod .= "setInterval(function(){ ";
								$cod .= "$('#sugestao').hide();";
								$cod .= "$('#sugestao').html();}";
							$cod .= ",20000);";
						$cod .= "}";
					$cod .= "});";
				$cod .= "}";
			$cod .= "}";
			
			$cod .= "function preencher(sugestao_id, sugestao){";
				$cod .= "if(sugestao_id){";
					$cod .= "$(\"input[name='id_empresa']\").val(sugestao_id);";
					$cod .= "$(\"input[name='empresa']\").val(sugestao);";
				$cod .= "}";
				$cod .= "$(\"#sugestao\").hide();";
				$cod .= "$(\"#sugestao\").html();";
			$cod .= "}";
			
			$cod .= "function filtro(){";
				$cod .= "valida = true;";
				$cod .= "cod = '';";
				$cod .= "if($(\"input[name=id_empresa]\").val()==''){";
					$cod .= "$(\"input[name=empresa]\").attr(\"class\", \"avisoInput\");";
					$cod .= "cod += \"Você precisa digitar o nome da sua empresa e clicar na sugestão que irá aparecer.<br>\";";
					$cod .= "valida = false;";
				$cod .= "}else{";
					$cod .= "$(\"input[name=empresa]\").attr(\"class\", \"\");";
				$cod .= "}";
				
				$cod .= "if($(\"input[name=login]\").val()==''){";
					$cod .= "$(\"input[name=login]\").attr(\"class\", \"avisoInput\");";
					$cod .= "cod += \"Precisamos de seu login de acesso.<br>\";";
					$cod .= "valida = false;";
				$cod .= "}else{";
					$cod .= "$(\"input[name=login]\").attr(\"class\", \"\");";
				$cod .= "}";
				
				$cod .= "if($(\"input[name=senha]\").val()==''){";
					$cod .= "$(\"input[name=senha]\").attr(\"class\", \"avisoInput\");";
					$cod .= "cod += \"Sua senha é necessária para acessar o sistema.<br>\";";
					$cod .= "valida = false;";
				$cod .= "}else{";
					$cod .= "$(\"input[name=senha]\").attr(\"class\", \"\");";
				$cod .= "}";
				
				$cod .= "if(!valida){";
					$cod .= "$('#msg').show(1000);";
					$cod .= "$('#msg').html(cod);";
				$cod .= "}else{";
					$cod .= "$('#msg').hide(1000);";
					$cod .= "$('#msg').html('');";
				$cod .= "}";
				
				$cod .= "return valida;";
			$cod .= "}";
			
			
		$cod .= "</script>";
		
		$cod .= "<title>Delphos :: Login</title>";
	$cod .= "</head>";
	$cod .= "<body id='bodyLogin' style='padding-top:1em; text-align: center;'>";
		
		$cod .= "<img id='imgindex' src='img/ico_white.png'><br>";
		
		$cod2 = "</body>";
		$cod2 .= "</html>";
		
		
			
		function form($empresa = null, $id_empresa = null, $login = null, $senha = null, $end = "indexUsuario.php"){
			$cod = "<form method='post' action='login.php' enctype='multipart/form-data' onsubmit='return filtro();' class='form'>";
				$cod .= "<input type='hidden' name='op' value='logar'>";
				$cod .= "<input type='hidden' name='end' value='$end'>";
				$cod .= "<div class='column'>";
					$cod .= "<div id='msg' style='display:hidden;'></div>";
					$cod .= "<label for='empresa'>Nome da Empresa</label>";
					$cod .= "<input type='text' id='empresa' name='empresa' placeholder='Rocket Solution' value='$empresa' onkeypress='verificarSugestao(this.value);' autocomplete='off'>";
					$cod .= "<input type='hidden' name='id_empresa' value='$id_empresa'>";
					$cod .= "<div id='sugestao' style='display:none;'></div>";
					$cod .= "<label for='login'>Dados de acesso</label>";
					$cod .= "<input type='text' id='login' name='login' placeholder='LOGIN' value='$login' autocomplete='off'>";
					$cod .= "<input type='password' name='senha' placeholder='Senha'>";
					$cod .= "<div class='submit-wrap'><input class='submit' type='submit' value='Entrar'></div>";
					$cod .= "<br><a href='indexEsqueceuSenha.php'>ESQUECEU A SENHA</a><br>";
					$cod .= "<a href='gestor/cadastrarEmpresaUsuario.php'>CADASTRAR SUA EMPRESA</a>";
				$cod .= "</div>";
			$cod .= "</form>";
			return $cod;
		}

		//includes do php para todas as paginas
		file_exists("conecta.php") ? include "conecta.php" : $conexao = null;
		include "inc/funcoes.inc.php";
		if(isset($_COOKIE["id_empresa"])){
			$conn = TConnection::open("gestor");

			$criterio = new TCriteria;
			$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

			$sql = new TSqlSelect;
			$sql->setEntity("cliente");
			$sql->addColumn('alcunha');
			$result = $conn->query($sql->getInstruction());
			if($result->rowCount()){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				define("ALCUNHA", $alcunha);
			}
		}

		
		
		$empresa = $id_empresa = $login = $senha = null;
		
		extract($_COOKIE);
		
		if(!isset($_POST["login"])){
			if($id_empresa){// se existe o cookie empresa, deixar marcado a input empresa com o nome dela, é claro verificar se ela existe
				$sql = query("select nome as empresa, conectasrc from cliente where id='$id_empresa' and status='1'");
				if(mysqli_num_rows($sql)){
					extract(mysqli_fetch_assoc($sql));
				}
			}
			if(isset($_GET["end"])){
				$end = $_GET["end"];
			}else{
				$end = "indexUsuario.php";
			}
			$cod .= form($empresa, $id_empresa, $login, $senha, $end);
			
		}else{
			
			extract($_POST);
			
			if($op=="logar"){
				
				$sql = query("select conectasrc, alcunha from cliente where id='$id_empresa'");
				extract(mysqli_fetch_assoc($sql));
				
				if(file_exists($conectasrc)){
					
					include $conectasrc;
					defined("ALCUNHA") or define("ALCUNHA", $alcunha);
					
					
					$sql = query("select login, senha from usuario where login='$login' and senha='".md5($senha)."'");
					if(mysqli_num_rows($sql)){
						$cod.= "<div class='form'>";
							$cod.= "<div class='column'>";
								//$cod .= "<label>Você está conectado a<br><b>".registro('1', "empresa", "nome")."</b>, ";
								$cod .= "como <b>$login.</b></label>";
								$cod .= "<label><img src='img/loading_bar.gif'></label>";
								$cod .= "Iniciando sistema.";
							$cod.= "</div>";
						$cod.= "</div>";
						$cod.= "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=$end'>";
						$cod.= "<br><br><br><a href='cadastrarOrcamento.php'>Problemas ao se conectar?</a>";
						$sql = query("update usuario set online='1' where login='$login' and senha='".md5($senha)."'");
						setcookie("login", $login);
						setcookie("senha", md5($senha));
						setcookie("id_empresa", $id_empresa);
						setcookie("chat", "0");
					}else{
						$cod.= "<div class='column'>";
							$cod .= "Login, senha e/ou empresa não conferem.";
						$cod.= "</div>";
						$cod.= "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=login.php?end=$end'>";
						setcookie("login", "");
						setcookie("senha", "");
						setcookie("id_empresa", "");
					}
				}
			}
		}
	
	
	echo $cod;
    include "inc/copy.inc";
    echo $cod2;
?>
	