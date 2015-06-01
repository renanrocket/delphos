<?php
define ("CONST_ARQ", "individual"); // constante para pasta onde estão todos os arquivos individuais de cada sistema
define ("EMAIL", "renan.the.rock@gmail.com"); // constante para pagamento ao pagSeguro do sistema
define ("TOKEN", "576AFCA593164F00B2348E102DF12647"); //segunda constante para pagamento ao pagSeguro do sistema
define ("EMAIL_ROCKET", "falecom@rocketsolution.com.br");


define ("OPERACAO_ERRO", "Sentimos muito mas não foi possível completar esta operação.<br>Por favor, tente mais tarde ou contate a administração do sistema.<br>");


//recebe o cookie do login e retorna o id dele (renan)
function getIdCookieLogin($cookie, $conn = ALCUNHA) {

	if ($cookie) {
		$conn = TConnection::open($conn);
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter("login", "=", $cookie));
		
		$sql = new TSqlSelect;
		$sql->setEntity("usuario");
		$sql->addColumn('id');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
		}else{
			$id = 0;
		}

		//deletar esse codigo apois migração do app.ado
		if(isset($conexao)){
			$sql = query("select id from usuario where login='$cookie'");
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
		}
	} else {
		$id = 0;
	}

	return $id;

}

//recebe o cookie do login e retorna o nome dele (renan)
function getNomeCookieLogin($cookie, $completo = true, $conn = ALCUNHA) {
	
	if ($cookie) {
		$conn = TConnection::open($conn);
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter("login", "=", $cookie));
		
		$sql = new TSqlSelect;
		$sql->setEntity("usuario");
		$sql->addColumn('nome');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
		}

		//deletar esse codigo apois migração do app.ado
		if(isset($conexao)){
			$sql = query("select nome from usuario where login='$cookie'");
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
		}
		
	} else {
		$nome = 0;
	}	
	if(!$completo){
		$nome = explode(" ", $nome);
		$cont = count($nome);
		$nome = $nome[0]." ".$nome[$cont-1];
	}

	return $nome;

}

function primeiroUltimo($nome, $primeiraM = false){
	$nome = explode(" ", $nome);
	$cont = count($nome);
	$nome = $nome[0]." ".$nome[$cont-1];
	if($primeiraM){
		$nome = ucwords(strtolower($nome));
	}
	return $nome;
}

//recebe o cookie do login e retorna o nome dele (renan)
function getNomeUsuarioCliente($pk, $busca = "id", $tabela = "usuario", $completo = true, $conn = ALCUNHA) {
	
	if ($pk) {
		$conn = TConnection::open($conn);
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter("id", "=", $pk));
		
		$sql = new TSqlSelect;
		$sql->setEntity($tabela);
		$sql->addColumn('nome');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
		}

		//deletar esse codigo apois migração do app.ado
		if(isset($conexao)){
			$sql = query("select nome from $tabela where $busca='$pk'");
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
		}
		
	} else {
		$nome = 0;
	}	
	if(!$completo){
		$nome = explode(" ", $nome);
		$cont = count($nome);
		$nome = $nome[0]." ".$nome[$cont-1];
	}

	return $nome;

}
function getCredencialUsuarioInter($credencial){
	if($credencial == "cadastrarUsuario.php" and (isset($_GET["ID"]) or isset($_POST["ID"]))){
		return true;
	}elseif($credencial=="indexUsuario.php" and isset($_COOKIE["login"])) {
		return true;
	}else{
		if(!strstr($credencial, ".php")){
			$credencial .= ".php";
		}
		$instrucao = "select * from credenciais where ferramenta like '%$credencial' and id_usuario='".getIdCookieLogin($_COOKIE["login"])."'";
		$sqlCredencial = query($instrucao);
		if(mysqli_num_rows($sqlCredencial)>0){
			return true;
		}else{
			return false;
		}
	}
}
function getCredencialUsuario($CREDENCIAL){
	
	if(is_array($CREDENCIAL)){
		foreach ($CREDENCIAL as $chave => $credencial) {
			if(is_array($credencial)){
				foreach ($credencial as $key => $value) {
					$valores[] = getCredencialUsuarioInter($value);
				}
			}else{
				$valores[] = getCredencialUsuarioInter($credencial);
			}
		}
		if(in_array(true, $valores)){
			return true;
		}else{
			return false;
		}
	}else{
		return getCredencialUsuarioInter($CREDENCIAL);
	}
}

//funcao calcular preco do produto por id de produto
//funcao recebe o id do produto e recebe true de acordo com o valor q deseja se retornar entre (ml, descMax)
function precoProduto($id = null, $ml = false, $descMax = false, $custo = false) {
	if($id){
		$sql = query("select valor_compra, mlpor, descMaxpor from produto where id='$id'");
		
		if(mysqli_num_rows($sql)){
			extract(mysqli_fetch_assoc($sql));
			
			$sql = query("select * from produto_tributacao where id_produto='$id'");
			for($i = $tributacaoValor = 0; $i<mysqli_num_rows($sql); $i++){
				extract(mysqli_fetch_assoc($sql));
				if($tipo_valor==0){
					$tributacaoValor += $valor;
				}elseif($tipo_valor==1){
					$tributacaoValor += $valor_compra * $valor / 100;
				}
			}
			$valor_custo = $tributacaoValor + $valor_compra;
			if ($ml) {
				return round(($mlpor / 100 * $valor_custo), 2);
			}
			if ($descMax) {
				$ml = round(($mlpor / 100 * $valor_custo), 2);
				return round($ml - ($descMaxpor / 100 * $ml), 2);
			}
			if($custo){
				return round($valor_custo, 2);
			}
		}else{
			return "";
		}
	}else{
		return "";
	}
	
}
//funcao que calcula preco do servico por id do produto e quantidade relacionada
function precoServico($id, $qtd){
	$instrucao1="select * from servico_preco where id_servico='$id' and limite>='$qtd' order by limite asc";
	$instrucao2="select * from servico_preco where id_servico='$id' order by limite desc";
	$sql = query($instrucao1);
	if(mysqli_num_rows($sql)==0){
		$sql = query($instrucao2);
	}
	extract(mysqli_fetch_assoc($sql));
	return $valor;
}

//funcao para inserir no historico
function insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal) {

	$instrucao = "insert into historico ";
	$instrucao .= "(id_usuario, data, acao, tabela_afetada, chave_principal) values ";
	$instrucao .= "('$id_usuario', '$data', '$acao', '$tabela_afetada', '$chave_principal')";
	$sql = query($instrucao);
	return $sql;
}

//funcao formataDataInv, formata a data para forma do banco de dados. Recebe uma variavel assim DD/MM/YYYY HH:II:SS
//e a transforma assim: YYYY-MM-DD HH:II:SS
function formataDataInv($data) {
	if ($data) {
		$data = explode("/", $data);
		if (strstr($data[2], " ", true)) {
			$Data = explode(" " . $data[2]);
			$data = $Data[0] . "-" . $data[1] . "-" . $data[0] . " " . $Data[1];
		} else {
			$data = $data[2] . "-" . $data[1] . "-" . $data[0];
		}
	} else {
		$data = "0000-00-00";
	}
	return $data;
}

//funcao data, formata a data. Recebe uma variavel assim: YYYY-MM-DD HH:II:SS
//e a transforma assim: DD/MM/YYYY HH:II:SS
function formataData($DATA, $somenteHora = false, $somenteData = false) {
	if ($DATA and $DATA <> "0000-00-00") {
		$data = explode("-", $DATA);
		if (strstr($data[2], " ") == True) {
			$Data = explode(" ", $data[2]);
			if($somenteHora){
			    return $Data[1];
			}elseif($somenteData){
				return $Data[0] . "/" . $data[1] . "/" . $data[0];
			}else{
                return $Data[0] . "/" . $data[1] . "/" . $data[0] . " " . $Data[1];    
			}
			
		} else {
			return $data[2] . "/" . $data[1] . "/" . $data[0];
		}
	} else {
		return "";
	}
}

//volta ou avança a pagina recebe um numero inteiro
function historico($valor) {
	if ($valor < 0) {
		$txt = "Voltar.";
	} else {
		$txt = "Ir.";
	}
	return "<br><a href='javascript:window.history.go($valor)'>$txt</a><br>";
}

//funcao que criar uma string de opções recebe um parametro stringo (forma, tipo)
function pagamento($valor) {
	$instrucao = "select * from pagamento_" . $valor . " order by " . $valor . "_pagamento";
	$sql = query($instrucao);
	$linhas = mysqli_num_rows($sql);
	$opcao = "<option value=''>--</option>";
	for ($i = 0; $i < $linhas; $i++) {
		$reg = mysqli_fetch_row($sql);
		$opcao .= "<option value='$reg[0]'>$reg[1]</option>";
	}
	return $opcao;
}

//funcao para montar os selects
function opcaoSelect($nomeTb, $atributo, $status = NULL, $selected = NULL, $usar_reg1 = NULL, $SQL = null, $REG = 0, $traco = true) {
	
	
	$instrucao = "select * from $nomeTb";

	if ($status <> NULL) {
		$instrucao .= " where status='$status'";
	}

	if ($SQL) {
		$instrucao .= " " . $SQL;
	}
    
    
	$sql = query($instrucao);

	$linhas = mysqli_num_rows($sql);
    if($traco){
        $script = "<option value='0'>--</option>";
    }else{
    	$script = "";
    }
	
	for ($i = $index = 0; $i < $linhas; $i++) {

		if(!is_numeric($atributo)){
			$reg = mysqli_fetch_assoc($sql);
			$REG == 0 ? $REG = "id" : false;
			$index = "id";
		}else{
			$reg = mysqli_fetch_row($sql);
		}
        
        if ($usar_reg1 <> NULL) {
            if ($reg[$atributo] == $selected) {
                $script .= "<option value='" . $reg[$atributo] . "' selected='yes'>" . $reg[$atributo] . "</option>";
            } else {
                $script .= "<option value='" . $reg[$atributo] . "'>" . $reg[$atributo] . "</option>";
            }
        } else {
            if ($reg[$REG] == $selected) {
                $script .= "<option value='".$reg[$index]."' selected='yes'>" . $reg[$atributo] . "</option>";
            } else {
                $script .= "<option value='".$reg[$index]."'>" . $reg[$atributo] . "</option>";
            }
        }    

	}
	return $script;
}

//funcao q seleciona o atributo da tabela a partir de um resultado do PK
//pk eh o numero do cont
//nomeTabela eh o nome da tabela a qual se refere
//atributo eh o numero do atributo na sequencia q segue a tabela
function registro($pk, $nomeTabela, $atributo_exibir, $atributo_de_busca = "id", $conn = ALCUNHA) {
	
	
	$conn = TConnection::open($conn);
		
	$criterio = new TCriteria;
	$criterio->add(new TFilter($atributo_de_busca, "=", $pk));
	
	$sql = new TSqlSelect;
	$sql->setEntity($nomeTabela);
	$sql->addColumn($atributo_exibir);
	$sql->setCriteria($criterio);
		
	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		return $$atributo_exibir;
	}else{
		return '';
	}

	//deletar esse codigo apois migração do app.ado
	if(isset($conexao)){
		$sql = query("select * from $nomeTabela where $atributo_de_busca='$pk'");
		$reg = mysqli_fetch_assoc($sql);
		return $reg[$atributo_exibir];
	}
	//fim
}

