<?php
include "templates/upLogin.inc.php";
//js especifico para pagina
echo "<script type='text/javascript' src='js/cadastrarUsuarioFiltro.js'></script>";
echo "<script type='text/javascript' src='js/filtroIndex.js'></script>";

function cadastroUsuario($ID = null) {

	echo "<center>";
	if ($ID) {
		$cod = "<input type='hidden' name='op' value='editar'>";
		$cod .= "<input type='hidden' name='ID' value='".base64_encode($ID)."'>";
		$sql = query("select * from usuario where id='$ID'");
		extract(mysqli_fetch_assoc($sql));
		$admissao = formataData($data_admissao);
		$nascimento = formataData($data_nascimento);
		$salario = $salario_base;
		$CEP = $cep;
	} else {
		$cod = "<input type='hidden' name='op' value='novo'>";
		$ID = $login = $senha = $nome = $nascimento = $funcao = $email = $telefone1 = $telefone2 = 
		$endereco = $numero = $bairro = $municipio = $rg = $cpf = $carteira = $salario = $dependentes = 
		$demissao = $status = $estado = $cidade = $CEP = $ass_digital_usar = $ass_digital_end_max = $ass_digital_end_min = null;
		$admissao = date('d/m/Y');

	}

	$instrucao = "select ferramenta from credenciais where id_usuario='" . getIdCookieLogin($_COOKIE["login"]) . "' and ferramenta='cadastrarUsuario.php'";
	$sql = query($instrucao);
	$checkCredencial = mysqli_num_rows($sql);

	echo "<form name='formularioUsuario' method='post' action='cadastrarUsuario.php' enctype='multipart/form-data' onsubmit='return filtro();'>";
	echo $cod;
	echo "<table id='gradient-style'>";
	echo "<tr>";
	echo "<th colspan='2'  style='width:25%'>Informações de acesso</th>";
	$ID ? $usuarioId = $ID - 1 : $usuarioId = null; 
	echo "<th colspan='3'  style='width:50%'>Usuário $usuarioId</th>";
	echo "<th  style='width:25%'>";
	if ($ID and $checkCredencial > 0) {
		if ($status == "Ativo") {
			echo "<a href='cadastrarUsuario.php?ativacao=false&ID=".base64_encode($ID)."' title='Desativar Usuario'><img style='width:30px;' src='img/usuarioDesativar.png'></a>";
		} else {
			echo "<a href='cadastrarUsuario.php?ativacao=true&ID=".base64_encode($ID)."' title='Ativar Usuario'><img style='width:30px;' src='img/usuarioAtivar.png'></a>";
		}
	} elseif ($ID) {
		echo "$status";
	}
	echo "</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'  style='width:25%'>Nome Completo<br><input type='text' name='nome' value='$nome'></td>";
	echo "<td  style='width:25%'>Login<br>";
	if ($ID) {
		echo "$login";
		echo "<input type='hidden' name='login' value='$login'>";
	} else {
		echo "<input type='text' name='login' style='text-transform:none;' onkeyup=\"this.value=this.value.toLowerCase(); this.value=trim(this.value); \" value='$login'>";
	}
	echo "</td>";
	echo "<td colspan='2'  style='width:25%'>";
	if($ID){
		echo "Senha<br> ***** <a href='#' " . pop("usuarioSenha.php?id=$ID") . ">Alterar</a>";
	}
	echo "</td>";
	echo "<td  style='width:25%'>Função";
	if ($checkCredencial > 0) {
		echo "<a href='#' " . pop("cadastrarFuncao.php", 300, 500) . " title='Cadastrar Funcao'><img class='imgHelp' src='img/mais.png'></a>";
		echo "<br><select name='funcao'>";
		echo opcaoSelect("funcao", "1", "Ativo", $funcao);
		echo "</select>";
	} else {
		echo "<br><input type='hidden' name='funcao' value='$funcao'>";
		echo registro($funcao, "funcao", "nome");
	}
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'>E-mail<br><input type='text' name='email' size='50' value='$email' style='text-transform:none;'></td>";
	echo "<td colspan='1'>Data de Nascimento<br>" . inputData("formularioUsuario", "nascimento", NULL, $nascimento) . "</td>";
	echo "<td colspan='2'>Telefone 1<br><input type='text' name='telefone1' maxlength='14' value='$telefone1' " . mascara("Telefone", "15") . "></td>";
	echo "<td colpsan='1'>Telefone 2<br><input type='text' name='telefone2' maxlength='14' value='$telefone2' " . mascara("Telefone", "15") . "></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th colspan='6'>Endereço</th>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Rua<br><input type='text' name='endereco' value='$endereco'></td>";
	echo "<td colspan='3'>Bairro<br><input type='text' name='bairro' value='$bairro'></td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "<td colspan='2'>Número<br><input type='text' name='numero' value='$numero' " . mascara("Integer") . "></td>";
	echo "<td>Estado<br><select name='estados' id='estados' onchange=\"ajax(this.value, 'cidades')\">";
	echo opcaoSelect("estados", "2", false, $estado);
	echo "<select></td>";
	if ($cidade) {
		echo "<td colspan='2' id='tdCidades'>";
		$sql = query("select * from cidades where estados_cod_estados='$estado'");
		echo "Cidades*<br><select name='cidades' onchange=\"ajax(this.value, 'cep')\"'>";
		for ($linha = mysqli_num_rows($sql); $linha > 0; $linha--) {
			extract(mysqli_fetch_assoc($sql));
			if ($cidade == $cod_cidades) {
				echo "<option value='$cod_cidades' selected='yes'>$nome</option>";
			} else {
				echo "<option value='$cod_cidades'>$nome</option>";
			}
		}
		echo "</select>";
		echo "</td>";
	} else {
		echo "<td colspan='2' id='tdCidades'><input type='hidden' name='cidades' value='0'>Cidade<br><select id='cidades' disabled>";
		echo "</select></td>";
	}
	echo "<td id='tdCep'>CEP<br><input type='text'  name='cep' " . mascara("Cep", "9") . " value='$CEP'></td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "<tr>";
	echo "<th colspan='6'>Documentos e informações trabalhistas</th>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>R.G.<br><input type='text' name='rg' value='$rg' " . mascara("Integer") . "></td>";
	echo "<td colspan='3'>C.P.F.<br><input type='text' name='cpf' maxlength='14' value='$cpf' " . mascara("Cpf", "14") . "></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='2'>Data de Admissão<br>" . inputData("formularioUsuario", "admissao", NULL, $admissao) . "</td>";
	echo "<td colspan='1'>Carteira de Trabalho<br><input type='text' name='carteira' value='$carteira' " . mascara("Integer") . "></td>";
	echo "<td colspan='2'>Dependentes<br><input type='text' name='dependentes' value='$dependentes' " . mascara("Integer") . "></td>";
	echo "<td>Salario Base<br>";
	if ($checkCredencial > 0) {
		echo "<input size='10' type='text' name='salario' value='" . real($salario) . "' " . mascara("Valor3", 20);
	} else {
		echo "<input type='hidden' name='salario' value='$salario'>";
		echo "R$ " . real($salario);
	}
	echo "</td>";
	echo "</tr>";
	
	if($ID){
		$style="";
	}else{
		$style="style='display:none;'";
	}
	
	echo "<tr $style>";
	echo "<th colspan='3'><div class='checkboxText'>Identificação visual</div><a href='javascript:void(0)' title='Adicionar imagem do Usuário.'".pop("usuario_imagem_upload.php?id=".$ID)."><img src='img/mais.png'></a></th>";
	echo "<th colspan='3'><div class='checkboxText'>Assinatura digital</div>";
	if($ass_digital_usar){
		checkbox('ass_digital', 1);
		//echo "<input type='checkbox' name='assDigital' onclick='mudaAss();' checked='yes'>";
		//echo "<input type='hidden' name='ass_digital' value='1'>";
	}else{
		checkbox('ass_digital', 0);
		//echo "<input type='checkbox' name='assDigital' onclick='mudaAss();'>";
		//echo "<input type='hidden' name='ass_digital' value='0'>";
	}
	echo " <a href='#' title='Adicionar imagem do Usuário.'".pop("usuarioass_imagem_upload.php?id=".$ID)."><img src='img/mais.png'></a>";
	echo "</th>";
	echo "</tr>";
	
	echo "<tr $style>";
	echo "<td colspan='3'>";
		echo showImagemUsuario($ID, 3, 0, true);
	echo "</td>";
	echo "<td colspan='3'>";
		if($ass_digital_end_min){
			echo "<a href='#' title='Clique para ver apliado.' ".pop("imagem.php?img=$ass_digital_end_max")."><img class='ass_ativo' src='$ass_digital_end_min'></a><br>";
			echo "<a class='ass_ativo' href='#' ".pop("usuarioass_imagem_upload.php?id=$ID&a=delete&idDeletar=$ID", 10, 10)." title='Deletar esta ass.'>Deletar</a>";
		}
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th colspan='6'>Credênciais do usuário</th>";
	echo "</tr>";
	$arrayCod = getFerramentaOpcao("cadastrar");
	$checkCredencial > 0 ? $opcao = "" : $opcao = "disabled";
	echo "<tr>";
	echo "<td colspan='6'>Cadastro / Pesquisa: <input type='checkbox' name='cadastro' id='cadastro' onclick=\"cadastrar_pesquisar();\" $opcao></td>";
	echo "</tr>";

	$i = 1;
	$checked = "";

	function checkFerramenta($parametro, $valor) {
		global $checked, $_POST, $op, $ID, $info;
		if (isset($op)) {

			if ($op == "editar" or $op == "visualizar") {
				$instrucao = "select * from credenciais where id_usuario='" . $ID . "' and ferramenta='" . $valor . ".php'";
				$sql = query($instrucao);
				$linha = mysqli_num_rows($sql);
				if ($linha > 0) {
					$checked = "checked='yes'";
				} else {
					$instrucao = "select * from credenciais where id_usuario='" . getIdCookieLogin($_COOKIE["login"]) . "' and ferramenta='cadastrarUsuario.php'";
					if (mysqli_num_rows(query($instrucao)) > 0) {
						$checked = "";
					} else {
						$checked = "disabled='yes'";
					}

				}
			} else {
				if (in_array($valor, $_POST[$parametro])) {
					$checked = "checked='yes'";
				} else {
					$checked = "";
				}
			}

		} elseif (isset($_POST[$parametro])) {
			if (in_array($valor, $_POST[$parametro])) {
				$checked = "checked='yes'";
			} else {
				$checked = "";
			}

		} else {
			$checked = "";
		}
		return $checked;
	}

	foreach ($arrayCod as $key => $value) {
		if ($i == 1) {
			echo "<tr>";
		}

		$checked = checkFerramenta("ferramentaCP", $value);

		echo "<td valing='middle' style='text-align:right; width:5%;'><input type='checkbox' name='ferramentaCP[]' value='$value' $checked></td>";
		echo "<td colspan='2' valing='middle'><img src='img/icones/$value.png' class='imgFerramenta'>$key</td>";

		if ($i == 2) {
			echo "<tr>";
		}
		if ($i == 3) {
			$i = 1;
		}
		$i++;
	}
	if($i==2){
		echo "<td colspan='3'></td>";
	}
	
	/*
	$arrayCod = getFerramentaOpcao("pesquisa");
	
	echo "<tr>";
	echo "<td colspan='4'><h3>Pesquisa: <input type='checkbox' name='pesquisa' id='pesquisa' onclick=\"pesquisar();\" $opcao></h3></td>";
	echo "</tr>";

	$i = 1;
	$checked = "";

	foreach ($arrayCod as $key => $value) {
		if ($i == 1) {
			echo "<tr>";
		}

		$checked = checkFerramenta("ferramentaP", $value);

		echo "<td valing='middle'><input type='checkbox' name='ferramentaP[]' value='$value' $checked></td>";
		echo "<td valing='middle'><img src='img/icones/$value.png' class='imgFerramenta'>$key</td>";

		if ($i == 2) {
			echo "<tr>";
		}
		if ($i == 3) {
			$i = 1;
		}
		$i++;
	}
	*/
	
	
	$arrayCod = getFerramentaOpcao("relatorio");

	echo "<tr>";
	echo "<td colspan='6'>Relatórios: <input type='checkbox' name='relatar' id='relatar' onclick=\"relatorio();\" $opcao></td>";
	echo "</tr>";

	$i = 1;
	$checked = "";

	foreach ($arrayCod as $key => $value) {
		if ($i == 1) {
			echo "<tr>";
		}

		$checked = checkFerramenta("ferramentaR", $value);

		echo "<td valing='middle' style='text-align:right; width:5%;'><input type='checkbox' name='ferramentaR[]' value='$value' $checked></td>";
		echo "<td colspan='2' valing='middle'><img src='img/icones/$value.png' class='imgFerramenta'>$key</td>";

		if ($i == 2) {
			echo "<tr>";
		}
		if ($i == 3) {
			$i = 1;
		}
		$i++;
		
	}
	if($i==2){
		echo "<td colspan='3'></td>";
	}
	
	/*
	$arrayCod = getFerramentaOpcao("rh");

	echo "<tr>";
	echo "<td colspan='6'>Recursos Humanos: <input type='checkbox' name='rh' id='rh' onclick=\"recursosHumanos();\" $opcao></td>";
	echo "</tr>";

	$i = 1;
	$checked = "";

	foreach ($arrayCod as $key => $value) {
		if ($i == 1) {
			echo "<tr>";
		}

		$checked = checkFerramenta("ferramentaRH", $value);

		echo "<td valing='middle' style='text-align:right; width:5%;'><input type='checkbox' name='ferramentaRH[]' value='$value' $checked></td>";
		echo "<td colspan='2' valing='middle'><img src='img/icones/$value.png' class='imgFerramenta'>$key</td>";

		if ($i == 2) {
			echo "<tr>";
		}
		if ($i == 3) {
			$i = 1;
		}
		$i++;
	}
	if($i==2){
		echo "<td colspan='3'></td>";
	}
	*/
	
	$arrayCod = getFerramentaOpcao("administrativo");

	echo "<tr>";
	echo "<td colspan='6'>Administrativo: <input type='checkbox' name='administrar' id='administrar' onclick=\"admin();\"></td>";
	echo "</tr>";

	$i = 1;
	$checked = "";

	foreach ($arrayCod as $key => $value) {
		if ($i == 1) {
			echo "<tr>";
		}

		$checked = checkFerramenta("ferramentaA", $value);

		echo "<td valing='middle' style='text-align:right; width:5%;'><input type='checkbox' name='ferramentaA[]' value='$value' $checked></td>";
		echo "<td colspan='2' valing='middle'><img src='img/icones/$value.png' class='imgFerramenta'>$key</td>";

		if ($i == 2) {
			echo "<tr>";
		}
		if ($i == 3) {
			$i = 1;
		}
		$i++;
	}
	if($i==2){
		echo "<td colspan='3'></td>";
	}
	
	
	echo "</tr>";

	echo "<tr>";
	echo "<th colspan='6' style='text-align:right;'><input type='submit' class='btnEnviar' value='Enviar'></th>";
	echo "</tr>";

	echo "</table>";

	echo "</form>";
	echo "</center>";
}

