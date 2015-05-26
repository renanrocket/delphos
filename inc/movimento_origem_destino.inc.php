<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
	header('content-type: text/html; charset=ISO-8859-1');
	include "funcoes.inc.php";
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

		//deletar esse codigo apois migração do app.ado
		file_exists("../conecta.php") ?
		include "../conecta.php" : $conexao = null;
		$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
		extract(mysqli_fetch_assoc($sql));
		include "../".$conectasrc;
		//fim
	}else{
		echo "Impossível se conectar com o banco de dados.";
		die;
	}
	
	
	if(!$db) {
		// Show error if we cannot connect.
		echo 'ERROR: Não pode se conectar com o banco de dados.<br>'. mysqli_connect_error();
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $db->real_escape_string($_POST['queryString']);
			$inputId = $db->real_escape_string($_POST['id']);
			$op = $db->real_escape_string($_POST['op']);
			$id_produto = $db->real_escape_string($_POST['id_produto']);
			$qtd = $db->real_escape_string($_POST['qtd']);
            $selecao = $db->real_escape_string($_POST['selecao']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
			    $busca2 = "";
                switch ($selecao) {
                    case 'cliente_fornecedor':
                        $busca = "nome";
                        $busca2 = ", formato";
                        break;
                    case 'orcamento':
                        $busca = "id";
                        break;
                    case 'pdv':
                        $busca = "nome";
                        break;
                    case 'ordem_servico':
                        $busca = "id";
                        break;
                    default:
                        $busca = "o_d";
                        $busca2 = ", ex";
                        break;
                }				
                
				$instrucao = "SELECT id, $busca $busca2 FROM $selecao ";
				$instrucao .= "WHERE $busca LIKE '%$queryString%'";
				$instrucao .= "LIMIT 10";
				$query = $db->query($instrucao);
				if($query) {

					$li = "li1";
					while ($result = $query ->fetch_object()) {
						
                        switch ($selecao) {
                            case 'cliente_fornecedor':
                                $nome = $result->nome;
                                $formato = $result->formato;
                                $alerta = "com o Cliente / Fornecedor";
                                break;
                            case 'orcamento':
                                $nome = "Or&ccedil;amento n&ordm; ".$result->id;
                                $alerta = "no orcamento";
                                break;
                            case 'pdv':
                                $nome = $result->nome;
                                $alerta = "no PDV";
                                break;
                            case 'ordem_servico':
                                $nome = "Ordem de servi&ccedil;o n&ordm; ".$result->id;
                                $alerta = "na ordem de servico";
                                break;
                            default:
                                $nome = $result->o_d;
                                $alerta = "em";
                                break;
                        }
						
						$id = $result->id;
                        if($busca2==", ex"){
                            $ex = $result->ex;
                        }else{
                            if(isset($formato)){
                                if($formato=="fornecedor"){
                                    $ex = 1;
                                }else{
                                    $ex = 0;
                                }
                            }else{
                                $ex = 0;
                            }
                        }
						
						
						$sql = query("select * from movimento_produto where id_produto='$id_produto' and id_destino='$id' and tabela_destino='$selecao'");
						$quantidade1 = mysqli_num_rows($sql);
						$sql = query("select * from movimento_produto where id_produto='$id_produto' and id_origem='$id' and tabela_origem='$selecao'");
						$quantidade2 = mysqli_num_rows($sql);
						
						if($op=="origem"){
							$funcao2 = "preencherO";
							$alerta = "Voc&ecirc; n&atilde;o pode retirar o ".registro($id_produto, "produto", "nome");
							$alerta .= ", pois n&atilde;o existe produto suficiente $alerta $nome";
							if($ex=="1"){
								
								$total = "<b style='font-size:40px; line-height:15px;'>&infin;</b>";
								$check = 1;
								
							}elseif ($ex=="0"){
								
								$total = $check = $quantidade1 - $quantidade2;
								if($check<=0 or $qtd>$total){
									$alerta = "N&atilde;o existe produtos suficientes para voc&eacute; retirar.";
									$alerta .= " Voc&ecirc; deseja retirar $qtd e existe apenas $total deste produto $alerta $nome.";
									$check = 0;
								}
								
								
							}
							
							if($check<=0){
								$funcao1 = "alert('".$alerta."');";
							}else{
								$funcao1 = $funcao2."('$id', '$nome', '$inputId'); qtdDisabled($inputId);";
							}
						}else{
							
							$funcao2 = "preencherD";
							if($ex=="1"){
								$total = $quantidade1 - $quantidade2;
								if($total<0){
									$total = "<b style='font-size:40px; line-height:15px;'>&infin;</b>";
								}
								$check= 1;
							}elseif($ex=="0"){
								$total = $quantidade1 - $quantidade2;
								$check = 1;
							}
							$funcao1 = $funcao2."('$id', '$nome', '$inputId'); qtdDisabled($inputId);";
						}
						
	         			echo "<li  class='$li' onClick=\"$funcao1\">$nome<ul class='precoLista'><span id='p1'>$total</span>und.    </ul></li>";
	         			
						if($li=="li1"){
							$li= "li2";
						}else{
							$li="li1";
						}
	         		}
				} else {
					echo 'ERROR: Existe um problema com a query.<br>'.$instrucao."<br>".mysqli_error();
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
	mysqli_close($conexao);
?>