//funcao que retorna true caso o email seja valido
function validaEmail($email){
	if($email){
		if (strstr($email,"@")==false or strstr($email,".")==false){
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
	
}

//funcao para detectar se o cnpj eh valido ou nao
function validaCNPJ($cnpj) {
	if (!$cnpj) {
		$cnpj = "00.000.000/0000-00";
	}
	if (strlen($cnpj) <> 18)
		return 0;
	$soma1 = ($cnpj[0] * 5) + ($cnpj[1] * 4) + ($cnpj[3] * 3) + ($cnpj[4] * 2) + ($cnpj[5] * 9) + ($cnpj[7] * 8) + ($cnpj[8] * 7) + ($cnpj[9] * 6) + ($cnpj[11] * 5) + ($cnpj[12] * 4) + ($cnpj[13] * 3) + ($cnpj[14] * 2);
	$resto = $soma1 % 11;
	$digito1 = $resto < 2 ? 0 : 11 - $resto;
	$soma2 = ($cnpj[0] * 6) + ($cnpj[1] * 5) + ($cnpj[3] * 4) + ($cnpj[4] * 3) + ($cnpj[5] * 2) + ($cnpj[7] * 9) + ($cnpj[8] * 8) + ($cnpj[9] * 7) + ($cnpj[11] * 6) + ($cnpj[12] * 5) + ($cnpj[13] * 4) + ($cnpj[14] * 3) + ($cnpj[16] * 2);
	$resto = $soma2 % 11;
	$digito2 = $resto < 2 ? 0 : 11 - $resto;
	return (($cnpj[16] == $digito1) && ($cnpj[17] == $digito2));
}

//funcao para detectar se o cpf eh valido ou nao
function validaCPF($cpf) {
	if (!$cpf) {
		$cpf = "000.000.000-00";
	}
	//Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cpf em diferentes formatos como "000.000.000-00", "00000000000", "000 000 000 00" etc...
	$j = 0;
	for ($i = 0; $i < (strlen($cpf)); $i++) {
		if (is_numeric($cpf[$i])) {
			$num[$j] = $cpf[$i];
			$j++;
		}
	}
	//Etapa 2: Conta os dígitos, um cpf válido possui 11 dígitos numéricos.
	if (count($num) != 11) {
		$isCpfValid = false;
	}
	//Etapa 3: Combinações como 00000000000 e 22222222222 embora não sejam cpfs reais resultariam em cpfs válidos após o calculo dos dígitos verificares e por isso precisam ser filtradas nesta parte.
	else {
		for ($i = 0; $i < 10; $i++) {
			if ($num[0] == $i && $num[1] == $i && $num[2] == $i && $num[3] == $i && $num[4] == $i && $num[5] == $i && $num[6] == $i && $num[7] == $i && $num[8] == $i) {
				$isCpfValid = false;
				break;
			}
		}
	}
	//Etapa 4: Calcula e compara o primeiro dígito verificador.
	if (!isset($isCpfValid)) {
		$j = 10;
		for ($i = 0; $i < 9; $i++) {
			$multiplica[$i] = $num[$i] * $j;
			$j--;
		}
		$soma = array_sum($multiplica);
		$resto = $soma % 11;
		if ($resto < 2) {
			$dg = 0;
		} else {
			$dg = 11 - $resto;
		}
		if ($dg != $num[9]) {
			$isCpfValid = false;
		}
	}
	//Etapa 5: Calcula e compara o segundo dígito verificador.
	if (!isset($isCpfValid)) {
		$j = 11;
		for ($i = 0; $i < 10; $i++) {
			$multiplica[$i] = $num[$i] * $j;
			$j--;
		}
		$soma = array_sum($multiplica);
		$resto = $soma % 11;
		if ($resto < 2) {
			$dg = 0;
		} else {
			$dg = 11 - $resto;
		}
		if ($dg != $num[10]) {
			$isCpfValid = false;
		} else {
			$isCpfValid = true;
		}
	}
	//Trecho usado para depurar erros.
	/*
	 if($isCpfValid==true)
	 {
	 echo "<font color=\"GREEN\">Cpf é Válido</font>";
	 }
	 if($isCpfValid==false)
	 {
	 echo "<font color=\"RED\">Cpf Inválido</font>";
	 }
	 */
	//Etapa 6: Retorna o Resultado em um valor booleano.
	return $isCpfValid;
}

//funcao de confirmação do banco de dados
function confirmacaoDB($msg, $redirecionamento = null) {
    
	global $conexao;
	
	if (mysqli_affected_rows($conexao) > 0) {
		echo info($msg);
		if($redirecionamento){
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=$redirecionamento'>";
		}
	} else {
		include_once "inc/msgErro.inc";
	}
}

//funcao para converter em 0 se caso for NULL ou empty
function turnZero($var, $null = null) {
		
	if (empty($var)) {
			
		if($null){
			$var = "NULL";
		}else{
			$var = 0;
		}
		
	}
	return $var;
}


function showImagemProduto($idProduto, $qtdPorLinha, $quantidadeMax = 0, $mostrarDelete = false){
		
	//detectando se estar em um iframe
	if(!file_exists("img/icones/pesquisaClienteFornecedor.png")){
		$caminhoCheck = "../";
	}else{
		$caminhoCheck = "";
	}
	
	$script = "";
	
	$sql = query("select * from produto_imagem where id_produto='$idProduto'");
	if(mysqli_num_rows($sql)>0){
		$quantidadeMax == 0 ? $imagensQtd =  mysqli_num_rows($sql) : $imagensQtd = $quantidadeMax;
		$quantidadeMax > mysqli_num_rows($sql) ? $imagensQtd = mysqli_num_rows($sql) : false;
		
		for($i=1; $i<=$imagensQtd; $i++){
			
			extract(mysqli_fetch_assoc($sql));
			$script .= "<div style='display:inline-block;'>";
			if(!file_exists($caminhoCheck.$imagem)){
				$imagem = $caminhoCheck."img/icones/pesquisaProduto.png";
			}
			if(!file_exists($caminhoCheck.$miniatura)){
				$miniatura = $caminhoCheck."img/icones/pesquisaProduto.png";
			}
			
			//detectando se estar em um iframe
			if(!file_exists("img/icones/pesquisaClienteFornecedor.png")){
				$pop1 = $pop2 = "";
			}else{
				$pop1 = pop($caminhoCheck."imagem.php?img=$imagem");
				$pop2 = pop($caminhoCheck."produto_imagem_upload.php?id=$idProduto&a=delete&idDeletar=$id", 10, 10);
			}
			
			$script .= "<a href='javascript:void(0)' $pop1 title=\"<img style='height:400px;' src='".$caminhoCheck."$imagem' >\"><img src='".$caminhoCheck."$miniatura'></a><br>";
			if($mostrarDelete){
				$script .= "<a href='javascript:void(0)' $pop2 title='Deletar esta imagem.'>Deletar</a>";
			}
			$script .= "</div>";
			if($i%$qtdPorLinha==0){
				$script .= "<br>";
			}
			
		}	
	}else{
		$script .= "<div style='display:inline-block;'>";
		$script .= "<a href='javascript:void(0)' title='Sem imagem'><img src='".$caminhoCheck."img/icones/pesquisaProduto.png'></a>";
		$script .= "</div>";
	}
	
	
	return $script;
}

function showImagemExercicio($idExercicio, $qtdPorLinha, $quantidadeMax = 0, $mostrarDelete = false){
		
	//detectando se estar em um iframe
	if(!file_exists("img/icones/pesquisaExercicio.png")){
		$caminhoCheck = "../";
	}else{
		$caminhoCheck = "";
	}
	
	$script = "";
	
	$sql = query("select * from exercicio_imagem where id_exercicio='$idExercicio'");
	if(mysqli_num_rows($sql)>0){
		$quantidadeMax == 0 ? $imagensQtd =  mysqli_num_rows($sql) : $imagensQtd = $quantidadeMax;
		$quantidadeMax > mysqli_num_rows($sql) ? $imagensQtd = mysqli_num_rows($sql) : false;
		
		for($i=1; $i<=$imagensQtd; $i++){
			
			extract(mysqli_fetch_assoc($sql));
			$script .= "<div style='display:inline-block;'>";
			if(!file_exists($caminhoCheck.$imagem)){
				$imagem = $caminhoCheck."img/icones/pesquisaExercicio.png";
			}
			if(!file_exists($caminhoCheck.$miniatura)){
				$miniatura = $caminhoCheck."img/icones/pesquisaExercicio.png";
			}
			
			//detectando se estar em um iframe
			if(!file_exists("img/icones/pesquisaExercicio.png")){
				$pop1 = $pop2 = "";
			}else{
				$pop1 = pop($caminhoCheck."imagem.php?img=$imagem");
				$pop2 = pop($caminhoCheck."exercicio_imagem_upload.php?id=$idExercicio&a=delete&idDeletar=$id", 10, 10);
			}
			
			$script .= "<a href='javascript:void(0)' $pop1 title=\"<img style='height:400px;' src='".$caminhoCheck."$imagem' >\"><img src='".$caminhoCheck."$miniatura'></a><br>";
			if($mostrarDelete){
				$script .= "<a href='javascript:void(0)' $pop2 title='Deletar esta imagem.'>Deletar</a>";
			}
			$script .= "</div>";
			if($i%$qtdPorLinha==0){
				$script .= "<br>";
			}
			
		}	
	}else{
		$script .= "<div style='display:inline-block;'>";
		$script .= "<a href='javascript:void(0)' title='Sem imagem'><img src='".$caminhoCheck."img/icones/pesquisaExercicio.png'></a>";
		$script .= "</div>";
	}
	
	return $script;
}

function showImagemClienteFornecedor($idClienteFornecedor, $qtdPorLinha, $quantidadeMax = 0, $mostrarDelete = false){
	
	$script = "";
	//detectando se estar em um iframe
	if(!file_exists("img/icones/pesquisaClienteFornecedor.png")){
		$caminhoCheck = "../";
	}else{
		$caminhoCheck = "";
	}
	
	$sql = query("select * from cliente_fornecedor_imagem where id_cliente_fornecedor='$idClienteFornecedor'");
	
	
	if(mysqli_num_rows($sql)>0){
		$quantidadeMax == 0 ? $imagensQtd =  mysqli_num_rows($sql) : $imagensQtd = $quantidadeMax;
		$quantidadeMax > mysqli_num_rows($sql) ? $imagensQtd = mysqli_num_rows($sql) : false;
		
		for($i=1; $i<=$imagensQtd; $i++){
			extract(mysqli_fetch_assoc($sql));
			$script .= "<div style='display:inline-block;'>";
			if(!file_exists($caminhoCheck.$imagem)){
				$imagem = $caminhoCheck."img/icones/pesquisaClienteFornecedor.png";
			}else{
				$imagem = $caminhoCheck.$imagem;
			}
			if(!file_exists($caminhoCheck.$miniatura)){
				$miniatura = $caminhoCheck."img/icones/pesquisaClienteFornecedor.png";
			}else{
				$miniatura = $caminhoCheck.$miniatura;
			}
			//detectando se estar em um iframe
			if(!file_exists("img/icones/pesquisaClienteFornecedor.png")){
				$caminhoCheck = "../";
			}
			$pop1 = pop($caminhoCheck."imagem.php?img=$imagem");
			$pop2 = pop($caminhoCheck."cliente_fornecedor_imagem_upload.php?id=$idClienteFornecedor&a=delete&idDeletar=$id", 10, 10);
			
			
			$script .= "<a href='javascript:void(0)' $pop1 title=\"<img style='height:400px;' src='$imagem' >\"><img src='$miniatura'></a><br>";
			if($mostrarDelete){
				$script .= "<a href='javascript:void(0)' $pop2 title='Deletar esta imagem.'>Deletar</a>";
			}
			$script .= "</div>";
			if($i%$qtdPorLinha==0){
				$script .= "<br>";
			}
			
		}	
	}else{
		$script .= "<div style='display:inline-block;'>";
		$script .= "<a href='javascript:void(0)' title='Sem imagem'><img src='".$caminhoCheck."img/icones/pesquisaClienteFornecedor.png'></a>";
		$script .= "</div>";
	}
	
	
	return $script;
}

function showImagemUsuario($idUsuario, $qtdPorLinha, $quantidadeMax = 0, $mostrarDelete = false){
	
	$script = "";
	//detectando se estar em um iframe
	if(!file_exists("img/icones/cadastrarUsuario.png")){
		$caminhoCheck = "../";
	}else{
		$caminhoCheck = "";
	}
	
	$instrucao = "select * from usuario_imagem where id_usuario='$idUsuario'";
	$sql = query($instrucao);
	
	
	if(mysqli_num_rows($sql)>0){
		$quantidadeMax == 0 ? $imagensQtd =  mysqli_num_rows($sql) : $imagensQtd = $quantidadeMax;
		$quantidadeMax > mysqli_num_rows($sql) ? $imagensQtd = mysqli_num_rows($sql) : false;
		
		for($i=1; $i<=$imagensQtd; $i++){
			extract(mysqli_fetch_assoc($sql));
			$script .= "<div style='display:inline-block;'>";
			if(!file_exists($caminhoCheck.$imagem)){
				$imagem = $caminhoCheck."img/icones/pesquisaClienteFornecedor.png";
			}else{
				$imagem = $caminhoCheck.$imagem;
			}
			if(!file_exists($caminhoCheck.$miniatura)){
				$miniatura = $caminhoCheck."img/icones/pesquisaClienteFornecedor.png";
			}else{
				$miniatura = $caminhoCheck.$miniatura;
			}
			
			
			$script .= "<a href='javascript:void(0)' ".pop($caminhoCheck."imagem.php?img=$imagem")." title=\"Clique para ver apliado.\"><img src='$miniatura'></a><br>";
			if($mostrarDelete){
				$script .= "<a href='javascript:void(0)' ".pop($caminhoCheck."usuario_imagem_upload.php?id=$idUsuario&a=delete&idDeletar=$id", 10, 10)." title='Deletar esta imagem.'>Deletar</a>";	
			}
			$script .= "</div>";
			if($i%$qtdPorLinha==0){
				$script .= "<br>";
			}
			
		}	
	}else{
		$script .= "<div style='display:inline-block;'>";
		$script .= "<a href='javascript:void(0)' title='Sem imagem'><img src='".$caminhoCheck."img/icones/pesquisaClienteFornecedor.png'></a>";
		$script .= "</div>";
	}
	
	
	return $script;
}

function empresa($op = "novo", $id= null, $imgsrc = null, $razao_social = null, $nome = null, $cnpj = null, 
        $data_fundacao = null, $email = null, $fone1 = null, $fone2 = null, $fone3 = null, $endereco = null, 
        $numero = null, $complemento = null, $bairro = null, $cidade = null, $estado = null, $cep = null, $usarTimbrado = null) {
	
	//js para esta pagina em especifico
	echo "<script src='js/cadastrarEmpresaFiltro.js' type='text/javascript'></script>
		<script src='js/cadastrarEmpresaFormulario.js' type='text/javascript'></script>";

	echo "<div style='font-size:10px; position: absolute; top:40px; width:1000px;' id='msg'></div>";
	echo "<form name='formCadastraEmpresa' id='formCadastraEmpresa' method='post' action='cadastrarEmpresa.php' enctype='multipart/form-data' onSubmit='return filtro();'>";

	echo "<input type='hidden' name='op' value='$op'>";

	if($op=="editar"){
	    echo "<input type='hidden' name='id' value='$id'>";
	    $sql = query("select * from empresa where id='$id'");
        extract(mysqli_fetch_assoc($sql));
	}

	echo "<center>";
	echo "<div id='visualizar'></div>";

	echo "<table>";
	echo "<tr>";
	echo "<td>Razão Social<br><input type='text' name='razao_social' value='$razao_social'></td>";
	echo "<td colspan='2'>Nome fantasia<br><input type='text' name='nome' value='$nome'></td>";
	echo "<tr>";
	echo "<tr>";
	echo "<td colspan='3'>Endereço<br><textarea name='endereco'>$endereco</textarea></td>";
	echo "<tr>";
	echo "<tr>";
	echo "<td>Bairro<br><input type='text' name='bairro' value='$bairro'></td>";
	echo "<td>Complemento<br><input type='text' name='complemento' value='$complemento'></td>";
	echo "<td>Número<br><input type='text' name='numero' value='$numero' " . mascara("Integer") . "></td>";
	echo "<tr>";
	echo "<tr>";
	echo "<td>Estado<br><select name='estados' id='estados' onchange=\"ajax(this.value, 'cidades')\">";
	echo opcaoSelect("estados", "2", false, $estado);
	echo "<select></td>";
	if ($cidade) {
		echo "<td id='tdCidades'>";
		$sql = query("select cod_cidades, nome from cidades where estados_cod_estados='$estado'");
		echo "Cidades<br><select name='cidade' onchange=\"ajax(this.value, 'cep')\"' id='cidade'>";
		for ($linha = mysqli_num_rows($sql); $linha > 0; $linha--) {
			extract(mysqli_fetch_assoc($sql));
			if ($cod_cidades == $cidade) {
				echo "<option value='$cod_cidades' selected='yes'>$nome</option>";
			} else {
				echo "<option value='$cod_cidades'>$nome</option>";
			}

		}
		echo "</select>";
		echo "</td>";
	} else {
		echo "<td id='tdCidades'>Cidade<br><select name='cidade' id='cidade' disabled>";
		echo "</select></td>";
	}
	echo "<td id='tdCep'>CEP<br><input type='text' name='cep' value='$cep' " . mascara("Cep", "9") . "></td>";
	echo "<tr>";

	echo "<tr>";
	echo "<td>Telefone 1<br><input type='text' name='fone1' value='$fone1' " . mascara("Telefone", "15") . "></td>";
	echo "<td>Telefone 2<br><input type='text' name='fone2' value='$fone2' " . mascara("Telefone", "15") . "></td>";
	echo "<td>Telefone 3<br><input type='text' name='fone3' value='$fone3' " . mascara("Telefone", "15") . "></td>";
	echo "<tr>";

	echo "<tr>";
	echo "<td>E-mail<br><input type='text' name='email' value='$email' class='email'></td>";
	echo "<td>Data de fundação<br>".inputData("formCadastraEmpresa", "data_fundacao", NULL, formataData($data_fundacao))."</td>";
	echo "<td align='center' valign='middle' id='logo'>Logomarca<br>";
	if ($imgsrc) {
		echo "<img style='height:100px;' src='$imgsrc'><br>";
		echo "<a href='javascript:void(0)' onclick='logoInput();'>Trocar de imagem</a>";
	} else {
		echo "<input type='file' name='imgsrc' id='imgsrc' value='$imgsrc' accept='image/*'><br><span style='font-size:11px;'>Recomenda-se imagem do tamaho de até 182px X 100px</span>";
	}
	echo "</td>";
	echo "<tr>";

	echo "<tr>";
	echo "<td>CNPJ<br><input type='text' name='cnpj' value='$cnpj' " . mascara("Cnpj", "18") . "></td>";
	echo "<td colspan='2'>Usar esses dados como timbrado?<br><select name='usarTimbrado'>";
	if ($id == null){
		echo "<option value='0'>Não</option>";
		echo "<option value='1' selected='yes'>Sim</option>";
	} elseif($usarTimbrado == 0 or $usarTimbrado == "") {
        echo "<option value='0' selected='yes'>Não</option>";
        echo "<option value='1'>Sim</option>";
    }else{
		echo "<option value='0'>Não</option>";
		echo "<option value='1' selected='yes'>Sim</option>";
	}
	echo "</select></td>";
	echo "<tr>";

	echo "<tr>";
	echo "<td colspan='2'></td>";
	echo "<td><input type='submit' class='btnEnviar' value='Enviar'></td>";
	echo "<tr>";

	echo "</table>";

	echo "</center>";

	echo "</form>";
}

//funcao para selecionar a mascara nas input text
##  Tipos de mascara ##
/*
 * Integer Telefone TelefoneCall Cpf Cep Cnpj Romanos Data Hora Valor Valor2 Valor3 Area Placa
 */
function mascara($mascara, $tamanho = null, $codExtra = null , $onKeyDown = null, $onKeyPress = null, $onKeyUp = null) {
	$cod = "onKeyDown='Mascara(this,$mascara); $onKeyDown' onKeyPress='Mascara(this,$mascara); $onKeyPress' onKeyUp='Mascara(this,$mascara); $onKeyUp'";
	switch ($mascara) {
		case 'Telefone':
			$cod .= "placeholder='(00) 00000.0000'";
			break;
		case 'Cpf':
			$cod .= "placeholder='000.000.000-00'";
			break;
		case 'Cep':
			$cod .= "placeholder='00000-000'";
			break;
		
		default:
			# code...
			break;
	}
	
	if ($tamanho) {
		$cod .= " maxlength='$tamanho'";
	}
	if ($codExtra) {
		$cod .= " " . $codExtra;
	}

	return $cod;
}

//funcao para criar uma input data com um botao de calendario do lado
#----------------------------- nescessario nomear o formulario e por ele na variavel NOMEFORMULARIO ---------------------------#
function inputData($nomeFormulario, $nomeInput, $posicao, $valorInput = null, $classInput = null, $id = null) {
	
	$cod = "<script type='text/javascript'>\n";
	$cod .= "$(function() {\n";
    			$cod .= "$(\"input[name='".$nomeInput."']\").datepicker();\n";
			$cod .= "});\n";
	$cod .= "</script>";
	$cod .= "<input type='text' name='$nomeInput' value='$valorInput' placeholder='DD/MM/AAAA' class='inputData $classInput' ".mascara("Data", "10").">";
	
	return $cod;
}

// funcao que corrige o problema do ponto e da virgula em valores que envolve moeda. essa funcao recebem um valor float
function real($num, $ponto = null) {
	
	if($ponto){
		$ponto = ".";
		$ponto2 = ",";
		$stringAntes = explode($ponto2,$num);
		$stringAntes = strlen($stringAntes[0]);
		$num = substr( $num, 0, $stringAntes + 3 );
		//$num= round($num,2);
		if ($num) {

			
			$num = str_replace(".", $ponto, $num);
			
			if (strstr($num, $ponto) == false) {
				$num .= $ponto."00";
			}
			$check = explode($ponto, $num);
			if (strstr($num, $ponto) == true and strlen($check[1]) < 2) {
				$num .= "0";
			}
		} else {
			$num = "00".$ponto."00";
		}
	}else{
		if(!strstr($num, ',')){

			$num = explode('.', $num);
			$num[0] = strrev($num[0]);
			for($i=0, $num2 = null; $i<strlen($num[0]); $i++){
				if($i%3==0 & $i!=0){
					$num2.='.';
				}
				$num2.= $num[0]{$i};
			}

			if(!isset($num[1])){
				$num[1] = '00';
			}else{
				if(strlen($num[1])<2){
					$num[1].= '0';
				}
			}
			if($num2>0 and strlen($num2)<2){
				$num2.='0';
			}elseif(strlen($num2)<1){
				$num2=0;
			}
			

			$num = strrev($num2).','.$num[1];
		}

	}
	
	
	return $num;
}

function real2($num) {
	
	$num = str_replace('.', '', $num);
	$num = str_replace(',', '.', $num);
	
	
	return $num;
}

function orcamento($id_orcamento = null){
	//js para esta pagina em especifico
	echo "<script src='js/cadastrarOrcamentoFiltro.js' type='text/javascript'></script>";
		echo "<center>";

		//orcamento por email começa aqui
		if($id_orcamento){
			$sql = query("select * from orcamento where id='$id_orcamento'");
			extract(mysqli_fetch_assoc($sql));
			if($status==1){
				$tipo="Orçamento";
			}else{
				$tipo="Venda";
			}
			$sqlEmpresa = query("select * from empresa where usarTimbrado='1'");
			extract(mysqli_fetch_assoc($sqlEmpresa));
			$email_empresa = $email;
			
			$msg = "<form style='display:block' target='_blank' class='form' id='email' method='post' enctype='multipart/form-data'>";
			$msg.= "<div class='column' style='padding:none;'>";
			$msg.= "<label for='remetente'>Remetente</label>";
			$msg.= "<input type='text' style='text-transform:none;' id='remetente' name='remetente' placeholder='E-mail do remetente' value='$email_empresa'>";
			$msg.= "<label for='nome'>Destinatário</label>";
			if ($id_cliente){
				$sqlCliente = query("select email as emailCliente from cliente_fornecedor where id='$id_cliente'");
				extract(mysqli_fetch_assoc($sqlCliente));
			}else{
				$emailCliente = null;
			}
			$msg.= "<input type='text' name='nome' placeholder='Nome do destinatário' value='$cliente'>";
			$msg.= "<input type='text' style='text-transform:none;' id='destinatario' name='destinatario' placeholder='E-mail do destinatario' value='$emailCliente'>";
			$msg.= "<label for='nome'>Assunto</label>";
			$assunto = "$nome - $tipo $id_orcamento";
			$msg.= "<input type='text' name='assunto' id='assunto' placeholder='Assunto que irá aparecer no e-mail' value='$assunto'>";
			$msg.= "<label for='email'>E-mail</label>";
			
			$email= "<center>
			<table>
				<tr>
					<td rowspan='5'><img src='http://" . $_SERVER["HTTP_HOST"] . "/$imgsrc'></td>
				</tr>
				<tr>
					<td style='font-size: 17px;'>$nome<br>$fone1 $fone2 $fone3</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$razao_social</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$cnpj</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$endereco $numero $bairro 
					".registro($cidade, "cidades", "nome", "cod_cidades")." 
					".registro($estado, "estados", "sigla", "cod_estados")."</td>
				</tr>
			</table><br><br><br>
			Prezado ".ucwords(strtolower($cliente)).",<br>
			Estamos enviando o link para visualizar/imprimir $tipo <b>".$id_orcamento."</b>.";
			$link = "http://" . $_SERVER["HTTP_HOST"] . "/popup/orcamentoImp.php?orcamento=".base64_encode($id_orcamento)."&empresa=".base64_encode($_COOKIE["id_empresa"]);
			$email.= "<br>
			<a href='$link'><img src='http://" . $_SERVER["HTTP_HOST"] . "/img/icones/cadastrarOrcamento.png'></a><br>
			Se não conseguir visualizar o link <a href='$link'>Clique aqui!</a><br><br><br>
			Att.<br>
			".getNomeCookieLogin($_COOKIE["login"], false)."<br>".registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "email");
			

			$msg.= "<textarea name='email' id='corpo' class='ckeditor'>$email</textarea>";
			$onclick = "onclick='ajaxEnviarEmail(\"#remetente\", \"#destinatario\", \"#assunto\", \"#corpo\", \"email\");'";
			$msg.= "<script src='js/enviar_email.js' type='text/javascript'></script>";
			$msg.= "<input type='button' class='submit' value='Enviar e-mail' $onclick>";	
			$msg.= "</div>";
			$msg.= "</form>";
			
			info($msg, "green", null, "none", "email");
		}			               
        
		

		echo "<form name='formulario' method='post' action='cadastrarOrcamento.php' enctype='multipart/form-data' onSubmit='return (filtroClienteFornecedor() && filtroOrcamento() ? true: false);'>";
			if($id_orcamento){
				$sql = query("select * from orcamento where id='$id_orcamento'");
				extract(mysqli_fetch_assoc($sql));
				echo "<input type='hidden' name='op' value='editar'>";//o restante do hidden esta na ultima variavel
				$statusOrcamento = $status;
				$observacoesO = $observacoes;
			}else{
				$id = $id_cliente = $cliente = $fone = $descPor = $descReal = $observacoesO = $id_usuario = $data_emissao = $data_venda = null;
				$statusOrcamento = "";
				echo "<input type='hidden' name='op' value='novo'>";
			}
	
			echo "<table class='itens' id='gradient-style'>";
				
				if($id_orcamento){
					echo "<tr>";
						echo "<th colspan='6'>";
							echo "Orçamento $id_orcamento";
							echo "<div style='float:right;'>";
							switch ($statusOrcamento) {
								case '1':
									$opStatus = "venda";
									$title = "Efetuar venda";
									$link = "Plus";
									break;
								default:
									$opStatus = "venda_cancelar";
									$title = "Cancelar venda";
									$link = "Minus";
									break;
							}
							if($id_cliente){
								echo arqVisualizar("itensForm3", "Visualizar / Esconder restante do cadastro do cliente");
							}
							$instrucao = "select * from credenciais where ferramenta='pesquisaConta.php' and id_usuario='".getIdCookieLogin($_COOKIE["login"])."'";
							$sqlCredencial = query($instrucao);
							if(mysqli_num_rows($sqlCredencial)>0){
							    if(empty($id_cliente)){//cliente não cadastrado
							        $busca = $cliente;
							    }else{
							        $busca = $id_cliente;
							    }
								$instrucao = "select id as idConta from conta where entidade='$busca' and referido='$id_orcamento' and tabela_referido='orcamento'";
								$sqlConta = query($instrucao);
								extract(mysqli_fetch_assoc($sqlConta));
								echo "<a href='pesquisaConta2.php?conta=".base64_encode($idConta)."' title='Visualizar Conta'><img src='img/icones/pesquisaConta.png'></a> ";
							}
							if($statusOrcamento=="1" or $statusOrcamento=="2"){
								$msg = $title."?<br><br>";
								$msg .= "<a class='aSubmit' href='cadastrarOrcamento.php?orcamento=".base64_encode($id_orcamento)."&op=$opStatus'>Sim</a>";
								$msg .= "<a class='aSubmit' href='javascript:void(0)' onclick=\"infoApagar();\">Não</a>";
								info($msg, "#e2d000", null, "none", "confirmarVenda");
								echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "confirmarVenda")." title='$title'><img src='img/carrinho".$link.".png'></a>";
							}
							if(getCredencialUsuario("cadastrarPdv.php")){
			                    echo "<a href='javascript:void(0)' ".pop("cupom.php?tipo=orcamento&cupom=".base64_encode($id_orcamento), "auto", "auto")." title='Cupom não fiscal'><img src='img/icones/cadastrarOrcamento.png'></a>";
			                }
							echo "<a href='javascript:void(0)' ".pop("orcamentoImp.php?orcamento=".base64_encode($id_orcamento)."&empresa=".base64_encode($_COOKIE["id_empresa"]), "auto", "auto")." title='Versão para impressão'><img src='img/impressora.png'></a>";
			                echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "email")." title='Enviar Orçamento por E-mail'><img src='img/enviar_email.png'></a>";
			                if(getCredencialUsuario("administrativoToken.php") and $statusOrcamento!="4"){
			                    $msg = "Você deseja realmente deletar este orçamento?<br><br>";
								$msg .= "<a class='aSubmit' href='cadastrarOrcamento.php?id=".base64_encode($id_orcamento)."&op=deletar'>Sim</a>";
								$msg .= "<a class='aSubmit' href='javascript:void(0)' onclick=\"infoApagar();\">Não</a>";
								info($msg, "red", null, "none", "confirmar");
			                    echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "confirmar")." title='Deletar orçamento e suas respectivas contas geradas'><img src='img/deletar.png'></a> ";
			                }elseif(getCredencialUsuario("administrativoToken.php") and $statusOrcamento=="4"){
			                	$msg = "Você deseja realmente reincluir este orçamento?<br><br>";
						        $msg .= "<a class='aSubmit' href='cadastrarOrcamento.php?id=".base64_encode($id_orcamento)."&op=incluir'>Sim</a>";
								$msg .= "<a class='aSubmit' href='javascript:void(0)' onclick=\"infoApagar();\">Não</a>";
								info($msg, "green", null, "none", "confirmar");
			                	echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "confirmar")." title='Incluir novamente este orçamento.'><img src='img/inserir.png'></a> ";
			                }
							echo "</div>";
						echo "</th>";
					echo "</tr>";
				}
				
				
				if($id_cliente){
					$instrucao = "select * from cliente_fornecedor where id='$id_cliente'";
					$sql = query($instrucao);
					extract(mysqli_fetch_assoc($sql));
				}else{
					$id = $formato = $tipo = $razao_social = $cpf_cnpj = $rg_ie = $data_nascimento = $email = 
					$fone2 = $endereco = $numero = $bairro = $cidade = $estado = $cep = $referencia = $observacoes = 
					$status = $latitude = $longitude = null;
					$nome = $cliente;
					$fone1 = $fone;
				}
				$cliente = new cliente_fornecedor($id, $formato, $tipo, $nome, $razao_social, $cpf_cnpj, $rg_ie, $data_nascimento, $email,
				$fone1, $fone2, $endereco, $numero, $bairro, $cidade, $estado, $cep, $referencia, $observacoes, $latitude, $longitude, $status);
				echo $cliente->formulario(true);
				
				echo "<tr>";
					echo "<th scope='col'>";
						echo "<a href='#campoItem' class='adicionarCampo' title='Adicionar item ao orçamento'><img src='img/mais.png' width='30'></a>";
					echo "</th>";
					echo "<th scope='col' colspan='2'>Itens do orçamento</th>";
					echo "<th scope='col'>Quantidade</th>";
					echo "<th scope='col'>Sub Total</th>";
					echo "<th scope='col'>Total Item</th>";
				echo "</tr>";
				$sql = query("select * from orcamento_itens where id_orcamento='$id_orcamento'");
				$qtdItem= mysqli_num_rows($sql);
				if($qtdItem==0){
					$qtdItem=1;
				}
				echo "<input type='hidden' name='qtdItem' id='qtdItem' value='$qtdItem'>";
				$subTotal = $total = null;
				for($i=1; $i<=$qtdItem;$i++){
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						//verificando se o orcamento esta com produtos previamente cadastrados
						if(is_numeric($id_item) and $tabela_item!="item"){
							$item = registro($id_item, $tabela_item, "nome");
						}else{
							$item = $id_item;
						}
					}else{
						$id_item = $item = $descricao_item = null;
						$quantidade = $valor_produto = 0;
						$tabela_item= "item";
					}
					
					echo "<tr class='campoItem'>";
						echo "<td><a href='#campoItem' class='removerCampo' title='Remover item do orçamento'><img src='img/menos.png' width='30'></a> ";
						echo "<a href='javascript:void(0)' class='link' onclick=\"lookup($('#item_$i').val(), 'item_$i');\"><img src='img/lupa.png'></a></td>";
						echo "<td colspan='2'>";
							if($tabela_item=="servico"){
								$instrucao = "select id as idOrdemServico from ordem_servico where id_orcamento='$id_orcamento' and id_orcamento<>'0' and id_servico='$id_item'";
								$sqlOrdemServico = query($instrucao);
								if(mysqli_num_rows($sqlOrdemServico)>0){
									extract(mysqli_fetch_assoc($sqlOrdemServico));
									echo "<a href='cadastrarOrdemServico.php?op=visualizar&id=".base64_encode($idOrdemServico)."' title='Visualizar Ordem de Servico $idOrdemServico'><img style='position: relative; top: 10px;' src='img/icones/pesquisaOrdemServico.png'></a> ";
								}
									
							}
							echo "<input type='hidden' name='tabelaItem[]' value='$tabela_item' id='tabelaItem_$i'>";
							echo "<input type='hidden' name='idItem[]' value='$id_item' id='idItem_$i'>";
							echo "<input type='text' name='item[]' placeholder='Cod. ou nome do produto' value='$item' style='width:250px;' onkeyup='lookup(this.value, this.id)' id='item_$i' autocomplete='off'>";
							echo "<div class='suggestionsBox' id='suggestions_$i' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
							echo "<div class='suggestionList' id='autoSuggestionsList_$i'></div></div>";
						echo "</td>";
						$js1 = "verificaTipoItem(this); lookupOff();";
						$js2 = "calcularTotal();";
						echo "<td><input type='text' name='quantidade[]' value='$quantidade' size='2' id='quantidade_$i'  ";
						echo mascara("Valor", null, "onblur='calcularTotal();' onclick='lookupOff();' autocomplete='off'", $js1, $js1, $js1)."></td>";
						echo "<td class='tdSubTotal' id='tdSubTotal_$i'><input type='text' name='subTotal[]' value='".real($valor_produto)."' class='totalValor preco' id='subTotal_$i' ";
						echo mascara("Valor2", null, "autocomplete='off' onblur='calcularTotal();'")."></td>";
						$itemTotal = $quantidade * $valor_produto;
						$subTotal += $valor_produto;
						$total += $itemTotal;
						echo "<td rowspan='2'><input style='height:100%; font-size:3em; width: 230px;' type='text' name='itemTotal[]' value='".real($itemTotal)."' class='inputValor totalValor preco' id='itemTotal_$i' ";
						echo mascara("Valor2", null, "autocomplete='off'", $js2, $js2, $js2)."></td>";
					echo "</tr>";
                    echo "<tr class='campoItem'>";
                        echo "<td></td>";
                        echo "<td colspan='4'><input type='text' name='descItem[]' value='$descricao_item' placeholder='Descrição para este produto (opcional)'></td>";
                    echo "</tr>";
				}
				
				echo "<tr>";
				echo "<td colspan='9'>";
				echo arqVisualizar("obsOrcamento", "Clique para mostrar as observações deste orçamento.", 1);
				echo "Observações<br>";
				echo "<div class='obsOrcamento' style='display:none;'><textarea id='obsOrcamento' class='ckeditor' name='observacoesO' cols='55' rows='3'>$observacoesO</textarea></div>";
				echo "</td>";
				echo "</tr>";

                echo "<tr>";
                    echo "<td align='right' colspan='4'>Desconto</td>";
                    echo "<td align='right'><input type='text' name='totalDescontoPor' value='".real($descPor)."' class='porcentagem totalValor' ".mascara("Valor2")." onblur='calcularTotalDescPor();'></td>";
                    echo "<td><input type='text' name='totalDescontoReal' value='".real($descReal)."' class='preco totalValor' ".mascara("Valor2")." onblur='calcularTotal();'></td>";
                echo "</tr>";

                echo "<tr>";
					echo "<td colspan='4'></td>";
					echo "<td align='right'><input type='text' name='totalSubTotal' value='".real($subTotal)."' class='inputValor preco totalValor' ".mascara("Valor2")."></td>";
                    $total = $total - $descReal;
                    echo "<td><input type='text' name='totalItemTotal' value='".real($total)."' class='inputValor preco totalValor' ".mascara("Valor2")."></td>";
				echo "</tr>";
				echo "<tr>";
					$sql= query("select * from conta where referido='$id_orcamento' and tabela_referido='orcamento'");
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						if($parcelas){
							$display2= "";
						}
					}else{
						$id = $endidade = $id_referido = $referido = $tipo = $valor = $forma_pagamento = $parcelas = $nota_fiscal = $data = $id_usuario = null;
						$display2= "display:none;";
					}
					echo "<td colspan='5' align='right'>Forma de Pagamento</td>";
					echo "<td><select name='pgaForma' onchange=\"showParcela(this.value);\">".opcaoSelect("pagamento_forma", 1, "Ativo", $forma_pagamento)."</select></td>";
				echo "</tr>";
				echo "<tr>";
					$cod = "";
					$instrucao = "select * from conta_itens where id_conta='$id' and id_conta<>'0'";
					$sql = query($instrucao);
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						if($tipo_pagamento_sub){
							$display1= "";
							$sql= query("SELECT * FROM pagamento_tipo_sub WHERE id_pagamento_tipo IN (SELECT id_pagamento_tipo FROM pagamento_tipo_sub WHERE id='$tipo_pagamento_sub' and status='Ativo')");
							$linha = mysqli_num_rows($sql);
							$cod .= "<td colspan='5' align='right'>Sub tipo de pagamento</td>";
							$cod .= "<td colspan='1'>";
								$cod .= "<select name='subTipoPagamento'>";
								$cod .= "<option value=''>--</option>";
								for($i=0; $i<$linha; $i++){
									extract(mysqli_fetch_assoc($sql));
									if($id==$tipo_pagamento_sub){
										$cod .= "<option value='$id' selected='yes'>$sub_tipo_pagamento</option>";	
									}else{
										$cod .= "<option value='$id'>$sub_tipo_pagamento</option>";	
									}
									
								}
								$cod .= "</select>";
							$cod .= "</td>";
						}else{
							$display1= "display: none;";
							$cod = "";
						}
						
					}else{
						$id = $id_conta = $tipo_pagamento = $tipo_pagamento_sub = $valor = $data_pagamento = $data_vencimento = $id_usuario = null;
						$display1= "display: none;";
						$display2= "display: none;";
					}
					echo "<td colspan='5' align='right'>Tipo de Pagamento</td>";
					echo "<td colspan='1'><select name='pgaTipo' onchange=\"ajaxTipoPagamentoSub(this.value, '$tipo_pagamento_sub');\">".opcaoSelect("pagamento_tipo", 1, "Ativo", $tipo_pagamento)."</select></td>";
				echo "</tr>";
				
				echo "<tr id='pagamentoTipoSub' style='$display1'>";
					echo $cod;
				echo "</tr>";
				
				echo "<tr id='parcela' style='$display2'>";
					echo "<td colspan='5' align='right'>Parcelas</td>";
					echo "<td colspan='1'>";
						echo "<select name='pgaParcelas' id='parcelaSelect'>";
						echo "<option value=''>--</option>";
						$sql = query("select parcelaMax from pagamento_parcela where id='1'");
						extract(mysqli_fetch_assoc($sql));
						for($i=1;$i<=$parcelaMax;$i++){
							if($i==$parcelas){
								echo "<option value='$i' selected='yes'>$i</option>";	
							}else{
								echo "<option value='$i'>$i</option>";
							}
							
						}
						echo "</select>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					$sql = query("select id from ordem_servico where id_orcamento='$id_orcamento' and status='1' and id_orcamento!='0'");
					$qtdOrdemServico = mysqli_num_rows($sql);
					if($statusOrcamento!='1' and $qtdOrdemServico>0){
						if($qtdOrdemServico >1){
							$OS = "as ordens de serviço ";
							$texto = "outras ordens de serviço serão criadas.";
						}else{
							$OS = "a ordem de serviço ";
							$texto = "uma outra ordem de serviço será criada.";
						}
						for($i=0; $i<$qtdOrdemServico;$i++){
							extract(mysqli_fetch_assoc($sql));
							$i!=0 ? $OS.=", ": false;
							$OS .= "$id";
						}
						
						echo "<td colspan='6' align='right'>";
						echo "Para editar os itens deste orçamento será necessário cancelar a venda, ";
						echo "fazendo isso, vocês estará cancelando $OS.<br>";
						echo "Quando o orçamento for editado $texto";
						echo "</td>";
					}else{
						echo "<td colspan='4'></td>";
						echo "<td align='right'><input type='submit' class='btnEnviar' value='Enviar'></td>";
						echo "<td><input type='reset' value='Cancelar'></td>";
					}
					
					
				echo "</tr>";
			echo "</table>";
		echo "</center>";
		if($id_orcamento){
			echo "<input type='hidden' name='idOrcamento' value='$id_orcamento'>";
		}
	echo "</form>";
}

