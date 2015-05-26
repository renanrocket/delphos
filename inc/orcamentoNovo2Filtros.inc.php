<?php

function validacaoToken(){
	
	global $cod, $cont, $array, $_POST, $op2;
	
	echo "<form method='post' action='cadastrarOrcamento2.php' enctype='multipart/form-data' style='display:inline;'>";
	for ($i=0; $i<$cont; $i++){
		if(is_array($_POST[$array[$i]])){
			$cont2= count($_POST[$array[$i]]);
			for($j=0; $j<$cont2;$j++){
				echo "<input type='hidden' name='". $array[$i]."[]' value='".$_POST[$array[$i]][$j]."'>";
			}
		}else{
			echo "<input type='hidden' name='". $array[$i]."' value='".$_POST[$array[$i]]."'>";
		}
	}
	echo "<input type='hidden' name='op2' value='$op2'>";
	echo $cod;
	echo "Para validar o orçamento insira um token<br>";
	echo "<input type='password' name='token'>";
	echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
}
if(!isset($op2)){
	$op2=0;
}
//marcando o inicio dos produtos
if($tipoPessoa=="f"){
	$marcador = 19;
}else{
	$marcador = 20;
}
$info = "";
$cor = "green";
$validaToken = false;//irá dizer se precisará de token ou não
$cod = "";
$msg = "";

//verificar se os precos dos produtos estao abaixo do desconto maximo e pedir token
for($j=0; $j<$qtdProd;$j++){//percorre a matriz do id do produto e do subTotal do produto
	if(is_array($_POST[$array[$marcador]])){//no caso do orçamento ter mais de um produto
		if($_POST[$array[$marcador]][$j]){//verifica se o produto é um produto cadastrado
			$_POST[$array[$marcador+3]][$j] = str_replace(",", ".", $_POST[$array[$marcador+3]][$j]);//troca a virgula por ponto do subTotal
			if(precoProduto($_POST[$array[$marcador]][$j], false, true)>$_POST[$array[$marcador+3]][$j]){//verifica se o maximoDescont > subTotal
				$validaToken = true;
				$cod .= "O produto <b>".$_POST[$array[$marcador+1]][$j]."</b> está com valor ";
				$cod .= "(R$".real($_POST[$array[$marcador+3]][$j], true).") abaixo do permitido (R$".real(precoProduto($_POST[$array[$marcador]][$j], false, true)).")<br>";
			}
		}
	}else{//no caso do orcamento ter apenas um produto
		$_POST[$array[$marcador+3]] = str_replace(",", ".", $_POST[$array[$marcador+3]]);//troca a virgula por ponto do subTotal
		if(precoProduto($_POST[$array[$marcador]], false, true)>$_POST[$array[$marcador+3]]){//verifica se o maximoDescont > subTotal
			$validaToken = true;
			$cod .= "O produto <b>".$_POST[$array[$marcador+1]]."</b> está com valor ";
			$cod .= "(R$".real($_POST[$array[$marcador+3]], true).") abaixo do permitido (R$".real(precoProduto($_POST[$array[$marcador]], false, true)).")<br>";
		}
	}
}
	
//verificar se a forma de pagamento eh a vista para clientes não cadastrados e pedir token
if(!$id_cliente and $op2==0 and $pgaForma!=1){
	$validaToken = true;
	$cod .= "Cliente $contato não é cadastrado e a forma de pagamento está como ".registro($pgaForma, "pagamento_forma", "forma_pagamento");
	$cod .= "<br>Para clientes não cadastrados aceitamos apenas pagamentos a Vista.";
}

