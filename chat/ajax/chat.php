<?php

	include_once "../../inc/funcoes.inc.php";
	date_default_timezone_set('America/Sao_Paulo');
		
	if(isset($_COOKIE["id_empresa"])){
		//includes do php para todas as paginas
		$conn = TConnection::open("gestor");
	
		$criterio = new TCriteria;
		$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

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

	}else{
		echo "Impossível se conectar com o banco de dados.";
		die;
	}
	
	
	
	$acao = $_POST['acao'];
	$de = $_COOKIE["id_empresa"]."_".getIdCookieLogin($_COOKIE['login']);
	$conn = TConnection::open(ALCUNHA);
	$connChat = TConnection::open('chat');
	$connGestor = TConnection::open('gestor');
	
	switch($acao){
		case 'inserir':
			$para = $_POST['para'];
			$mensagem = strip_tags($_POST['mensagem']);
			
			$sql = new TSqlInsert;
			$sql->setEntity("mensagens");
			$sql->setRowData('id_de', $de);
			$sql->setRowData('id_para', $para);
			$sql->setRowData('data', date('Y-m-d H:i:s'));
			$sql->setRowData('mensagem', $mensagem);
			if($result = $connChat->query($sql->getInstruction())){
				echo "<li class=\"voce\"><span>Voce: </span><p>$mensagem</p></li>";
			}

			//logar
			$conn = TConnection::open(ALCUNHA);

			$criterio = new TCriteria;
			$criterio->add(new TFilter("login", "=", $_COOKIE["login"]));
			
			$sql = new TSqlUpdate;
			$sql->setEntity('usuario');
			$sql->setRowData('online', 1);
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());	

			
		break;
		
		case 'verificar':
			$ids = $contatos = '';
			$retorno = array();
			extract($_POST);
			
			if($ids == ''){
				$retorno['mensagens'] = '';
			}else{
				foreach($ids as $indice => $ID){

					
					$criterio1 = new TCriteria;
					$criterio1->add(new TFilter('id_de', '=', $de));
					$criterio1->add(new TFilter('id_para', '=', $ID));

					$criterio2 = new TCriteria;
					$criterio2->add(new TFilter('id_de', '=', $ID));
					$criterio2->add(new TFilter('id_para', '=', $de));

					$criterio = new TCriteria;
					$criterio->add($criterio1, TExpression::OR_OPERATOR);
					$criterio->add($criterio2, TExpression::OR_OPERATOR);



					$sql = new TSqlSelect;
					$sql->setEntity('mensagens');
					$sql->addColumn('*');
					$sql->setCriteria($criterio);
					$result = $connChat->query($sql->getInstruction());
						
					for($i=0, $chat = ''; $i<$result->rowCount(); $i++){
						$row = $result->fetch(PDO::FETCH_ASSOC);
						extract($row);

						$DE = explode('_', $id_de);
						
						$connGestor = TConnection::open('gestor');
						$criterio = new TCriteria;
						$criterio->add(new TFilter('id', '=', $DE[0]));

						$sql = new TSqlSelect;
						$sql->setEntity('cliente');
						$sql->addColumn('alcunha');
						$sql->setCriteria($criterio);
						$resultDE = $connGestor->query($sql->getInstruction());
						$retorno['mensagens'][$ID] = $sql->getInstruction();
						
						if($result->rowCount()){
							$row = $resultDE->fetch(PDO::FETCH_ASSOC);
							extract($row);
							
							$nomeDE = getNomeCookieLogin(registro($DE[1], "usuario", "login", "id", $alcunha), false, $alcunha);
							$seunome = getNomeCookieLogin($_COOKIE["login"], false);
							if( $nomeDE == $seunome and 
								registro($DE[1], "usuario", "login", "id", $alcunha) == $_COOKIE["login"] and
								$DE[1] == registro($_COOKIE["login"], "usuario", "id", "login", ALCUNHA)
								){
								$nomeDE = "Voce";
								$class= 'voce';
							}else{
								$class= '';
							}
							
							$nomeDE = ucwords($nomeDE);

							$chat .= "<li class=\"$class\"><span>$nomeDE:</span><p>$mensagem</p></li>";
						}
						

					}
					$retorno['mensagens'][$ID] = $chat;

				}
			}

			//checando se contatos está ou não online
			if($contatos == ''){
				$retorno['online'] = '';
				$retorno['offline'] = '';
			}else{
				foreach ($contatos as $indece => $ID) {
					$var = explode('_', $ID);

					$criterio = new TCriteria;
					$criterio->add(new TFilter('id', '=', $var[0]));

					$sql = new TSqlSelect;
					$sql->setEntity('cliente');
					$sql->addColumn('alcunha');
					$sql->setCriteria($criterio);
					$result = $connGestor->query($sql->getInstruction());

					if($result->rowCount()){
						$row = $result->fetch(PDO::FETCH_ASSOC);
						extract($row);
						$connAlcunha = TConnection::open($alcunha);
						$criterio = new TCriteria;
						$criterio->add(new TFilter('id', '=', $var[1]));

						$sql = new TSqlSelect;
						$sql->setEntity('usuario');
						$sql->addColumn('online');
						$sql->setCriteria($criterio);
						$result = $connAlcunha->query($sql->getInstruction());
						if($result->rowCount()){
							$row = $result->fetch(PDO::FETCH_ASSOC);
							extract($row);
							if($online){
								$retorno['online'][] = $ID;
								//$retorno['offline'][] = '';
							}else{
								//$retorno['online'][] = '';
								$retorno['offline'][] = $ID;
							}
						}
					}
				}
			}

			//marcar online o seu status
			$criterio = new TCriteria;
			$criterio->add(new TFilter('login', '=', $_COOKIE["login"]));
			
			$sql = new TSqlUpdate;
			$sql->setEntity('usuario');
			$sql->setRowData('online', 1);
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			//verificar mensagens não lidas
			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_para', '=', $de));
			$criterio->add(new TFilter('lido', '=', 0));
			$criterio->setProperty('group', 'id_de');

			$sql = new TSqlSelect;
			$sql->setEntity('mensagens');
			$sql->addColumn('id_de');
			$sql->setCriteria($criterio);
			$result = $connChat->query($sql->getInstruction());

			if($result->rowCount()){
				for($i=0; $i<$result->rowCount(); $i++){
					$row = $result->fetch(PDO::FETCH_ASSOC);
					extract($row);

					$retorno['nao_lidos'][] = $id_de;
				}
			}else{
				$retorno['nao_lidos'] = $sql->getInstruction();
			}
			
			$retorno = json_encode($retorno);
			echo $retorno;

		break;
		
		case 'mudar_status':

			$criterio = new TCriteria;
			$criterio->add(new TFilter("id_de", "=", $_POST['user']));
			$criterio->add(new TFilter("id_para", "=", $de));
			
			$sql = new TSqlUpdate;
			$sql->setEntity('mensagens');
			$sql->setRowData('lido', 1);
			$sql->setCriteria($criterio);
			$result = $connChat->query($sql->getInstruction());

			
		break;

		case 'deslogar':

			$conn = TConnection::open(ALCUNHA);

			$criterio = new TCriteria;
			$criterio->add(new TFilter("login", "=", $_COOKIE["login"]));
			
			$sql = new TSqlUpdate;
			$sql->setEntity('usuario');
			$sql->setRowData('online', 0);
			$sql->setRowData('online_data', date('Y-m-d H:i:s'));
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			
		break;

		case 'turn_off':
			extract($_POST);
			foreach ($contatos as $indece => $ID) {
				$var = explode('_', $ID);

				$criterio = new TCriteria;
				$criterio->add(new TFilter('id', '=', $var[0]));

				$sql = new TSqlSelect;
				$sql->setEntity('cliente');
				$sql->addColumn('alcunha');
				$sql->setCriteria($criterio);
				$result = $connGestor->query($sql->getInstruction());

				if($result->rowCount()){
					$row = $result->fetch(PDO::FETCH_ASSOC);
					extract($row);
					$connAlcunha = TConnection::open($alcunha);
					$criterio = new TCriteria;
					$criterio->add(new TFilter('id', '=', $var[1]));

					$sql = new TSqlSelect;
					$sql->setEntity('usuario');
					$sql->addColumn('online');
					$sql->setCriteria($criterio);
					$result = $connAlcunha->query($sql->getInstruction());
					if($result->rowCount()){
						$row = $result->fetch(PDO::FETCH_ASSOC);
						extract($row);
						if($online=='1'){
							$sql = new TSqlUpdate;
							$sql->setEntity('usuario');
							$sql->setRowData('online', 0);
							$sql->setRowData('online_data', date('Y-m-d H:i:s'));
							$sql->setCriteria($criterio);
							$result = $connAlcunha->query($sql->getInstruction());
						}
					}
				}
			}

			
		break;

	}
?>