function query($instrucao, $conect = null, $testar = null){
    if(is_null($conect)){
        global $conexao;
        global $conexaoMaster;
        isset($conexao) ? $conect = $conexao : $conect = $conexaoMaster;
    }
	$pagina = explode("/", $_SERVER['PHP_SELF']);
	$include = "";
	for($i=3; $i<count($pagina); $i++){
		$include .= "../";
	}
	$include .= "inc/msgErro.inc";
	if($testar){
		$sql = $instrucao;
	}else{
		$sql = mysqli_query($conect, $instrucao) or die(include $include);
	}
	
	return $sql;
}

function num_rows($sql){
	return mysqli_num_rows($sql);
}

function info($msg, $cor = "#e2d000", $posicao = null, $display = "", $class = null, $js = null){
	if($js){
		return "onclick=\"$('.".$js."').show();\"";
	}else{
		if($posicao){//posicao ira receber 2 numeros com espaços entre si o primeiro representa a posicao left e o segundo a posicao top
	        $posicao = explode(" ", $posicao);
	        $posicao = "left: ".$posicao[0]."%; top: ".$posicao[1]."%;";
	    }
		echo "<div class='infoBack $class' style='display: $display;'></div>";
		echo "<div id='info' class='info ".$class."' style='$posicao; display: $display;' align='center' valign='middle'>";
		echo "<span class='infoSpan' style='color: #F6F4F7; font-weight: bolder; background-color: $cor; position:absolute; top:-1px; left:-1px; width:100%; line-height:20px; display:block;'>Mensagem do sistema!</span><br>";
		echo "<span style='float:right; position: absolute; right:-10px; top:-10px;'>";
		echo "<input type='button' id='deletar' style='border-radius:100px; padding-left:9px; padding-right:9px; box-shadow:none;' value='X' onclick=\"infoApagar();\"></span>";
		echo "$msg</div>";
	}
    
}

function conta($conta = null){
	
	echo "<script src='js/cadastrarContaFormularioFiltro.js' type='text/javascript'></script>";
	if($conta){
		$sql = query("select * from conta where id='$conta'");
		$reg = mysqli_fetch_assoc($sql);
		//$conta || reg || id referido
		$opForm = "editar_conta";
		$botaoForm = "Editar Formulário acima";
	}else{
		$sql = query("select max(id)+1 as id from conta");
		$reg = mysqli_fetch_assoc($sql);
		$conta = $reg["id"];
		$reg["id_usuario"] = registro($_COOKIE["login"], "usuario", "id", "login");
		$reg["data"] = date('Y-m-d H:i:s');
		$reg["referido"] = $reg["entidade"] = $reg["nota_fiscal"] = $reg["valor"] = $reg["observacoes"] = 
		$reg["parcelas"] = $reg["status"] = $reg["documento"] = $reg["conta_plano"] = $reg["empresa"] = $reg["forma_pagamento"] = null;
		$opForm = "nova_conta";
		$botaoForm = "Cadastrar Conta";
	}
	echo "<form method='post' action='pesquisaConta2.php?conta=".base64_encode($conta)."' enctype='multipart/form-data' onSubmit='return filtro();'>";
	echo "<input type='hidden' name='op' value='$opForm'>";
	echo "<table id='gradient-style' >";
    echo "<thead>";
	echo "<tr>";
	echo "<th colspan='1'>Conta ".$reg["id"]."</td>";
	echo "<th colspan='2'>Aberta por " .getNomeCookieLogin(registro($reg["id_usuario"], "usuario", "login"), false) . "</th>";
	echo "<th colspan='2'>Data ".formataData($reg["data"])."</th>";
	echo "<th colspan='1'>";
	$Sql = query("select id, nome from empresa");
	$linha = mysqli_num_rows($Sql);
	echo "<select name='empresa'>";
	for ($i = 0; $i < $linha; $i++) {
		$Reg = mysqli_fetch_row($Sql);
		if ($reg["empresa"] == $Reg[0]) {
			echo "<option value='$Reg[0]' selected='yes'>$Reg[1]</option>";
		} else {
			echo "<option value='$Reg[0]'>$Reg[1]</option>";
		}
	}
	echo "</select>";
	echo "</th>";
	
	if (!empty($reg["tabela_referido"])) {
		if($reg["tabela_referido"]=="orcamento"){
			echo "<th colspan='3' style='line-height: 27px; vertical-align: top;'>Orçamento ";
			echo "<a title='Visualizar Orçamento ".$reg["referido"]."' href='cadastrarOrcamento.php?op=visualizar&id=".base64_encode($reg["referido"])."'>";
			echo "<img  src='img/icones/pesquisaOrcamento.png'></a>";
			echo "</th>";
		}elseif($reg["tabela_referido"]=="pdv"){
			echo "<th colspan='3' style='line-height: 27px; vertical-align: top;'>PDV ";
			echo "<a style='vertical-align: bottom;' title='Visualizar PDV ".$reg["referido"]."' href='cadastrarPDV.php?pdv=".base64_encode($reg["referido"])."'>";
			echo "<img  src='img/icones/pesquisaPDV.png'></a>";
			echo "</th>";
			
		}elseif($reg["tabela_referido"]=="ordem_servico"){
			echo "<th colspan='3' style='line-height: 27px; vertical-align: top;'>Ordem de Serviço ";
			echo "<a title='Visualizar Ordem de serviço ".$reg["referido"]."' href='cadastrarOrdemServico.php?op=visualizar&id=".base64_encode($reg["referido"])."'>";
			echo "<img  src='img/icones/pesquisaOrdemServico.png'></a>";
			echo "</th>";
		}
		elseif($reg["tabela_referido"]=="plano_assinatura"){
			echo "<th colspan='3' style='line-height: 27px; vertical-align: top;'>Matricula ";
			$sqlPlanoAssinatura = query("select id_matricula from matricula_plano_assinatura where id='".$reg["referido"]."'");
			extract(mysqli_fetch_assoc($sqlPlanoAssinatura));
			echo "<a title='Visualizar Plano / Assinatura $id_matricula' href='cadastrarMatricula.php?op=visualizar&id=".base64_encode($id_matricula)."'>";
			echo "<img  src='img/icones/pesquisaMatricula.png'></a>";
			echo "</th>";
		}
	} else {
		echo "<th colspan='3'></td>";
	}
	echo "</tr>";
	echo "</thead>";
	
	echo "<tr>";
	echo "<td colspan='2' style='line-height:30px;'>Cliente / Fornecedor<br>";
	//se a conta n for associada entao ponha o formulario para alterar
	if (is_numeric($reg["entidade"])) {
		if($reg["tabela_entidade"] == "cliente_fornecedor"){
			$tabelaEntidade = ajudaTool("Visualizar Cadastro", "cadastrarClienteFornecedor.php?op=visualizar&id_cliente_fornecedor=".base64_encode($reg["entidade"]), "<img class='imgFerramenta' src='img/icones/pesquisaClienteFornecedor.png'>");
			if(registro($reg["entidade"], "cliente_fornecedor", "formato")=="cliente"){
				$tabelaEntidade .= " Cliente";
			}else{
				$tabelaEntidade .= " Fornecedor";
			}
		}elseif($reg["tabela_entidade"] == "usuario"){
			$tabelaEntidade = "Colaborador";
		}else{
			$tabelaEntidade = $reg["tabela_entidade"];
		}
		$cliente_fornecedor = registro($reg["entidade"], $reg["tabela_entidade"], "nome");
		echo "<input type='hidden' value='".$reg["entidade"]."' name='entidade'>$tabelaEntidade $cliente_fornecedor</td>";
	} elseif(is_numeric($reg["entidade"]) and $reg["tabela_entidade"]) {//caso seja associado
		echo "<input type='text' name='entidade' cols='55' rows='3'>".$reg["entidade"]."</td>";
	}else{
		echo "<input type='text' name='entidade' value='".$reg["entidade"]."'></td>";
	}
	//se a conta n for associada entao ponha o formulario para alterar
	if (!is_numeric($reg["referido"])) {
		echo "<td colspan='3'>Referente A<br><input type='text' name='referido' value='".$reg["referido"]."' style='width:98%'></td>";
	} elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]) {//caso seja associado
		if($reg["tabela_referido"] == "orcamento"){
			$tabelaReferido = "Orcamento";
		}elseif($reg["tabela_referido"] == "pdv"){
			$tabelaReferido = "Ponto de venda (".registro($reg["referido"], "pdv", "nome").")";
		}elseif($reg["tabela_referido"]=="ordem_servico"){
			$tabelaReferido = "Ordem de serviço";
		}elseif($reg["tabela_referido"]=="plano_assinatura"){
			$tabelaReferido = "Plano / Assinatura";
		}else{
			$tabelaReferido = $reg["tabela_referido"];
		}
		echo "<td colspan='3'>Referente A<br><input type='hidden' value='".$reg["referido"]."' name='referido'>$tabelaReferido ".$reg["referido"]."</td>";
	}
	if(is_numeric($reg["referido"])){//se caso conta associada a algum orcamento ou pdv ou ordem de serviço não editar o valor total.
		echo "<td colspan='4'>Valor Total<br><input size='4' type='hidden' value='" . real($reg["valor"]) . "' name='valorTotal'><b style='font-size:17px;'>R$ " . real($reg["valor"]) . "<b></td>";
	}else{
		echo "<td colspan='4'>Valor Total<br><input class='preco totalValor' size='4' type='text' value='" . real($reg["valor"]) . "' name='valorTotal' id='preco' ".mascara("Valor2")."></td>";
	}
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='2'>Tipo de conta<br>";
	echo "<select name='status'>";
    if(is_numeric($reg["referido"])){//caso a conta seja associada a algum orçamento ou PDV ou ordem de serviço ela n pode estar com status "a pagar"
        echo opcaoSelect("conta_status", 1, "Ativo", $reg["status"], null, "and id<>'3'");
    }else{
        echo opcaoSelect("conta_status", 1, "Ativo", $reg["status"]);
    }
	echo "</select>";
	echo "</td>";
	echo "<td colspan='2'>Forma de pagamento<br>";
	echo "<select name='formaPagamento' id='formapagamento'>";
	echo opcaoSelect("pagamento_forma", 1, "Ativo", $reg["forma_pagamento"]);
	echo "</select></td>";
	echo "<td colspan='1'>Parcelas<br>";
	echo "<select name='parcelas' id='parcelas'>";
	$sqlParcela = query("select parcelaMax from pagamento_parcela where id='1'");
	extract(mysqli_fetch_assoc($sqlParcela));
	for ($i = 1; $i <= $parcelaMax; $i++) {
		if ($reg["parcelas"] == $i) {
			echo "<option value='$i' selected='yes'>$i</option>";
		} else {
			echo "<option value='$i'>$i</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "<td colspan='1'>Plano de conta<br>";
	echo "<input type='hidden' name='cadastrarPlano' value='false'>";
	echo "<input type='hidden' name='idPlanoConta' value='".$reg["conta_plano"]."'>";
	echo "<input type='text' name='planoConta' onkeyup='sugestaoContaPlano(this.value); planoOperacao(this.value);' value='".registro($reg["conta_plano"], "conta_plano", "nome")."' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='contaPlanoSugestoes' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='contaPlanoLista'></div></div>";
	echo "</td>";
	echo "<td colspan='3'>Documento<br><input type='text' name='documento' value='".$reg["documento"]."'></td>";
	echo "</tr>";


	echo "<tr>";
	echo "<td colspan='9'>";
	echo arqVisualizar("obsConta", "Clique para mostrar as observações desta conta.", 1);
	echo "Observações<br>";
	echo "<div class='obsConta' style='display:none;'><textarea class='ckeditor' name='observacoes' cols='55' rows='3'>".$reg["observacoes"]."</textarea></div>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	if($reg["parcelas"]>1 and $reg["status"]==2){
		$cod = "<a href='javascript:void(0)' title='Imprimir carnê' ".pop("carne.php?carne=".base64_encode($reg["id"]), 650, 300,"_black")."><img src='img/carne.png'></a>";
	}else{
		$cod = "";
	}
	echo "<td colspan='4' align='center'><input class='btnEnviar' type='submit' value='$botaoForm'></td>";
	echo "</form>";
	echo "<td colspan='5' align='center'>";
	echo $cod;
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th style='white-space:nowrap;'>Tipo de pagamento</th>";
	echo "<th style='white-space:nowrap'>Valor (R$)</th>";
	echo "<th width='150' style='white-space:nowrap'>Data de vencimento</th>";
	echo "<th width='150' style='white-space:nowrap;'>Data da quitação</th>";
    if($reg["status"]==1 or $reg["status"]==2){
        $cod = "Creditar";
    }else{
        $cod = "Debitar";
    }
    echo "<th style='white-space:nowrap'>$cod caixa <a href='javascript:void(0)' ".pop("caixa.php")." title='Cadastrar novo caixa'><img class='imgHelp' src='img/mais.png'></a></th>";
	echo "<th style='white-space:nowrap'>Cadastrado por</th>";
	$msg = "Ao clicar em Lançar você estará lançando valores na conta. Efetuando um recebimento ou um pagamento.";
	$msg .= "Ao clicar em Editar, você estará apenas editando o tipo de pagamento e / ou  valores e / ou data de vencimento.";
	echo "<th style='white-space:nowrap' colspan='3'>Operação ".ajudaTool($msg)."</th>";
	echo "</tr>";
	
	$Sql = query("select * from conta_itens where id_conta='".$reg["id"]."'");
	$linha = mysqli_num_rows($Sql);
	
	//corrigindo problema de pagamento a vista com zero de entrada
	//capturando o valor das parcelas quando existir
	if ($linha > 0) {
		$valor = 0;
		$parcelasQuitadas = 0;
		for ($i = 0; $i < $linha; $i++) {
			$regValor = mysqli_fetch_row($Sql);
			if ($regValor[4]) {
				$parcelasQuitadas++;
			}
			$valor += $regValor[4];
		}
		if (($reg["parcelas"] - $parcelasQuitadas) <= 0) {
			$valorParcela = 0;
		} else {
			$valorParcela = round(($reg["valor"] - $valor) / ($reg["parcelas"] - $parcelasQuitadas), 2);
		}
	
	}
	
	
	$sql = query("select * from pagseguro");
	if(mysqli_num_rows($sql)>0){
		$credentials = true;
	}

	$instrucao = "select * from conta_itens where id_conta='".$reg["id"]."'";
	$sql = query($instrucao);
	$linha = mysqli_num_rows($sql);
	if($linha==0){
		$dataVencimento = date('Y-m-d');
		$instrucao = "insert into conta_itens ";
		$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
		$instrucao .= "values ('".$reg["id"]."', '1', '0','$dataVencimento', '".getIdCookieLogin($_COOKIE["login"])."')";
		$sql = query($instrucao);
	
		$instrucao = "select * from conta_itens where id_conta='".$reg["id"]."'";
		$sql = query($instrucao);
		$linha = mysqli_num_rows($sql);
		$valorParcela = $reg['valor'];
	}
	
	for ($i = 1; $i <= $linha; $i++) {
		$regLog_conta = mysqli_fetch_assoc($sql);
		echo "<form method='post' name='formulario$i' id='formulario$i' action='pesquisaConta2.php?conta=".base64_encode($conta)."' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='conta_itens' value='".$regLog_conta["id"]."'>";
		echo "<input type='hidden' name='op' value='editar_conta_itens'>";
		echo "<input type='hidden' name='op2' id='op2_$i' value='lancar'>";
		//backup das variaveis para detectar se estão editando ou lancando o log de conta
		echo "<input type='hidden' name='TIPOPAGAMENTO' id='TIPOPGA_$i' value='".$regLog_conta["tipo_pagamento"]."'>";
		echo "<input type='hidden' name='TIPOPAGAMENTOSUB' id='TIPOPAGAMENTOSUB_$i' value='".$regLog_conta["tipo_pagamento"]."'>";
		echo "<input type='hidden' name='DATAVENCIMENTO' id='DATAVENCIMENTO_$i' value='".formataData($regLog_conta["data_vencimento"])."'>";
		echo "<input type='hidden' name='DATAQUITACAO' id='DATAQUITACAO_$i' value='".formataData($regLog_conta["data_pagamento"])."'>";
        //selecionando o caixa
        $sqlCaixa = query("select id_caixa from caixa_movimento where id='".$regLog_conta["id_caixa_movimento"]."'");
        if(mysqli_num_rows($sqlCaixa)==1){
            extract(mysqli_fetch_assoc($sqlCaixa));
            echo "<input type='hidden' name='CAIXA' id='CAIXA_$i' value='$id_caixa'>";
        }else{
            echo "<input type='hidden' name='CAIXA' id='CAIXA_$i' value='0'>";
        }
		//fim do backup
		echo "<tr>";
		echo "<td>";
		echo "<select name='tipoPagamento' id='tipoPga_$i' onchange=\"showTipoPagamentoSub(this.value, this);\">";
		echo opcaoSelect("pagamento_tipo", 1, "Ativo", $regLog_conta["tipo_pagamento"]);
		echo "</select><br>";
		echo "<input type='hidden' name='tipo_pagamento_sub' id='tipoPagamentoSub_$i' value='".$regLog_conta["tipo_pagamento_sub"]."'>";
		if($regLog_conta["tipo_pagamento_sub"]){
			echo "<span id='spanTipoPagamentoSub_$i'>";
		}else{
			echo "<span id='spanTipoPagamentoSub_$i' style='display:none;'>";
		}
		echo "<select name='tipoPagamentoSub' onchange='mudaTipoPagamentoSub(this.value, \"$i\");'>";
		echo opcaoSelect("pagamento_tipo_sub", 2, "Ativo", $regLog_conta["tipo_pagamento_sub"], null, "and id_pagamento_tipo='".$regLog_conta["tipo_pagamento"]."'");
		echo "</select>";
		echo "</span>";
		echo "</td>";
		if ($regLog_conta["valor"] > 0) {
			$valorParcelaLocal = $regLog_conta["valor"];
		}else{
			$valorParcelaLocal = $valorParcela;
		}
		echo "<td><input class='preco totalValor' style='width:100px;' type='text' name='valor' id='valor_$i' value='" . real($valorParcelaLocal) . "' ".mascara("Valor2")."></td>";

		echo "<td>".inputData("formulario".$i, "datavencimento", null, formataData($regLog_conta["data_vencimento"]), null, $i)."</td>";
		echo "<td>".inputData("formulario".$i, "dataquitado", null, formataData($regLog_conta["data_pagamento"]), null, $i)."</td>";
		
		echo "<td>";
        echo "<select name='caixa'>";
        if(isset($id_caixa)){
        echo opcaoSelect("caixa", 1, "Ativo", $id_caixa);
        }else{
            echo opcaoSelect("caixa", 1, "Ativo");
        }
        $id_caixa = 0;
        echo "</select>";
        echo "</td>";
        
		echo "<td>" . getNomeCookieLogin(registro($regLog_conta["id_usuario"], "usuario", "login"), false) . "</td>";

		echo "<td colspan='3'>";
		if($reg["status"]!=4){
			echo "<a href='javascript:void(0)' title='Editar está parcela' onclick='mudarOp(\"$i\", \"editar\"); $(\"#formulario$i\").submit();'><img src='img/editar.png'></a>";
			echo "<a href='javascript:void(0)' title='Lançar está parcela' onclick='mudarOp(\"$i\", \"lancar\"); $(\"#formulario$i\").submit();'><img src='img/salvar.png'></a>";
		}
		echo "</form>";
		
		$sqlEmpresa = query("select * from empresa where usarTimbrado='1'");
		extract(mysqli_fetch_assoc($sqlEmpresa));
		$email_empresa = $email;

		if ($regLog_conta["valor"] > 0) {

			echo "<form method='post' id='formulario_deletar$i' action='pesquisaConta2.php?conta=".base64_encode($conta)."' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='conta_itens' value='".$regLog_conta["id"]."'>";
			echo "<input type='hidden' name='op' value='editar_conta_itens'>";
			echo "<input type='hidden' name='op2' value='deletar'>";
			echo "<a href='javascript:void(0)' title='Deletar lançamento' onclick='$(\"#formulario_deletar$i\").submit();'><img src='img/deletar.png'></a>";
			//echo "<input type='submit' value='X' id='deletar'>";
			echo "</form>";
			echo "<a href='javascript:void(0)' title='Imprimir Recibo' ".pop("recibo.php?recibo=".base64_encode($regLog_conta["id"])."&empresa=".base64_encode($_COOKIE["id_empresa"]), "730", "500")."><img src='img/recibo.png'></a>";
			
			//$msg = "<form style='display:block' target='_blank' class='form' method='post' action='popup/enviarEmail.php?empresa=".base64_encode($_COOKIE["id_empresa"])."' enctype='multipart/form-data'>";
			$msg = "<form style='display:block' target='_blank' class='form' id='recibo_email' method='post' enctype='multipart/form-data'>";
			$msg.= "<div class='column' style='padding:none;'>";
			$msg.= "<label for='remetente'>Remetente</label>";
			$msg.= "<input type='text' style='text-transform:none;' id='recibo_remetente' name='remetente' placeholder='E-mail do remetente' value='$email_empresa'>";
			$msg.= "<label for='nome'>Destinatário</label>";
			if (is_numeric($reg["entidade"])) {
				$cliente_fornecedor = registro($reg["entidade"], $reg["tabela_entidade"], "nome");
				if($reg["tabela_entidade"]!="pdv"){
					$email_cliente_fornecedor = registro($reg["entidade"], $reg["tabela_entidade"], "email");	
				}else{
					$email_cliente_fornecedor = null;
				}
			}else{
				$cliente_fornecedor = $reg["entidade"];
				$email_cliente_fornecedor = null;
			}
			$msg.= "<input type='text' name='nome' id='recibo_destinatario' placeholder='Nome do destinatário' value='$cliente_fornecedor'>";
			$msg.= "<input type='text' style='text-transform:none;' id='recibo_destinatario_email' name='destinatario' placeholder='E-mail do destinatario' value='$email_cliente_fornecedor'>";
			$msg.= "<label for='nome'>Assunto</label>";
			$assunto = "$nome - Recibo ".$regLog_conta["id"];
			$msg.= "<input type='text' name='assunto' placeholder='Assunto que irá aparecer no e-mail' id='recibo_assunto' value='$assunto'>";
			$msg.= "<label for='email'>E-mail</label>";
			
			$email= "<center>
			<table>
				<tr>
					<td rowspan='5'><img src='http://" . $_SERVER["HTTP_HOST"] . "/$imgsrc'></td>
				</tr>
				<tr>
					<td style='font-size: 17px;'>$nome<br>$fone1 $fone2 $fone3</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$razao_social</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$cnpj</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$endereco $numero $bairro 
					".registro($cidade, "cidades", "nome", "cod_cidades")." 
					".registro($estado, "estados", "sigla", "cod_estados")."</td>
				</tr>
			</table><br>
			Prezado ".ucwords(strtolower($cliente_fornecedor)).",<br>
			Estamos enviando o link para impressão do recibo <b>".$regLog_conta["id"]."</b>, 
			a importância de <b>R$ ".real($regLog_conta["valor"])."</b>, referente à ";
			if($i!=1){
				$email.= "parte do pagamento de";
			}
			$email.= ":<br><br>";
			
			if (!is_numeric($reg["referido"])) {
				$email.= $reg["referido"];
			} elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]) {//caso seja associado
				$referido = null;
				if($reg["tabela_referido"] == "orcamento"){
					$sqlReferido = query("select * from orcamento_itens where id_orcamento='".$reg["referido"]."'");
					for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
						extract(mysqli_fetch_assoc($sqlReferido));
						if(is_numeric($id_item)){
							$referido.= $quantidade." X R$ ".real($valor_produto)." ".registro($id_item, $tabela_item, "nome");
						}else{
							$referido.= $quantidade." X R$ ".real($valor_produto)." ".$id_item;
						}
						$referido.= "<br>";
					}
				}elseif($reg["tabela_referido"] == "pdv"){
					$sqlReferido = query("select * from pdv_itens where id_pdv='".$reg["referido"]."'");
					for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
						extract(mysqli_fetch_assoc($sqlReferido));
						$referido.= $quantidade." X R$ ".real($preco)." ".registro($id_produto, "produto", "nome");
						$referido.= "<br>";
					}
				}elseif($reg["tabela_referido"]=="ordem_servico"){
					$sqlReferido = query("select * from ordem_servico where id='".$reg["referido"]."'");
					extract(mysqli_fetch_assoc($sqlReferido));
					$referido.= $quantidade." X R$ ".real($valor)." ".registro($id_servico, "servico", "nome");
				}elseif($reg["tabela_referido"]=="plano_assinatura"){
					$referido.= "R$ ".real(registro($reg["referido"], "matricula_plano_assinatura", "valor"))." ";
					$referido.= registro(registro($reg["referido"], "matricula_plano_assinatura", "id_plano_assinatura"), "plano_assinatura", "nome");
					$referido.= "<br>";
				}else{
					$referido = $reg["tabela_referido"];
				}
				$email.= $referido;
			}
			$link = "http://" . $_SERVER["HTTP_HOST"] . "/popup/recibo.php?recibo=".base64_encode($regLog_conta["id"])."&empresa=".base64_encode($_COOKIE["id_empresa"]);
			$email.= "<br>
			<a href='$link'><img src='http://" . $_SERVER["HTTP_HOST"] . "/img/recibo.png'></a><br>
			Se não conseguir visualizar o link <a href='$link'>Clique aqui!</a><br>
			Sinceramente.<br>
			".getNomeCookieLogin($_COOKIE["login"], false)."<br>".registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "email");
			

			$msg.= "<textarea name='email' class='ckeditor' id='recibo_corpo'>$email</textarea>";
			//$msg.= "<input type='submit' class='submit' value='Enviar e-mail'>";	
			$onclick = "onclick='ajaxEnviarEmail(\"#recibo_remetente\", \"#recibo_destinatario_email\", \"#recibo_assunto\", \"#recibo_corpo\", \"recibo_email\");'";
			$msg.= "<script src='js/enviar_email.js' type='text/javascript'></script>";
			$msg.= "<input type='button' class='submit' value='Enviar e-mail' $onclick>";
			$msg.= "</div>";
			$msg.= "</form>";
			
			info($msg, "green", null, "none", "email");
			echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "email")." title='Enviar Recibo por E-mail'><img src='img/enviar_email.png'></a>";
			echo "</td>";
		}elseif(isset($credentials) and $regLog_conta["valor"]<=0 and ($reg["status"]==1 or $reg["status"]==2)){
			
			
			$msg = "<form style='display:block' target='_blank' class='form' method='post' action='popup/enviarEmail.php?empresa=".base64_encode($_COOKIE["id_empresa"])."' enctype='multipart/form-data'>";
			$msg.= "<input type='hidden' name='id_conta' value='$conta'>";
			$msg.= "<input type='hidden' name='valor' value='$valorParcela'>";
			$msg.= "<div class='column' style='padding:none;'>";
			$msg.= "<label for='remetente'>Remetente</label>";
			$msg.= "<input type='text' style='text-transform:none;' name='remetente' placeholder='E-mail do remetente' value='$email_empresa'>";
			$msg.= "<label for='nome'>Destinatário</label>";
			if (is_numeric($reg["entidade"])) {
				$cliente_fornecedor = registro($reg["entidade"], $reg["tabela_entidade"], "nome");
				$email_cliente_fornecedor = registro($reg["entidade"], $reg["tabela_entidade"], "email");
			}else{
				$cliente_fornecedor = $reg["entidade"];
				$email_cliente_fornecedor = null;
			}
			$msg.= "<input type='text' name='nome' placeholder='Nome do destinatário' value='$cliente_fornecedor'>";
			$msg.= "<input type='text' style='text-transform:none;' name='destinatario' placeholder='E-mail do destinatario' value='$email_cliente_fornecedor'>";
			$msg.= "<label for='nome'>Assunto</label>";
			$assunto = "$nome - Quitação de débitos";
			$msg.= "<input type='text' name='assunto' placeholder='Assunto que irá aparecer no e-mail' value='$assunto'>";
			$msg.= "<label for='email'>E-mail</label>";
			
			$email= "<center>
			<table>
				<tr>
					<td rowspan='5'><img src='http://" . $_SERVER["HTTP_HOST"] . "/$imgsrc'></td>
				</tr>
				<tr>
					<td style='font-size: 17px;'>$nome<br>$fone1 $fone2 $fone3</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$razao_social</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$cnpj</td>
				</tr>
				<tr>
					<td style='font-size: 10px'>$endereco $numero $bairro 
					".registro($cidade, "cidades", "nome", "cod_cidades")." 
					".registro($estado, "estados", "sigla", "cod_estados")."</td>
				</tr>
			</table><br>
			Prezado ".ucwords(strtolower($cliente_fornecedor)).",<br>
			Conforme foi estabelecido em nosso contrato de venda, o 
			pagamento da conta <b>$conta</b> seria feito no dia 
			<b>".formataData($regLog_conta["data_vencimento"])."</b> a importância de 
			<b>R$ ".real($valorParcela)."</b>, referente à ";
			if($i!=1){
				$email.= "parte do pagamento de";
			}
			$email.= ":<br><br>";
			
			if (!is_numeric($reg["referido"])) {
				$email.= $reg["referido"];
			} elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]) {//caso seja associado
				$referido = null;
				if($reg["tabela_referido"] == "orcamento"){
					$sqlReferido = query("select * from orcamento_itens where id_orcamento='".$reg["referido"]."'");
					for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
						extract(mysqli_fetch_assoc($sqlReferido));
						if(is_numeric($id_item)){
							$referido.= $quantidade." X R$ ".real($valor_produto)." ".registro($id_item, $tabela_item, "nome");
						}else{
							$referido.= $quantidade." X R$ ".real($valor_produto)." ".$id_item;
						}
						$referido.= "<br>";
					}
				}elseif($reg["tabela_referido"] == "pdv"){
					$sqlReferido = query("select * from pdv_itens where id_pdv='".$reg["referido"]."'");
					for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
						extract(mysqli_fetch_assoc($sqlReferido));
						$referido.= $quantidade." X R$ ".real($preco)." ".registro($id_produto, "produto", "nome");
						$referido.= "<br>";
					}
				}elseif($reg["tabela_referido"]=="ordem_servico"){
					$sqlReferido = query("select * from ordem_servico where id='".$reg["referido"]."'");
					extract(mysqli_fetch_assoc($sqlReferido));
					$referido.= $quantidade." X R$ ".real($valor)." ".registro($id_servico, "servico", "nome");
				}elseif($reg["tabela_referido"]=="plano_assinatura"){
					$referido.= "R$ ".real(registro($reg["referido"], "matricula_plano_assinatura", "valor"))." ";
					$referido.= registro(registro($reg["referido"], "matricula_plano_assinatura", "id_plano_assinatura"), "plano_assinatura", "nome");
					$referido.= "<br>";
				}else{
					$referido = $reg["tabela_referido"];
				}
				$email.= $referido;
			}

			$email.= "<br>Estamos enviando um link para que possa efetuar o pagamento.<br>
			<a href='#cod'><img src='http://" . $_SERVER["HTTP_HOST"] . "/img/pagSeguro.gif'></a><br>
			Se não conseguir visualizar o link para pagamento <a href='#cod'>Clique aqui!</a><br>
			Caso o mesmo não tenha sido providenciado, apreciamos sua pronta atenção.<br>
			Sinceramente.<br>
			".registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "nome")."<br>".registro(getIdCookieLogin($_COOKIE["login"]), "usuario", "email");
			

			$msg.= "<textarea name='email' class='ckeditor'>$email</textarea>";
			$msg.= "<input type='submit' class='submit' value='Enviar e-mail'>";	
			$msg.= "</div>";
			$msg.= "</form>";
			
			info($msg, "green", null, "none", "pagSeguro");
			echo "<a href='javascript:void(0)' ".info(null, null, null, null, null, "pagSeguro")." title='Gerar botão de pagamento e enviar por email'><img src='img/pagSeguro.png'></a>";
		}
		echo "</td>";
		echo "</tr>";
	}

    echo "</tr>";
	echo "</table>";
}


