<?php
include "templates/upLogin.inc.php";

//all
$op = null;
extract($_GET);
extract($_POST);
$cont = count($_POST);
$array = array_keys($_POST);

if ($op == "novo" or $op == "editar") {

	$tipo2 = "cliente";
	if (!isset($cadastrarCliente)) {
		$cadastrarCliente = "false";
	}

	function validacaoToken() {

		global $cod, $cont, $array, $_POST, $_FILES;

		echo "<form style='width:30%;' method='post' action='cadastrarOrdemServico.php' enctype='multipart/form-data' style='display:inline;'>";
		for ($i = 0; $i < $cont; $i++) {
			if (is_array($_POST[$array[$i]])) {
				$cont2 = count($_POST[$array[$i]]);
				for ($j = 0; $j < $cont2; $j++) {
					echo "<input type='hidden' name='" . $array[$i] . "[]' value='" . $_POST[$array[$i]][$j] . "'>";
				}
			} else {
				echo "<input type='hidden' name='" . $array[$i] . "' value='" . $_POST[$array[$i]] . "'>";
			}
		}
		echo $cod;
		echo "<br>Para validar a ordem de serviço insira um token.<br>";
		echo "<input type='password' name='token'>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
		echo "</form>";
	}

	$info = "";
	$cor = "green";
	$validaToken = false;
	$valida = true;
	//irá dizer se precisará de token ou não
	$cod = "";
	$msg = "";

	//filtro dos arquivos
	$tamanhoMax = "3000000";
	//3mb
	$tamanhoMaxAbr = "3mb";
	if (isset($_FILES["arq"])) {
		$count = count($_FILES["arq"]["size"]);
		for ($i = $j = 0; $i < $count; $i++) {
			if ($_FILES["arq"]["size"][$i] > $tamanhoMax) {
				$info .= "Algum arquivo enviado é maior do que o valor máximo permitido ($tamanhoMaxAbr).<br>";
				$info .= $_FILES["arq"]["size"][$i] . "<br>";
				$valida = false;
			}
			for($h = 0; isset($valorAtributo[$j]) && $h<100; $h++){
				if(strstr($valorAtributo[$j], "arq_") == false){
					$j++;
				}
			}
			/*while (strstr($valorAtributo[$j], "arq_") == false and isset($valorAtributo[$j])) {
				$j++;
			}*/
			if(isset($valorAtributo[$j])){
				$idComparacao = explode("_", $valorAtributo[$j]);
				if (registro($idComparacao[1], "ordem_servico_atributo_padrao", "tipo_item") == "Imagem" 
				and strstr($_FILES["arq"]["type"][$i], "image") == false and $_FILES["arq"]["type"][$i] != null) {
					$info .= "Existe um atributo que aceita somente arquivos de imagens.<br>";
					$valida = false;
				}	
			}
		}
	}
	$subTotal = str_replace(",", ".", $subTotal);
	if($op!="editar"){
		$idOrdemServico = null;
	}
	
	if ((precoServico($idServico, $quantidade) > $subTotal and $op=="novo") or 
		(precoServico($idServico, $quantidade) > $subTotal and $subTotal < registro($idOrdemServico, "ordem_servico", "valor") and $op=="editar")) {
		$validaToken = true;
		$cod .= "O serviço <b>" . $servico . "</b> está com valor ";
		$cod .= "(R$" . real($subTotal) . ") abaixo do permitido (R$" . real(precoServico($idServico, $quantidade)) . ")<br>";
	}

	//verificar se a forma de pagamento eh a vista para clientes não cadastrados e pedir token
	if (!$id_cliente_fornecedor and $pgaForma != 1 and $cadastrarCliente == "false") {
		$validaToken = true;
		$cod .= "Cliente $contato não é cadastrado e a forma de pagamento está como " . registro($pgaForma, "pagamento_forma", "forma_pagamento") . ".";
		$cod .= "<br>Para clientes não cadastrados aceitamos apenas pagamentos a Vista.";
	}

	if ($validaToken and !isset($token)) {//precisa de token
		//pergunta se realmente quer cadastrar o cliente
		echo validacaoToken();
	} else {// nao precisa de token ou token existe
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
		if (!$validaToken) {

			//os dados foram confirmados do cadastro do cliente e o usuario deseja cadastrar o cliente
			//cadastrando cliente
			if (!isset($razao_social)) {
				$razao_social = "NULL";
			}
			if (!isset($cidade)) {
				$cidade = "NULL";
			}
			if((!isset($tipo) or !isset($doc1) or !isset($doc2) or !isset($observacoes) or !isset($endereco) or !isset($cidade) or !isset($nome)) and $id_cliente_fornecedor){
				$sql = query("select tipo, cpf_cnpj as doc1, rg_ie as doc2, observacoes, endereco, cidade, nome from cliente_fornecedor where id='$id_cliente_fornecedor'");
				extract(mysqli_fetch_assoc($sql));
			}
			!isset($cep)? $cep = null: false;
			$cliente_fornecedor = new cliente_fornecedor($id_cliente_fornecedor, $tipo2, $tipo, $nome, $razao_social, $doc1, $doc2, $data, $email, 
			$telefone1, $telefone2, $endereco, $numero, $bairro, $cidade, $estado, $cep, $referencia, $observacoes, $latitude, $longitude, 1);

			if ($cadastrarCliente == "true") {

				if ($cliente_fornecedor -> inserir()) {
					$info .= "Cliente $cliente_fornecedor->nome cadastrado.<br>";
					$idCliente = ultimaId("cliente_fornecedor");
				}

			} elseif ($cadastrarCliente == "atualizar" or is_numeric($id_cliente_fornecedor)) {

				if ($cliente_fornecedor -> update()) {
					$info .= "Cliente $cliente_fornecedor->nome atualizado.<br>";
				}
				$idCliente = turnZero($id_cliente_fornecedor);

			}else{
				//id do cliente nulo caso n existe cliente cadastrado para o orcamento ou retornar o id do cliente se caso o orcamento tiver cliente cadastrado
				$idCliente = $id_cliente_fornecedor;
			}

			//capturando a id do usuario
			$instrucao = "select id as idUsuario from usuario where login='" . $_COOKIE["login"] . "'";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));

			if ($op == "novo" and !$validaToken and $valida) {

				//verificando duplicidade de orçamento
				$instrucao = "select id as idOrdemServico from ordem_servico where ";
				$instrucao .= "id_cliente='$idCliente' and cliente='$contato' and fone='$telefoneC' ";
				$instrucao .= "and id=any(select id from ordem_servico_usuario where id_usuario_contribuidor='$idUsuario' ";
				$instrucao .= "and id_ordem_servico=any(select id from ordem_servico where id_cliente='$idCliente' and cliente='$contato' and fone='$telefoneC' ";
				$instrucao .= "and data_venda>='" . date('Y-m-d H:i:s', strtotime("- 30 minutes")) . "' and id_servico='$idServico')) ";
				$instrucao .= "and data_venda>='" . date('Y-m-d H:i:s', strtotime("- 30 minutes")) . "' ";
				$instrucao .= "and id_servico='$idServico'";
				$sqlOSRepetido = query($instrucao);
				mysqli_num_rows($sqlOSRepetido) > 0;

				if (mysqli_num_rows($sqlOSRepetido) > 0) {
					extract(mysqli_fetch_assoc($sqlOSRepetido));
					$info .= "Você pode está duplicando esta Ordem de Serviço.";
					info($info);
					ordemServico($idOrdemServico);
				} else {
					//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
					//cadastrando orcamento
					$instrucao = "insert into ordem_servico ";
					$instrucao .= "(id_cliente, cliente, fone, entrega_tipo, id_servico, quantidade, valor, data_venda, data_previsao) ";
					$instrucao .= "values ";
					$instrucao .= "('$idCliente','$contato','$telefoneC', '$entregaTipo', '$idServico', '".round($quantidade, 2)."', '$subTotal', '" . date('Y-m-d H:i:s') . "', '" . formataDataInv($dataPrevisao) . " " . $dataPrevisaoHora . ":00" . "');";
					$sql = query($instrucao);
					$idOrdemServico = mysqli_insert_id($conexao);

					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Cadastrou uma ordem de serviço.";
					$tabela_afetada = "ordem_servico";
					$chave_principal = $idOrdemServico;

					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
					
					$instrucao = "insert into ordem_servico_usuario ";
					$instrucao .= "(id_ordem_servico, id_usuario_contribuidor) ";
					$instrucao .= "values ";
					$instrucao .= "('$idOrdemServico', '".getIdCookieLogin($_COOKIE["login"])."')";
					$sql = query($instrucao);

					if (empty($historico)) {
						$historico = "Ordem de serviço foi criada.";
					}
					$instrucao = "insert into ordem_servico_historico ";
					$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
					$instrucao .= "values ";
					$instrucao .= "('$idOrdemServico', '$historico', '$id_usuario', '$dataAtual')";
					$sql = query($instrucao);
										
					
					if(mysqli_num_rows(query("select * from ordem_servico_atributo_padrao where status='1' and tipo_item<>'Imagem' and tipo_item<>'Arquivo'"))>0){
						//cadastrando ordem_servico_atributo
						$instrucao = "insert into ordem_servico_atributo ";
						$instrucao .= "(id_ordem_servico, id_ordem_servico_padrao, valor) ";
						$instrucao .= "values ";
						for ($i = $j = 0, $l = 1; $i < $qtdAtributos; $i++) {
							//regra de negocio para input do tipo file
							if (isset($valorAtributo[$i])){
									
								if($i!=0 && substr($instrucao, -2, -1)!= "," && substr($instrucao, -2, -1)!= "s"){
									$instrucao .= ", ";
								}
								
								if(strstr($valorAtributo[$i], "arq_") == true && isset($_FILES["arq"])){
									$sql = query("select tipo_item from ordem_servico_atributo_padrao where id='$idAtributo[$i]'");
									extract(mysqli_fetch_assoc($sql));
									$pasta = END_ARQ."arq_ordem_servico/" . $idOrdemServico . "/".$tipo_item."_".$idAtributo[$i]."/";
									$nome_imagem = $_FILES['arq']['name'][$j];
									$ext = strtolower(strrchr($nome_imagem, "."));
									$nome_atual = md5(uniqid(time())) . $ext;
									$tmp = $_FILES['arq']['tmp_name'][$j];
									if (!is_dir($pasta)) {
										mkdir($pasta, 0755, true);
										chmod($pasta, 0755);
									}
									if (!is_dir($pasta)) {
										mkdir($pasta, 0755, true);
										chmod($pasta, 0755);
									}
									if (empty($tmp)) {
										//$instrucao = substr($instrucao, 0, -2);
										//Elimina o ", "
									}else{
										if(move_uploaded_file($tmp, $pasta . $nome_atual)){
											$instrucao .= "('$idOrdemServico', '" . $idAtributo[$i] . "', '$l')";
										}else{
											info("Falha ao enviar o arquivo.<br>", "red");
											//$instrucao = substr($instrucao, 0, -2);
											//Elimina o ", "
										}
									}
									$l++;
									$j++;
								}else{
									$instrucao .= "('$idOrdemServico', '" . $idAtributo[$i] . "', '" . $valorAtributo[$i] . "')";
								}
							}
						}
						//correção de bug, caso depois de ter cadastrado a OS o usuario tenha cadastrado outro atibuto para a OS e resolva editar
						//a OS já existente. Esta correção tira a virgula do final da instrução para n buga-la
						if(substr($instrucao, -2, -1)== ","){
							$instrucao = substr($instrucao, 0, -2);
						}
						$sql = query($instrucao);
					}
					

					$pgaParcelas == "" ? $pgaParcelas = 1 : false;

					//se o cliente não foi cadastrado e n eh um orcamento de cliente pre-cadastrado entao inserir a entidade referente na conta com o nome do contato do orcamento
					if (!$id_cliente_fornecedor and $idCliente == 0) {
						$tabela_cliente = "";
						$idCliente = $contato;
					} else {
						$tabela_cliente = "cliente_fornecedor";
					}

					$valor = $quantidade * $subTotal;
					//abrindo conta
					$instrucao = "insert into conta ";
					$instrucao .= "(tabela_entidade, entidade, tabela_referido, referido, status, valor, forma_pagamento, parcelas, conta_plano, data, id_usuario) ";
					$instrucao .= "values ";
					//conta plano = 4 é igual a Ordem e Serviço
					$instrucao .= "('$tabela_cliente', '$idCliente', 'ordem_servico','$idOrdemServico', '2', '$valor','$pgaForma','$pgaParcelas', '4','" . date('Y-m-d H:i:s') . "','" . getIdCookieLogin($_COOKIE["login"]) . "');";

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
					if (!isset($subTipoPagamento)) {
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
						if ($i != 0) {
							$instrucao .= ", ";
						}
						$instrucao .= "('$idConta','$pgaTipo','$subTipoPagamento', NULL, NULL,'$vencimento','".getIdCookieLogin($_COOKIE["login"])."')";

						$i++;
					} while($i<$pgaParcelas);

					$sql = query($instrucao);
					$info .= "Ordem de Serviço cadastrado com sucesso.";
					echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarOrdemServico.php?op=visualizar&id=".base64_encode($idOrdemServico)."'>";
				}

			} elseif ($op == "editar" and !$validaToken) {
				
				$instrucao = "select valor as valorServicoAntes from ordem_servico where id='$idOrdemServico'";
				$sql= query($instrucao);
				extract(mysqli_fetch_assoc($sql));
				$precoServico = $quantidade * $subTotal;
				$instrucao = "select * from ordem_servico where valor<>'$precoServico' and id='$idOrdemServico'";
				$sql= query($instrucao);
				$alterarConta = mysqli_num_rows($sql); 
				//independente dos dados confirmados ou não, o usuario não deseja cadastrar o cliente
				//editar orcamento
				$instrucao = "update ordem_servico set ";
				$instrucao .= "id_cliente='$idCliente', cliente='$contato', fone='$telefoneC', ";
				$instrucao .= "entrega_tipo='$entregaTipo', data_previsao='" . formataDataInv($dataPrevisao) . " " . $dataPrevisaoHora . ":00" . "', id_servico='$idServico', ";
				$instrucao .= "quantidade='".round($quantidade , 2)."', valor='$subTotal' where id='$idOrdemServico'";
				$sql = query($instrucao);

				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Alterou uma ordem de serviço.";
				$tabela_afetada = "ordem_servico";
				$chave_principal = $idOrdemServico;

				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

				if (empty($historico)) {
					$historico = "Ordem de serviço foi editada.";
				}
				$instrucao = "insert into ordem_servico_historico ";
				$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
				$instrucao .= "values ";
				$instrucao .= "('$idOrdemServico', '$historico', '$id_usuario', '$dataAtual')";
				$sql = query($instrucao);

				//deletando todos os itens do orcamento e inserindo novamente
				$sql = query("delete from ordem_servico_atributo where id_ordem_servico='$idOrdemServico'");
				//cadastrando ordem_servico_atributo
				$instrucao = "insert into ordem_servico_atributo ";
				$instrucao .= "(id_ordem_servico, id_ordem_servico_padrao, valor) ";
				$instrucao .= "values ";
				if(mysqli_num_rows(query("select * from ordem_servico_atributo_padrao where status='1' and tipo_item<>'Imagem' and tipo_item<>'Arquivo'"))>0){
					
					for ($i = $j = 0, $l = 1; $i < $qtdAtributos; $i++) {
						
						if($i!=0 && substr($instrucao, -2, -1)!= "," && substr($instrucao, -2, -1)!= "s"){
							$instrucao .= ", ";
						}
						//regra de negocio para input do tipo file
						if (strstr($valorAtributo[$i], "arq_") == true && isset($_FILES["arq"])) {
							$sql = query("select tipo_item from ordem_servico_atributo_padrao where id='$idAtributo[$i]'");
							extract(mysqli_fetch_assoc($sql));
							$pasta = END_ARQ."arq_ordem_servico/" . $idOrdemServico . "/".$tipo_item."_".$idAtributo[$i]."/";
							$nome_imagem = $_FILES['arq']['name'][$j];
							$ext = strtolower(strrchr($nome_imagem, "."));
							$nome_atual = md5(uniqid(time())) . $ext;
							$tmp = $_FILES['arq']['tmp_name'][$j];
							if (!is_dir($pasta)) {
								mkdir($pasta, 0755, true);
								chmod($pasta, 0755);
							}
							if (empty($tmp)) {
								//$instrucao = substr($instrucao, 0, -2);
								//Elimina o ", "
							} else {
								if (move_uploaded_file($tmp, $pasta . $nome_atual)) {
									$instrucao .= "('$idOrdemServico', '" . $idAtributo[$i] . "', '$l')";
								} else {
									info("Falha ao enviar o arquivo.<br>", "red");
									//$instrucao = substr($instrucao, 0, -2);
									//Elimina o ", "
								}
							}
							$l++;
							$j++;
						} else {
							$instrucao .= "('$idOrdemServico', '" . $idAtributo[$i] . "', '" . $valorAtributo[$i] . "')";
						}
					}
					//correção de bug, caso depois de ter cadastrado a OS o usuario tenha cadastrado outro atibuto para a OS e resolva editar
					//a OS já existente. Esta correção tira a virgula do final da instrução para n buga-la
					if(substr($instrucao, -2, -1)== ","){
						$instrucao = substr($instrucao, 0, -2);
					}
					$sql = query($instrucao);
				}
				$info .= "Ordem de Serviço editado com sucesso.";

				if (!$pgaParcelas) {
					$pgaParcelas = 1;
				}
				if ($idCliente == 0) {
					$idCliente = $contato;
				}


				if(registro($idOrdemServico, 'ordem_servico', 'id_orcamento')==0){
					$tabela_referido = 'ordem_servico';
					$referido = $idOrdemServico;
					$valorTotal = $precoServico;
				}else{
					$tabela_referido = 'orcamento';
					$referido = registro($idOrdemServico, 'ordem_servico', 'id_orcamento');
					$sql = query("select id, tabela_item, id_item, quantidade as Quantidade, valor_produto from orcamento_itens where id_orcamento='$referido'");
					for($i=$valorTotal=0; $i<mysqli_num_rows($sql); $i++){
						extract(mysqli_fetch_assoc($sql));
						if($tabela_item=='servico' and $id_item==$idServico){
							$sqlUpdateOrc = query("update orcamento_itens set quantidade='".round($quantidade , 2)."', valor_produto='$subTotal' where id='$id'");
							$Quantidade = $quantidade;
							$valor_produto = $subTotal;
						}
						$valorTotal += $Quantidade * $valor_produto;
					}
					
					
				}
				//editar conta apenas se o valor tiver sido alterado;
				
				
				if(!$alterarConta){
					//editando conta
				
					$instrucao = "update conta set ";
					$instrucao .= "entidade='$idCliente', valor='$valorTotal', forma_pagamento='$pgaForma', ";
					//$instrucao .= "parcelas='$pgaParcelas', data='" . date('Y-m-d H:i:s') . "', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where referido='$idOrdemServico' and tabela_referido='ordem_servico'";
					$instrucao .= "parcelas='$pgaParcelas', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where referido='".$referido."' and tabela_referido='$tabela_referido'";
					
					$sql = query($instrucao);

					$idConta = mysqli_fetch_row(query("select id from conta where tabela_referido='$tabela_referido' and referido='$referido'"));

					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Editou uma conta.";
					$tabela_afetada = "conta";
					$chave_principal = $idConta[0];

					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
					
					if(registro($idConta, 'conta', 'valor')<$valorTotal){
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
						$sql = query("select id_caixa_movimento from conta_itens where id_conta='" . $idConta[0] . "'");
						for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
							extract(mysqli_fetch_assoc($sql));
							$sqlCaixa = query("delete from caixa_movimento where id='$id_caixa_movimento'");
						}
						//deletando log de contas
						$sql = query("delete from conta_itens where id_conta='" . $idConta[0] . "'");
						//abrindo log de conta
						$instrucao = "insert into conta_itens";
						$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, valor, data_pagamento, data_vencimento, id_usuario) ";
						$instrucao .= "values ";
						$i = 0;
						//fazer o restante da insercao com uma rotina de repetição
						if (!isset($subTipoPagamento)) {
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
							if ($i != 0) {
								$instrucao .= ", ";
							}
							$instrucao .= "('" . $idConta[0] . "','$pgaTipo','$subTipoPagamento','0','0000-00-00','$vencimento','".getIdCookieLogin($_COOKIE["login"])."')";

							$i++;
						} while($i<$pgaParcelas);

						$sql = query($instrucao);
						$info .= "<br>As contas e valores recebidos (se houver) foram resetados.";
						$info .= "<br><div style='font-size:20px; color:red;'>AVISO!</div>";
						$info .= "Por favor, se havia algum valor recebido, certifique-se foi realmente recebido na conta deste orçamento.";
					}
				}
			}

			info($info, $cor);

			ordemServico($idOrdemServico);
		}
	}

} elseif ($op == "visualizar") {

	$historico = "Visualizou a ordem de serviço.";
	$instrucao = "insert into ordem_servico_historico ";
	$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
	$instrucao .= "values ";
	$instrucao .= "('" . base64_decode($id) . "', '$historico', '" . getIdCookieLogin($_COOKIE["login"]) . "', '" . date('Y-m-d H:i:s') . "')";
	$sql = query($instrucao);

	ordemServico(base64_decode($id));

} elseif ($op == "mudarStatus") {
	$idOrdemServico = base64_decode($id);
	$msg = "";
	if ($status == "3") {
		if (!isset($confirma)) {
			echo "Você deseja realmente cancelar esta ordem de serviço?<br>";
			echo "<form method='post' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='op' value='mudarStatus'>";
			echo "<input type='hidden' name='status' value='$status'>";
			echo "<input type='hidden' name='confirma' value='true'>";
			echo "<br>";
			echo "<input type='submit' value='Sim'>";
			echo "</form>";
			echo "<form method='get' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='submit' value='Não'>";
			echo "</form>";
		} else {


			//deletando o ordem de servico mas antes pegando seu status anterior pois dependendo da situação irar dar alta no estoque
			$sql = query("select status as statusAnterior from ordem_servico where id='$idOrdemServico'");
			extract(mysqli_fetch_assoc($sql));
			$instrucao = "update ordem_servico set status='3', data_concluida='" . date('Y-m-d H:i:s') . "' where id='$idOrdemServico'";
			$sql = query($instrucao);

			$msg = "Ordem de serviço deletada com sucesso.<br>";

			$historico = "Cancelou a ordem de serviço.";
			$instrucao = "insert into ordem_servico_historico ";
			$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
			$instrucao .= "values ";
			$instrucao .= "('$idOrdemServico', '$historico', '" . getIdCookieLogin($_COOKIE["login"]) . "', '" . date('Y-m-d H:i:s') . "')";
			$sql = query($instrucao);

			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cancelou uma ordem de serviço.";
			$tabela_afetada = "ordem_servico";
			$chave_principal = base64_decode($id);

			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

		}
	} elseif ($status == "2") {
		
		if (!isset($usuario)) {
			echo "<script type='text/javascript'>";
			echo "function mudaValor(valor, input){";
			echo "valor=='1' ? $(input).val(0) : $(input).val(1);";
			echo "}";
			echo "</script>";
			echo "<form method='get' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='op' value='mudarStatus'>";
			echo "<input type='hidden' name='status' value='$status'>";
			echo "<table id='gradient-style'>";
			echo "<tr>";
			echo "<th colspan='3'>Selecione os colaboradores que participaram da produção deste serviço:</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<th align='center'>Nome</th>";
			echo "<th align='center'>Aplicar Comissão</th>";
			echo "<th align='center'>Aplicar Penalidade</th>";
			echo "</tr>";

			$instrucao = "select id_usuario_contribuidor, aplicar_comissao, aplicar_penalidade from ordem_servico_usuario where id_ordem_servico='$idOrdemServico'";
			$sql = query($instrucao);
			$regra = array();
			for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
				extract(mysqli_fetch_assoc($sql));
				$regra[$i] = array($id_usuario_contribuidor, $aplicar_comissao, $aplicar_penalidade);
			}
			$sql = query("select * from usuario where status='Ativo' and id<>1");
			for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
				extract(mysqli_fetch_assoc($sql));
				echo "<tr>";
				echo "<td align='center'>";
				!isset($regra[$i][0]) ? $regra[$i][0] = array() : false;
				!isset($regra[$i][1]) ? $regra[$i][1] = null : false;
				!isset($regra[$i][2]) ? $regra[$i][2] = null : false;
				if (in_array($id, $regra[$i])) {
					//checkbox('usuario[]', 1);
					echo "<input type='checkbox' name='usuario[]' value='$id' checked='true'> $nome";
				} else {
					//checkbox('usuario[]', 0);
					echo "<input type='checkbox' name='usuario[]' value='$id'> $nome";
				}
				echo ' '.$nome.' ';
				echo "</td>";
				echo "<td align='center'>";
				if ($regra[$i][1]) {
					//checkbox('comissao[]', 1);
					echo "<input type='checkbox' name='comissao[]' value='1' onclick='mudaValor(this.value, this);' checked='true'>";
				} else {
					//checkbox('comissao[]', 0);
					echo "<input type='checkbox' name='comissao[]' value='0' onclick='mudaValor(this.value, this);'>";
				}
				echo "</td>";
				echo "<td align='center'>";
				if ($regra[$i][2]) {
					//checkbox('penalidade[]', 1);
					echo "<input type='checkbox' name='penalidade[]' value='1' checked='true' onclick='mudaValor(this.value, this);'>";
				} else {
					//checkbox('penalidade[]', 0);
					echo "<input type='checkbox' name='penalidade[]' value='0' onclick='mudaValor(this.value, this);'>";
				}
				echo "</td>";
				echo "</tr>";

			}
			echo "<th colspan='4' align='right'>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'>";
			echo "</th>";
			echo "</table>";
			echo "</form>";
		} else {

			//pegando seu status anterior pois dependendo da situação irar dar alta no estoque
			$sql = query("select status as statusAnterior from ordem_servico where id='$idOrdemServico'");
			extract(mysqli_fetch_assoc($sql));
			$instrucao = "update ordem_servico set status='2', data_concluida='" . date('Y-m-d H:i:s') . "' where id='$idOrdemServico'";
			$sql = query($instrucao);

			$msg = "Ordem de serviço concluida.<br>";
			$msg .= "Com os seguintes colaboradores:<br>";

			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Concluiu uma ordem de serviço.";
			$tabela_afetada = "ordem_servico";
			$chave_principal = $idOrdemServico;

			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

			$sql = query("delete from ordem_servico_usuario where id_ordem_servico='$idOrdemServico'");
			$instrucao = "insert into ordem_servico_usuario ";
			$instrucao .= "(id_ordem_servico, id_usuario_contribuidor, aplicar_comissao, aplicar_penalidade, id_usuario) ";
			$instrucao .= "values ";
			$qtdUsuario = count($usuario);
			for ($i = 0; $i < $qtdUsuario; $i++) {
				$i != 0 ? $instrucao .= ", " : false;
				!isset($comissao[$i]) ? $comissao[$i] = 0 : false;
				!isset($penalidade[$i]) ? $penalidade[$i] = 0 : false;
				$instrucao .= "('$idOrdemServico', '$usuario[$i]', '$comissao[$i]', '$penalidade[$i]', '" . getIdCookieLogin($_COOKIE["login"]) . "')";
				$msg .= registro($usuario[$i], "usuario", "nome") . "<br>";
			}
			$sql = query($instrucao);

			$historico = "Concluíu a ordem de serviço.";
			$instrucao = "insert into ordem_servico_historico ";
			$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
			$instrucao .= "values ";
			$instrucao .= "('$idOrdemServico', '$historico', '$id_usuario', '$dataAtual')";
			$sql = query($instrucao);

			//dando baixa no estoque se houver necessidade
			$instrucao = "select id_servico, quantidade as qtdServico from ordem_servico where id='$idOrdemServico'";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			$instrucao = "select produto, id_produto, qtd as qtdProduto from servico_produto where id_servico='$id_servico'";
			$sql = query($instrucao);
			for ($i = 0; $i < num_rows($sql); $i++) {
				extract(mysqli_fetch_assoc($sql));
				if($id_produto<>0){
					$qtdSubtrair = $qtdProduto * $qtdServico;
					$sqlProduto = query("select qtd_estoque, contabilizar_estoque from produto where id='$id_produto'");
					extract(mysqli_fetch_assoc($sqlProduto));
					if($contabilizar_estoque){
						$qtd_estoque = $qtd_estoque - $qtdSubtrair;
						$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_produto'");
						$msg .= "Produto $produto retirado do estoque $qtdSubtrair und, totalizando $qtd_estoque.<br>";	
					}	
				}
			}

		}
	} else {
		//pegando seu status anterior pois dependendo da situação irar dar alta no estoque
		$sql = query("select status as statusAnterior from ordem_servico where id='$idOrdemServico'");
		extract(mysqli_fetch_assoc($sql));
		$instrucao = "update ordem_servico set status='$status', data_concluida=NULL where id='$idOrdemServico'";
		$sql = query($instrucao);

		$msg = "Ordem de serviço com status=" . registro($status, "ordem_servico_status", "nome") . ".<br>";
		
		//retirando usuarios que concluirão esta ordem de serviço caso exista
		$sql = query("delete from ordem_servico_usuario where id_ordem_servico='$idOrdemServico'");
		if(mysqli_affected_rows($conexao)>0){
			$msg .= "Usuarios que haviam concluído está ordem de serviço foram deletados registro.<br>";
		}

		$historico = "Mudou o status da ordem de serviço para " . registro($status, "ordem_servico_status", "nome") . ".";
		$instrucao = "insert into ordem_servico_historico ";
		$instrucao .= "(id_ordem_servico, texto, id_usuario, data) ";
		$instrucao .= "values ";
		$instrucao .= "('$idOrdemServico', '$historico', '" . getIdCookieLogin($_COOKIE["login"]) . "', '" . date('Y-m-d H:i:s') . "')";
		$sql = query($instrucao);

		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Mudou o status da ordem de serviço para " . registro($status, "ordem_servico_status", "nome") . ".";
		$tabela_afetada = "ordem_servico";
		$chave_principal = $idOrdemServico;

		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	}

	if (isset($statusAnterior)) {
		if ($statusAnterior == "2" and $statusAnterior!=$status) {//ou seja essa ordem de serviço ja havia sido concluida
			//dando alta no estoque se houver necessidade
			$instrucao = "select id_servico, quantidade as qtdServico from ordem_servico where id='$idOrdemServico'";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			$instrucao = "select produto, id_produto, qtd as qtdProduto from servico_produto where id_servico='$id_servico'";
			$sql = query($instrucao);
			for ($i = 0; $i < num_rows($sql); $i++) {
				extract(mysqli_fetch_assoc($sql));
				if($id_produto<>0){
					$qtdSomar = $qtdProduto * $qtdServico;
					$sqlProduto = query("select qtd_estoque, contabilizar_estoque from produto where id='$id_produto'");
					extract(mysqli_fetch_assoc($sqlProduto));
					if($contabilizar_estoque){
						$qtd_estoque = $qtd_estoque + $qtdSomar;
						$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$id_produto'");
						$msg .= "Produto $nome reposto no estoque $qtdSomar und, totalizando $qtd_estoque.<br>";	
					}	
				}
			}
		}
	}

	extract(mysqli_fetch_assoc(query("select id_orcamento from ordem_servico where id='$idOrdemServico'")));
	
	if (strlen($msg) > 0) {
		//se caso o status for diferente de cancelar e não for uma ordem de serviço atrelado a orçamento então
		//mudar status da conta para a receber
		if($status!=3 and !$id_orcamento){
			$instrucao = "update conta set status='2' where (entidade=(select id_cliente from ordem_servico where id='$idOrdemServico') or entidade=(select cliente from ordem_servico where id='$idOrdemServico')) ";
			$instrucao .= "and referido='$idOrdemServico' and tabela_referido='ordem_servico'";
			$sql = query($instrucao);
			$msg .= "Conta referente a está ordem de serviço foi editada.<br>";
		}elseif($status==3 and !$id_orcamento){
			//se caso o status for igual a cancelar e não for uma ordem de serviço atrelado a orçamento então
			//mudar status da conta para deletar
			//deletando as contas
			$instrucao = "update conta set status='4' where (entidade=(select id_cliente from ordem_servico where id='$idOrdemServico') or entidade=(select cliente from ordem_servico where id='$idOrdemServico')) ";
			$instrucao .= "and referido='$idOrdemServico' and tabela_referido='ordem_servico'";
			$sql = query($instrucao);
			$msg .= "Conta referente a está ordem de serviço foi deletada.<br>";
		}
		info($msg);
		ordemServico($idOrdemServico);
	}
	

} elseif ($op == "deletarHistorico") {
	$sql = query("select * from ordem_servico_historico where id='" . base64_decode($idHistorico) . "'");
	extract(mysqli_fetch_assoc($sql));

	$id_usuario = getIdCookieLogin($_COOKIE["login"]);
	$dataAtual = date('Y-m-d H:i:s');
	$acao = "Deletou um histórico da ordem de serviço.";
	$tabela_afetada = "ordem_servico";
	$chave_principal = $id_ordem_servico;

	insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

	$sql = query("delete from ordem_servico_historico where id='" . base64_decode($idHistorico) . "'");
	info("Histórico excluido");
	ordemServico($id_ordem_servico);
} else {
	ordemServico();
}
include "templates/downLogin.inc.php";
?>

