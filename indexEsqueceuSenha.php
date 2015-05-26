<?php
header ( "Expires: Mon, 4 Jan 1999 12:00:00 GMT" ); // Expired already
header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" ); // good for HTTP/1.1
header ( "Pragma: no-cache" );
define ( "WP_MAX_MEMORY_LIMIT", "512M" );
// header("Cache: no-cache");

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

$cod .= "function filtro(){";
$cod .= "var valida = true;";
$cod .= "var alerta = '';";
$cod .= "if($(\"input[name='senha1']\").val() != $(\"input[name='senha2']\").val()){";
$cod .= "valida = false;";
$cod .= "alerta = \"Senhas não são iguais.\";";
$cod .= "$(\"input[name='senha1']\").attr(\"class\", \"avisoInput\");";
$cod .= "$(\"input[name='senha2']\").attr(\"class\", \"avisoInput\");";
$cod .= "}else{";
$cod .= "$(\"input[name='senha1']\").attr(\"class\", \"\");";
$cod .= "$(\"input[name='senha2']\").attr(\"class\", \"\");";
$cod .= "}";
$cod .= "if(!valida){";
$cod .= "alert(alerta);";
$cod .= "}";
$cod .= "return valida;";
$cod .= "}";

$cod .= "function filtro1(){";
$cod .= "valida = true;";
$cod .= "cod = '';";
$cod .= "if($(\"input[name=id_empresa]\").val()==''){";
$cod .= "$(\"input[name=empresa]\").attr(\"class\", \"avisoInput\");";
$cod .= "cod += \"Você precisa digitar o nome da sua empresa e clicar na sugestão que irá aparecer.<br>\";";
$cod .= "valida = false;";
$cod .= "}else{";
$cod .= "$(\"input[name=empresa]\").attr(\"class\", \"\");";
$cod .= "}";

$cod .= "if($(\"input[name=loginEmail]\").val()==''){";
$cod .= "$(\"input[name=loginEmail]\").attr(\"class\", \"avisoInput\");";
$cod .= "cod += \"Precisamos de seu login de acesso.<br>\";";
$cod .= "valida = false;";
$cod .= "}else{";
$cod .= "$(\"input[name=login]\").attr(\"class\", \"\");";
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


$cod .= "</script>";

$cod .= "<title>Delphos :: Recuperar Senha</title>";
$cod .= "</head>";
$cod .= "<body id='bodyLogin' style='padding-top:1em; text-align: center;'>";

echo $cod;

unset ( $_COOKIE );

file_exists ( "conecta.php" ) ? include "conecta.php" : $conexao = null;
include "inc/funcoes.inc.php";
!isset($conexao) ? $conexao = null : false;


extract($_POST);
extract($_GET);
function sendEmail($msg = null, $send = false) {
	echo "<form method='post' action='indexEsqueceuSenha.php' enctype='multipart/form-data' class='form' onsubmit='return filtro1();'>";
	echo "<input type='hidden' name='op' value='enviar_email'>";
	echo "<div class='column'>";
	echo "<h1>Recuperar Senha</h1>";
	
	echo "<img src='img/chave.png'>";
	
	if ($msg) {
		echo "<div id='msg'>$msg</div>";
	}else{
		echo "<div id='msg'></div>";
	}
	if (!$send) {
		echo "<label for='empresa'>Nome da Empresa</label>";
		echo "<input type='text' id='empresa' name='empresa' placeholder='Rocket Solution' value='' onkeypress='verificarSugestao(this.value);' autocomplete='off'>";
		echo "<input type='hidden' name='id_empresa' value=''>";
		echo "<div id='sugestao' style='display:none;'></div>";
		echo "<label for='loginEmail' style='white-space:nowrap;'>Digite seu Login ou e-mail:</label>";
		echo "<input type='text' name='loginEmail' style='text-transform: none;'>";
		echo "<div class='submit-wrap'><input type='submit' class='submit' value='Enviar'></div>";
	} else {
		echo "<div class='submit-wrap'><a href='index.php' class='submit'>Voltar</a></div>";
	}
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "</form>";
}