//funcao que transforma um valor em numero extenso. recebe o valor no formato 123.13 onde apois o ponto representa os
//centavos e recebe true ou false para saber se a primeira letra de cada palavra eh maiuscula (true) ou menuscula(false)
function extenso($valor = 0, $maiusculas = false) {

	$singular = array("centavo", "real", "mil", "milh&atilde;o", "bilhão", "trilh&atilde;o", "quatrilh&atilde;o");
	$plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es", "quatrilh&otilde;es");

	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis", "sete", "oito", "nove");

	$z = 0;
	$rt = "";

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for ($i = 0; $i < count($inteiro); $i++)
		for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
			$inteiro[$i] = "0" . $inteiro[$i];

	$fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
	for ($i = 0; $i < count($inteiro); $i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
		$t = count($inteiro) - 1 - $i;
		$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")
			$z++;
		elseif ($z > 0)
			$z--;
		if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
			$r .= (($z > 1) ? " de " : "") . $plural[$t];
		if ($r)
			$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
	}

	if (!$maiusculas) {
		return ($rt ? $rt : "zero");
	} else {
		if ($rt)
			$rt = preg_replace("{ E }", " e ", ucwords($rt));
		return (($rt) ? ($rt) : "Zero");
	}

}

//funcao para imprimir cabecalho nos timbrados
function cabecalho($id = false){
	
	if($id){
		$sql= query("select * from empresa where id='$id'");
	}else{
		$sql= query("select * from empresa where usarTimbrado=1");
	}
	$linha= mysqli_num_rows($sql);
	if ($linha>0){
		$reg = mysqli_fetch_assoc($sql);
		if($reg["imgsrc"]){
			$codImg = "<img src='../".$reg["imgsrc"]."' height='100'>";
		}else{
			$codImg = "IMG LOGO EMPRESA<br>182px x 100px";
		}
		
	}else{
		$codImg = "IMG LOGO EMPRESA<br>182px x 100px";
		$reg=null;
	}
	
	echo "<table>";
		echo "<tr>";
			echo "<td style='width:182xp; height:100px; border:none;' align='center' valign='middle' rowspan='4'>$codImg</td>";
			echo "<td style='border:none; font-size:30px; font-weight:bold; text-transform:capitalize;' width='625' align='center' valign='middle'>".$reg["nome"]."</td>";
		echo "</tr>";
		echo "<tr>";
			if($reg["razao_social"] and $reg["cnpj"]){
				echo "<td class='tdNone' align='center' valign='middle'>".$reg["razao_social"]." / ".$reg["cnpj"]."</td>";
			}else{
				echo "<td class='tdNone'></td>";
			}
		echo "</tr>";
		echo "<tr>";
			echo "<td class='tdNone' align='center' valign='middle'>".$reg["endereco"]." ".$reg["numero"]." ".$reg["complemento"]." ".$reg["bairro"]." - ".registro($reg["cidade"], "cidades", "nome", "cod_cidades")." / ".registro($reg["estado"], "estados", "nome", "cod_estados")."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='tdNone' style='font-size:15px; font-weight:bold; text-transform:capitalize;' align='center' valign='middle'>".$reg["fone1"]." ".$reg["fone2"]." ".$reg["fone3"]."</td>";
		echo "</tr>";
	echo "</table>";
}

//funcao para inserir um item com key assossiativa logo apois o ultimo elemento
//usado para definir funcoes de usuarios e menu do sistema
function array_push_associative(&$arr) {
   $args = func_get_args();
   foreach ($args as $arg) {
       if (is_array($arg)) {
           foreach ($arg as $key => $value) {
               $arr[$key] = $value;
           }
       }else{
           $arr[$arg] = "";
       }
   }
   return $arr;
}

//funcao para retornar o array da ferramentas
//cadastrar pesquisa relatorio administrativo
function getFerramentaOpcao($ferramenta){
    
    $chave = new chave;
    $sql = query("select valor from registro where id=(select max(id) from registro)");
    extract(mysqli_fetch_assoc($sql));
    $chave->valor = $valor;
    $chave->decodificar_atribuir();
    
    $instrucao = "select ferramenta, nome from ferramentas where ferramenta like '$ferramenta%' ";
    
    if($chave->modulo=="basico"){
        //o modulo basico começa a a partir do id da ferramenta 25
        $instrucao .= "and id<31";
    }elseif($chave->modulo=="ordemServico"){
        $instrucao .= "and id<42";
    }elseif($chave->modulo=="academia"){
    	$instrucao .= "and id<39";
    }
    
	$SQL = query($instrucao);
	$linha = mysqli_num_rows($SQL);
	if(mysqli_num_rows($SQL)){
		for($u = 0; $u<$linha; $u++){
			$reg = mysqli_fetch_assoc($SQL);
			$reg["ferramenta"] = explode(".php", $reg["ferramenta"]);
			$arrayAux = array($reg["nome"] => $reg["ferramenta"][0]);
			if(isset($arrayCod)){
				$arrayCod = array_push_associative($arrayCod, $arrayAux);
			}else{
				$arrayCod = array($reg["nome"] => $reg["ferramenta"][0]);
			}
		}
	}else{
		$arrayCod = null;
	}
	return $arrayCod;
}

//funcao para abrir janela popup
function pop($destino, $width=500, $height=200, $tipo = "page", $onclick = null){

	$cod = "onclick=\"$onclick window.open('popup/$destino','$tipo'"; 
	if($tipo!="page"){
		$cod .= ", 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=$width,height=$height'";
	}
	$cod .= ");\"";
	
	return $cod;
}

//funcao de debug
//exibe variaveis na tela
function debugVariaveis(){
	$cod = "<div class='msg'>";
	$cont = count($_POST);
	$array = array_keys($_POST);
	$cod.= "<h3>VARIAVEIS DO POST</h3>";
	$cod.= "<table>";
	for ($i=0; $i<$cont; $i++){
		if(is_array($_POST[$array[$i]])){
			$cont2= count($_POST[$array[$i]]);
			for($j=0; $j<$cont2;$j++){
				$cod.= "<tr>";
				$cod.= "<td><pre>$i</td>";
				$cod.= "<td><pre>Variavel:</td>";
				$cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
				$cod.= "<td><pre>Valor:</td>";
				$cod.= "<td><pre><b style='color:red;'>".$_POST[$array[$i]][$j]."</b></td>";
				$cod.= "</tr>";
			}
		}else{
			$cod.= "<tr>";
			$cod.= "<td><pre>$i</td>";
			$cod.= "<td><pre>Variavel:</td>";
			$cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
			$cod.= "<td><pre>Valor:</td>";
			$cod.= "<td><pre><b style='color:red;'>".$_POST[$array[$i]]."</b></td>";
			$cod.= "</tr>";
		}
	}
	$cod.= "</table>";
	$cont = count($_GET);
	$array = array_keys($_GET);
	$cod.= "<h3>VARIAVEIS DO GET</h3>";
	$cod.= "<table>";
	for ($i=0; $i<$cont; $i++){
		if(is_array($_GET[$array[$i]])){
			$cont2= count($_GET[$array[$i]]);
			for($j=0; $j<$cont2;$j++){
				$cod.= "<tr>";
				$cod.= "<td><pre>$i</td>";
				$cod.= "<td><pre>Variavel:</td>";
				$cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
				$cod.= "<td><pre>Valor:</td>";
				$cod.= "<td><pre><b style='color:red;'>".$_GET[$array[$i]][$j]."</b></td>";
				$cod.= "</tr>";
			}
		}else{
			$cod.= "<tr>";
			$cod.= "<td><pre>$i</td>";
			$cod.= "<td><pre>Variavel:</td>";
			$cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
			$cod.= "<td><pre>Valor:</td>";
			$cod.= "<td><pre><b style='color:red;'>".$_GET[$array[$i]]."</b></td>";
			$cod.= "</tr>";
		}
	}
	$cod.= "</table>";
	
	$cont = count($_REQUEST);
	$array = array_keys($_REQUEST);
	$cod.= "<h3>VARIAVEIS DO REQUEST</h3>";
	$cod.= "<table>";
	for ($i=0; $i<$cont; $i++){
		if(is_array($_REQUEST[$array[$i]])){
			$cont2= count($_REQUEST[$array[$i]]);
			for($j=0; $j<$cont2;$j++){
				$cod.= "<tr>";
				$cod.= "<td><pre>$i</td>";
				$cod.= "<td><pre>Variavel:</td>";
				$cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
				$cod.= "<td><pre>Valor:</td>";
				$cod.= "<td><pre><b style='color:red;'>".$_REQUEST[$array[$i]][$j]."</b></td>";
				$cod.= "</tr>";
			}
		}else{
			$cod.= "<tr>";
			$cod.= "<td><pre>$i</td>";
			$cod.= "<td><pre>Variavel:</td>";
			$cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
			$cod.= "<td><pre>Valor:</td>";
			$cod.= "<td><pre><b style='color:red;'>".$_REQUEST[$array[$i]]."</b></td>";
			$cod.= "</tr>";
		}
	}
	$cod.= "</table>";
	
	$cod.= "<table>";
	$cod.= "<h3>VARIAVEIS DO FILE</h3>";
	$cod.= "<tr><td>";
	$cod.= print_r($_FILES);
	$cod.= "</td></tr>";
	$cod.= "</table>";
	$cod.= "</div>";

	return $cod;
}

//elementos do cupom e PDV
function cupomLista($nomeProduto, $quantidade, $preco){

	$cod = null;
	
	if($nomeProduto=='0'){
		
		return '<span id="total" style="float:right">+ 10% R$ '.$preco.'</span>';

	}else{

		$linha1 = strlen($nomeProduto);
		if($linha1>19){
			$nomeProduto = wordwrap($nomeProduto, 19, "!@#$",true);
			$nome_Produto = explode("!@#$", $nomeProduto);
			$nomeProduto = $nome_Produto[0];
			$linha1 = strlen($nomeProduto);
		}
		
		if(is_numeric(str_replace(',', '.', $preco)) and $preco){
			$linha2 = strlen($quantidade." X R$ ".real($preco));
			$Linha2 = $quantidade." X R$ ".real($preco);
		}else{
			$linha2 = strlen("x ".$quantidade." (".$preco.")");
			$Linha2 = "x ".$quantidade." (".$preco.")";
		}
		$linha3 = 40-($linha1+$linha2);
		$texto = null;
		return $cod.$nomeProduto.str_pad($texto, $linha3, ".").$Linha2;
	}
	
	
}