if(($validaToken and !isset($token))){//precisa de token
	//pergunta se realmente quer cadastrar o cliente
	echo validacaoToken();
	
}else{// nao precisa de token ou token existe
	if(isset($token)){
		$hoje =  date('Y-m-d H:i:s');
		$instrucao = "select * from tokens where token='".md5($token)."' and data_validade>='$hoje' and vezes_permitido > any (select vezes_usado from tokens where token='".md5($token)."' and data_validade>='$hoje')";
		$sqlToken = query($instrucao);
		$linhaToken = mysqli_num_rows($sqlToken);
		if($linhaToken>0){
			$regToken = mysqli_fetch_assoc($sqlToken);
			$regToken["vezes_usado"]++;
			$sqlToken = query("update tokens set vezes_usado='".$regToken["vezes_usado"]."' where token='".md5($token)."'");
			$validaToken = false;
			
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Usou um token";
			$tabela_afetada = "tokens";
			$chave_principal = $regToken["id"];
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
		}else{
			echo validacaoToken();
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Tentou usar um token";
			$tabela_afetada = NULL;
			$chave_principal = NULL;
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		}
	}
	//token foi validado e dito q não precisa de token (validaToken = false)
	if(!$validaToken){
		
		//os dados foram confirmados do cadastro do cliente e o usuario deseja cadastrar o cliente
		//cadastrando cliente
		if(!isset($razao_social)){
			$razao_social = "NULL";
		}
		$data = formataDataInv($data);
		if($op2==1){
			
			$sql = query("select * from cliente_fornecedor where cpf_cnpj='$doc1'");
			if(mysqli_num_rows($sql)>0){
				$info .= "Já existe um cliente / fornecedor com esse CNPJ / CPF<br>";
				$cor = "red";
			}else{
				$instrucao = "insert into cliente_fornecedor ";
				$instrucao .= "(tipo, nome, razao_social, cpf_cnpj, rg_ie, data_nascimento, email, fone1, fone2, endereco, numero, ";
				$instrucao .= " bairro, cidade, estado, cep) ";
				$instrucao .= "values ";
				$instrucao .= "('$tipoPessoa','$contato','$razao_social','$doc1','$doc2','$data','$email','$telefone1','$telefone2','$endereco',";
				$instrucao .= "'$numero','$bairro','$cidades','$estados','$cep')";
				
				$sql = query($instrucao);
				
				$chave_principal = $id_cliente = mysqli_insert_id();
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cadastrou um cliente.";
				$tabela_afetada = "cliente_fornecedor";
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			}
			
		}elseif($op2==0 and $id_cliente){
			
			$instrucao = "update cliente_fornecedor set ";
			$instrucao .= "tipo='$tipoPessoa', nome='$cliente', razao_social='$razao_social', cpf_cnpj='$doc1', rg_ie='$doc2', data_nascimento='$data', ";
			$instrucao .= "email='$email', fone1='$telefone1', fone2='$telefone2', endereco='$endereco', numero='$numero', ";
			$instrucao .= "bairro='$bairro', cidade='$cidades', estado='$estados', cep='$cep' where id='$id_cliente'";
			
			$sql = query($instrucao);
			
			if(mysqli_affected_rows($conexao)>0){
				$info .= "Cliente / Fornecedor $id_cliente - $cliente editado com sucesso.<br>";
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cadastrou um cliente.";
				$tabela_afetada = "cliente_fornecedor";
				$chave_principal = $id_cliente;
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			}
			
			
		}
		//id do cliente nulo caso n existe cliente cadastrado para o orcamento ou retornar o id do cliente se caso o orcamento tiver cliente cadastrado
		if(mysqli_insert_id()!=0){
			$idCliente = $id_cliente;
		}else{
			$idCliente = turnZero($id_cliente);
		}
		
		//capturando a id do usuario
		$instrucao = "select id from usuario where login='".$_COOKIE["login"]."'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		if($op=="novo"){
			
            $idCliente = turnZero($idCliente);
            
			//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
			//cadastrando orcamento
			$instrucao = "insert into orcamento ";
			$instrucao .= "(id_cliente, cliente, fone, id_usuario, data_emissao) ";
			$instrucao .= "values ";
			$instrucao .= "('$idCliente','$contato','$telefoneC','$id','".date('Y-m-d H:i:s')."');";
			
			$sql = query($instrucao);
			
			$idOrcamento = mysqli_insert_id();
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cadastrou um orçamaento";
			$tabela_afetada = "orcamento";
			$chave_principal = mysqli_insert_id();
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			$valor = "";
			
			//cadastrando itens do orcamento
			$instrucao = "insert into orcamento_itens ";
			$instrucao .= "(id_orcamento, id_produto, quantidade, valor_produto) ";
			$instrucao .= "values ";
			if(is_array($_POST[$array[$marcador]])){//no caso do orçamento ter mais de um produto
				for($j=0; $j<$qtdProd;$j++){//percorre a matriz do id do produto e do subTotal do produto
					if(!($_POST[$array[$marcador]][$j])){//verifica se o produto é um produto cadastrado, caso não seja entao irá inserir o campo nome do produto
						$_POST[$array[$marcador]][$j] = $_POST[$array[$marcador+1]][$j];
					}
					if($j>0 and $_POST[$array[$marcador]][$j]){
						$instrucao.= ", ";
					}
					if($_POST[$array[$marcador]][$j]){//para n inserir um item contendo nada dentro
						$_POST[$array[$marcador+3]][$j] = str_replace(",", ".", $_POST[$array[$marcador+3]][$j]);//troca a virgula por ponto do subTotal
						$instrucao .= "('$idOrcamento','".$_POST[$array[$marcador]][$j]."','".$_POST[$array[$marcador+2]][$j]."','".$_POST[$array[$marcador+3]][$j]."')";	
						//somando o valor total
						$valor += $_POST[$array[$marcador+2]][$j] * $_POST[$array[$marcador+3]][$j];
					}
				}
			}else{//no caso do orcamento ter apenas um produto
				if(!($_POST[$array[$marcador]])){//verifica se o produto é um produto cadastrado, caso não seja entao irá inserir o campo nome do produto
					$_POST[$array[$marcador]] = $_POST[$array[$marcador+1]];
				}
				if($_POST[$array[$marcador]]){//para n inserir um produto que não tenha nada nele
					$_POST[$array[$marcador+3]] = str_replace(",", ".", $_POST[$array[$marcador+3]]);//troca a virgula por ponto do subTotal
					$instrucao .= "('$idOrcamento','".$_POST[$array[$marcador]]."','".$_POST[$array[$marcador+2]]."','".$_POST[$array[$marcador+3]]."');";
					$valor += $_POST[$array[$marcador+2]] * $_POST[$array[$marcador+3]];
				}
			}
			$sql = query($instrucao);
			
			if(!$pgaParcelas){
				$pgaParcelas = 1;
			}
			//se o cliente não foi cadastrado e n eh um orcamento de cliente pre-cadastrado entao inserir a entidade referente na conta com o nome do contato do orcamento
			if(!$id_cliente and $idCliente==0){
				$idCliente = $contato;
			}
			//abrindo conta
			$instrucao = "insert into conta ";
			$instrucao .= "(entidade_tabela, entidade, referido, valor, forma_pagamento, parcelas, data, id_usuario) ";
			$instrucao .= "values ";
			$instrucao .= "('cliente_fornecedor', '$idCliente','$idOrcamento','$valor','$pgaForma','$pgaParcelas','".date('Y-m-d H:i:s')."','".getIdCookieLogin($_COOKIE["login"])."');";
			
			$sql = query($instrucao);
			
			$idConta = mysqli_insert_id();
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cadastrou uma conta.";
			$tabela_afetada = "conta";
			$chave_principal = mysqli_insert_id();
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			
			//abrindo log de conta
			$instrucao = "insert into conta_itens";
			$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, valor, data_pagamento, data_vencimento, id_usuario) ";
			$instrucao .= "values ";
			$i = 0;
			//fazer o restante da insercao com uma rotina de repetição
			if(!isset($subTipoPagamento)){
				$subTipoPagamento = "0";
			}
			do {
				//setando a data de vencimento de acordo com as parcelas
				$ano = date('Y');
				$mes = date('m');
				$dia = date('d');
				//ajustando o ano e o mes caso o mes passe de 12
				$mes = $mes + $i;
				if ($mes > 12) {
					$ano++;
					$mes = $mes - 12;
				}
				$vencimento = $ano . "-" . $mes . "-" . $dia;
				if($i!=0){
					$instrucao .= ", ";
				}
				$instrucao .= "('$idConta','$pgaTipo','$subTipoPagamento', NULL, NULL,'$vencimento','".getIdCookieLogin($_COOKIE["login"])."')";
			
			$i++;
			} while($i<$pgaParcelas);
			
		$sql = query($instrucao);
		$info .= "Orçamento cadastrado com sucesso.";
		
		}elseif($op=="editar"){
		
			//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
			//cadastrando orcamento
			$instrucao = "update orcamento set ";
			$instrucao .= "id_cliente='$idCliente', cliente='$contato', fone='$telefoneC', ";
			$instrucao .= "id_usuario='$id', data_emissao='".date('Y-m-d H:i:s')."' where id='$idOrcamento'";
			
			$sql = query($instrucao);
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Editou um orçamento.";
			$tabela_afetada = "orcamento";
			$chave_principal = $idOrcamento;
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			$valor = "";
			
			//deletando todos os itens do orcamento e inserindo novamente
			$sql = query("delete from orcamento_itens where id_orcamento='$idOrcamento'");
			//cadastrando itens do orcamento
			$instrucao = "insert into orcamento_itens ";
			$instrucao .= "(id_orcamento, id_produto, quantidade, valor_produto) ";
			$instrucao .= "VALUES ";
			if(is_array($_POST[$array[$marcador]])){//no caso do orçamento ter mais de um produto
				for($j=0; $j<$qtdProd;$j++){//percorre a matriz do id do produto e do subTotal do produto
					if(!($_POST[$array[$marcador]][$j])){//verifica se o produto é um produto cadastrado, caso não seja entao irá inserir o campo nome do produto
						$_POST[$array[$marcador]][$j] = $_POST[$array[$marcador+1]][$j];
					}
					if($j>0 and $_POST[$array[$marcador]][$j]){
						$instrucao.= ", ";
					}
					if($_POST[$array[$marcador]][$j]){//para n inserir um item contendo nada dentro
						$_POST[$array[$marcador+3]][$j] = str_replace(",", ".", $_POST[$array[$marcador+3]][$j]);//troca a virgula por ponto do subTotal
						$instrucao .= "('$idOrcamento','".$_POST[$array[$marcador]][$j]."','".$_POST[$array[$marcador+2]][$j]."','".$_POST[$array[$marcador+3]][$j]."')";	
						//somando o valor total
						$valor += $_POST[$array[$marcador+2]][$j] * $_POST[$array[$marcador+3]][$j];
					}
				}
			}else{//no caso do orcamento ter apenas um produto
				if(!($_POST[$array[$marcador]])){//verifica se o produto é um produto cadastrado, caso não seja entao irá inserir o campo nome do produto
					$_POST[$array[$marcador]] = $_POST[$array[$marcador+1]];
				}
				if($_POST[$array[$marcador]]){//para n inserir um produto que não tenha nada nele
					$_POST[$array[$marcador+3]] = str_replace(",", ".", $_POST[$array[$marcador+3]]);//troca a virgula por ponto do subTotal
					$instrucao .= "('$idOrcamento','".$_POST[$array[$marcador]]."','".$_POST[$array[$marcador+2]]."','".$_POST[$array[$marcador+3]]."');";
					$valor += $_POST[$array[$marcador+2]] * $_POST[$array[$marcador+3]];
				}
			}
			
			$sql = query($instrucao);
			
			
			if(!$pgaParcelas){
				$pgaParcelas = 1;
			}
			if($idCliente==0){
				$idCliente = $contato;
			}
			//editando conta
			$instrucao = "update conta set ";
			$instrucao .= "entidade='$idCliente', valor='$valor', forma_pagamento='$pgaForma', ";
			//$instrucao .= "parcelas='$pgaParcelas', data='".date('Y-m-d H:i:s')."', id_usuario='$id' ";
			$instrucao .= "parcelas='$pgaParcelas', id_usuario='$id' ";
			$instrucao .= "where referido='$idOrcamento' and tabela_referido='orcamento'";
			
			$sql = query($instrucao);
			
			$idConta = mysqli_fetch_row(query("select id from conta where referido='$idOrcamento' and tabela_referido='orcamento'"));
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Editou a conta.";
			$tabela_afetada = "conta";
			$chave_principal = $idConta[0];
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			//deletando carteira de pontos se necessário
		    //deletando no ponto_log
			$instrucao = "select id as idPonto_log from ponto_log where id_valor=any(select id from conta_itens where id_conta='".$idConta[0]."') and valor_ponto>0";
			$sql = query($instrucao);
			for($i=0; $i<mysqli_num_rows($sql); $i++){
				extract(mysqli_fetch_assoc($sql));
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$data = date('Y-m-d H:i:s');
				$acao = "Deletou a carteira de pontos.";
				$tabela_afetada = "ponto_log";
				$chave_principal = $idPonto_log;
				insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
			}
			$instrucao = "delete from ponto_log where id_valor=any(select id from conta_itens where id_conta='".$idConta[0]."') and valor_ponto>0";
		    $sql = query($instrucao);
			
			//deletando movimento de caixa
			$sql = query("select id_caixa_movimento from conta_itens where id_conta='".$idConta[0]."'");
            for($i=0;$i<mysqli_num_rows($sql);$i++){
                extract(mysqli_fetch_assoc($sql));
                $sqlCaixa = query("delete from caixa_movimento where id='$id_caixa_movimento'");
            }
			//deletando log de contas
			$sql = query("delete from conta_itens where id_conta='".$idConta[0]."'");
			//abrindo log de conta
			$instrucao = "insert into conta_itens";
			$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, valor, data_pagamento, data_vencimento, id_usuario) ";
			$instrucao .= "values ";
			$i = 0;
			//fazer o restante da insercao com uma rotina de repetição
			if(!isset($subTipoPagamento)){
				$subTipoPagamento = "0";
			}
			do {
				//setando a data de vencimento de acordo com as parcelas
				$ano = date('Y');
				$mes = date('m');
				$dia = date('d');
				//ajustando o ano e o mes caso o mes passe de 12
				$mes = $mes + $i;
				if ($mes > 12) {
					$ano++;
					$mes = $mes - 12;
				}
				$vencimento = $ano . "-" . $mes . "-" . $dia;
				if($i!=0){
					$instrucao .= ", ";
				}
				$instrucao .= "('".$idConta[0]."','$pgaTipo','$subTipoPagamento','0','0000-00-00','$vencimento','".getIdCookieLogin($_COOKIE["login"])."')";
			
			$i++;
			} while($i<$pgaParcelas);
			
		$sql = query($instrucao);
		$info .= "Orçamento editado com sucesso.<br>As contas e valores recebidos (se houver) foram resetados.<br>Por favor, se havia algum valor recebido, certifique se foi realmente recebido na<br>conta deste orçamento.";
		}
		
		info($info, $cor);
		
		orcamento($idOrcamento);
	}
}

?>