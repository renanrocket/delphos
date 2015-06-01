<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
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
		extract($_POST);
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			
			$queryString = $db->real_escape_string($_POST['queryString']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString)>0 and $op!="observacoes"){
				
				$li = "li1";

				if($op=="mobile"){
					$limite = 1;
				}else{
					$limite = 10;
				}
				
				$instrucao = "SELECT id as idProduto from produto ";
				$instrucao .= "WHERE ";
				//produto
				$instrucao .= "(produto.nome LIKE '%$queryString%' or produto.modelo LIKE '%$queryString%' or produto.cod_barra='$queryString' ";
				$instrucao .= "or produto.id_marca like (select id from marca where nome like '%$queryString%' LIMIT $limite) ";
				$instrucao .= "or produto.id_categoria like (select id from categoria where nome like '%$queryString%' LIMIT $limite) ";
				$instrucao .= "or produto.id_subcategoria like (select id from sub_categoria where nome like '%$queryString%' LIMIT $limite)) ";
				$instrucao .= "and busca='1' and status='1' LIMIT $limite";

				
				$queryProduto = $db->query($instrucao);
				
				if($queryProduto) {
					
					while ($result = $queryProduto ->fetch_object()) {
						
						//calcular o preco do produto
						$preco1 = precoProduto($result->idProduto, true, false);
						$preco2 = precoProduto($result->idProduto, false, true);
						
						$sql = query("select * from produto where id=".$result->idProduto);
						extract(mysqli_fetch_assoc($sql));
						
						//imagem
						$sql = query("select * from produto_imagem where id_produto=".$result->idProduto);
						for($i=0, $tooltip=null; $i<mysqli_num_rows($sql); $i++){
							extract(mysqli_fetch_assoc($sql));
							if(file_exists("../".$miniatura)){
								$tooltip .= "<img ".pop("imagem.php?id=$result->idProduto&atributo=produto_imagem&referencia=id_produto", 600, 600)." src=\"$miniatura\">";
							}
						}
                        $precoPC = "<span id='p1'>R$ ".real($preco1)."</span>";
                        $precoMB = "R$ ".real($preco1);
                        $sqlHP = query("select * from administrativo where taxonomia='happyhour' and valor='1'");
                        if(mysqli_num_rows($sqlHP)>0){
                            //happyhour
                            $sqlHP = query("select hp_hora_inicio, hp_hora_final, hp_dias from produto where id='$result->idProduto'");
                            extract(mysqli_fetch_assoc($sqlHP));
                            if($hp_hora_inicio and $hp_hora_final and $hp_dias){
                                $hp_dias = explode(',', $hp_dias);
                                $hpI = explode(':', $hp_hora_inicio);
                                $hpF = explode(':', $hp_hora_final);
                                if(in_array(date('w'), $hp_dias)){
                                    date_default_timezone_set('America/Fortaleza');
                                    if(mktime($hpI[0],$hpI[1],0,0,0,0)<=mktime(date('H'),date('i'),0,0,0,0) and
                                        mktime(date('H'),date('i'),0,0,0,0)<=mktime($hpF[0],$hpF[1],0,0,0,0)){
                                        //happyhour ativo então pegar o preço do desconto.
                                        $precoPC ="<span id='p1'>R$ ".real(precoProduto($result->idProduto, false, true))."</span><br>";
                                        $precoPC.= "Em Happy Hour <span style='text-decoration:line-through; font-size:13px;'>R$ ".real(precoProduto($result->idProduto, true))."</span> ";

                                    }
                                }
                            }
                        }
						if($op=="mobile"){
							$js = "onClick=\"preencherMobile('$nome','$cod_barra');\"";
							echo "<label $js>$nome $precoMB</label>";
						}else{
							if($li=="li1"){
								$li= "li2";
							}else{
								$li="li1";
							}
							$js = "onClick=\"preencher('$nome','$cod_barra');\"";
							echo "<li class='$li'>";
		         			echo "<table class='listaItensOrcamento'>";
		         			echo "<tr>";
		         			echo "<td rowspan='2' class='nomeLista'>$tooltip<span $js>$nome</span></td>";
		         			echo "<td class='precoLista' align='right'>$precoPC</td>";
		         			echo "</tr>";
		         			if($contabilizar_estoque){
								echo "<tr>";
								echo "<td class='precoLista' align='right'><span id='p2'>Possuímos $qtd_estoque em estoque.</span></td>";
								echo "</tr>";	
							}else{
								echo "<tr>";
								echo "<td class='precoLista' align='right'><span id='p2'>Produto não contabilizado em estoque.</span></td>";
								echo "</tr>";
							}
		         			echo "</table>";
		         			echo "</li>";
						}
						
	         		}
				}

			} elseif(strlen($queryString)>0 and $op=="observacoes"){

				$instrucao = "update pdv set observacoes='$obs' where id='$id'";
				$sql = query($instrucao);
			}
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
	mysqli_close($conexao);
?>