function formPDV(){
	
	if(isset($_GET["pdv"])){
		$idPdv = base64_decode($_GET["pdv"]);
		echo "<form id='form_pdv' method='post' action='cadastrarPDV.php?pdv=".$_GET["pdv"]."' enctype='multipart/form-data'>"; // o $_GET["pdv"] ja esta criptografado
		echo "<input type='hidden' name='op' value='editar'>";
		$sql = query("select nome as pdvNome from pdv where id='".base64_decode($_GET["pdv"])."'");
		extract(mysqli_fetch_assoc($sql));
		
	}else{
		
		$sql = query("select max(id)+1 as pdvNome from pdv");
		extract(mysqli_fetch_assoc($sql));
		if(!$pdvNome){
			$pdvNome = 1;
		}
		$idPdv = $pdvNome;
		echo "<form id='form_pdv' method='post' action='cadastrarPDV.php?pdv=".base64_encode($pdvNome)."' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='op' value='novo'>";
		$pdvNome = "PDV ".$pdvNome;
		
	}
	
	echo "<table id='gradient-style'>";
		echo "<tr>";
			echo "<th colspan='3'>Ponto de Venda $idPdv<span id='checkNome'></span></th>";
		echo "</tr>";
		echo "<tr>";
			if(isset($_GET["pdv"])){
				echo "<td colspan='3'><input type='text' name='pdvNome' value='$pdvNome' onblur='salvarNome(this.value, \"nome\", \"".base64_decode($_GET["pdv"])."\");'></td>";
			}else{
				echo "<td colspan='3'><input type='text' name='pdvNome' value='$pdvNome' onblur='salvarNome(this.value, \"nome\", \"0\");'></td>";
			}
		echo "</tr>";
		echo "<tr>";
			echo "<td>Cod. Barras</td>";
			echo "<td>Qtd.</td>";
			echo "<td>Nome Produto</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td><input type='text' name='cod' id='cod' autocomplete='off' autofocus>";
			echo "<div class='suggestionsBox' id='suggestions' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
			echo "<div class='suggestionList' id='autoSuggestionsList'></div></div>";
			echo "</td>";
			echo "<td><input type='text' name='quantidade' value='1' style='width:30px' ".mascara("Integer")."></td>";
			echo "<td><input type='text' name='produto' id='produto' autocomplete='off' onkeyup='produtoShow(this);'></td>";
		echo "</tr>";
		echo "<tr>";
				echo "<td colspan='9'>";
				echo arqVisualizar("obs", "Clique para mostrar as observações deste item.", 1);
				echo "Observações<br>";
				echo "<div class='obs' style='display:none;'><textarea style='width:97%' name='observacoes' cols='55' rows='3'></textarea></div>";
				echo "</td>";
				echo "</tr>";
		echo "<tr>";
			echo "<th></th>";
			echo "<th></th>";
			echo "<th align='right'><input type='submit' class='btnEnviar' value='Enviar'></th>";
		echo "</tr>";
		
		if(isset($_GET["pdv"])){
			formLista($_GET["pdv"]);
		}
		echo "<tr>";
			echo "<td colspan='3'>";
				//echo "asd";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "</form>";
}

function formLista($idPdv){
	
	$idPdv = base64_decode($idPdv);
	echo "<tr>";
	echo "<th colspan='3'>Itens do PDV</th>";
	echo "</tr>";
	
	//selecao do pdvItem
	$instrucao = "select * from pdv_itens where id_pdv='$idPdv'";
	$sql = query($instrucao);
	
	for($i = $total = 0, $linha = mysqli_num_rows($sql); $i<$linha; $i++){
		extract(mysqli_fetch_assoc($sql));
		echo "<tr>";
		echo "<td>";
		echo "<div id='cupomFormLista' style='white-space:normal !important;'><a href='cadastrarPDV.php?pdv=".base64_encode($idPdv)."&op=deletar&idPdvItem=$id' title='Remover item do PDV'>";
		echo "<img src='img/menos.png' style='float:left;'></a> ";
		if($despachado){
			echo "<a href='#' title='Despachado' style='float:left;'><img src='img/check-ok.png'></a>";
			$op='des-voltar';
		}else{
			$op='des';
		}
		
		echo "<a href='cadastrarPDV.php?pdv=".base64_encode($id_pdv)."' ".pop("cupom.php?op=$op&tipo=pdv_itens&cupom=".base64_encode($id))."'>";
		if($id_produto==0){
			echo "10%";
			echo "</a>";
			echo "</div> ";
			echo "</td>";
			echo "<td></td>";
			echo "<td align='right'>R$ ".real($preco)."</td>";
		}else{
			echo registro($id_produto, "produto", "nome");
			if($observacoes){
				echo " ($observacoes)";	
			}
			echo "</a>";
			echo "</div> ";
			echo "</td>";
			echo "<td></td>";
			echo "<td align='right'>$quantidade X R$ ".real($preco)."</td>";
		}
		echo "</tr>";
		$total += $preco * $quantidade;
	}
	
	echo "<tr>";
	echo "<th colspan='2' style='text-align: left !important;'>";
	echo "<a href='cadastrarPDV.php?pdv=".base64_encode($idPdv)."' ".pop('cupom.php?tipo=pdv_itens&op=nao-despachados&cupom='.base64_encode($idPdv))." title='Despachar todos.'><img src='img/impressora.png'></a></th>";
	echo "<th style='font-size:17px; text-align: right !important;'>R$ ".real($total)."</th>";
	echo "</tr>";
	
}