extract($_GET);
extract($_POST);


if (isset($op)) {
	
	if(isset($ID)){
		$ID = base64_decode($ID);
		$numeroUsuario = $ID;
	}
	
	if (empty($dependentes)) {
		$dependentes = 0;
	}
	if (empty($numero)) {
		$numero = 0;
	}

	if (!isset($_POST["ferramentaCP"])) {
		$_POST["ferramentaCP"] = array();
	}
	/*
	if (!isset($_POST["ferramentaP"])) {
		$_POST["ferramentaP"] = array();
	}
	*/
	if (!isset($_POST["ferramentaR"])) {
		$_POST["ferramentaR"] = array();
	}
	if (!isset($_POST["ferramentaRH"])) {
		$_POST["ferramentaRH"] = array();
	}
	if (!isset($_POST["ferramentaA"])) {
		$_POST["ferramentaA"] = array();
	}


	function obrigatorio($variavel, $variavelDisplay) {
		global $valida, $info;
		if (empty($variavel) or $variavel == "0") {
			$info .= "O campo $variavelDisplay é obrigatório.<br>";
			$valida = false;
		}
	}

	function invalido($variavel, $variavelDisplay, $tamanho, $obrigatoriedade = NULL) {
		global $valida, $info;
		if ($obrigatoriedade) {
			if (strlen($variavel) < $tamanho and $variavel <> "") {
				$info .= "$variavelDisplay inválido.<br>";
				$valida = false;
			}
		} else {
			if (strlen($variavel) < $tamanho) {
				$info .= "$variavelDisplay inválido.<br>";
				$valida = false;
			}
		}

	}

	$info = "";
	$valida = true;

	//filtro
	if ($op == "novo" or $op == "editar") {
		
		if(!isset($ass_digital)){
			$ass_digital=0;
		}
		obrigatorio($nome, "Nome");
		if ($op == "novo") {
			obrigatorio($login, "Login");

			if (strstr($login, " ") == true) {
				$info .= "Por favor não digite espaços no campo do login.<br>";
				$valida = false;
			}

			$sql = query("select nome, id from usuario where login='$login'");
			if (mysqli_num_rows($sql) and $op == "novo") {
				$info .= "já existe um usuario com esse login.<br>";
				$valida = false;
			}
		}
		obrigatorio($funcao, "Função");
		//obrigatorio($admissao, "Data de admissão");
		//obrigatorio($telefone1, "Telefone 1");
		//obrigatorio($salario, "Salário Base");
		//obrigatorio($rg, "R.G.");
		//obrigatorio($cpf, "C.P.F");

		if (strstr($nome, " ") == false) {
			$info .= "Por favor digite o nome completo.<br>";
			$valida = false;
		}

		invalido($admissao, "Data de admissão", "10");
		invalido($nascimento, "Data de nascimento", "10", true);
		invalido($telefone1, "Telefone 1", "14", true);
		invalido($telefone2, "Telefone 2", "14", true);
		invalido($cep, "CEP", "9", true);

		if (!validaCPF($cpf) and strlen($cpf) > 0) {
			$info .= "CPF informado é invalido.<br>";
			$valida = validaCPF($cpf);
		}
		if ($op == "novo") {
			$instrucao = "select * from usuario where cpf='$cpf' and cpf<>''";
			$instrucao2 = "select * from usuario where email='$email' and email<>''";
		}
		if ($op == "editar") {
			$instrucao = "select * from usuario where cpf='$cpf' and cpf<>'' and id<>'$ID'";
			$instrucao2 = "select * from usuario where email='$email' and email<>'' and id<>'$ID'";
		}
		$sql = query($instrucao);
		$linha = mysqli_num_rows($sql);
		if ($linha > 0) {
			$info .= "CPF já pertence a outra pessoa.";
			$valida = false;
		}
		$sql = query($instrucao2);
		$linha = mysqli_num_rows($sql);
		if ($linha > 0) {
			$info .= "E-mail já pertence a outra pessoa.";
			$valida = false;
		}

		if ((strstr($email, "@") == false or strstr($email, ".") == false) and $email) {
			$info .= "E-mail inválido<br>";
			$valida = false;
		}
	}

	if ($op == "novo" and $valida) {



		$admissao = formataDataInv($admissao);
		$nascimento = formataDataInv($nascimento);
		if (!isset($cidade)) {
			$cidade = 0;
		} else {
			$cidade = turnZero($cidade);
		}
		if (!isset($estado)) {
			$estado = 0;
		} else {
			$estado = turnZero($estado);
		}

		$senha = rand("10000", "99999");
		$SENHA = md5($senha);

		echo "<div class='msg'><h2>Senha de acesso:</h2><h3 style='color:red;'>$senha</h3></div>";

		$instrucao = "insert into usuario ";
		$instrucao .= "(login, senha, nome, data_nascimento, funcao, email, telefone1, telefone2, endereco, ";
		$instrucao .= "numero, bairro, cidade, estado, cep, rg, cpf, carteira, salario_base, dependentes, data_admissao, ass_digital_usar) ";
		$instrucao .= "values ";
		$instrucao .= "('$login','$SENHA','$nome','$nascimento','$funcao','$email','$telefone1','$telefone2','$endereco', ";
		$instrucao .= "'$numero','$bairro','$cidade','$estado','$cep','$rg','$cpf','$carteira','$salario','$dependentes','$admissao', '$ass_digital');";

		$sql = query($instrucao);
		$numeroUsuario = mysqli_insert_id($conexao);

		$instrucao = "insert into credenciais ";
		$instrucao .= "(id_usuario, ferramenta) ";
		$instrucao .= "values ";
		$virgula = false;
		foreach ($_POST["ferramentaCP"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$value = str_replace("cadastrar", "", $value);
			$instrucao .= "('$numeroUsuario', 'cadastrar".$value.".php'), ";
			$instrucao .= "('$numeroUsuario', 'pesquisa".$value.".php')";
			$virgula = true;
		}
		/*
		foreach ($_POST["ferramentaP"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		*/
		foreach ($_POST["ferramentaR"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		foreach ($_POST["ferramentaRH"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		
		foreach ($_POST["ferramentaA"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inseriu um novo usuário.";
		$tabela_afetada = "usuario";
		$chave_principal = $numeroUsuario;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);	

		$info .= "Usuario inserido com sucesso.<br>";
		

	} elseif ($op == "editar" and $valida) {
		
		$admissao = formataDataInv($admissao);
		$nascimento = formataDataInv($nascimento);

		$instrucao = "update usuario set ";
		$instrucao .= "nome = '$nome', data_nascimento = '$nascimento', funcao = '$funcao', email = '$email', telefone1 = '$telefone1', ";
		$instrucao .= "telefone2 = '$telefone2', endereco = '$endereco', numero = '$numero', bairro = '$bairro', cidade = '$cidades', estado = '$estados', ";
		$instrucao .= "cep = '$cep', rg = '$rg', cpf = '$cpf', carteira = '$carteira', salario_base = '$salario', dependentes = '$dependentes', ";
		$instrucao .= "data_admissao = '$admissao', ass_digital_usar='$ass_digital' ";
		$instrucao .= "where id = '$ID'";

		$sql = query($instrucao);
		$numeroUsuario = $ID;

		$instrucao = "delete from credenciais where id_usuario='$ID'";
		$sql = query($instrucao);

		$instrucao = "insert into credenciais ";
		$instrucao .= "(id_usuario, ferramenta) ";
		$instrucao .= "values ";
		$virgula = false;
		foreach ($_POST["ferramentaCP"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$value = str_replace("cadastrar", "", $value);
			$instrucao .= "('$numeroUsuario', 'cadastrar".$value.".php'), ";
			$instrucao .= "('$numeroUsuario', 'pesquisa".$value.".php')";
			$virgula = true;
		}
		/*
		foreach ($_POST["ferramentaP"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		*/
		foreach ($_POST["ferramentaR"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		
		foreach ($_POST["ferramentaRH"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}

		foreach ($_POST["ferramentaA"] as $key => $value) {
			if ($virgula) {
				$instrucao .= ", ";
			}
			$instrucao .= "('$numeroUsuario', '$value.php')";
			$virgula = true;
		}
		$sql = query($instrucao);
		$info .= "Usuario editado com sucesso.<br>";
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou um novo usuário.";
		$tabela_afetada = "usuario";
		$chave_principal = $ID;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
	} elseif ($op == "visualizar") {
		$numeroUsuario = $ID;
	}else{
		$numeroUsuario = null;
	}
	if ($valida) {
		$cor = "green";
	} else {
		$cor = "red";
	}
	if (!empty($info)) {
		info($info, $cor);
	}
	
	cadastroUsuario($numeroUsuario);

} elseif (isset($_GET["ativacao"])) {

	function validacaoToken() {
		echo "<form method='post' action='cadastrarUsuario.php?ativacao=" . $_GET["ativacao"] . "&ID=" . base64_encode($_GET["ID"]) . "' enctype='multipart/form-data' style='display:inline;'>";
		echo "Para completar essa operação insira um token<br>";
		echo "<input type='password' name='token' style='width:auto;'>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
		echo "</form>";
	}

	extract($_GET);
	extract($_POST);

	if (isset($token)) {
		$hoje = date('Y-m-d H:i:s');
		$instrucao = "select * from tokens where token='" . md5($token) . "' and data_validade>='$hoje' and vezes_permitido > any (select vezes_usado from tokens where token='" . md5($token) . "' and data_validade>='$hoje')";
		$sqlToken = query($instrucao);
		$linhaToken = mysqli_num_rows($sqlToken);
		if ($linhaToken > 0) {
			$regToken = mysqli_fetch_assoc($sqlToken);
			$regToken["vezes_usado"]++;
			$sqlToken = query("update tokens set vezes_usado='" . $regToken["vezes_usado"] . "' where token='" . md5($token) . "'");
			$validaToken = false;

			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Usou um token.";
			$tabela_afetada = "tokens";
			$chave_principal = $regToken["id"];

			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

		} else {
			echo validacaoToken();

			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Tentou usar um token.";
			$tabela_afetada = NULL;
			$chave_principal = NULL;

			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		}
	} elseif(getCredencialUsuario("administrativoToken")) {
		$acao = "Usou um token";
	}else{
		echo validacaoToken();
		$acao = "";
	}
	$ID = base64_decode($ID);
	if ($ativacao == "true" and $acao == "Usou um token") {
		$instrucao = "update usuario set status='Ativo', data_demissao='0000-00-00' where id='$ID'";
		$info = "Usuario ativado com sucesso.<br>";
		$sql = query($instrucao);
		info($info);
		cadastroUsuario($ID);
	} elseif ($ativacao == "false" and $acao == "Usou um token") {
		$demissao = date('Y-m-d');
		$instrucao = "update usuario set status='Inativo', data_demissao='$demissao' where id='$ID'";
		$info = "Usuario desativado com sucesso.<br>";
		$sql = query($instrucao);
		info($info);
		cadastroUsuario($ID);
	}
} else {
	cadastroUsuario();
}

include "templates/downLogin.inc.php";
?>