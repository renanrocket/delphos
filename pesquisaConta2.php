<?php
include "templates/upLogin.inc.php";

//all

extract($_GET);//conta
extract($_POST);
$info = "";

if($_POST){//apenas visualizar a conta
	
	//filtros
	if($op == "editar_conta" or $op == "nova_conta"){
		
		
		$valorTotal = str_replace(".", "", $valorTotal);
		$valorTotal = str_replace(",", ".", $valorTotal);
				
		//filtros
		$valida = true;
		if ($formaPagamento <> "2" and $parcelas > 1) {
			$info .= "Forma de pagamento marcada como " . registro($formaPagamento, "pagamento_forma", "forma_pagamento") . " e as parcelas são maiores que 1.<br>";
			$valida = false;
		}
		/*if ($formaPagamento == "2" and $parcelas <= 1) {
			echo "Forma de pagamento marcada como " . registro($formaPagamento, "pga_forma", "1") . " e as parcelas s&atilde;o menores que 2.<br>";
			$valida = FALSE;
		}*/
		###########   filtro de selecao do DB  ###########
		$sql = query("select valor from conta_itens where id_conta='".base64_decode($conta)."'");
		$linha = mysqli_num_rows($sql);
		$valor = 0;
		for ($i = 0; $i < $linha; $i++) {
			$reg = mysqli_fetch_row($sql);
			$valor += $reg[0];
		}
		if ($valorTotal < $valor) {
			$info .= "O valor recebido (R$ " . real($valor) . ") maior que o valor que você quer alterar(R$ " . real($valorTotal) . ").<br>$valor<br>$valorTotal";
			$valida = false;
		}
		$sql = query("select valor from conta_itens where id_conta='".base64_decode($conta)."' and valor<>'NULL'");
		$linha = mysqli_num_rows($sql);
		if ($linha > $parcelas and ($linha <> 1 and $parcelas <> 0)) {
			$info = "Existem $linha pagamento(s) efetuado(s) e você deseja devidir a quantidade de parcela(s) para $parcelas.<br>";
			$valida = false;
		}
	}
	
	if($op == "editar_conta"){
		
		//se for uma conta n relacionada a ordem de servico
		if ($valida) {
			
			//verificando se o plano de conta já existe ou se é um novo plano de contas
			if($cadastrarPlano == "true"){
				$instrucao = "select id as idPlanoConta from conta_plano where ";
				$instrucao .= "nome='$planoConta'";
				$sql = query($instrucao);
				if(mysqli_num_rows($sql)<=0){
					$instrucao = "insert into conta_plano (nome, status) values ('$planoConta', 'Ativo')";
					$sql = query($instrucao);
					$idPlanoConta = mysqli_insert_id($conexao);
				}else{
					extract(mysqli_fetch_assoc($sql));
				}
			}
		
			//inserindo dados no DB
		
			#apagando ou inserindo parcelas no conta_itens
			$sql = query("select valor from conta_itens where id_conta='".base64_decode($conta)."'");
			$linha = mysqli_num_rows($sql);
			#apagando parcelas no conta_itens
			if ($linha > $parcelas) {
				
				
				$parcelaSub = $linha - $parcelas;
				
				for ($i = 0; $i < $parcelaSub; $i++) {
					$sql = query("select max(data_vencimento) from conta_itens where id_conta='".base64_decode($conta)."'");
					$reg = mysqli_fetch_row($sql);
					$instrucao = "delete from conta_itens where id_conta='".base64_decode($conta)."' and data_vencimento='$reg[0]'";
					$sql = query($instrucao);
				}
		
				#inserindo parcelas no conta_itens
			} elseif ($linha < $parcelas) {
				
				$parcelaSub = $parcelas - $linha;
				$sql = query("select max(data_vencimento) from conta_itens where id_conta='".base64_decode($conta)."'");
				if (mysqli_num_rows($sql)>0) {
					$reg = mysqli_fetch_row($sql);
					$dataVencimento = explode("-", $reg[0]);
				} else {
					$dataVencimento = explode("-", date('Y-m-d'));
				}
				for ($i = 0; $i < $parcelaSub; $i++) {
					
					//somando o mes e validando a data
					$dataVencimento[1]++;
					if ($dataVencimento[1] > 12) {
						$dataVencimento[1] = $dataVencimento[1] - 12;
						$dataVencimento[0]++;
					}
					$dia = $dataVencimento[2];
					while (checkdate($dataVencimento[1], $dia, $dataVencimento[0])==false){
						$dia--;
					}
					$data = $dataVencimento[0] . "-" . $dataVencimento[1] . "-" . $dia;
					$sql = query("select tipo_pagamento, tipo_pagamento_sub, id_usuario from conta_itens where id_conta='".base64_decode($conta)."'");
					if(mysqli_num_rows($sql)>0){
						$reg = mysqli_fetch_assoc($sql);
						$reg["tipo_pagamento_sub"] = '' ? $reg["tipo_pagamento_sub"] = 0: null;
					}else{
						$reg["tipo_pagamento"] = 1;
						$reg["tipo_pagamento_sub"] = 0;
						$reg["id_usuario"] = getIdCookieLogin($_COOKIE["login"]);
					}
					$instrucao = "insert into conta_itens ";
					$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
					
					$instrucao .= "values ('".base64_decode($conta)."', ".$reg["tipo_pagamento"].", ".$reg["tipo_pagamento_sub"].",'$data',  ".$reg["id_usuario"].")";
					$sql = query($instrucao);
					//login foi setado no templateUP
		
				}
		
			}
			
			$instrucao = "update conta set ";
			$instrucao .= "entidade='$entidade', referido='$referido', status='$status', valor='$valorTotal', forma_pagamento='$formaPagamento', ";
			$instrucao .= "parcelas='$parcelas', documento='$documento', observacoes='$observacoes', conta_plano='$idPlanoConta', observacoes='$observacoes', ";
			$instrucao .= "empresa='$empresa' where id='".base64_decode($conta)."'";
			$sql = query($instrucao);
			
			//caso alterar o status da conta alterar tbm o status do orcamento
			if(is_numeric($referido) and $tabela_referido='orcamento'){
				$instrucao = "update orcamento set status='$status', cliente='$entidade' where id='$referido'";
				$sql = query($instrucao);
			}
			$info .= "Conta alterada com sucesso.<br>";
		
		}
		
		$idConta = base64_decode($conta);

		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou uma conta.";
		$tabela_afetada = "conta";
		$chave_principal = $idConta;

		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

	}elseif($op == "nova_conta"){//cadastrar uma nova conta
		
		//se for uma conta n relacionada a ordem de servico
		if ($valida) {
				
			//verificando se o plano de conta já existe ou se é um novo plano de contas
			if($cadastrarPlano == "true"){
				$instrucao = "select id as idPlanoConta from conta_plano where ";
				$instrucao .= "nome='$planoConta'";
				$sql = query($instrucao);
				if(mysqli_num_rows($sql)<=0){
					$instrucao = "insert into conta_plano (nome, status) values ('$planoConta', 'Ativo')";
					$sql = query($instrucao);
					$idPlanoConta = mysqli_insert_id($conexao);
				}else{
					extract(mysqli_fetch_assoc($sql));
				}
			}
			
		
			//inserindo dados no DB
			$conn = TConnection::open(ALCUNHA);
			$sql = new TSqlInsert;
			$sql->setEntity('conta');
			$sql->setRowData('entidade', $entidade);
			$sql->setRowData('referido', $referido);
			$sql->setRowData('status', $status);
			$sql->setRowData('valor', $valorTotal);
			$sql->setRowData('forma_pagamento', $formaPagamento);
			$sql->setRowData('parcelas', $parcelas);
			$sql->setRowData('conta_plano', $idPlanoConta);
			$sql->setRowData('documento', $documento);
			$sql->setRowData('observacoes', $observacoes);
			$sql->setRowData('data', date('Y-m-d H:i:s'));
			$sql->setRowData('id_usuario', getIdCookieLogin($_COOKIE["login"]));
			$sql->setRowData('empresa', $empresa);
			$conn->query($sql->getInstruction());
			$idConta = $conn->lastInsertId();
			
			/*
			$instrucao = "insert into conta ";
			$instrucao .= "(entidade, referido, status, valor, forma_pagamento, parcelas, conta_plano, documento, observacoes, ";
			$instrucao .= "data, id_usuario, empresa) values ('$entidade', '$referido', '$status', '$valorTotal', ";
			$instrucao .= "'$formaPagamento', '$parcelas', '$idPlanoConta', '$documento', '$observacoes', '".date("Y-m-d H:i:s")."', ";
			$instrucao .= "'".getIdCookieLogin($_COOKIE["login"])."', '$empresa')";
			$sql = query($instrucao);
			
			$idConta = mysqli_insert_id($conexao);
			*/
			
			$dataVencimento = explode("-", date('Y-m-d'));
			for ($i = 0; $i < $parcelas; $i++) {
				
				//somando o mes e validando a data
				if($i!=0){
					$dataVencimento[1]++;
				}
				if ($dataVencimento[1] > 12) {
					$dataVencimento[1] = $dataVencimento[1] - 12;
					$dataVencimento[0]++;
				}
				$dia = $dataVencimento[2];
				while (checkdate($dataVencimento[1], $dia, $dataVencimento[0])==false){
					$dia--;
				}
				$data = validaData($dataVencimento[0] . "-" . $dataVencimento[1] . "-" . $dia);
				$instrucao = "insert into conta_itens ";
				$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
				$instrucao .= "values ('$idConta', '1', '0','$data', '".getIdCookieLogin($_COOKIE["login"])."')";
				$sql = query($instrucao);
			}
			
			$info .= "Conta cadastrada com sucesso.<br>";
		
		}
		
		$idConta = base64_decode($conta);

		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Cadastrou uma nova conta.";
		$tabela_afetada = "conta";
		$chave_principal = $idConta;

		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
	}elseif($op == "editar_conta_itens"){//editar log de conta
		$login = registro($_COOKIE["login"], "usuario", "id", "login");
		if(isset($valor)){
			$valor = str_replace(".", "", $valor);
			$valor = str_replace(",", ".", $valor);	
		}
		if(isset($datavencimento)){
			$dataVencimento = validaData(formataDataInv($_POST["datavencimento"]));
		}
		if(!isset($tipo_pagamento_sub)){
			$tipo_pagamento_sub = 0;
		}
		
		
		
		if ($op2 == "lancar") {
			if (empty($_POST["dataquitado"])) {
				$dataQuitado = date('Y-m-d');
			} else {
				$dataQuitado = formataDataInv($_POST["dataquitado"]);
			}
			
			
			$valida = true;
		
			if (empty($tipoPagamento) or $tipoPagamento==0) {
				$info .=  "Você deve selecionar o tipo de pagamento.<br>";
				$valida = false;
			}
			if (empty($caixa) or $caixa==0) {
                $info .=  "Você deve selecionar o caixa para essa operação.<br>";
                $valida = false;
            }
            if (registro(registro($conta_itens, "conta_itens", "id_conta"), "conta", "status")==4){
            	$info .= "Esta conta está deletada. Você não pode editar contas deletadas.<br>";
            	$valida = false;
            }
            if (registro($conta_itens, "conta_itens", "valor")){
            	$info .= "Esta conta já foi lançada. Você não pode lança-la novamente.<br>";
            	$valida = false;	
            }
            if($valor<=0){
            	$info .= "Valor inválido. Tente novamente com outro valor.<br>";
            	$valida = false;
            }
            
		
			
			if ($valida) {
				
                
                #
                #
                #
                # REALIZANDO OPERAÇÃO DO CAIXA_MOVIMENTO
                #
                #
                #
                //primeiramente verificando se esta conta_itens ja possui um id de operação
				
                if(isset($troco)){
                	$troco = str_replace(",", ".", $troco);
                	$valor = $valor-$troco;
                }
                
                $instrucao = "select id_caixa_movimento from conta_itens where id='$conta_itens'";
                
                $sql = query($instrucao);
                if(mysqli_num_rows($sql)>0){
                	extract(mysqli_fetch_assoc($sql));
                    //capturando o status da conta para saber se é uma operação de crédito ou uma operação de debito
                    $instrucao = "select status from conta where id=(select id_conta from conta_itens where id='$conta_itens')";
                    extract(mysqli_fetch_assoc(query($instrucao)));
                    //atualizando o status da conta caso ela seja aguardando venda e esteja recebendo algum valor
                    //segundo a logica; vc so receberia algum valor de um orçamento caso ele esteja vendido.
                    if($status==1){//operacao será um credito
                        $instrucao = "update conta set status='2' where id=(select id_conta from conta_itens where id='$conta_itens')";
                        $sql = query($instrucao);
                        $debito_credito = "1";
                    }elseif($status==2){//operação eh um credito
                        $debito_credito = "1";
                    }elseif($status==3){//operação eh uma retirada
                        $debito_credito = "0";
                    }elseif($status==4){
                    	$debito_credito = "1";
                    }
                    $instrucao = "insert into caixa_movimento ";
                    $instrucao .= "(debito_credito, valor, id_caixa, data, id_usuario) values";
                    $instrucao .= "('$debito_credito', '$valor', '$caixa', '$dataQuitado ".date('H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
                    $sql = query($instrucao);
                    $id_caixa_movimento = mysqli_insert_id($conexao);
                }else{
                    //fazendo a atualização da operação
                    $instrucao = "update caixa_movimento set valor='$valor', id_caixa='$caixa', data='$dataQuitado ".date('H:i:s')."' where id='$id_caixa_movimento'";
                    $sql = query($instrucao);
                }
                
                
                //apenas editando a parcela
                if(empty($valor) or empty($dataVencimento)){
                    
					$instrucao = "update conta_itens set ";
					$instrucao .= "tipo_pagamento='$tipoPagamento', tipo_pagamento_sub='$tipo_pagamento_sub', data_vencimento='$dataVencimento', id_caixa_movimento='$id_caixa_movimento', id_usuario='$login' where id='$conta_itens'";
					$acao = "Editou um parcela de conta.";
					$sql = query($instrucao);
                    
				}else{//realmente lançando a parcela
				    
				    
				    //checar se é necessário inserir nos pontos
				    //se o cliente está cadastrado
				    $instrucao = "select entidade as idCliente from conta where ";
				    $instrucao .= "id=(select id_conta from conta_itens where id='$conta_itens') and ";
					$instrucao .= "tabela_entidade='cliente_fornecedor' and status='2'";
				    $sql = query($instrucao);
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						if(is_numeric($idCliente)){
							//inserindo no ponto_log
						    $instrucao = "insert into ponto_log ";
							$instrucao .= "(id_cliente, id_valor, quantidade, valor_ponto, id_usuario, data) values";
							$instrucao .= "('$idCliente', '$conta_itens', '1', '$valor', '".getIdCookieLogin($_COOKIE["login"])."', '".date("Y-m-d H:i:s")."')";
						    $sql = query($instrucao);
							
							$id_usuario = getIdCookieLogin($_COOKIE["login"]);
							$data = date('Y-m-d H:i:s');
							$acao = "Creditou ".real($valor)." em pontos para o cliente ".registro($idCliente, "cliente_fornecedor", "nome");
							$tabela_afetada = "ponto_log";
							$chave_principal = mysqli_insert_id($conexao);
							insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
						}
					}
				    
				    
					//atualizando dados na conta_itens
					$instrucao = "update conta_itens set valor='$valor', tipo_pagamento='$tipoPagamento', tipo_pagamento_sub='$tipo_pagamento_sub', data_pagamento='$dataQuitado', ";
					$instrucao .= "data_vencimento='$dataVencimento', id_caixa_movimento='$id_caixa_movimento', id_usuario='$login' where id='$conta_itens'";
					$acao = "Lançou uma parcela de conta no valor de R$ ".real($valor)." ".registro($tipoPagamento, 'pagamento_tipo', 'tipo_pagamento');
					if($tipo_pagamento_sub){
						$acao.= " ".registro($tipo_pagamento_sub, 'pagamento_tipo_sub', 'sub_tipo_pagamento');	
					}
					$acao.= " quitado no dia ".formataData($dataQuitado)." no caixa ".registro($id_caixa_movimento, 'caixa', 'nome').".";
					$sql = query($instrucao);
					
					//caso o valor lançado seja o da última parcela desta conta e o valor somado de todas as parcelas seja
					//menor do que o valor total da conta, então acrescentar automaticamente mais uma parcela
					$cod = "from conta_itens where id_conta=(select id_conta from conta_itens where id='$conta_itens')";
					$cod2 = "where id=(select id_conta from conta_itens where id='$conta_itens')";
					$instrucao = "select sum(valor) as valorSomado $cod";
					$sql = query($instrucao);
					extract(mysqli_fetch_assoc($sql));
					$instrucao = "select parcelas as parcelasCheck, valor as valorTotalConta, id as idConta from conta $cod2";
					$sql = query($instrucao);
					extract(mysqli_fetch_assoc($sql));
					
					$sql = query("select id as idCheck $cod");
					for($i=0; $i<$parcelasCheck; $i++){
						extract(mysqli_fetch_assoc($sql));
					}
					if($idCheck == $conta_itens and round($valorSomado, 2) < $valorTotalConta){
						//identifica se eh o limite das parcelas que o sistema permite fazer
						extract(mysqli_fetch_assoc(query("select parcelaMax from pagamento_parcela where id='1'")));
						if($i<$parcelaMax){
						    
							extract(mysqli_fetch_assoc(query("select data_vencimento as dataVencimento from conta_itens where id='$conta_itens'")));
							$dataVencimento = explode("-", date('Y-m-d'));
								
							//somando o mes e validando a data
							$dataVencimento[1]++;
							if ($dataVencimento[1] > 12) {
								$dataVencimento[1] = $dataVencimento[1] - 12;
								$dataVencimento[0]++;
							}
							$dia = $dataVencimento[2];
							while (checkdate($dataVencimento[1], $dia, $dataVencimento[0])==false){
								$dia--;
							}
							$data = $dataVencimento[0] . "-" . $dataVencimento[1] . "-" . $dia;
							
							$instrucao = "insert into conta_itens ";
							$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
							$instrucao .= "values ('$idConta', '1', '0','$data', '".getIdCookieLogin($_COOKIE["login"])."')";
							$sql = query($instrucao);
							
							//mudando a forma de pagamento se caso ela for a vista
							$instrucao = "update conta set forma_pagamento='2', parcelas='".($i+1)."' $cod2";
							$sql = query($instrucao);
						}
						
					}
					
				}
				
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$data = date('Y-m-d H:i:s');
				
				$tabela_afetada = "conta_itens";
				$chave_principal = $conta_itens;
				
				insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
		
				$info .= "Dados enviados.<br>";
			}
		} elseif ($op2 == "editar") {
		    
            if (empty($_POST["dataquitado"])) {
                $dataQuitado = date('Y-m-d');
            } else {
                $dataQuitado = formataDataInv($_POST["dataquitado"]);
            }
            
            //checar se é necessário editar os pontos
		    //se o cliente está cadastrado
		    $instrucao = "select entidade as idCliente from conta where ";
		    $instrucao .= "id=(select id_conta from conta_itens where id='$conta_itens') and ";
			$instrucao .= "tabela_entidade='cliente_fornecedor' and status='2'";
		    $sql = query($instrucao);
			if(mysqli_num_rows($sql)>0){
				extract(mysqli_fetch_assoc($sql));
				if(is_numeric($idCliente)){
					//editou no ponto_log
					//a condição valor_ponto>0 é justamente para não conflitar com as operações de debito que poderam ter identidades iguais
					//a unica coisa que as diferenciam e se a operação for de credito ou de débito. operações de crédito nunca terão identidades
					//iguais já as de débito isso poderá ocorrer.
				    $instrucao = "update ponto_log set valor_ponto='$valor' where id_valor='$conta_itens' and valor_ponto>0";
				    $sql = query($instrucao);
					
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$data = date('Y-m-d H:i:s');
					$acao = "Editou ".real($valor)." nos pontos para o cliente ".registro($idCliente, "cliente_fornecedor", "nome");
					$tabela_afetada = "ponto_log";
					$chave_principal = extract(mysqli_fetch_assoc(query("select id from ponto_log where id_valor and valor_ponto>0")));
					insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
				}
			}
            
            //fazendo a atualização da operação
            $instrucao = "update caixa_movimento set valor='$valor', id_caixa='$caixa', data='$dataQuitado ".date('H:i:s')."' where id=(select id_caixa_movimento from conta_itens where id='$conta_itens')";
            $sql = query($instrucao);
		    
			$instrucao = "update conta_itens set ";
			$instrucao .= "tipo_pagamento='$tipoPagamento', tipo_pagamento_sub='$tipo_pagamento_sub', data_vencimento='$dataVencimento', id_usuario='$login' where id='$conta_itens'";
			$sql = query($instrucao);
			
			$info .= "Dados alterados.<br>";
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
	
			$acao = "Editou uma parcela de conta no valor de R$ ".real($valor)." ".registro($tipoPagamento, 'pagamento_tipo', 'tipo_pagamento');
			if($tipo_pagamento_sub){
				$acao.= " ".registro($tipo_pagamento_sub, 'pagamento_tipo_sub', 'sub_tipo_pagamento');	
			}
			$acao.= " quitado no dia ".formataData($dataQuitado)."	.";
			$tabela_afetada = "conta_itens";
			$chave_principal = $conta_itens;
			
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
		
		} elseif ($op2 == "deletar") {
            	
			//deletando carteira de pontos se necessário
		    //deletando no ponto_log
			$instrucao = "select id as idPonto_log from ponto_log where id_valor=any(select id from conta_itens where id_conta='$conta_itens') and valor_ponto>0";
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
			$instrucao = "delete from ponto_log where id_valor=any(select id from conta_itens where id_conta='$conta_itens') and valor_ponto>0";
		    $sql = query($instrucao);
			
		    
            //deletando a operação caixa_moviemnto
            $instrucao = "delete from caixa_movimento where id=(select id_caixa_movimento from conta_itens where id='$conta_itens')";
            $sql = query($instrucao);
			
			$instrucao = "update conta_itens set ";
			$instrucao .= "data_pagamento=NULL, tipo_pagamento_sub=NULL, valor=NULL, id_caixa_movimento=NULL, id_usuario='$login' where id='$conta_itens'";
			$sql = query($instrucao);

			$info .= "Registro de conta deletado.<br>";

			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
			$acao = "Cancelou uma parcela de conta.";
			$tabela_afetada = "conta_itens";
			$chave_principal = $conta_itens;
			
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
			
		}
	}
}

if($info){
	if(isset($valida)){
		if($valida){
			$cor = "green";
		}else{
			$cor = "red";
		}
	}else{
		$cor = "green";
	}
	info($info, $cor);	
}

conta(base64_decode($conta));

if(registro(base64_decode($conta), 'conta', 'tabela_referido')=='pdv'){
	echo "<script type='text/javascript'>\n";
	echo "$(function(){\n";
	echo "$.post('inc/ajaxVerificarDespachar.inc.php',{\n";
	echo "conta: ".base64_decode($conta)."\n";
	echo "});\n";
	echo "});\n";
	echo "</script>";
}
//end all

include "templates/downLogin.inc.php";
?>