function cupom($cupom, $tipo){
	

	$cupom = base64_decode($cupom);

	if($tipo=='orcamento'){
		$sql = query("select observacoes from $tipo where id='$cupom'");
		if(mysqli_num_rows($sql)){
			extract(mysqli_fetch_assoc($sql));
		}else{
			$observacoes = null;
		}
	}
	
	if($tipo!='pdv_itens'){
		//selecionando a empresa
		$instrucao = "select razao_social, cnpj, nome, endereco, numero, complemento, bairro, cidade, estado, cep, fone1 ";
		if($cupom){
			$instrucao .= "from empresa where id=(select id_empresa from $tipo where id='$cupom')";
		}else{
			$instrucao .= "from empresa where usarTimbrado='1'";
		}
		$sql = query($instrucao);
		if(mysqli_num_rows($sql)>0){
		    extract(mysqli_fetch_assoc($sql));
		}else{
		    $nome = $cnpj = $razao_social = $endereco = $numero = $complemento = $bairro = $cidade = $estado = $cep = $fone1 = null;
		}
	}
	
	echo "<table id='cupom'>";
		if($tipo!='pdv_itens'){
			echo "<tr>";
				echo "<td id='top' style='text-transform: uppercase; font-weight: bold;'>";
				echo "<tag style='font-size:22px;'>".wordwrap($nome, 20, "<br>",true)."</tag>";
				echo "<br><tag style='font-size:15px;'>$fone1</tag></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='text-transform: uppercase;'>".wordwrap($razao_social, 20, "<br>",true);
				echo "<br>$cnpj<br>";
			echo wordwrap($endereco." ".$numero." ".$complemento." ".$bairro." ".registro($cidade, "cidades", "nome", "cod_cidades")." ".registro($estado, "estados", "sigla", "cod_estados")." ".$cep, 30, "<br>")."</td>";
			echo "</tr>";
		}
		if(strstr($tipo, 'pdv')){
			echo "<tr>";
				echo "<td>----------------------------------------</td>";
			echo "</tr>";
			echo "<tr>";
				if($tipo=='pdv'){
					echo "<td>".registro($cupom, "pdv", "nome")."</td>";
				}else{
					echo "<td>".registro(registro($cupom, 'pdv_itens', 'id_pdv'), 'pdv', 'nome')."</td>";
				}
			echo "</tr>";	
		}
		echo "<tr>";
			echo "<td>----------------------------------------</td>";
		echo "</tr>";
			if($tipo!='pdv_itens'){
				//instrucao capturar itens do pdv
				$instrucao = "select * from ".$tipo."_itens where id_".$tipo."='$cupom'";
				$sql = query($instrucao);
				$tabela_item = "produto";
				for($i = $total = 0, $linha = mysqli_num_rows($sql); $i<$linha; $i++){
					extract(mysqli_fetch_assoc($sql));
					if(isset($valor_produto)){
						$preco = $valor_produto;
						$id_produto = $id_item;
					}
					
					$sql10 = query("select * from administrativo where taxonomia='dezporcento' and valor='1'");
					if($i+1==$linha and mysqli_num_rows($sql10)>0 and $id_produto==0){
						echo "<tr>";
							echo "<td id='total'>R$ ".real($total)."</td>";
						echo "</tr>";
					}
					
					echo "<tr>";
						echo "<td style='text-transform: uppercase;'>";
							if($id_produto!=0){
								$nomeProduto = registro($id_produto, $tabela_item, "nome");
							}else{
								$nomeProduto = $id_produto;
							}
							echo cupomLista($nomeProduto, $quantidade, real($preco));
						echo "</td>";
					echo "</tr>";
				

					if($observacoes and $tipo=='pdv'){
						echo "<tr>";
							echo "<td style='text-transform: uppercase; white-space:normal !important;'>$observacoes</td>";
						echo "</tr>";
					}
					$total += $preco * $quantidade;
				}
			}else{
				$conn = TConnection::open(ALCUNHA);
				$criterio = new TCriteria;
				if(isset($_GET['op'])){
					if($_GET['op']=='nao-despachados'){
						$criterio->add(new TFilter('id_pdv', '=', $cupom));
					}else{
						$criterio->add(new TFilter('id', '=', $cupom));
					}
				}else{
					$criterio->add(new TFilter('id', '=', $cupom));
				}

				$sql= new TSqlSelect;
				$sql->setEntity('pdv_itens');
				$sql->addColumn('*');
				$sql->setCriteria($criterio);
				$result = $conn->query($sql->getInstruction());
				for($i=0; $i<$result->rowCount(); $i++){
					$row = $result->fetch(PDO::FETCH_ASSOC);
					extract($row);
					echo "<tr>";
						echo "<td style='text-transform: uppercase;'>";
							$nomeProduto = registro($id_produto, 'produto', "nome");
							echo cupomLista($nomeProduto, $quantidade, $preco);
						echo "</td>";
					echo "</tr>";
					if($observacoes and $tipo=='pdv'){
						echo "<tr>";
							echo "<td style='text-transform: uppercase;'>$observacoes</td>";
						echo "</tr>";
					}

					$criterio = new TCriteria;
					$criterio->add(new TFilter('id', '=', $id));

					$sql = new TSqlUpdate;
					$sql->setEntity('pdv_itens');
					$sql->setRowData('despachado', 1);
					$sql->setCriteria($criterio);
					$result2 = $conn->query($sql->getInstruction());

					$historico_msg = "Despachou o item ".registro($id, 'pdv_itens', 'quantidade')." x ".registro(registro($id, 'pdv_itens', 'id_produto'), 'produto', 'nome');
					$historico = new historico(null, 'pdv_itens', $id, $historico_msg);
					$historico->update();
				}
			}
	 	echo "<tr>";
			echo "<td>----------------------------------------</td>";
		echo "</tr>";

		if($tipo!='pdv_itens'){

			$cod = "TOTAL";
			//verificar se existem contas quitadas e faz a sobtração do total
			$instrucao = "select sum(valor) as valor from conta_itens where id_conta=(select id from conta where tabela_referido='$tipo' and referido='$cupom')";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			$valor = round($valor,2);//correção do bug da soma do mysql
			$total = round($total,2);//correção do bug da soma do mysql
			if($valor>0 and $total!=$valor){
				echo "<tr>";
					echo "<td id='total' style='font-size:15px;'>Valor Total R$ ".real($total)."</td>";
				echo "</tr>";
				$total = $total - $valor;
				echo "<tr>";
					echo "<td id='total' style='font-size:15px;'>Recebido R$ ".real($valor)."</td>";
				echo "</tr>";
				$cod = "Diferenca";
			}
			
			echo "<tr>";
				echo "<td id='total' style='font-size:22px;'>$cod R$ ".real($total)."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>----------------------------------------</td>";
			echo "</tr>";	

			if($tipo=="orcamento"){
				if($observacoes){
					echo "<tr>";
						echo "<td><b>Observações:</b>".wordwrap(strip_tags($observacoes), 30, "<br>",true)."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>----------------------------------------</td>";
					echo "</tr>";	
				}
				$sql = query("select id_cliente, cliente, fone, id_usuario, data_venda, data_emissao from $tipo where id='$cupom'");
				extract(mysqli_fetch_assoc($sql));
				$data_venda ? $data = $data_venda : $data = $data_emissao;
				$sql = query("select * from cliente_fornecedor where id='$id_cliente'");
				if(mysqli_num_rows($sql)){
					extract(mysqli_fetch_assoc($sql));
					echo "<tr>";
						echo "<td>";
							echo "<table>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Cliente</td>";
									echo "<td>$nome</td>";
								echo "</tr>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Telefone</td>";
									echo "<td>$fone1 $fone2</td>";
								echo "</tr>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Endereço</td>";
									echo "<td>".wordwrap($endereco." ".$numero." ".$bairro." ".registro($cidade, "cidades","nome", "cod_cidades")." ".registro($estado, "estados", "sigla", "cod_estados"), 30, "<br>",true)."</td>";
								echo "</tr>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Referencia</td>";
									echo "<td>$referencia</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
					echo "</tr>";
				}else{
					echo "<tr>";
						echo "<td>";
							echo "<table>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Cliente</td>";
									echo "<td>$cliente</td>";
								echo "</tr>";
								echo "<tr>";
									echo "<td style='font-weight: bold;'>Telefone</td>";
									echo "<td>$fone</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
					echo "<td>----------------------------------------</td>";
				echo "</tr>";	
			}
			if(isset($id_usuario)){
				echo "<tr>";
					echo "<td>Operador ".getNomeCookieLogin(registro($id_usuario, "usuario", "login"), false)." Data ".formataData($data)."</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>----------------------------------------</td>";
				echo "</tr>";	
			}
			echo "<tr>";
				echo "<td>Desenvolvido por www.rocketsolution.com.br<br>NAO VALIDO COMO CUPOM FISCAL</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='color:white;'>.</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='color:white;'>.</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='color:white;'>.</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='color:white;'>.</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td style='color:white;'>_</td>";
			echo "</tr>";
		}
	
		
	 echo "</table>";
}

//fim dos elementos do cupom e pdv

//funcao para resumir o codigo da imput valor (interface)
function inputValor($valor, $size=4){
    return "<input type='text' class='valorConta' size='$size' value='".real($valor)."'>";
}

//retorna false se o usuario não possui a credencial de gerar token ou true caso ele possuir
function verificaCredencialToken(){
    
    $sql = query("select id_usuario from credenciais where id_usuario='".getIdCookieLogin($_COOKIE["login"])."' and ferramenta='administrativoToken.php'");
    if(mysqli_num_rows($sql)>0){
        return true;
    }else{
        return false;
    }
    
}



//carregar automaticamente as classes
function __autoload($classe){
	$class_file = "class/{$classe}.class.php";
	$class_file_ado = "app.ado/{$classe}.class.php";
	$pasta = "";
	for($i=0; $i<4; $i++){
		if($i>0){
			$pasta .= "../";
		}
		if(file_exists($pasta.$class_file)){
			include_once $pasta.$class_file;
			break;
		}elseif(file_exists($pasta.$class_file_ado)){
			include_once $pasta.$class_file_ado;
			break;
		}
	}
}

//funcão para direcionar na web o formulário de pagamento e solicitação de chave para acessar o sistema.
function enderecoNovaChave(){
    $ponteiro = fopen("inc/enderecoNovaChave.php", "r");
    while (!feof ($ponteiro)) {
      $endereco = fgets($ponteiro,4096);
    }//FECHA WHILE
    fclose ($ponteiro);
    return $endereco;
}

/*
 * tipo = tipo do grafico Radar | Bar | Doughnut | Line | Pie | PolarArea
 * labels = colunas do graficos
 * datasets = linhas do grafico podendo ser uma matriz 
 */
function grafico($tipo, $labels, $Datasets, $Legenda, $width_height = array(400, 400)){
	
	$legenda = $datasets = null;
	
	$tamanho = "width='".$width_height[0]."' height='".$width_height[1]."'";

	if($Legenda){
		foreach ($Legenda as $value) {
			$legenda[] = $value;
		}
	}else{
		$legenda[] = 1;
	}
	
	$linha = count($Datasets);
	$coluna = count($Datasets, COUNT_RECURSIVE);
	$coluna = ($coluna - $linha) / $linha; //identificar tamanho da matriz
	
	for($i=0; $i<$linha; $i++){
		if(isset($Datasets[$i])){
			$datasets[] = $Datasets[$i]; 	
		}
	}
	
	$labelsNUM = count($labels);
	$datasetsNUM = count($legenda);

	for($i=0, $LABELS = null; $i<$labelsNUM; $i++){
		if($i!=0){
			$LABELS .= ", ";
		}
		if(isset($labels[$i])){
			$LABELS .= "'".$labels[$i]."'";
		}
	}
	$r1 = 220;
	$g1 = 220;
	$b1 = 220;

	$r2 = 151;
	$g2 = 187;
	$b2 = 205;

	for($i=0, $variacao = 50; $i<10; $i++){
		
		$r1 = mt_rand(0, 220);
		$g1 = mt_rand(0, 220);
		$b1 = mt_rand(0, 220);
		
		//$r2 = mt_rand(0, 220);
		//$g2 = mt_rand(0, 220);
		//$b2 = mt_rand(0, 220);
		
		$fillColor[] = "'rgba(".($r1-$variacao*$i).",".($g1-$variacao*$i).",".($b1-$variacao*$i).",0.5)'";
		//$fillColor[] = "'rgba(".($r2+$variacao*$i).",".($g2+$variacao*$i).",".($b2+$variacao*$i).",0.5)'";
		
		$strokeColor[] = $pointColor[] = "'rgba(".($r1-$variacao*$i).",".($g1-$variacao*$i).",".($b1-$variacao*$i).",1)'";
		//$strokeColor[] = $pointColor[] = "'rgba(".($r2+$variacao*$i).",".($g2+$variacao*$i).",".($b2+$variacao*$i).",1)'";
		
	}

	$pointStrokerColor = "'#fff'";

	
	$linha = $datasetsNUM * $labelsNUM;
	for($i=0; $i<$linha; $i++){
		$color[] = "'#".random_color()."'";
	}

	if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
		
		for($i = $j = 1, $data[$j] = null; $i<=$linha; $i++){
			if($i!=1 and (($i - 1)%(count($datasets)/$datasetsNUM))==0 and $datasetsNUM!=1){
				$j++;
				$data[$j] = null;
			}
			if(isset($datasets[(($i) - 1)])){
				$data[$j] .= $datasets[(($i) - 1)];
			}
			if(($i%(count($datasets)/$datasetsNUM))!=0 or $datasetsNUM==1){
				$data[$j] .= ", ";
			}
		}
		
	}else{
		for($i = 0; $i<$linha; $i++){
			$valor[] = $datasets[$i];
		}
	}

	if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
		$contador = $datasetsNUM;
	}else{
		$contador = $linha;
	}

	for($i=1, $DATASETS = $cor = null; $i<=$contador; $i++){
		if($i!=1){
			$DATASETS .= ", \n";
		}
		
		$DATASETS .= "{\n";
		if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
			$DATASETS .= "fillColor : ".$fillColor[$i - 1].",\n";
			$DATASETS .= "strokeColor : ".$strokeColor[$i - 1].",\n";
			if($tipo!="Bar"){
				$DATASETS .= "pointColor : ".$pointColor[$i - 1].",\n";
				$DATASETS .= "pointStrokeColor : ".$pointStrokerColor.",\n";
			}
			$DATASETS .= "data : [".$data[$i]."]\n";
			$cor[] = $fillColor[$i-1];
		}else{
			$DATASETS .= "value : ".$valor[$i - 1].",\n";
			$DATASETS .= "color : ".$color[$i - 1]."\n";
			$cor[] = $color[$i-1];
		}
		
		$DATASETS .= "}\n";
	}

	if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
		$CharData = "{\n";
		$CharData .= "labels: [ $LABELS ] ,\n";
		$CharData .= "datasets: [\n $DATASETS \n] \n}";
	}else{
		$CharData = "[\n";
		$CharData .= "$DATASETS \n]";
	}

	if(file_exists("plugins/chart/docs/Chart.js")){
		$script = "plugins/chart/docs/Chart.js";
	}else{
		$script = "../plugins/chart/docs/Chart.js";
	}
	$id = rand(1000, 9999);
	echo "<script src='$script'></script>";
	echo "<canvas class='canvas' id='canvas_$id' $tamanho></canvas>";
	echo "<script>";
	echo "var ChartData_$id = $CharData;";
	echo "var myRadar_$id = new Chart(document.getElementById(\"canvas_$id\").getContext(\"2d\")).$tipo(ChartData_$id);";
	echo "</script>";

	if(count($legenda)>1){
		echo "<br><div class='legenda' style='width:".$width_height[0]."px;'><h3>Legenda</h3>";
		for($l=0; $l<count($legenda); $l++){
			$cor[$l] = str_replace("'", "", $cor[$l]);
			echo "<span style='background-color:".$cor[$l].";'></span>".$legenda[$l]."<br>";
		}
		echo "</div>";
	}	
}
function random_color_part() {
    return str_pad(dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}


/**
* phpHtmlChart
*
* This function will output a bar chart in HTML given
* the supplied information.
* The data array should be a multi-dim array as follows:
*          0              1             2
*    -------------------------------------------
*    | Data Label | | Data Value | Unit Symbol |
*    -------------------------------------------
*  0 | Apples     | |    50      |      f      |
*  1 | Oranges    | |    25      |      f      |
*  2 | Limes      | |    15      |      f      |
*    -------------------------------------------
*
* @author       Jason D. Agostoni (jason@agostoni.net)
* @param    $paData             Multi-dim array of graph data
* @param    $psTitle            (optional) Chart title (prints on the top)
* @param    $psAxisLabel        (optional) Axis Label (prints on the data axis)
* @param    $psFontSize         (optional) Font size to use for label/title (ex. 8pt)
* @param    $piMaxSize          (optional) Max size of the graph (width for Horiz., Height for Vert.)
* @param    $psMaxSizeUnit      (optional) Measurement unit of max size (px, cm, mm, etc.)
* @param    $piBarSize          (optional) Width of the bar
* @param    $psBarUnit          (optional) Measurement unit of the bar width
* @param    $paColors           (optional) Array of HTML color codes to cycle through for bar colors
* @return   Returns the HTML to render the chart
*/
function geradorGrafico($paData, $id= 'grafico', $psTitle = '', $psAxisLabel = '', $psFontSize = '8pt',
                      $piMaxSize = 100, $psMaxSizeUnit = 'px', $piBarSize = 15, $psBarUnit = 'px', $tamanhoMinimo = 0,
                      $paColors = Array('#B9C9FE', '#E8EDFF')) {

    $iColors = sizeof($paColors);
	$contador = count($paData);
	
	$sHTML = "
	<script type='text/javascript'>
		function orientacao(orien, identidade){
			document.getElementById(orien+identidade).style.display= '';
			if(orien=='V'){
				document.getElementById('H'+identidade).style.display= 'none';
			}else{
				document.getElementById('V'+identidade).style.display= 'none';
			}
		}
	</script>
	";
	
	
    // Start HTML...
    $sHTML .= "
        <table style='font-family: Arial; font-size: $psFontSize'>
        	<tr>
        		<td align='center'><b>$psTitle</b></td>
        	</tr>
        	<tr>
        		<td align='center'>
        			<select name='horientacao' onchange=\"orientacao(this.value, '$id')\" style='width:auto;'>
        				<option value='V'>Vertical</option>
        				<option value='H'>Horizontal</option>
        			</select>
        		</td>
        	</tr>
        	
        	<tr>
        	<td>
        		<table id='H".$id."' style='display:none;'>
        			<tr>
        				<td align='right'>
    ";

    // Headers/scale
    $iMax = 0;
    for($iRow = 0; $iRow < sizeof($paData); $iRow++) {
        // Test for max...
        if($paData[$iRow][1] > $iMax) $iMax = $paData[$iRow][1];

        // Ouput the label
    	$sHTML .= "<div style ='height: $piBarSize$psBarUnit; padding:5px;'>".$paData[$iRow][0]."</div>";
    } // Rows in paData...

    $iScale = $iMax / $piMaxSize;

    $sHTML .= "
        </td><td align='center'>
        <table style='border-bottom: 1px solid black; border-left: 1px solid black;font-family: Arial; font-size: $psFontSize;'>
            <tr><td >
    ";
    
    // Ouput the rows
    for($iRow = 0; $iRow < sizeof($paData); $iRow++) {
        $sColor = $paColors[$iRow%$iColors];
        $iScale == 0 ? $iScale =1: false;
        $iBarLength = $paData[$iRow][1] / $iScale;
		if($paData[$iRow][2]== "R$"){
			$label = $paData[$iRow][2]." ".real($paData[$iRow][1]);
		}else{
			$label = $paData[$iRow][1]." ".$paData[$iRow][2];
		}
		while($iBarLength<$tamanhoMinimo){
			$iBarLength += 1;
		}
		
		$sHTML .= "
            <div style='background-color: $sColor; text-align: right; color: #333399;
                         height: $piBarSize$psBarUnit; 
                         padding:5px;
                         width: $iBarLength$psMaxSizeUnit;
                         white-space:nowrap;'>".
            $label."&nbsp;</div>
        ";
        
    }

    // Wrap up HTML
    $sHTML .= "
        					</td>
        				</tr>  
        			</table>
        		</td>
        	</tr>
        </table>
    ";
    
    $sHTML .= "
        <table style='border-bottom: 1px solid black; border-left: 1px solid black;font-family: Arial; font-size: $psFontSize;' id='V".$id."'>
            <tr><td >
    ";
    
    // Ouput the rows
    for($iRow = 0; $iRow < sizeof($paData); $iRow++) {
        $sColor = $paColors[$iRow%$iColors];
        $iBarLength = $paData[$iRow][1] / $iScale;
		if($paData[$iRow][2]== "R$"){
			$label = $paData[$iRow][2]." ".real($paData[$iRow][1]);
		}else{
			$label = $paData[$iRow][1]." ".$paData[$iRow][2];
		}
		while($iBarLength<$tamanhoMinimo){
			$iBarLength += 1;
		}
		$sHTML .= "
            <div style='background-color: $sColor; text-align: right; color: #333399;
                         display: inline-block;
                         vertical-align: bottom;
                         padding:5px;
                         height: ".($iBarLength/3)."px;'> ".
            $label."<br>
            <div style='padding-top:".(($iBarLength/3)-35)."px; text-align: center;'>
            ".$paData[$iRow][0]."
            </div></div>
        ";
        
    }

    // Wrap up HTML
    $sHTML .= "
        					</td>
        				</tr>  
        			</table>
        		</td>
        	</tr>
        	<tr>
        		<td align='center'>$psAxisLabel</td>
        	</tr>
        </table>
    ";
    return $sHTML;
}

//funcao para identificar o mes de data 0000-00-00 00:00:00 ou 0000:00:00
//e deixalo nominal
function mesNominal($data){
	$mes = explode("-", $data);
	switch ($mes[1]) {
		case '1':
			$mes = "Janeiro";
			break;
		case '2':
			$mes = "Fevereiro";
			break;
		case '3':
			$mes = "Março";
			break;
		case '4':
			$mes = "Abril";
			break;
		case '5':
			$mes = "Maio";
			break;
		case '6':
			$mes = "Junho";
			break;
		case '7':
			$mes = "Julho";
			break;
		case '8':
			$mes = "Agosto";
			break;
		case '9':
			$mes = "Setembro";
			break;
		case '10':
			$mes = "Outubro";
			break;
		case '11':
			$mes = "Novembro";
			break;
		default:
			$mes = "Dezembro";
			break;
	}
	return $mes;
}

function ajudaTool($msg, $link = "javascript:void(0)", $texto = "<img class='imgHelp' src='img/info.png'>"){
	return "<a href='$link' title='$msg'>$texto</a>";
}

function servicoForm($ID = 0, $op="novo"){
	
	$msgServico = "Os produtos cadastrados para a fabricação desse serviço devem ser informados<br>";
	$msgServico .= "a quantidade para fabricaçao de 1 unidade deste Serviço.<br>";
	$msgServico .= "Por Exemplo; Para a personalização em pintura de 1 camisa será necessário 0,01<br>";
	$msgServico .= "litro de tinta.";
	
	$msgComissao = "Comissões são geradas somente quando a Ordem de serviço está concluída. Além disso,<br>";
	$msgComissao .= "a comissão é calculada somente em cima dos valores recebidos e não do valor vendido, ou seja,<br>";
	$msgComissao .= " caso seja vendido um serviço e não seja recebido nenhum valor, essa ordem de serviço não<br>";
	$msgComissao .= " irá gerar comissão de participação até que seja arrecadado algum valor.<br>";
	$msgComissao .= "Além disso para receber a comissão o colaborador deverá ter seu nome marcado na lista de<br>";
	$msgComissao .= " usuários que concluiram a ordem de serviço.";
	
	$msgComissaoPenalidade = "Essa regrá passará a valer quando o serviço for concluido em atraso.";
	$msgComissaoPenalidade .= " Ela será calculada em cima da comissão gerada. Por Exemplo; A Ordem de serviço foi";
	$msgComissaoPenalidade .= " concluida e gerou uma comissão de R$50,00. Contudo ela foi concluida em atraso, e a";
	$msgComissaoPenalidade .= " penalidade para esse serviço é de 30%. Logo o colaborador que conclui este serviço";
	$msgComissaoPenalidade .= " recebera não R$50,00, mas sim R$35,00 que corresponde a R$50,00 menos 30%.";
	
	$msgComissaoEscolha = "Escolha um valor fixo para gerar comissão ou uma porcentagem em cima do valor final do servico.<br>";
	$msgComissaoEscolha .= "A diferença é que com o valor fixo (No valor de) idependente do colaborador concluir 1 unidade";
	$msgComissaoEscolha .= " deste serviço ou 100 unidades deste serviço ele sempre receberá aquele valor.<br>";
	$msgComissaoEscolha .= "Já com a escolha dinâmica (Porcentagem em cima do valor) caso o colaborador conclua a Ordem de serviço";
	$msgComissaoEscolha .= " o valor da comissão será calculado no preço do serviço multiplicado pela quantidade produzida";
	$msgComissaoEscolha .= " multiplicado pela porcentagem da regra de comissão. Por exemplo; Para instalar 1 condicionador";
	$msgComissaoEscolha .= "de ar é cobrado R$200,00 e o bônus de comissão de 10%, contudo o colaborador concluiu uma Ordem de serviço";
	$msgComissaoEscolha .= " que havia 3 instalações, logo R$200,00 vezes 3 vezes 10% equivale a R$60,00 de comissão";
	
	$msgComissaoMax = "Deixando esse campo com o valor 0 você não estará limitando o valor maximo desta comissão.";
	$msgComissaoMax .= "Preenchendo algum valor diferente de 0 este serviço sofrerá uma limitação caso o valor gerado";
	$msgComissaoMax .= " da comissão ultrapasse o valor limitado. Por exemplo; Numa mesma Ordem de serviço foi vendido";
	$msgComissaoMax .= " a aplicação de fumê em 2 carros, cobrando o valor de R$ 300,00 cada carro. A comissão para este";
	$msgComissaoMax .= " serviço está marcado como 5%, e o valor maximo permitido é de R$25,00. Logo o colaborador que";
	$msgComissaoMax .= " concluir esta ordem de serviço ganhará uma comissão de R$15,00 em cada carro, total de R$30,00.";
	$msgComissaoMax .= " Contudo este valor ultrapassou a margem maxima permitida (R$25,00), logo em vez de receber";
	$msgComissaoMax .= " R$30,00 ele será bonificado com o valor maximo que seria; R$25,00.";
	
	$msgPreco = "Regras de preço servem para dar uma maior flexibilidade nos preços dos serviços.<br>";
	$msgPreco .= "Além de que quanto maior a quantidade que o cliente comprar, um preço diferenciado<br>";
	$msgPreco .= " poderá ser oferecido.";
	
	//js para esta pagina em especifico
	echo "<script src='js/cadastrarServicoFormulario.js' type='text/javascript'></script>
			<script src='js/cadastrarServicoFiltro.js' type='text/javascript'></script>";
	
	
	
	if ($ID) {
		$instrucao = "select * from servico where id='$ID'";
        $sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		//id nome modelo id_categoria id_subcategoria id_marca quantidade_minima
		//descricao rate_click rate_compra valor_compra pis cofins icms ipi outros
		//ml1por ml2por ml3por
		$titulo = "Edição";
		$get = "?id=".base64_encode($ID);
		
	} else {
		
		$titulo = "Cadastro";
		$nome  = $id_categoria = $id_subcategoria =  $descricao = $rate_click = $rate_compra =
		$mlpor = $descMaxpor = $qtd_minima = $get = null;
		$produtoQtd = 1;
		$valor = 0;
		
	}
	
	echo "<form name='formCadastraServico' method='post' action='cadastrarServico.php".$get."' enctype='multipart/form-data' onSubmit='return filtro();'>";
	echo "<input type='hidden' name='id' value='$ID'>";
	echo "<input type='hidden' name='op' value='$op'>";
	
	echo "<table class='servico' id='gradient-style' style='width:80%'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th scope='col' colspan='6' align='center'>$titulo de Serviço</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td colspan='2'>";
					echo "Categoria<a href='javascript:void(0)' ".pop("categoriaProduto.php")." title='Cadastrar nova categoria'><img class='imgHelp' src='img/mais.png'></a><br>
	                    <select name='categoria' onChange=\"detSubCat();\">";
				    echo opcaoSelect("categoria", "1", "Ativo", $id_categoria, null, "order by nome");
				    echo "</select>";
				    echo "<input type='hidden' name='subcategoria' value='$id_subcategoria'>";
				echo "</td>";
				echo "<td colspan='2' id='selectSubCat'>";
				    echo "Sub Categoria<a href='javascript:void(0)' ".pop("subCategoriaProduto.php")." title='Cadastrar nova sub categoria'><img class='imgHelp' src='img/mais.png'></a><br>";
				    $sql = query("select id_categoria as idCategoria from sub_categoria where id_categoria='$id_categoria'");
				    if (mysqli_num_rows($sql)>0) {
				        echo "<select name='SUBCATEGORIA' onchange='mudaSubCat(this.value)'>";
				        echo opcaoSelect("sub_categoria", "2", "Ativo", $id_subcategoria, NULL, "and id_categoria='$id_categoria' order by nome");
				    } else {
				        echo "<select name='SUBCATEGORIA' disabled>";
				    }
				    echo "</select>";
				echo "</td>";
				echo "<td colspan='2' style='width:40%; white-space: nowrap;'>";
				echo "Nome<br><input type='text' name='nome' id='nome' value='$nome' style='width:90%;' autocomplete='off' ";
				echo "onkeyup=\"verificaNome();\" onkeydown=\"verificaNome();\" onkeypress=\"verificaNome();\">";
				echo "<input type='hidden' name='checkNomeInput' id='checkNomeInput' value='true'>";
				echo "<span id='checkNomeSpan' style='display:none;'></span>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='6'>Descrição<br><textarea class='ckeditor' name='descricao'>$descricao</textarea></td>";
			echo "</tr>";
			
			if($ID){
				$sqlProduto = query("select * from servico_produto where id_servico='$ID'");
				$produtoQtd = mysqli_num_rows($sqlProduto);
			}
			if($produtoQtd==0){
				$produtoQtd=1;
			}
			echo "<tr>";
				echo "<th>";
				echo "<input type='hidden' name='produtoQtd' id='produtoQtd' value='$produtoQtd'>";
				echo "<a href='#adcionarCampoProduto' class='adicionarCampoProduto' onclick=\"\" title='Adicionar mais um produto.'><img class='imgHelp' src='img/mais.png'></a>";
				echo "</th>";
				echo "<th colspan='5'>Produtos usados para fabricação desse serviços ".ajudaTool($msgServico)."</th>";
			echo "</tr>";
			echo "<tr>";
				echo "<th></th>";
				echo "<th>ID Nome</th>";
				echo "<th style=' width:10%;'>Quantidade</th>";
				echo "<th style='white-space: nowrap; width:10%;'>Volume <a href='javascript:void(0)' ".pop("volume.php")." title='Adicionar um volume'><img class='imgHelp' src='img/mais.png'></a></th>";
				echo "<th>Sub Total</th>";
				echo "<th>Total</th>";
			echo "</tr>";
			for($i=1, $subTotalProduto = $totalProduto = "00,00"; $i<=$produtoQtd; $i++){
				if($ID){
					extract(mysqli_fetch_assoc($sqlProduto));
					$id_produto==0 ? $id_produto = "" : false;
				}else{
					$id_produto = $produto = $id_produto_volume = $subTotal = null;
					$qtd = 0;
				}
				echo "<tr class='campoProduto'>";
					echo "<td><a href='#adicionarCampoProduto' class='removerCampoProduto' title='Deletar esse item' class='removerCampo'><img class='imgHelp' src='img/menos.png'></a></td>";
					echo "<td style='white-space:nowrap;'>";
					echo "<input type='text' name='id_produto[]' size='1' value='$id_produto' id='produtoId_".$i."' ".mascara("Integer", null, "style='width:15%;'  autocomplete='off'", null, null, "buscarProduto(this, true);").">";
					echo "<input type='text' name='produto[]' value='$produto' id='produto_".$i."' style='width:75%;' onkeyup='buscarProduto(this, false);' autocomplete='off'>";
					echo "<div class='suggestionsBox' id='suggestions_".$i."' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
					echo "<div class='suggestionList' id='autoSuggestionsList_".$i."'></div></div>";
					echo "</td>";
					echo "<td>";
					$javaScript = "calcularPreco(this, \"produtoSubTotal_\",\"produtoTotal_\");";
					echo "<input type='text' name='quantidade[]' value='".real($qtd)."' ";
					echo mascara("Valor2", null, "style='width:auto;' autocomplete='off' size='2' onblur(\"$javaScript turnZero(this);\")", $javaScript, $javaScript, $javaScript);
					echo " id='produtoQtd_".$i."'>";
					echo "</td>";
					echo "<td>";
					echo "<select name='volume[]' id='produtoVolume_".$i."'>";
					echo opcaoSelect("produto_volume", "abreviatura", "Ativo", $id_produto_volume);
					echo "</select>";
					echo "</td>";
					echo "<td>";
					echo "<input type='text' name='produtoSubTotal[]' value='".real($subTotal)."' id='produtoSubTotal_".$i."' ";
					$javaScript = "calcularPreco(this, \"produtoQtd_\", \"produtoTotal_\");";
					echo mascara("Valor3", null, "onblur=\"turnZero(this);\" autocomplete=\"off\"", $javaScript, $javaScript, $javaScript);
					echo " class='totalValor preco' >";
					echo "</td>";
					$total = $qtd * $subTotal;
					echo "<td><input type='text' onblur=\"turnZero(this);\" name='produtoTotal[]' value='".real($total)."' id='produtoTotal_".$i."' ".mascara("Valor3")." class='totalValor preco' autocomplete='off'></td>";
				echo "</tr>";
				
				$subTotalProduto += $subTotal;
				$totalProduto += $total;
				
			}
			echo "<tr>";
				echo "<td colspan='4' align='right'><a href='#campoProduto' title='Clique aqui para atualizar os valores totais.' onclick=\"calcularProdutoPrecoTotal();\"><img src='img/refresh.png' width='30'></a></td>";
				echo "<td><input type='text' class='totalValor inputValor preco' name='subTotalProduto' id='subTotalProduto' value='".real($subTotalProduto)."'></td>";
				echo "<td><input type='text' class='totalValor inputValor preco' name='totalProduto' id='totalProduto' value='".real($totalProduto)."'></td>";
			echo "</tr>";
			
			
			
			
			echo "<tr>";
				echo "<th><a href='#adicionarRegraComissao'  class='adicionarRegraComissao' title='Adicionar mais uma regra de comissão.'><img class='imgHelp' src='img/mais.png'></a></th>";
				echo "<th colspan='2'>";
				echo "Regras de comissão ".ajudaTool($msgComissao);
				if($ID){
					$sqlComissao = query("select * from servico_comissao where id_servico='$ID'");
					$comissaoQtd = mysqli_num_rows($sqlComissao);
				}else{
					$comissaoQtd = 1;
					$id = $id_servico = $usuario_valor = $preco_tipo = null;
					$preco_valor = $preco_por = $preco_max = $penalidade = 0;
					$usuario_tabela = "funcao";
				}
				
				echo "<input type='hidden' name='comissaoQtd' id='comissaoQtd' value='$comissaoQtd'>";
				echo "</th>";
				echo "<th colspan='2'>Tipo de comissão ".ajudaTool($msgComissaoEscolha)."</th>";
				echo "<th>Penalidade ".ajudaTool($msgComissaoPenalidade)."</th>";
			echo "</tr>";
			
			for($i=1;$i<=$comissaoQtd; $i++){
				if($ID){
					extract(mysqli_fetch_assoc($sqlComissao));
				}
				echo "<tr class='regraComissao'>";
					echo "<td><a href='#adicionarRegraComissao' class='removerRegraComissao' title='Deletar esse item'><img class='imgHelp' src='img/menos.png'></a></td>";
					echo "<td>";
						echo "<select name='usuarioTabela[]' id='usuarioTabela_".$i."' onchange=\"showUsuarioValor(this)\">";
							if($usuario_tabela== "usuario"){
								echo "<option value='funcao'>Por função</option>";
								echo "<option value='usuario' selected='yes'>Usuário específico</option>";
							}else{
								echo "<option value='funcao' selected='yes'>Por função</option>";
								echo "<option value='usuario'>Usuário específico</option>";
							}
						echo "</select>";
					echo "</td>";
					echo "<td class='usuarioValor' id='usuarioValorTd_".$i."'>";
						echo "<select name='usuarioValor[]' id='usuarioValor_".$i."'>";
							echo opcaoSelect($usuario_tabela, "nome", "ativo", $usuario_valor);
						echo "</select>";
					echo "</td>";
					echo "<td>";
						echo "<select name='precoTipo[]' id='precoTipo_".$i."' onchange=\"showPrecoValor(this)\">";
							if($preco_tipo){
								echo "<option value='1' selected='yes'>No valor de</option>";
								echo "<option value='0'>Porcentagem em cima do preço</option>";
							}else{
								echo "<option value='1'>No valor de</option>";
								echo "<option value='0' selected='yes'>Porcentagem em cima do preço</option>";
							}
						echo "</select> ";
					echo "</td>";
					echo "<td align='center'>";
						if($preco_tipo){
							$styleReal = "inline-block;";
							$stylePor = "none;";
							$precoValorReal = $preco_valor;
							$precoValorPor = $preco_por;
							$precoValorMax = $preco_max;
						}else{
							$styleReal = "none;";
							$stylePor = "inline-block;";
							$precoValorReal = $preco_valor;
							$precoValorPor = $preco_por;
							$precoValorMax = $preco_max;
						}
						echo "<div class='precoValorReal' id='precoValorReal_".$i."' style='display: $styleReal'>";
						echo "<input type='text' class='totalValor preco' ".mascara("Valor2")." name='precoValorReal[]' id='precoValorRealInput_".$i."' onblur=\"turnZero(this);\" value='".real($precoValorReal)."'>";
						echo "</div>";
						echo "<div class='precoValorPor' id='precoValorPor_".$i."' style='display: $stylePor'><center>";
						echo "<input type='text' class='totalValor porcentagem' ".mascara("Valor2")." style='width:auto;' size='5' name='precoValorPor[]' id='precoValorPorInput_".$i."' ";
						echo "onblur=\"turnZero(this);\" value='".real($precoValorPor)."'>";
						echo "<br>até o valor maximo de<br>";
						echo "<a href='javascript:void(0)' title='$msgComissaoMax'>";
						echo "<input type='text' class='totalValor preco' ".mascara("Valor2")." style='width:auto;' size='5' name='precoValorMax[]'  id='precoValorMaxInput_".$i."' ";
						echo "onblur=\"turnZero(this);\" value='".real($precoValorMax)."'>";
						echo "</a>";
						echo "</center><div>";
					echo "</td>";
					echo "<td><input type='text' name='penalidade[]' ".mascara("Valor2")." class='totalValor porcentagem' id='penalidadeInput_".$i."' onblur=\"turnZero(this);\" value='".real($penalidade)."'></td>";
				echo "</tr>";
			}
			
			$sqlPreco = query("select * from servico_preco where id_servico='$ID'");
			mysqli_num_rows($sqlPreco)>0 ? $qtdPreco = mysqli_num_rows($sqlPreco) : $qtdPreco = 1;
			echo "<tr>";
				echo "<th>";
				echo "<a href='#adicionarRegraPreco' class='adicionarRegraPreco' title='Adicionar mais uma regra de preço.'><img class='imgHelp' src='img/mais.png'></a>";
				echo "<input type='hidden' name='precoQtd' id='precoQtd' value='$qtdPreco'>";
				echo "</th>";
				echo "<th colspan='5'>Regras de preços ".ajudaTool($msgPreco)."</th>";
			echo "</tr>";
			
			for($i=1; $i<= $qtdPreco; $i++){
				if(mysqli_num_rows($sqlPreco)>0){
					extract(mysqli_fetch_assoc($sqlPreco));
				}else{
					$limite = $valor = 0;
				}
				echo "<tr class='regraPreco'>";
					echo "<td><a href='#adicionarRegraPreco' class='removerRegraPreco' title='Deletar esse item'><img class='imgHelp' src='img/menos.png'></a></td>";
					echo "<td colspan='5'>";
						echo "Abaixo de ";
						echo "<a href='#adicionarRegraPreco' title='Utilize o valor 0 (zero) para representar o infinito'>";
						$javaScript = "calcularPreco2(this, \"preco_\", true);";
						echo "<input type='text' name='quantidadePreco[]' id='quantidadePreco_".$i."' ".mascara("Integer", null, "onblur=\"turnZero(this); \", size='2' style='width:auto;'" ,$javaScript, $javaScript, $javaScript)." value='$limite'>";
						echo "</a>";
						echo " unidades será aplicado o preço de ";
						$javaScript = "calcularPreco2(this, \"quantidadePreco_\", false);";
						echo "<input type='text' name='preco[]' size='4' id='preco_".$i."' ".mascara("Valor2", null, "onblur=\"turnZero(this); \", class='totalValor preco' style='width:auto;'" ,$javaScript, $javaScript, $javaScript)." value='".real($valor)."'>";
						echo " e o preço total será de ";
						$limite == 0 ? $valorTotal = $valor : $valorTotal = $limite * $valor;
						echo "<input type='text' name='precoTotal[]' size='4' id='precoTotal_".$i."' onblur=\"turnZero(this);\" value='".real($valorTotal)."' class='totalValor inputValor preco' style='width:auto;'>";
					echo "</td>";
				echo "</tr>";
			}
			$sql = query("select * from servico_imagem where id_servico='$ID'");
			$imagensQtd = mysqli_num_rows($sql);
			
			if($ID and $op=="editar"){
				echo "<tr>";
					echo "<th colspan='6'><a href='javascript:void(0)' ".pop("servico_imagem_upload.php?id=$ID", 600, 700)." title='Adicionar imagens para $nome'><img class='imgHelp' src='img/mais.png'></a> Imagens deste serviço</th>";
				echo "<tr>";
			}
			if($imagensQtd>0 and $op =="editar"){
				echo "<tr>";
				echo "<td colspan='6' align='center'>";
				
				echo showImagemServico($ID, 8, 0, true);
				
				echo "</td>";
				echo "</tr>";
				
			}
			
			echo "<tr>";
				echo "<th colspan='4'></th>";
				echo "<th align='right'><input type='submit' class='btnEnviar' value='Enviar'></th>";
				echo "<th align='right'><input type='reset' value='Cancelar'></th>";
			echo "</tr>";
			
		echo "</tbody>";
	echo "</table>";
	echo "</form>";
}

function showImagemServico($idServico, $qtdPorLinha, $quantidadeMax = 0, $mostrarDelete = false){
	
	$script = "";
	
	$sql = query("select * from servico_imagem where id_servico='$idServico'");
	if(mysqli_num_rows($sql)>0){
		$quantidadeMax == 0 ? $imagensQtd =  mysqli_num_rows($sql) : $imagensQtd = $quantidadeMax;
		$quantidadeMax > mysqli_num_rows($sql) ? $imagensQtd = mysqli_num_rows($sql) : false;
		
		for($i=1; $i<=$imagensQtd; $i++){
			
			extract(mysqli_fetch_assoc($sql));
			$script .= "<div style='display:inline-block;'>";
			if(!file_exists($imagem)){
				$imagem = "img/icones/pesquisaProduto.png";
			}
			if(!file_exists($miniatura)){
				$miniatura = "img/icones/pesquisaProduto.png";
			}
			$script .= "<a href='javascript:void(0)' ".pop("imagem.php?img=$imagem")." title=\"<img style='height:400px;' src='$imagem' >\"><img src='$miniatura'></a><br>";
			if($mostrarDelete){
				$script .= "<a href='javascript:void(0)' ".pop("servico_imagem_upload.php?id=$idServico&a=delete&idDeletar=$id", 10, 10)." title='Deletar esta imagem.'>Deletar</a>";	
			}
			$script .= "</div>";
			if($i%$qtdPorLinha==0){
				$script .= "<br>";
			}
			
		}	
	}else{
		$script .= "<div style='display:inline-block;'>";
		$script .= "<a href='javascript:void(0)' title='Sem imagem'><img src='img/icones/pesquisaProduto.png'></a>";
		$script .= "</div>";
	}
	
	
	return $script;
}

function ultimaId($tabela){
	$sql = query("select max(id) as id from $tabela");
	extract(mysqli_fetch_assoc($sql));
	return $id;	
}


function ordemServico($idOrdemServico = null){
	//js para esta pagina em especifico
	echo "<script src='js/cadastrarOrdemServicoFiltro.js' type='text/javascript'></script>";
		echo "<table class='itens' id='gradient-style'>";
			if($idOrdemServico){
				$sql = query("select * from ordem_servico where id='$idOrdemServico'");
				extract(mysqli_fetch_assoc($sql));
				$statusOrdemServico = $status;
				echo "<tr>";
					echo "<th colspan='6'>";
						echo "Ordem de Serviço $idOrdemServico";
						echo "<div style='float:right;'>";
						echo "<form method='post' action='cadastrarOrdemServico.php' enctype='multipart/form-data' style='display:inline-block'>";
						echo "<input type='hidden' name='op' value='mudarStatus'>";
						echo "<input type='hidden' name='id' value='".base64_encode($idOrdemServico)."'>";
						$sqlStatus = query("select * from ordem_servico_status where id='$statusOrdemServico'");
						extract(mysqli_fetch_assoc($sqlStatus));
						echo "<select name='status' onchange='this.form.submit();' style='background-color:$cor_bg; color:$cor_font; width:auto;'>";
						$sqlStatus = query("select * from ordem_servico_status");
						for($i=0; $i<mysqli_num_rows($sqlStatus); $i++){
							extract(mysqli_fetch_assoc($sqlStatus));
							if($id==$statusOrdemServico){
								echo "<option style='background-color:$cor_bg; color:$cor_font;' value='$id' selected='yes'>$nome</option>";
							}else{
								echo "<option style='background-color:$cor_bg; color:$cor_font;' value='$id'>$nome</option>";
							}
						}
						echo "</select>";
						echo "</form>";
						if($id_orcamento){
					    	$tabela_referido = "orcamento";
							$referido = $id_orcamento;
					    }else{
							$tabela_referido = "ordem_servico";
							$referido = $idOrdemServico;
					    }
						
						echo arqVisualizar("itensForm3", "Visualizar / Esconder restante do cadastro do cliente");
						
						if(getCredencialUsuario("pesquisaConta.php")){
						    if(empty($id_cliente)){//cliente não cadastrado
						        $busca = $cliente;
						    }else{
						        $busca = $id_cliente;
						    }
							$instrucao = "select id as idConta from conta where entidade='$busca' and referido='$referido' and tabela_referido='$tabela_referido'";
							$sqlConta = query($instrucao);
							extract(mysqli_fetch_assoc($sqlConta));
							echo "<a href='pesquisaConta2.php?conta=".base64_encode($idConta)."' title='Visualizar Conta'><img src='img/icones/pesquisaConta.png'></a> ";
						}
						if(getCredencialUsuario("pesquisaOrcamento.php") and $tabela_referido=="orcamento"){
							echo "<a href='cadastrarOrcamento.php?op=visualizar&id=".base64_encode($id_orcamento)."' title='Visualizar Orçamento'><img src='img/icones/pesquisaOrcamento.png'></a> ";
						}
						echo "<a href='javascript:void(0)' ".pop("ordemServicoImp.php?os=".base64_encode($idOrdemServico), "auto", "auto")." title='Versão para impressão'><img src='img/impressora.png'></a>";
						echo "</div>";
					echo "</th>";
				echo "</tr>";
			}
			
			echo "<form name='formulario' method='post' action='cadastrarOrdemServico.php' enctype='multipart/form-data' onSubmit='return (filtroClienteFornecedor() && filtroOrdemServico() ? true: false);'>";
			if($idOrdemServico){
				$sql = query("select * from ordem_servico where id='$idOrdemServico'");
				extract(mysqli_fetch_assoc($sql));
				echo "<input type='hidden' name='op' value='editar'>";//o restante do hidden esta na ultima variavel
				$statusOrcamento = $status;
				$precoServico = $valor;
				$referido = $idOrdemServico;
			}else{
				$id = $id_orcamento = $id_cliente = $cliente = $fone = $entrega_tipo = $expedicao = $tabela_referido = $statusOrdemServico =
				$id_servico = $quantidade = $valor = $status = $data_concluida = $precoServico = $referido = $latitude = $longitude = null;
				$data_venda = date("Y-m-d");
				$data_previsao = date("Y-m-d H:i", strtotime("+ 5 days + 2 hours"));
				echo "<input type='hidden' name='op' value='novo'>";
			}
			if($id_cliente){
				$sql = query("select * from cliente_fornecedor where id='$id_cliente'");
				extract(mysqli_fetch_assoc($sql));
				$busca = $id_cliente;
			}else{
				$id = $formato = $tipo = $razao_social = $cpf_cnpj = $rg_ie = $data_nascimento = $email = $latitude = $longitude = 
				$fone2 = $endereco = $numero = $bairro = $cidade = $estado = $cep = $referencia = $observacoes = 
				$status = $busca = $latitude = $longitude = null;
				$nome = $cliente;
				$fone1 = $fone;
			}
			
			$cliente = new cliente_fornecedor($id, $formato, $tipo, $nome, $razao_social, $cpf_cnpj, $rg_ie, $data_nascimento, $email,
			$fone1, $fone2, $endereco, $numero, $bairro, $cidade, $estado, $cep, $referencia, $observacoes, $latitude, $longitude, $status);
			echo $cliente->formulario(true);
			
			echo "<tr>";
				echo "<th scope='col' colspan='2' align='center'>Data de abertura</th>";
				echo "<th scope='col' colspan='3' align='center'>Data de previsão de entrega</th>";
				echo "<th scope='col' colspan='1' align='center'>Data de finalização</th>";
			echo "</tr>";
			echo "<tr>";
				echo "<td scope='col' colspan='2' align='center'><input type='hidden' name='dataAbertura' value='$data_venda'>".formataData($data_venda)."</td>";
				if(!empty($data_previsao)){
					$data_previsao = explode(" ", $data_previsao);
					$data_previsao_hora = explode(":", $data_previsao[1]);
					$data_previsao_hora = $data_previsao_hora[0].":".$data_previsao_hora[1];
					$data_previsao = $data_previsao[0];
				}else{
					$data_previsao_hora = date("H:i", strtotime("+ 2 hours"));
				}
				echo "<td scope='col' colspan='3' align='center'>".inputData("formulario", "dataPrevisao", null, formataData($data_previsao))." ";
				echo "<input name='dataPrevisaoHora' type='text' ".mascara("Hora", 5, "value='$data_previsao_hora' style='width:auto;' size='4'")."</td>";
				echo "<td scope='col' colspan='1' align='center'><input type='hidden' name='dataConcluida' value='$data_concluida'>".formataData($data_concluida)."</td>";
			echo "</tr>";
			
			echo "<tr>";
				$msg = "Se marcado como \"Entregar\", o cliente solicitou que entregasse o serviço no endereço do cadastro do cliente.";
				$msg .= "Se marcado como \"Buscar\", o cliente concordou em bsucar o serviço na empresa.";
				echo "<th scope='col' colspan='3' align='center'>Tipo de Entrega ".ajudaTool($msg)."</th>";
				echo "<th scope='col' colspan='3' align='center'>Produziram está Ordem de Serviço</th>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<td scope='col' colspan='3' align='center'>";
				echo "<select name='entregaTipo'>";
				if($entrega_tipo=="entregar"){
					echo "<option value='entregar' selected='yes'>Entregar</option>";
					echo "<option value='buscar'>Buscar</option>";
				}else{
					echo "<option value='entregar'>Entregar</option>";
					echo "<option value='buscar' selected='yes'>Buscar</option>";
				}
				echo "</select>";
				echo "</td>";
				echo "<td scope='col' colspan='3' align='center'>";
				$sqlProduziram= query("select * from ordem_servico_usuario where id_ordem_servico='$idOrdemServico' and id_ordem_servico!='0'");
				for($i=0; $i<mysqli_num_rows($sqlProduziram); $i++){
					extract(mysqli_fetch_assoc($sqlProduziram));
					echo registro($id_usuario_contribuidor, "usuario", "nome")." ";
					echo registro(registro($id_usuario_contribuidor, "usuario", "funcao"), "funcao", "nome")."<br>";
				}
				echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<th scope='col' colspan='6' align='center'>";
				echo "<input type='hidden' name='historicoVisualizacao' value='0'>";
				echo "<a href='#historico' title='Mostrar histórico' onclick='historico();'><img src='img/arq_show.png'></a>";
				echo "Histórico desta Ordem de Serviço</th>";
			echo "</tr>";
			$sqlHistorico = query("select * from ordem_servico_historico where id_ordem_servico='$idOrdemServico' and id_ordem_servico!='0'");
			for($i=0; $i<mysqli_num_rows($sqlHistorico); $i++){
				extract(mysqli_fetch_assoc($sqlHistorico));
				echo "<tr class='historico' style='display:none;'>";
					if(getCredencialUsuario("administrativoToken.php")){
						echo "<td scope='col' colspan='1' align='center'>";
						echo "<a title='Deletar esse histórico' href='cadastrarOrdemServico.php?op=deletarHistorico&idHistorico=".base64_encode($id)."'>";
						echo "<img  src='img/deletar.png'>";
						echo "</a>";
						echo "</td>";
					}else{
						echo "<td></td>";
					}
					echo "<td scope='col' colspan='3' align='center'>".nl2br($texto)."</td>";
					echo "<td scope='col' colspan='1' align='center'>".registro($id_usuario, "usuario", "nome")."</td>";
					echo "<td scope='col' colspan='1' align='center'>".formataData($data)."</td>";
				echo "</tr>";
			}
			echo "<tr  class='historico' style='display:none;'>";
				echo "<td scope='col' colspan='6' align='center'><textarea class='ckeditor' name='historico'></textarea></td>";
			echo "</tr>";
			
			$sqlAtributos = query("select * from ordem_servico_atributo_padrao where status='1'");
			for($i=0;$i<mysqli_num_rows($sqlAtributos); $i++){
				if($i==0){
					echo "<tr>";
						echo "<th scope='col' colspan='6' align='center'><input type='hidden' name='qtdAtributos' value='".mysqli_num_rows($sqlAtributos)."'>Atributos da Ordem de Serviço</th>";
					echo "</tr>";
				}
				
				extract(mysqli_fetch_assoc($sqlAtributos));
				$idAtributo = $id;
				echo "<tr>";
					//echo "<td scope='col' colspan='2' align='center' style='text-transform:capitalize; width:25%; white-space:normal;'>";
					echo "<td colspan='6' style='text-transform:capitalize; white-space:normal;'>";
					echo "$nome<input type='hidden' name='nomeAtributo[]' value='$nome' id='nome_$idAtributo'>";
					echo "<input type='hidden' name='idAtributo[]' value='$idAtributo'>";
					$caminho = END_ARQ."arq_ordem_servico/".$idOrdemServico."/".$tipo_item."_".$idAtributo."/";
					echo "<input type='hidden' name='obrigadoriedade[]' value='$obrigatoriedade' id='obrigatoriedade_$idAtributo'>";
					//echo "</td>";
					//echo "<td scope='col' colspan='4' style='white-space:normal;'>";
					echo "<br>";
					$instrucao = "select valor as valorAtributo from ordem_servico_atributo where id_ordem_servico='$idOrdemServico' ";
					$instrucao .= "and id_ordem_servico_padrao='$idAtributo'";
					$sqlAtributosValor = query($instrucao);
					mysqli_num_rows($sqlAtributosValor)>0 ? extract(mysqli_fetch_assoc($sqlAtributosValor)) : $valorAtributo = null;
					//"Texto curto", "Texto com parágrafo", "Arquivo", "Seleção de itens", "Imagem"
					if($tipo_item=="Texto curto"){
						echo "<input type='text' name='valorAtributo[]' value='$valorAtributo' id='valorAtributo_$idAtributo'>";
					}elseif($tipo_item=="Texto com parágrafo"){
						echo arqVisualizar("text_$idAtributo", "Clique para mostrar a caixa de texto.", 1);
						echo "<div class='text_$idAtributo' style='display:none;'><textarea class='ckeditor' name='valorAtributo[]' id='valorAtributo_$idAtributo'>$valorAtributo</textarea></div>";
					}elseif($tipo_item=="Arquivo"){
						$tamanhoMax = "3000000";
						$msg = "Este arquivo tem o tamanho maior que o permitido (3mb).<br>Por favor diminuia o tamanho ou tente um novo arquivo.";
						echo info($msg, "red", null, "none", "arq_".$idAtributo);
						echo "<script type='text/javascript'>\n";
							//função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
							echo "function checarTamanho_$idAtributo(arq){\n";
								//this.files[0].size gets the size of your file.
								echo "if(arq.files[0].size >= $tamanhoMax){\n";
									echo "$('.arq_$idAtributo').show();\n";
									echo "$(arq).attr('value', '');\n";
								echo "}\n";
							echo "}\n";
						echo "</script>\n";
						echo "<input type='file' name='arq[]' id='arq_$idAtributo' onchange=\"checarTamanho_$idAtributo(this)\" multiple /><br>";
						if($idOrdemServico){
							if(is_dir($caminho)){
								$diretorio = dir($caminho);	
							}else{
								mkdir($caminho, 0777, true);
								chmod($caminho, 0777);
								$diretorio = dir($caminho);
							}
							echo "<input type='hidden' name='valorAtributo[]' value='arq_$idAtributo'>";
							$valorAtributo = 0;
							while($arquivo = $diretorio -> read()){
								if(!is_dir($arquivo)){
									echo "<div class='arquivo'>";
									echo "<a title='Baixar arquivo.' href='".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo'>";
									echo "<img  src='img/arquivo_baixar.png'> ";
									echo "</a> ";
									echo "<a title='Deletar arquivo' ".pop("deletar.php?arq=".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo").">";
									echo "<img  src='img/arquivo_deletar.png'> ";
									echo "</a></div>";
									$valorAtributo++;
								}
							}
							echo "<input type='hidden' id='valorAtributo_$idAtributo' value='$valorAtributo'>";
							$diretorio -> close();
						}else{
							echo "<input type='hidden' name='valorAtributo[]' value='arq_$idAtributo'>";
						}
						
					}elseif($tipo_item=="Seleção de itens"){
						echo "<select name='valorAtributo[]' id='valorAtributo_$idAtributo'>";
						$sqlAtributosValorSub = query("select valor as valorAtributoSub from ordem_servico_atributo_padrao_sub where id_ordem_servico_atributo_padrao='$id'");
						for($j=0; $j<mysqli_num_rows($sqlAtributosValorSub); $j++){
							extract(mysqli_fetch_assoc($sqlAtributosValorSub));
							if($valorAtributo==$valorAtributoSub){
								echo "<option value='$valorAtributoSub' selected='yes'>$valorAtributoSub</option>";
							}else{
								echo "<option value='$valorAtributoSub'>$valorAtributoSub</option>";
							}
						}
						echo "</select>";
					}elseif($tipo_item=="Imagem"){
						$tamanhoMax = "3000000";
						$msg = "Este arquivo tem o tamanho maior que o permitido (3mb).<br>Por favor diminuia o tamanho ou tente um novo arquivo.";
						echo info($msg, "red", null, "none", "arq_".$idAtributo);
						echo "<script type='text/javascript'>\n";
							//função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
							echo "function checarTamanho_$idAtributo(arq){\n";
								//this.files[0].size gets the size of your file.
								echo "if(arq.files[0].size >= $tamanhoMax){\n";
									echo "$('.arq_$idAtributo').show();\n";
									echo "$(arq).attr('value', '');\n";
								echo "}\n";
							echo "}\n";
						echo "</script>\n";
						echo "<input type='file' name='arq[]' id='arq_$idAtributo' onchange=\"checarTamanho_$idAtributo(this)\" accept='image/*' multiple /><br>";
						if($idOrdemServico){
							if(is_dir($caminho)){
								$diretorio = dir($caminho);	
							}else{
								mkdir($caminho, 0777, true);
								chmod($caminho, 0777);
								$diretorio = dir($caminho);
							}
							echo "<input type='hidden' name='valorAtributo[]' value='arq_$idAtributo'>";
							$valorAtributo = 0;
							while($arquivo = $diretorio -> read()){
								if(!is_dir($arquivo)){
									echo "<div class='arquivo'>";
									$link = "<img src=\"".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo\" style=\"height:100px;\">";
									echo "<a title='Baixar arquivo.<br>$link' href='".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo'>";
									echo "<img  src='img/arquivo_baixar.png'> ";
									echo "</a> ";
									echo "<a title='Deletar arquivo.<br>$link' ".pop("deletar.php?arq=".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo").">";
									echo "<img  src='img/arquivo_deletar.png'> ";
									echo "</a></div>";
									$valorAtributo++;
								}
							}
							echo "<input type='hidden' id='valorAtributo_$idAtributo' value='$valorAtributo'>";
							$diretorio -> close();
						}else{
							echo "<input type='hidden' name='valorAtributo[]' value='arq_$idAtributo'>";
						}
						
					}
					echo "</td>";
				echo "</tr>";
			}

			//para usuarios que possão visualizar os valores da ordem de serviço
			if(getCredencialUsuario("cadastrarOrcamento.php") or getCredencialUsuario("cadastrarConta.php")){
				
				echo "<tr>";
					echo "<th scope='col' colspan='3' align='center'>Serviço</th>";
					echo "<th scope='col' align='center' style='width:100px;'>Quantidade</th>";
					echo "<th scope='col' align='center'>Sub Total</th>";
					echo "<th scope='col' align='center'>Total</th>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan='3'>";
						echo "<input type='hidden' name='idServico' value='$id_servico'>";
						echo "<input type='text' name='servico' value='".registro($id_servico, "servico", "nome")."' onkeyup='buscarServico(this.value);' onblur='preencher();' autocomplete='off'>";
						echo "<div class='suggestionsBox' id='sugestoes' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"fechar();\"></span>";
						echo "<div class='suggestionList' id='sugestoesLista'></div></div>";
					echo "</td>";
					$js1 = "calcularTotal();";
					$js2 = "calcularTotal(); calculaPrecoQuantidade();";
					echo "<td  style='width:100px;'><input type='text' name='quantidade' style='width:100px;' value='$quantidade' size='2'  ";
					echo mascara("Valor", null, "onblur='$js2' autocomplete='off'")."></td>";
					echo "<td id='tdSubTotal'><input type='text' name='subTotal' style='width:75%;' value='".real($precoServico)."' class='totalValor preco' ";
					echo mascara("Valor2", null, "autocomplete='off'", $js1, $js1, $js1)."><span id='spanSubTotal'></span></td>";
					$servicoTotal = $quantidade * $precoServico;
					echo "<td><input type='text' name='servicoTotal' style='width:75%;' value='".real($servicoTotal)."' class='inputValor totalValor preco' ";
					echo mascara("Valor2", null, "autocomplete='off'", $js1, $js1, $js1)." readonly='true'></td>";
				echo "</tr>";
				echo "<tr>";
					if(registro($idOrdemServico, 'ordem_servico', 'id_orcamento')==0){
						$tabela_referido = 'ordem_servico';
						$referido = $idOrdemServico;
					}else{
						$tabela_referido = 'orcamento';
						$referido = registro($idOrdemServico, 'ordem_servico', 'id_orcamento');
					}
					$sql= query("select * from conta where referido='$referido' and tabela_referido='$tabela_referido'");
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						if($parcelas){
							$display2= "";
						}
					}else{
						$id = $endidade = $id_referido = $referido = $tipo = $valor = $forma_pagamento = $parcelas = $nota_fiscal = $data = $id_usuario = null;
					}
					echo "<td colspan='5' align='right'>Forma de Pagamento</td>";
					echo "<td colspan='1'><select name='pgaForma' onchange=\"showParcela(this.value);\">".opcaoSelect("pagamento_forma", 1, "Ativo", $forma_pagamento)."</select></td>";
				echo "</tr>";
				echo "<tr>";
					$cod = "";
					$instrucao = "select * from conta_itens where id_conta='$id' and id_conta<>'0'";
					$sql = query($instrucao);
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						
						if($tipo_pagamento_sub){
							$display1= "";
							$sql= query("SELECT * FROM pagamento_tipo_sub WHERE id_pagamento_tipo in (SELECT id_pagamento_tipo FROM pagamento_tipo_sub WHERE id='$tipo_pagamento_sub' and status='Ativo')");
							$linha = mysqli_num_rows($sql);
							$cod .= "<td colspan='5' align='right'>Sub tipo de pagamento</td>";
							$cod .= "<td colspan='1'>";
							$cod .= "<select name='subTipoPagamento'>";
							$cod .= "<option value=''>--</option>";
							for($i=0; $i<$linha; $i++){
								extract(mysqli_fetch_assoc($sql));
								if($id==$tipo_pagamento_sub){
									$cod .= "<option value='$id' selected='yes'>$sub_tipo_pagamento</option>";	
								}else{
									$cod .= "<option value='$id'>$sub_tipo_pagamento</option>";	
								}
								
							}
							$cod .= "</select>";
							$cod .= "</td>";
						}else{
							$display1= "display: none;";
							$display2= "display: none;";
							$cod = "";
						}
						
					}else{
						$id = $id_conta = $tipo_pagamento = $tipo_pagamento_sub = $valor = $data_pagamento = $data_vencimento = $id_usuario = null;
						$display1= "display: none;";
						$display2= "display: none;";
					}
					echo "<td colspan='5' align='right'>Tipo de Pagamento</td>";
					echo "<td colspan='1'><select name='pgaTipo' onchange=\"ajaxTipoPagamentoSub(this.value, '$tipo_pagamento_sub');\">".opcaoSelect("pagamento_tipo", 1, "Ativo", $tipo_pagamento)."</select></td>";
				echo "</tr>";
				
				echo "<tr id='pagamentoTipoSub' style='$display1'>";
					echo $cod;
				echo "</tr>";
				
				echo "<tr id='parcela' style='$display2'>";
					echo "<td colspan='5' align='right'>Parcelas</td>";
					echo "<td colspan='1'>";
						echo "<select name='pgaParcelas' id='parcelaSelect'>";
						echo "<option value=''>--</option>";
						$sql = query("select parcelaMax from pagamento_parcela where id='1'");
						extract(mysqli_fetch_assoc($sql));
						for($i=1;$i<=$parcelaMax;$i++){
							if($i==$parcelas){
								echo "<option value='$i' selected='yes'>$i</option>";
							}else{
								echo "<option value='$i'>$i</option>";
							}
						}
						echo "</select>";
					echo "</td>";
				echo "</tr>";
				
				
			}else{
				
				
				
				//para usuarios que não podem visualizar os valores da ordem de servico
				echo "<tr>";
					echo "<th scope='col' colspan='5' align='center'>Serviço</th>";
					echo "<th scope='col' align='center'>Quantidade</th>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan='5'>";
						echo "<input type='hidden' name='idServico' value='$id_servico'>";
						echo "<input type='text' name='servico' value='".registro($id_servico, "servico", "nome")."' class='inputValor'>";
					echo "</td>";
					echo "<td><input type='text' name='quantidade' value='$quantidade' class='inputValor'></td>";
					echo "<input type='hidden' name='subTotal' value='".real($precoServico)."'>";
					$servicoTotal = $quantidade * $precoServico;
					echo "<input type='hidden' name='servicoTotal' value='".real($servicoTotal)."'>";
				echo "</tr>";
				echo "<tr>";
				$instrucao = "select * from conta where entidade='$busca' and referido='$referido' and tabela_referido='$tabela_referido'";
				$sql= query($instrucao);
				if(mysqli_num_rows($sql)>0){
					extract(mysqli_fetch_assoc($sql));
					$sql = query("select tipo_pagamento_sub from conta_itens where id_conta='$id'");
					extract(mysqli_fetch_assoc($sql));
				}else{
					$id = $endidade = $referido = $tipo = $valor = $forma_pagamento = $parcelas = $nota_fiscal = $data = $id_usuario = null;
				}
				echo "<input type='hidden' name='pgaForma' value='$forma_pagamento'>";
				echo "<input type='hidden' name='pgaTipo' value='$tipo_pagamento_sub'>";
				$sql= query("SELECT * FROM pagamento_tipo_sub WHERE id_pagamento_tipo IN (SELECT id_pagamento_tipo FROM pagamento_tipo_sub WHERE id='$tipo_pagamento_sub' and status='Ativo')");
				if(mysqli_num_rows($sql)>0){
					extract(mysqli_fetch_assoc($sql));
					echo "<input type='hidden' name='subTipoPagamento' value='$id'>";
				}
				echo "<input type='hidden' name='pgaParcelas' value='$parcelas'>";
			}
			
			echo "<tr>";
				echo "<td colspan='4'></td>";
				echo "<td align='right'><input type='submit' class='btnEnviar' value='Enviar'></td>";
				echo "<td><input type='reset' value='Cancelar'></td>";
			echo "</tr>";
		echo "</table>";
	if($idOrdemServico){
		echo "<input type='hidden' name='idOrdemServico' value='$idOrdemServico'>";
	}
	echo "</form>";
}


//datas do tipo Y-m-d ou Y-m-d H:i:s
function subtrairDatas($data_inicio, $data_termino , $modo = "dias"){
	
	if(strripos($data_inicio, " ")!=false and strripos($data_termino, " ")!=false){
		$dataI = explode(" ", $data_inicio);
		$dataF = explode(" ", $data_termino);

		$dataIymd = explode("-", $dataI[0]);
		$dataFymd = explode("-", $dataF[0]);

		$dataIhis = explode(":", $dataI[1]);
		$dataFhis = explode(":", $dataF[1]);
		
		$data_inicial = mktime($dataIhis[0], $dataIhis[1], $dataIhis[2], $dataIymd[1], $dataIymd[2], $dataIymd[0]);
		$data_final = mktime($dataFhis[0], $dataFhis[1], $dataFhis[2], $dataFymd[1], $dataFymd[2], $dataFymd[0]);
	}else{
		$dataI = explode("-", $data_inicio);
		$dataF = explode("-", $data_termino);
		
		$data_inicial = mktime(0, 0, 0, $dataI[1], $dataI[2], $dataI[0]);
		$data_final = mktime(0, 0, 0, $dataF[1], $dataF[2], $dataF[0]);
	}
	if($modo == "seg"){
		$denominador = 0.979166667/*1.935731786*/;
	}elseif($modo == "min"){
		$denominador = 58.75;
	}elseif($modo == "horas"){
		$denominador = 3525;
	}elseif($modo=="dias"){
		$denominador = 84600;
	}elseif($modo=="anos"){
		$denominador = 30794400;
	}
	
	return  floor(($data_final - $data_inicial)/$denominador);
	//return  $data_final." - ".$data_inicial." / ".$denominador;
}


function matriculaStatus($idMatricula){

	$conn = TConnection::open(ALCUNHA);
	
	global $conexao;
	
	//verificando se a matricula ainda é para ficar como ativa
	if($idMatricula==0){
		$instrucao = "select id, data_carencia, data_termino from matricula_plano_assinatura where (status='1' or status='2') and ";
		$instrucao.= "id_matricula=any(select id from matricula where status<>'2')";
	}else{
		$instrucao = "select id, data_carencia, data_termino from matricula_plano_assinatura where ";
		$instrucao .= "id=(select max(id) from matricula_plano_assinatura where id_matricula='$idMatricula' and status='1') and ";
		$instrucao .= "id_matricula=(select id from matricula where status<>'2' and id='$idMatricula')";
	}
	$sqlMatricula = query($instrucao);
	
	for($i=0; $i<mysqli_num_rows($sqlMatricula); $i++){
		extract(mysqli_fetch_assoc($sqlMatricula));
		//condição para realizar a operação
		if($data_carencia!=$data_termino){
			$hoje = date('Y-m-d');
			if(subtrairDatas($hoje, $data_carencia)<0){
				$status = 0;
				$text = "inativou uma matricula";
				$instrucao = "update matricula_plano_assinatura set data_termino='$hoje', status='0' where id='$id'";
				$sql = query($instrucao);
				$idMatricula = registro($id, "matricula_plano_assinatura", "id_matricula");

				if(is_numeric(registro($idMatricula, 'matricula', 'id_cliente'))){

					$remetente = registro(registro($idMatricula, 'matricula', 'id_cliente'), 'cliente_fornecedor', 'email');
					$destinatario = registro(1, 'empresa', 'email');

					$criterio = new TCriteria;
					$criterio->add(new TFilter('id', '=', '2'));//identidade do registro de enviar email para termino de pausa de matricula
					$criterio->add(new TFilter('status', '=', '1'));//saber se o envio deste email está ativo
					$sql= new TSqlSelect;
					$sql->setEntity('email');
					$sql->addColumn('*');
					$sql->setCriteria($criterio);
					$result = $conn->query($sql->getInstruction());
					if($result->rowCount()){

						$arrayVariaveis["nomeEmpresa"] 		= registro(1, 'empresa', 'nome');
						$arrayVariaveis["idMatricula"] 		= $idMatricula;
						$arrayVariaveis["clienteNome"]		= registro(registro($idMatricula, 'matricula', 'id_cliente'), 'cliente_fornecedor', 'nome');
						$arrayVariaveis["urlSistema"] 		= $_SERVER['HTTP_HOST'];
						$arrayVariaveis["imgsrc"] 			= registro(1, 'empresa', 'imgsrc');
						$arrayVariaveis["fone1"] 			= registro(1, 'empresa', 'fone1');
						$arrayVariaveis["fone2"] 			= registro(1, 'empresa', 'fone2');
						$arrayVariaveis["fone3"] 			= registro(1, 'empresa', 'fone3');
						$arrayVariaveis["razaoSocial"] 		= registro(1, 'empresa', 'razao_social');
						$arrayVariaveis["cnpj"] 			= registro(1, 'empresa', 'cnpj');
						$arrayVariaveis["endereco"] 		= registro(1, 'empresa', 'endereco')." ".registro(1, 'empresa', 'numero');
						$arrayVariaveis["endereco"] 	   .= registro(1, 'empresa', 'complemento')." ".registro(1, 'empresa', 'bairro');
						$arrayVariaveis["endereco"] 	   .= registro(registro(1, 'empresa', 'cidade'), 'cidades' , 'nome', 'cod_cidades') ." - ".registro(registro(1, 'empresa', 'bairro'), 'estados', 'sigla', 'cod_estados');
						$arrayVariaveis["nomeUsuario"]		= getNomeCookieLogin($_COOKIE['login'], false);
						$arrayVariaveis["emailUsuario"]		= registro(getIdCookieLogin($_COOKIE['login']), 'usuario', 'email');

						$email = new enviar_email($remetente, $destinatario,null, null);
						$email->setEmail('3', $arrayVariaveis);
						$email->enviarEmail();
						//echo $email->getAssunto()."<br>".$email->getCorpo();
						sleep(0.5);
					}
				}
			}else{
				$status = 1;
				$text = "ativou uma matricula";
			}
		
			$instrucao = "update matricula set status='$status' where id='$idMatricula'";
			$sql = query($instrucao);
			
			if(mysqli_affected_rows($conexao)>0){
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "O sistema automaticamente $text enquanto o usuario ".getNomeCookieLogin($_COOKIE["login"], false)." estava logado.";
				$tabela_afetada = "matricula";
				$chave_principal = $idMatricula;
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			}
		}
	}

	//verificando matriculas em pausa que precisão ser postas em play novamente
	if($idMatricula==0){
		$instrucao = "select id as idMatriculaPlanoAssinatura from matricula_plano_assinatura where status='1' and ";
		$instrucao .= "id_matricula=any(select id from matricula where status='2')";
	}else{
		$instrucao = "select id as idMatriculaPlanoAssinatura from matricula_plano_assinatura where ";
		$instrucao .= "id=(select max(id) from matricula_plano_assinatura where id_matricula='$idMatricula' and status='1') and ";
		$instrucao .= "id_matricula=(select id from matricula where status='2' and id='$idMatricula')";
	}
	$sqlMatricula = query($instrucao);
	for($i=0; $i<mysqli_num_rows($sqlMatricula); $i++){
		extract(mysqli_fetch_assoc($sqlMatricula));
		$sql = query("select id_matricula_plano_assinatura, data_termino from matricula_pausa where id_matricula_plano_assinatura='$idMatriculaPlanoAssinatura' order by id desc");
		if(mysqli_num_rows($sql)){
			extract(mysqli_fetch_assoc($sql));
			if(subtrairDatas(date('Y-m-d'), $data_termino)<0){
				$idMatricula = registro($id_matricula_plano_assinatura, "matricula_plano_assinatura", "id_matricula");
				$instrucao = "update matricula set status='1' where id='".$idMatricula."'";
				$sql = query($instrucao);
				if(is_numeric(registro($idMatricula, 'matricula', 'id_cliente'))){

					$remetente = registro(registro($idMatricula, 'matricula', 'id_cliente'), 'cliente_fornecedor', 'email');
					$destinatario = registro(1, 'empresa', 'email');

					$criterio = new TCriteria;
					$criterio->add(new TFilter('id', '=', '2'));//identidade do registro de enviar email para termino de pausa de matricula
					$criterio->add(new TFilter('status', '=', '1'));//saber se o envio deste email está ativo
					$sql= new TSqlSelect;
					$sql->setEntity('email');
					$sql->addColumn('*');
					$sql->setCriteria($criterio);
					$result = $conn->query($sql->getInstruction());
					if($result->rowCount()){

						$arrayVariaveis["nomeEmpresa"] 		= registro(1, 'empresa', 'nome');
						$arrayVariaveis["idMatricula"] 		= $idMatricula;
						$arrayVariaveis["clienteNome"]		= registro(registro($idMatricula, 'matricula', 'id_cliente'), 'cliente_fornecedor', 'nome');
						$arrayVariaveis["urlSistema"] 		= $_SERVER['HTTP_HOST'];
						$arrayVariaveis["imgsrc"] 			= registro(1, 'empresa', 'imgsrc');
						$arrayVariaveis["fone1"] 			= registro(1, 'empresa', 'fone1');
						$arrayVariaveis["fone2"] 			= registro(1, 'empresa', 'fone2');
						$arrayVariaveis["fone3"] 			= registro(1, 'empresa', 'fone3');
						$arrayVariaveis["razaoSocial"] 		= registro(1, 'empresa', 'razao_social');
						$arrayVariaveis["cnpj"] 			= registro(1, 'empresa', 'cnpj');
						$arrayVariaveis["endereco"] 		= registro(1, 'empresa', 'endereco')." ".registro(1, 'empresa', 'numero');
						$arrayVariaveis["endereco"] 	   .= registro(1, 'empresa', 'complemento')." ".registro(1, 'empresa', 'bairro');
						$arrayVariaveis["endereco"] 	   .= registro(registro(1, 'empresa', 'cidade'), 'cidades' , 'nome', 'cod_cidades') ." - ".registro(registro(1, 'empresa', 'bairro'), 'estados', 'sigla', 'cod_estados');
						$arrayVariaveis["nomeUsuario"]		= getNomeCookieLogin($_COOKIE['login'], false);
						$arrayVariaveis["emailUsuario"]		= registro(getIdCookieLogin($_COOKIE['login']), 'usuario', 'email');

						$email = new enviar_email($remetente, $destinatario,null, null);
						$email->setEmail('2', $arrayVariaveis);
						$email->enviarEmail();
						//echo $email->getAssunto()."<br>".$email->getCorpo();
						sleep(0.5);
					}
				}

				
				if(mysqli_affected_rows($conexao)>0){
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "O sistema automaticamente Ativou uma matricula enquanto o usuario ".getNomeCookieLogin($_COOKIE["login"], false)." estava logado.";
					$tabela_afetada = "matricula";
					$chave_principal = $idMatricula;
					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				}
				
			}
		}
	}
	
	
}

function formVisualizar($action, $id, $nameId = "id" ,$nameOp = "op"){
	$cod = "<form method='get' style='display:inline-block;' action='$action' enctype='multipart/form-data'>";
	$cod .= "<input type='hidden' name='$nameOp' value='visualizar'>";
	$cod .= "<input type='hidden' name='$nameId' value='".base64_encode($id)."'>";
	$cod .= "<input type='submit' value='$id'>";
	$cod .= "</form>";
	return $cod;
}

function arqVisualizar($classCSS, $msg, $mod = 0){
	if($mod == 0){
		$class = "";
		$img1 = "arq_hide";
		$img2 = "arq_show";
	}else{
		$img1 = $img2 = "obs";
		$class = "class='imgHelp'";
	}
	$cod = "<script type='text/javascript'>";
	$cod .= "function historico".$classCSS."(){";
		$cod .= "hist = $(\"input[name='historicoVisualizacao".$classCSS."']\").val();";
		$cod .= "if(hist==\"0\"){";
			$cod .= "$(\"input[name='historicoVisualizacao".$classCSS."']\").val(\"1\");";
			$cod .= "$(\".".$classCSS."\").show(1000);";
			$cod .= "$(\"#img".$classCSS."\").attr('src', 'img/$img1.png');";
		$cod .= "}else{";
			$cod .= "$(\"input[name='historicoVisualizacao".$classCSS."']\").val(\"0\");";
			$cod .= "$(\".".$classCSS."\").hide(1000);";
			$cod .= "$(\"#img".$classCSS."\").attr('src', 'img/$img2.png');";
		$cod .= "}";
	$cod .= "}";
	$cod .= "</script>";
	$cod .= "<input type='hidden' name='historicoVisualizacao".$classCSS."' value='0'>";
	$cod .= "<a href='#".$classCSS."' title='$msg' onclick='historico".$classCSS."();'><img id='img".$classCSS."' src='img/$img2.png' $class></a>";
	return $cod;
}

function validaData($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    while($d && $d->format($format) != $date){
    	if($format=='Y-m-d' or $format=='Y-m-d H:i:s'){
    		$Data = explode('-', $date);
    		if($format=='Y-m-d H:i:s'){
    			$DATA = explode(' ', $Data[2]);
    			if($DATA[0]==1 or $Data[1]>12){
    				$Data[1]--;
    				$DATA[0]=31;
    			}else{
    				$DATA[0]--;
    			}
    			$Data[2] = $Data[2].' '.$DATA[1];
    		}else{
    			if($Data[2]==1 or $Data[1]>12){
    				$Data[1]--;
    				$Data[2]=31;
    			}else{
    				$Data[2]--;
    			}
    		}
    		$date = $Data[0].'-'.$Data[1].'-'.$Data[2];
    	}elseif($format=='d/m/Y' or $format=='d/m/Y H:i:s'){
    		$Data = explode('/', $date);
    		if($Data[0]==1 or $Data[1]>12){
				$Data[1]--;
				$Data[0]=31;
			}else{
				$Data[0]--;
			}
    		$date = $Data[0].'/'.$Data[1].'/'.$Data[2];
    	}
    	
    	$d = DateTime::createFromFormat($format, $date);
    }
    return $date;
}

function checkbox($nome, $valor, $onclick = null){
	$NOME = $nome;
	if(strstr($nome, '[]')){
		$nome = str_replace('[]', '', $nome).rand(1,99999);
	}
	echo "<script type='text/javascript'>\n";
		echo "function muda$nome(valor){\n";
			echo "if(valor=='0'){\n";
				echo "$(\"#checkboxDiv$nome\").attr('checked', 'yes');\n";
				echo "$(\"#checkboxDiv$nome\").val('1')\n";
			echo "}else if(valor=='1'){\n";
				echo "$(\"#checkboxDiv$nome\").removeAttr('checked');\n";
				echo "$(\"#checkboxDiv$nome\").val('0');\n";
			echo "}else if(valor!='1' && valor!='0'){\n";
				echo "if($(\"#checkboxDiv$nome\").attr('checked')==undefined){\n";
					echo "$(\"#checkboxDiv$nome\").attr('checked', 'yes');\n";
				echo "}else if($(\"#checkboxDiv$nome\").attr('checked')=='checked'){\n";
					echo "$(\"#checkboxDiv$nome\").removeAttr('checked');\n";
					echo "$(\"#checkboxDiv$nome\").val('0');\n";
				echo "}\n";
			echo "}\n";
		echo "}\n";
	echo "</script>\n";
	echo "<div class='checkboxDiv'>";
	if($valor){
		$check = "checked='yes'";
	}else{
		$check = null;
	}
		echo "<input name='$NOME' type='checkbox' value='$valor' $check id='checkboxDiv$nome' onclick='muda$nome(this.value); $onclick'>";
		echo "<label for='checkboxDiv$nome'></label>";
	echo "</div>";
}


//funcao para criar 3 inputs (estado, cidade e cep) com o ajax ja configurado
function inputECC($nomeEstado, $nomeCidade, $nomeCep, $valorEstado = null, $valorCidade = null, $valorCep = null, $estilo = 'div'){

	$conn = TConnection::open('gestor');	//conexao com o banco de dados apenas para selecao
	$url = "inc/ajax_inputECC.inc.php";
	$urlimg = "img/loading.gif";
	while(!file_exists($url)){
		$url = '../'.$url;
		$urlimg = '../'.$urlimg;
	}
	$script = "<script type='text/javascript'>";
		$script .= "function mudaECC(valor, op, nomeInput, nomeInputCep){";
			$script .= "if(op=='cidade'){";
				$script .= "$(\"#".$nomeCidade."\").html(\"<img width='30' src='$urlimg'>\");";
			$script .= "}else{";
				$script .= "$(\"#".$nomeCep."\").html(\"<img width='30' src='$urlimg'>\");";
			$script .= "}";
			$script .= "$.post(\"$url\", {";
				$script .= "valor : \"\" + valor + \"\",";
				$script .= "op : \"\" + op + \"\",";
				$script .= "nomeInput : \"\" + nomeInput + \"\",";
				$script .= "nomeInputCep : \"\" + nomeInputCep + \"\"";
			$script .= "}, function(data) {";
				$script .= "if (data.length > 0) {";
					$script .= "if(op==\"cidade\"){";
						$script .= "$(\"#".$nomeCidade."\").html(data);";
					$script .= "}else if(op==\"cep\"){";
						$script .= "$(\"#".$nomeCep."\").html(data);";
					$script .= "}";
				$script .= "}";
			$script .= "});";
		$script .= "}";
	$script .= "</script>";
	
	if($estilo == 'div'){
		$style = "style='display:inline-block; width:33%;'";
		$script .= "<div $style>Estado</div>";
		$script .= "<div $style>Cidade</div>";
		$script .= "<div $style>CEP</div>";
		$script .= "<br>";
		$script .= "<div $style>";
		$script .= "<select name='$nomeEstado' onchange=\"mudaECC(this.value, 'cidade', '$nomeCidade', '$nomeCep')\">";
		
		$criteria = new TCriteria;
		$criteria->add(new TFilter("cod_estados", ">=", "0"));
		$criteria->setProperty('order', 'nome');
		
		$sql = new TSqlSelect;
		$sql->setEntity('estados');
		$sql->addColumn('*');
		$sql->setCriteria($criteria);
		
		$result = $conn->query($sql->getInstruction());
		if($result){
			!$valorEstado ? $valorEstado = 6: false;// 6 igual a ceará
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				if($cod_estados==$valorEstado){
					$cod_estados_selecionado = $cod_estados;
					$script .= "<option value='$cod_estados' selected='yes'>$nome - $sigla</option>";
				}else{
					$script .= "<option value='$cod_estados'>$nome - $sigla</option>";
				}
			}
		}
		$script .= "</select>";
		$script .= "</div>";
		
		$script .= "<div $style id='$nomeCidade'>";
		$script .= "<select name='$nomeCidade' onchange=\"mudaECC(this.value, 'cep', '$nomeCep', 'null')\">";
		
		$criteria = new TCriteria;
		$criteria->add(new TFilter("estados_cod_estados", ">=", "0"));
		$criteria->setProperty('order', 'nome');
		
		$sql = new TSqlSelect;
		$sql->setEntity('cidades');
		$sql->addColumn('*');
		$sql->setCriteria($criteria);
		
		$result = $conn->query($sql->getInstruction());
		if($result){
			!$valorCidade ? $valorCidade = 1722: false;// 6 igual a ceará
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				if($estados_cod_estados == $cod_estados_selecionado){
					if($cod_cidades==$valorCidade){
						$script .= "<option value='$cod_cidades' selected='yes'>$nome</option>";
					}else{
						$script .= "<option value='$cod_cidades'>$nome</option>";
					}
				}
			}
		}
		$script .= "</select>";
		$script .= "</div>";
		$script .= "<div $style id='$nomeCep'><input type='text' name='$nomeCep' value='$valorCep' ".mascara("Cep", 9)."></div>";
		
	}else{
		$script .= "<label for='$nomeEstado'>Estado</label>";
		$script .= "<select name='$nomeEstado' id='$nomeEstado' onchange=\"mudaECC(this.value, 'cidade', '$nomeCidade', '$nomeCep')\">";
		$criteria = new TCriteria;
		$criteria->add(new TFilter("cod_estados", ">=", "0"));
		$criteria->setProperty('order', 'nome');
		$sql = new TSqlSelect;
		$sql->setEntity('estados');
		$sql->addColumn('*');
		$sql->setCriteria($criteria);
		$result = $conn->query($sql->getInstruction());
		if($result){
			!$valorEstado ? $valorEstado = 6: false;// 6 igual a ceará
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				if($cod_estados==$valorEstado){
					$cod_estados_selecionado = $cod_estados;
					$script .= "<option value='$cod_estados' selected='yes'>$nome - $sigla</option>";
				}else{
					$script .= "<option value='$cod_estados'>$nome - $sigla</option>";
				}
			}
		}
		$script .= "</select>";
		
		$script .= "<label for='$nomeCidade'>Cidade</label>";
		$script .= "<span id='$nomeCidade'>";
		$script .= "<select name='$nomeCidade' onchange=\"mudaECC(this.value, 'cep', '$nomeCep', 'null')\">";
		$criteria = new TCriteria;
		$criteria->add(new TFilter("estados_cod_estados", ">=", "0"));
		$criteria->setProperty('order', 'nome');
		$sql = new TSqlSelect;
		$sql->setEntity('cidades');
		$sql->addColumn('*');
		$sql->setCriteria($criteria);
		$result = $conn->query($sql->getInstruction());
		if($result){
			!$valorCidade ? $valorCidade = 1722: false;// 6 igual a ceará
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				if($estados_cod_estados == $cod_estados_selecionado){
					if($cod_cidades==$valorCidade){
						$script .= "<option value='$cod_cidades' selected='yes'>$nome</option>";
					}else{
						$script .= "<option value='$cod_cidades'>$nome</option>";
					}
				}
			}
		}
		$script .= "</select>";
		$script .= "</span>";
		
		$script .= "<label for='$nomeCep'>CEP</label>";
		$script .= "<span id='$nomeCep'><input type='text' name='$nomeCep' value='$valorCep' ".mascara("Cep", 9)."></span>";
	}
	
	return $script;
}

?>