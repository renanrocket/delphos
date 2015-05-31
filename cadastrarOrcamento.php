<?php
//php
include "templates/upLogin.inc.php";

//all
$op=null;
extract($_GET);
extract($_POST);

$cont = count($_POST);
$array = array_keys($_POST);


if($op=="novo" or $op=="editar"){
	
	empty($contato) ? $contato = $nome : false;
	$tipo2 = "cliente";
	if(!isset($cadastrarCliente)){
		$cadastrarCliente = "false";
	}
	
	function validacaoToken(){
		
		global $cod, $cont, $array, $_POST;
		
		echo "<form style='width:30%;' method='post' action='cadastrarOrcamento.php' enctype='multipart/form-data' style='display:inline;'>";
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
		echo $cod;
		echo "<br>Para validar o orçamento insira um token.<br>";
		echo "<input type='password' name='token'>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
		echo "</form>";
	}
	$info = "";
	$cor = "green";
    $validaToken = false;//irá dizer se precisará de token ou não
	$cod = "";
	$msg = "";
	
	//verificar se os precos dos produtos estao abaixo do desconto maximo e pedir token
	for($j=0; $j<$qtdItem;$j++){//percorre a matriz do id do produto e do subTotal do produto

        $subTotal[$j] = real2($subTotal[$j]);
		if($tabelaItem[$j]=="produto" and is_numeric($idItem[$j]) and precoProduto($idItem[$j], false, true)>$subTotal[$j]){
			$validaToken = true;
			$cod .= "O produto <b>".$item[$j]."</b> está com valor ";
			$cod .= "(R$ ".real($subTotal[$j]).") abaixo do permitido (R$ ".real(precoProduto($idItem[$j], false, true)).")<br>";
		}elseif($tabelaItem[$j]=="servico" and precoServico($idItem[$j], $quantidade[$j])>$subTotal[$j] ){
			$validaToken = true;
			$cod .= "O serviço <b>".$item[$j]."</b> está com valor ";
			$cod .= "(R$ ".real($subTotal[$j]).") abaixo do permitido (R$ ".real(precoServico($idItem[$j], $quantidade[$j])).")<br>";
		}
	}
		
	//verificar se a forma de pagamento eh a vista para clientes não cadastrados e pedir token
	if(!$id_cliente_fornecedor and $pgaForma!=1 and $cadastrarCliente=="false"){
		$validaToken = true;
		$cod .= "Cliente $contato não é cadastrado e a forma de pagamento está como ".registro($pgaForma, "pagamento_forma", "forma_pagamento").".";
		$cod .= "<br>Para clientes não cadastrados aceitamos apenas pagamentos a Vista.";
	}

    //Se o usuário tiver acesso ao token
    if(getCredencialUsuario("administrativoToken.php")){
        $validaToken = false;
    }
	
	if($validaToken and !isset($token)){//precisa de token
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
				$acao = "Usou um token.";
				$tabela_afetada = "tokens";
				$chave_principal = $regToken["id"];
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
			}else{
				$cod = "O token utilizado está inválido.<br>Tente outro.";
				echo validacaoToken();
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Tentou usar um token.";
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
				if($id_cliente_fornecedor){
					$sqlRazao = query("select razao_social from cliente_fornecedor where id='$id_cliente_fornecedor'");
					if(mysqli_num_rows($sqlRazao)){
						extract(mysqli_fetch_assoc($sqlRazao));
					}
				}
			}
			if(!isset($cidade)){
				$cidade = "NULL";
				if($id_cliente_fornecedor){
					$sqlCidade = query("select cidade from cliente_fornecedor where id='$id_cliente_fornecedor'");
					if(mysqli_num_rows($sqlCidade)){
						extract(mysqli_fetch_assoc($sqlCidade));
					}
				}
			}
			if(!isset($cep)){
				$cep = null;
			}
			$cliente_fornecedor = new cliente_fornecedor($id_cliente_fornecedor, $tipo2, $tipo, $nome, $razao_social, $doc1, 
			$doc2, $data, $email, $telefone1, $telefone2, $endereco, $numero, $bairro, $cidade, $estado, $cep, $referencia, 
			$observacoes, $latitude, $longitude, 1);
			
			if($cadastrarCliente=="true"){
				if($cliente_fornecedor->inserir()){
					$info .= "Cliente $cliente_fornecedor->nome cadastrado.<br>";
					$idCliente = ultimaId("cliente_fornecedor");
					$cotato = $nome;
				}
			}elseif($cadastrarCliente=="atualizar"  or is_numeric($id_cliente_fornecedor)){
				if($cliente_fornecedor->update()){
					$info .= "Cliente $cliente_fornecedor->nome atualizado.<br>";
				}
				$idCliente = $id_cliente_fornecedor;
				$cotato = $nome;
			}else{
				//id do cliente nulo caso n existe cliente cadastrado para o orcamento ou retornar o id do cliente se caso o orcamento tiver cliente cadastrado
				$idCliente = turnZero($id_cliente_fornecedor);
				$contato = $nome;
			}
			
			
			
			//capturando a id do usuario
			$instrucao = "select id as idUsuario from usuario where login='".$_COOKIE["login"]."'";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			
			if($op=="novo" and !$validaToken){
				
				//verificando duplicidade de orçamento
				$instrucao = "select id as idOrcamento from orcamento where ";
				$instrucao .= "id_cliente='$idCliente' and cliente='$contato' and fone='$telefoneC' ";
				$instrucao .= "and id_usuario='$idUsuario' and data_emissao>='".date('Y-m-d H:i:s', strtotime("- 30 minutes"))."' ";
				$instrucao .= "and ";
				for($i=0; $i<$qtdItem; $i++){
					is_numeric($idItem[$i]) ? $id_item= $idItem[$i] : $id_item= $item[$i];
					$i!=0 ? $instrucao .= "and ": false;
					$instrucao .= "id=any(select id_orcamento from orcamento_itens where ";
					$instrucao .= "tabela_item='$tabelaItem[$i]' and id_item='$id_item' and quantidade='$quantidade[$i]' and valor_produto='$subTotal[$i]' ";
					$instrucao .= ") ";
				}
				$sqlOrcamentoRepetido = query($instrucao);
				if(mysqli_num_rows($sqlOrcamentoRepetido)>0){
					extract(mysqli_fetch_assoc($sqlOrcamentoRepetido));
					$info .= "Você pode está duplicando este orçamento.";
					info($info);
					orcamento($idOrcamento);
				}else{
					$totalDescontoPor = real2($totalDescontoPor);
                    $totalDescontoReal = real2($totalDescontoReal);
					//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
					//cadastrando orcamento
					$instrucao = "insert into orcamento ";
					$instrucao .= "(id_cliente, cliente, fone, descPor, descReal, observacoes, id_usuario, data_emissao) ";
					$instrucao .= "values ";
					$instrucao .= "('$idCliente','$contato','$telefoneC', '$totalDescontoPor', '$totalDescontoReal', '$observacoesO', '$idUsuario','".date('Y-m-d H:i:s')."');";
					
					$sql = query($instrucao);
					
					$idOrcamento = ultimaId("orcamento");
					
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Cadastrou um novo orçamento.";
					$tabela_afetada = "orcamento";
					$chave_principal = $idOrcamento;
					
					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
					
					//cadastrando itens do orcamento
					$instrucao = "insert into orcamento_itens ";
					$instrucao .= "(id_orcamento, tabela_item, id_item, quantidade, valor_produto, descricao_item) ";
					$instrucao .= "values ";
					for($i=0, $valor = ""; $i<$qtdItem;$i++){
						$i!=0 ? $instrucao.= ", ": false;
						$subTotal[$i] = str_replace(",", ".", $subTotal[$i]);
						//produto cadastrado
						if(is_numeric($idItem[$i])){
							$instrucao .= "('$idOrcamento', '$tabelaItem[$i]', '$idItem[$i]', '$quantidade[$i]', '$subTotal[$i]', '$descItem[$i]')";
						}elseif(!is_numeric($idItem[$i])){
							$instrucao .= "('$idOrcamento', '$tabelaItem[$i]', '$item[$i]', '$quantidade[$i]', '$subTotal[$i]', '$descItem[$i]')";
						}
						$valor += $quantidade[$i] * $subTotal[$i];
					}
                    $valor = $valor - $totalDescontoReal;
					$sql = query($instrucao);
					
					
					$pgaParcelas=="" ? $pgaParcelas = 1 : false;
	
					//se o cliente não foi cadastrado e n eh um orcamento de cliente pre-cadastrado entao inserir a entidade referente na conta com o nome do contato do orcamento
					if(!$id_cliente_fornecedor and $idCliente==0){
						$idCliente = $contato;
					}
					//abrindo conta
					$instrucao = "insert into conta ";
					$instrucao .= "(tabela_entidade, entidade, tabela_referido, referido, valor, forma_pagamento, parcelas, conta_plano, data, id_usuario) ";
					$instrucao .= "values ";
					//conta plano = 2 eh igual a Orçamento
					$instrucao .= "('cliente_fornecedor', '$idCliente', 'orcamento', '$idOrcamento','$valor','$pgaForma','$pgaParcelas', '2','".date('Y-m-d H:i:s')."','".getIdCookieLogin($_COOKIE["login"])."');";
					
					$sql = query($instrucao);
					
					$idConta = ultimaId("conta");
					
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Cadastrou uma nova conta.";
					$tabela_afetada = "conta";
					$chave_principal = $idConta;
					
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

				}
	            
				
			}elseif($op=="editar" and !$validaToken){
                $totalDescontoPor = real2($totalDescontoPor);
                $totalDescontoReal = real2($totalDescontoReal);
				//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
				//editar orcamento
				$instrucao = "update orcamento set ";
				$instrucao .= "id_cliente='$idCliente', cliente='$contato', fone='$telefoneC', observacoes='$observacoesO', descPor='$totalDescontoPor', ";
				$instrucao .= "descReal='$totalDescontoReal', id_usuario='$idUsuario', data_emissao='".date('Y-m-d H:i:s')."' where id='$idOrcamento'";
				
				$sql = query($instrucao);
				
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Alterou um orçamento.";
				$tabela_afetada = "orcamento";
				$chave_principal = $idOrcamento;
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
				//deletando todos os itens do orcamento e inserindo novamente
				$sql = query("delete from orcamento_itens where id_orcamento='$idOrcamento'");
				//cadastrando itens do orcamento
				$instrucao = "insert into orcamento_itens ";
				$instrucao .= "(id_orcamento, tabela_item, id_item, quantidade, valor_produto, descricao_item) ";
				$instrucao .= "values ";
				for($i=0, $valor = ""; $i<$qtdItem;$i++){
					$i!=0 ? $instrucao.= ", ": false;
					$subTotal[$i] = str_replace(",", ".", $subTotal[$i]);
					//produto cadastrado
					if(is_numeric($idItem[$i])){
						$instrucao .= "('$idOrcamento', '$tabelaItem[$i]', '$idItem[$i]', '$quantidade[$i]', '$subTotal[$i]', '$descItem[$i]')";
					}elseif(!is_numeric($idItem[$i])){
						$instrucao .= "('$idOrcamento', '$tabelaItem[$i]', '$item[$i]', '$quantidade[$i]', '$subTotal[$i]', '$descItem[$i]')";
					}
					$valor += $quantidade[$i] * $subTotal[$i];
				}
                $valor = $valor - $totalDescontoReal;
				$sql = query($instrucao);
				$info .= "Orçamento editado com sucesso.";
				
				
				if(!$pgaParcelas){
					$pgaParcelas = 1;
				}
				if($idCliente==0){
					$idCliente = $contato;
				}
				//editando conta
				$instrucao = "update conta set ";
				$instrucao .= "entidade='$idCliente', valor='$valor', forma_pagamento='$pgaForma', ";
				//$instrucao .= "parcelas='$pgaParcelas', data='".date('Y-m-d H:i:s')."', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where referido='$idOrcamento' and tabela_referido='orcamento'";
				$instrucao .= "parcelas='$pgaParcelas', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where referido='$idOrcamento' and tabela_referido='orcamento'";

				$sql = query($instrucao);
				
				$idConta = mysqli_fetch_row(query("select id from conta where tabela_referido='orcamento' and referido='$idOrcamento'"));
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Alterou uma conta.";
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
			$info .= "<br>As contas e valores recebidos (se houver) foram resetados.";
			$info .= "<br><div style='font-weight: bolder; color:#680000;'>";
			$info .= "Por favor, se havia algum valor recebido, certifique se foi realmente recebido na conta deste orçamento.</div>";
			$info .= "<a href='pesquisaConta2.php?conta=".base64_encode($idConta[0])."'><img style='width:30px;' src='img/icones/pesquisaConta.png'> Ir para conta ".$idConta[0]."</a>";
			}
			
			info($info, $cor);
			
			orcamento($idOrcamento);
		}
	}

}elseif($op=="visualizar"){
	
	$id = base64_decode($id);
	orcamento($id);
	
}elseif($op=="incluir"){
	
	$idOrcamento = base64_decode($id);
    
    //incluindo as contas
    $instrucao = "update conta set status='1' where (entidade=(select id_cliente from orcamento where id='$idOrcamento') or entidade=(select cliente from orcamento where id='$idOrcamento')) ";
    $instrucao .= "and tabela_referido='orcamento' and referido='$idOrcamento'";
    $sql = query($instrucao);
	
	
    
    //incluindo o orcamento
    $instrucao = "update orcamento set status='1' where id='$idOrcamento'";
    $sql = query($instrucao);
    
    confirmacaoDB("Orçamento e contas referente incluido com sucesso.");
    
    $id_usuario = getIdCookieLogin($_COOKIE["login"]);
    $dataAtual = date('Y-m-d H:i:s');
    $acao = "Incluiu novamente um orcamento deletado.";
    $tabela_afetada = "orcamento";
    $chave_principal = $idOrcamento;
    
    insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
    
    orcamento($idOrcamento);

}elseif($op=="deletar"){
    
	$msg ="";

    $idOrcamento = base64_decode($id);
	//esse codigo precisa estar aqui
	//dando alta no estoque se houver necessidade
	$instrucao = "select id_item, quantidade from orcamento_itens where tabela_item='produto' and id_orcamento=";
	$instrucao .= "(select id from orcamento where id='$idOrcamento' and status='2')";// se o operador desejar deletar o orçamento quando ele já estiver sido vendido, caso contrario ainda não haverá necessidade de dar baixa no estoque
	$sql = query($instrucao);
	for($i=0; $i<num_rows($sql); $i++){
		extract(mysqli_fetch_assoc($sql));
		if(is_numeric($id_item)){
			$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$id_item'");
			extract(mysqli_fetch_assoc($sqlProduto));
			if($contabilizar_estoque){
				$qtd_estoque += $quantidade;
				$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_item'");
				$msg .= "<br>Devolvido ao estoque $quantidade und do produto $nome deixando assim $qtd_estoque und em estoque atual.";	
			}
		}
	}
	
	
    $sqlOrdemServico = query("select id as chave_principal from ordem_servico where id_orcamento='$idOrcamento' and status!='3'");
	for($i=0; $i<mysqli_num_rows($sqlOrdemServico); $i++){
		extract(mysqli_fetch_assoc($sqlOrdemServico));
		$sql = query("update ordem_servico set status='3', data_concluida='".date('Y-m-d H:i:s')."' where id='$chave_principal'");//3 = cancelada
			if(mysqli_affected_rows($conexao)>0){
			$msg .= "<br>Ordem de serviço $chave_principal cancelada.";
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cancelou uma ordem de serviço.";
			$tabela_afetada = "ordem_servico";
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		}
	}
    
    //deletando as contas
    $instrucao = "update conta set status='4' where (entidade=(select id_cliente from orcamento where id='$idOrcamento') or entidade=(select cliente from orcamento where id='$idOrcamento')) ";
    $instrucao .= "and referido='$idOrcamento' and tabela_referido='orcamento'";
    $sql = query($instrucao);
	
    //deletando o orcamento
    $instrucao = "update orcamento set status='4' where id='$idOrcamento'";
    $sql = query($instrucao);
    
    $msg .= "Orçamento e contas referentes deletadas com sucesso.";
    
    $id_usuario = getIdCookieLogin($_COOKIE["login"]);
    $dataAtual = date('Y-m-d H:i:s');
    $acao = "Deletou um orçamento.";
    $tabela_afetada = "orcamento";
    $chave_principal = base64_decode($id);
    
    insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	
    $idOrcamento = $id;
	
	info($msg);
	echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarOrcamento.php'>";

}elseif($op=="venda"){
	
	$sql = query("select status from orcamento where id='".base64_decode($orcamento)."'");
	extract(mysqli_fetch_assoc($sql));
	if($status != "2"){
		
		$sql = query("update orcamento set status='2', data_venda='".date('Y-m-d H:i:s')."' where id='".base64_decode($orcamento)."'");
		$sql2 = query("update conta set status='2', data='".date('Y-m-d H:i:s')."', id_usuario=".getIdCookieLogin($_COOKIE["login"])." where referido='".base64_decode($orcamento)."'");
		
		$msg = "Venda efetuada e gerado as contas a receber.";
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Efetuou uma venda.";
		$tabela_afetada = "orcamento";
		$chave_principal = base64_decode($orcamento);
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		$sql = query("select id_cliente, cliente, fone from orcamento where id='".base64_decode($orcamento)."'");
		extract(mysqli_fetch_assoc($sql));
		
		$sqlOrcamento = query("select * from orcamento_itens where id_orcamento='".base64_decode($orcamento)."'");
		$qtdItem = mysqli_num_rows($sqlOrcamento);
		//criando OS caso seja necessário
		for($i=0; $i<$qtdItem; $i++){
			extract(mysqli_fetch_assoc($sqlOrcamento));
			//verificando se o item é um serviço e cadastrando caso for
			if(is_numeric($id_item) and $tabela_item=="servico"){
				$instrucao  = "insert into ordem_servico ";
				$instrucao .= "(id_orcamento, id_cliente, cliente, fone, id_servico, quantidade, valor, data_venda) ";
				$instrucao .= "values ";
				$instrucao .= "(".base64_decode($orcamento).", '$id_cliente', '$cliente', '$fone', '$id_item', '$quantidade', '$valor_produto', '".date('Y-m-d H:i:s')."')";
				$sql = query($instrucao);
				
				$chave_principal = mysqli_insert_id($conexao);
				
				$msg .= "<br>Ordem de serviço $chave_principal criada com sucesso.";
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cadastrou uma ordem de serviço.";
				$tabela_afetada = "ordem_servico";
				
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			}
		}	
		
		//dando baixa no estoque
		$sql = query("select id_item, quantidade from orcamento_itens where tabela_item='produto' and id_orcamento='".base64_decode($orcamento)."'");
		for($i=0; $i<num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			if(is_numeric($id_item)){
				$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$id_item'");
				extract(mysqli_fetch_assoc($sqlProduto));
				if($contabilizar_estoque){
					$qtd_estoque -= $quantidade;
					$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_item'");
					$msg .= "<br>Retirado do estoque $quantidade und do produto $nome deixando assim $qtd_estoque und em estoque atual.";	
				}
			}
		}
		
			
	}else{
		$msg = "Este orçamento já está com status vendido.";
	}
	
	info($msg, "green");
	orcamento(base64_decode($orcamento));

}elseif($op=="venda_cancelar"){
	
	$sql = query("select status from orcamento where id='".base64_decode($orcamento)."'");
	extract(mysqli_fetch_assoc($sql));
	if($status != "1"){
	
		$sql = query("update orcamento set status='1', data_venda=NULL where id='".base64_decode($orcamento)."'");//1 = aguardando venda
		$sql2 = query("update conta set status='1' where referido='".base64_decode($orcamento)."'");//1 = aguardando venda
	
		$msg = "Venda cancelada.<br>Contas a receber apagadas.";
	
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Cancelou uma venda.";
		$tabela_afetada = "orcamento";
		$chave_principal = base64_decode($orcamento);
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		$sqlOrdemServico = query("select id as chave_principal, status as statusAnterior from ordem_servico where id_orcamento='".base64_decode($orcamento)."' and status!='3'");
		for($i=0; $i<mysqli_num_rows($sqlOrdemServico); $i++){
			extract(mysqli_fetch_assoc($sqlOrdemServico));
			$sql = query("update ordem_servico set status='3', data_concluida='".date('Y-m-d H:i:s')."' where id='$chave_principal'");//3 = cancelada
			if(mysqli_affected_rows($conexao)>0){
				$msg .= "<br>Ordem de serviço $chave_principal cancelada.";
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cancelou uma ordem de serviço.";
				$tabela_afetada = "ordem_servico";
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			}
			
			if($statusAnterior=="2"){//ou seja essa ordem de serviço ja havia sido concluida
				//dando alta no estoque se houver necessidade
				$instrucao = "select id_servico, quantidade as qtdServico from ordem_servico where id='$chave_principal'";
				$sql = query($instrucao);
				extract(mysqli_num_rows($sql));
				$instrucao = "select nome, id_produto, qtd as qtdProduto from servico_produto where id_servico='$chave_principal'";
				$sql = query($instrucao);
				for ($i = 0; $i < num_rows($sql); $i++) {
					extract(mysqli_fetch_assoc($sql));
					$qtdSomar = $qtdProduto * $qtdServico;
					$sqlProduto = query("select qtd_estoque, contabilizar_estoque from produto where id='$id_produto'");
					if($contabilizar_estoque){
						extract(mysqli_fetch_assoc($sqlProduto));
						$qtd_estoque = $qtd_estoque + $qtdSomar;
						$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_produto'");
						$msg .= "Produto $nome reposto no estoque $qtdSomar und, totalizando $qtd_estoque.<br>";	
					}
				}
			}	
		}

		//dando alta no estoque
		$sql = query("select id_item, quantidade from orcamento_itens where tabela_item='produto' and id_orcamento='".base64_decode($orcamento)."'");
		for($i=0; $i<num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			if(is_numeric($id_item)){
				$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$id_item'");
				extract(mysqli_fetch_assoc($sqlProduto));
				if($contabilizar_estoque){
					$qtd_estoque += $quantidade;
					$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_item'");
					$msg .= "<br>Devolvido ao estoque $quantidade und do produto $nome deixando assim $qtd_estoque und em estoque atual.";	
				}
			}
		}
		
			
	}else{
		$msg = "Este orçamento já está com status vendido.";
	}
	

	info($msg);
	orcamento(base64_decode($orcamento));
}else{
	orcamento();	
}

	
//end all

include "templates/downLogin.inc.php";
?>