if (!isset($op)) {
	sendEmail();
}else{
	
	if ($op == "enviar_email") {
		
		$conn = TConnection::open('gestor');
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id', '=', $id_empresa));
		
		$sql = new TSqlSelect;
		$sql->setEntity('cliente');
		$sql->addColumn('alcunha');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());
		
		if($result->rowCount()){
			
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
			
			$conn2 = TConnection::open($alcunha);
			
			$criterio = new TCriteria;
			$criterio->add(new TFilter('login', '=', $loginEmail), TExpression::OR_OPERATOR);
			$criterio->add(new TFilter('email', '=', $loginEmail), TExpression::OR_OPERATOR);
			
			$sql = new TSqlSelect;
			$sql->setEntity('usuario');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn2->query($sql->getInstruction());
			
			if($result->rowCount()){
				
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				$corpo = "<div style=\"";
				$corpo .= "font-size:18px;";
				$corpo .= "font-family: Arial, Helvetica, sans-serif;";
				$corpo .= "border: solid 2px #4F0E11;";
				$corpo .= "padding: 10px;";
				$corpo .= "border-radius: 10px;";
				$corpo .= "\" >";
				$corpo .= "<img src='" . $_SERVER ["HTTP_HOST"] . "/img/boneco_duvida.png' style='float: right;'>";
				$corpo .= "<a href='http://www.rocketsolution.com.br'>";
				$corpo .= "<img src='" . $_SERVER ["HTTP_HOST"] . "/img/logo.png' style='margin-right: 30px; margin-bottom: 20px;'><br>";
				$corpo .= "</a>";
				$corpo .= "Olá $nome,<br><br>";
				$corpo .= "Identificamos em nosso sistema de Delphos, uma solicitação de recuperação de senha.<br>";
				$corpo .= "Caso não tenha sido você quem solicitou a recuperação de senha, por favor ignore esta mensagem.<br>";
				$corpo .= "Do contrário ";
				$corpo .= "<a href='" . $_SERVER ["HTTP_HOST"] . "/indexEsqueceuSenha.php?op=verificar&chave=".base64_encode($id)."&registro=".base64_encode($alcunha)."'>clique aqui</a>";
				$corpo .= " para recuperar sua senha.<br><br>";
				$corpo .= "Equipe Rocket Solution";
				$corpo .= "</div>";
				
				$enviar_email = new enviar_email(EMAIL_ROCKET, $email, 'Recuperar senha do sistema DELPHOS', $corpo);
				
				
				if ($enviar_email->enviarEmail()) {
					$send = true;
					$msg = "Foi enviado para seu e-mail para<br>";
					$tamanho = intval(strlen($email)/3);
					$msg .= "( " . substr_replace ( $email, '******', $tamanho, - 1 * $tamanho ) . " )<br>";
					$msg .= "com um link para recuperar sua senha.<br>";
				} else {
					$send = false;
					$msg = "Infelizmente não foi possível processar essa operação.<br>";
					$msg .= "Por favor tente novamente mais tarde.<br>";
					$msg .= "<a href='index.php'>Voltar.</a>";
				}
			}else{
				$msg = "Dados não conferem.<br>Tem certeza que digitou corretamente?";
				$send = false;
			}
			
			sendEmail ( $msg, $send );
			
		}else{
			sendEmail("Dados não conferem.<br>Tem certeza que digitou corretamente?");
		}
		
		
	}elseif($op=="verificar"){
		
		function verificaDados($chave, $msg = null, $registro) {
			$data_nascimento = "0000-00-00";
			$rg = $cpf = null;
			$id = base64_decode($chave);
			
			$conn = TConnection::open(base64_decode($registro));
			
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', $id));
			$criteria->add(new TFilter('data_nascimento', '<>', '0000-00-00'));
			$criteria->add(new TFilter('rg', '<>', ''), TExpression::OR_OPERATOR);
			$criteria->add(new TFilter('cpf', '<>', ''), TExpression::OR_OPERATOR);
			
			$sql = new TSqlSelect;
			$sql->setEntity('usuario');
			$sql->addColumn('*');
			$sql->setCriteria($criteria);
			$result = $conn->query($sql->getInstruction());
			if($result->rowCount()){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
			}else{
				echo "<div id='login'>";
				echo "<h1>Existe falta de informação em seu registro</h1>";
				echo "Solicite que o administrador do sistema pelo menos um desses campos:<br>";
				echo "<ul>";
				echo "<li>Data de nascimento</li>";
				echo "<li>RG</li>";
				echo "<li>CPF</li>";
				echo "</ul>";
				echo "</div>";
				include "inc/copy.inc";
				echo "</body>";
				echo "</html>";
				die ();
			}
			
						
			$escolhaDoc = rand ( 1, 3 );
			while ( ($data_nascimento == "0000-00-00" and $escolhaDoc == 1) or ($rg == null and $escolhaDoc == 2) or ($cpf == null and $escolhaDoc == 3) ) {
				$escolhaDoc = rand ( 1, 3 );
			}
			
			if ($escolhaDoc == 1) {
				$escolhaDoc = "<label>Data de Nascimento</label>";
				$escolhaTab = "data_nascimento";
				$input = inputData ( "formulario", "doc", null );
			} elseif ($escolhaDoc == 2) {
				$escolhaDoc = "<label>RG</label>";
				$escolhaTab = "rg";
				$input = "<input style='width:auto;' type='text' name='doc' ".mascara("Integer").">";
			} else {
				$escolhaDoc = "<labelCPF</label>";
				$escolhaTab = "cpf";
				$input = "<input style='width:auto;' type='text' name='doc' ".mascara("Cpf").">";
			}
			
			if (!$msg) {
				$msg = "Por favor confirme o seguinite dado:<br>";
			}
			
			
			echo "<form name='formulario' method='post' action='indexEsqueceuSenha.php' enctype='multipart/form-data' class='form'>";
			echo "<div class='column' style='text-align:center;'>";
			echo "<input type='hidden' name='op' value='verificar'>";
			echo "<input type='hidden' name='chave' value='$chave'>";
			echo "<input type='hidden' name='registro' value='$registro'>";
			echo "<input type='hidden' name='escolhaTab' value='$escolhaTab'>";
			echo "<h3>".$msg."</h3>";
			echo $escolhaDoc;
			echo $input;
			echo "<div class='submit-wrap'><input type='submit' class='submit' value='Enviar'></div>";
			echo "</div>";
			echo "</form>";
			
		}
		function novaSenha($chave, $registro) {
			
			echo "<form name='formulario' method='post' action='indexEsqueceuSenha.php' enctype='multipart/form-data' onsubmit='return filtro();' class='form'>";
			echo "<div class='column'>";
			echo "<input type='hidden' name='op' value='trocar'>";
			echo "<input type='hidden' name='chave' value='$chave'>";
			echo "<input type='hidden' name='registro' value='$registro'>";
			echo "<h3>Formulário para definir sua nova senha.</h3>";
			echo "<label>Digite sua nova senha:</label>";
			echo "<input type='password' name='senha1'>";
			echo "<label>Digite novamente:</label>";
			echo "<input type='password' name='senha2'>";
			echo "<div class='submit-wrap'><input type='submit' class='submit' value='Enviar'></div>";
			echo "</div>";
			echo "</form>";
			
		}
		
		if(!isset($doc)){
			verificaDados($chave, null, $registro);
		}else{
			
			$id = base64_decode($chave);
			if ($escolhaTab == "data_nascimento") {
				$doc = formataDataInv($doc);
			}
			
			$conn = TConnection::open(base64_decode($registro));
			
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', $id));
			$criteria->add(new TFilter($escolhaTab, '=', $doc));
			
			$sql = new TSqlSelect;
			$sql->setEntity('usuario');
			$sql->addColumn('*');
			$sql->setCriteria($criteria);
			$result = $conn->query($sql->getInstruction());
			
			
			if ($result->rowCount()) {
				novaSenha($chave, $registro);
			}else{
				$msg = "O valor do documento informado não é<br>";
				$msg .= "igual com que temos no nosso banco de dados.<br>";
				$msg .= "Deseja tentar novamente?<br>";
				
				verificaDados($chave,$msg,$registro);
			}
		}
	}elseif($op=="trocar"){
		
		if($senha1 == $senha2){
			
			$conn = TConnection::open(base64_decode($registro));
			
			
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', base64_decode($chave)));
			
			$sql = new TSqlUpdate;
			$sql->setEntity('usuario');
			$sql->setRowData('senha', md5($senha1));
			$sql->setCriteria($criteria);
			$result = $conn->query($sql->getInstruction());
			
			
			echo "<div class='form'>";
			echo "<div class='column'>";
			echo "<h1>Nova senha definida</h1>";
			echo "Você será redirecionado para pagina de login<br>";
			echo "onde poderá efetuar o login com a nova senha.";
			echo "</div>";
			echo "</div>";
			
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=index.php'>";
		}else{
			novaSenha($chave, base64_decode($registro));
		}
	}
}

include "inc/copy.inc";
?>
</body>
</html>