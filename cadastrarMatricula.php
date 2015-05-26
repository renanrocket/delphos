<?php
include "templates/upLogin.inc.php";

$op = null;

foreach ($_POST as $key => $value) {
	$_POST[$key] = str_replace("'", " ", $value);
}

extract($_POST);
extract($_GET);

$cont = count($_POST);
$array = array_keys($_POST);

?>
 <script>
  $(function() {
    $( "#tabs" ).tabs();
  });
  </script>
</head>
<?php

function matricula($ID = null){
	
	//js para esta pagina em especifico
	echo "<script src='js/cadastrarMatriculaFiltro.js' type='text/javascript'></script>";
	$cod_form1 = null;
	if($ID){	
		matriculaStatus($ID);
		
		$cod_form1 .= "<input type='hidden' name='op' value='editar'>";
		$cod_form1 .= "<input type='hidden' name='idMatricula' value='$ID'>";
		$sql = query("select * from matricula where id='$ID'");
		extract(mysqli_fetch_assoc($sql));
		$statusMatricula = $status;
		$matriculaObservacoes = $observacoes;
		
		if(is_numeric($id_cliente)){
			$sql = query("select * from cliente_fornecedor where id='$id_cliente'");
			extract(mysqli_fetch_assoc($sql));
		}else{
			$id = $formato = $tipo = $razao_social = $cpf_cnpj = $rg_ie = $data_nascimento = $email = $observacoes = $latitude = $longitude = 
			$fone2 = $endereco = $numero = $bairro = $cidade = $estado = $cep = $referencia = $matriculaObservacoes = $status = null;
			$nome = $id_cliente;
			$fone1 = $telefone;
		}
		
		$cod =  "";
		$sqlPlanosAssinatura = query("select max(id) as idPlanoAssinatura from matricula_plano_assinatura where id_matricula='$ID'");
		extract(mysqli_fetch_assoc($sqlPlanosAssinatura));
		$instrucao = "select id as idConta from conta where entidade='$id_cliente' and referido='$idPlanoAssinatura' and tabela_referido='plano_assinatura'";
		
		$sqlConta = query($instrucao);
		extract(mysqli_fetch_assoc($sqlConta));
		$cod .= "<a href='pesquisaConta2.php?conta=".base64_encode($idConta)."' title='Visualizar Conta $idConta'><img src='img/icones/pesquisaConta.png'></a> ";
		$sqlPlanosAssinatura = query("select id as idPlanoAssinatura from matricula_plano_assinatura where status='1' and id_matricula='$ID' and id<>'$idPlanoAssinatura' order by id desc");
		for($i=0; $i<mysqli_num_rows($sqlPlanosAssinatura); $i++){
			extract(mysqli_fetch_assoc($sqlPlanosAssinatura));
			$instrucao = "select id as idConta from conta where entidade='$id_cliente' and referido='$idPlanoAssinatura' and tabela_referido='plano_assinatura'";
			$sqlConta = query($instrucao);
			extract(mysqli_fetch_assoc($sqlConta));
			$instrucao = "select * from conta where id='$idConta' and ( valor > (select sum(valor) from conta_itens where id_conta='$idConta') or ";
			$instrucao .= " (select sum(valor) from conta_itens where id_conta = '$idConta') is null)";
			$sqlConta = query($instrucao);
			if(mysqli_num_rows($sqlConta)){
				$cod .= "<a href='pesquisaConta2.php?conta=".base64_encode($idConta)."' title='Visualizar Conta $idConta'><img style='width:43px;' src='img/icones/pesquisaConta.png'></a> ";	
			}
		}
		$cod_form1 .= "<tr>";
			$cod_form1 .= "<th colspan='6'>";
			$tabs1 = "Matricula $ID ";
			switch ($statusMatricula) {
				case '0':
					$tabs1 .= "Inativa";
					break;
				case '1':
					$tabs1 .= "Ativa";
					break;
				
				default:
					$tabs1 .= "Pausada";
					break;
			}
			$cod_form1 .= "<div style='float:right;'>";
			
			if($statusMatricula==2){
				$msg = "Deseja mesmo play está matricula?<br><br>";
				$msg .= "<a class='aSubmit' href='cadastrarMatricula.php?op=play&id=".base64_encode($ID)."'>Sim</a> ";
				$msg .= "<a class='aSubmit' href='#' onclick=\"infoApagar();\">Não</a>";
				info($msg, "green", null, "none", "play");
				$cod_form1 .= "<a title='Play matrícula $ID' href='#' ".info(null, null, null, null, null, "play")."><img src='img/matricula_play.png'></a>";
			}elseif($statusMatricula==1){
				$msg = "<script type='text/javascript'>";
					$msg .= "function getLink() {";
						$msg .= "DAY = 1000 * 60 * 60 * 24;";
						$msg .= "data1 = $('input[name=\"data_inicio_pausa\"]').val();";
						$msg .= "data2 = $('input[name=\"data_regresso_pausa\"]').val();";
						$msg .= "hoje = '".date('d/m/Y')."';";
						$msg .= "var nova1 = data1.toString().split('/');";
						$msg .= "Nova1 = nova1[1]+\"/\"+nova1[0]+\"/\"+nova1[2];";
						$msg .= "var nova2 = data2.toString().split('/');";
						$msg .= "Nova2 = nova2[1]+\"/\"+nova2[0]+\"/\"+nova2[2];";
						$msg .= "var hoje = hoje.toString().split('/');";
						$msg .= "Hoje = hoje[1]+\"/\"+hoje[0]+\"/\"+hoje[2];";
						$msg .= "d1 = new Date(Nova1);";
						$msg .= "d2 = new Date(Nova2);";
						$msg .= "h = new Date(Hoje);";
						$msg .= "days_passed = Math.round((d2.getTime() - d1.getTime()) / DAY);";
						$msg .= "days_hoje = Math.round((h.getTime() - d1.getTime()) / DAY);";
						$msg .= "if(data1.length<10){";
							$msg .= "$('input[name=\"data_inicio_pausa\"]').attr('class', 'avisoInput');";
							$msg .= "$('#dias').html('Verifique a data do inicio da pausa.');";
						$msg .= "}else if(data2.length<10){";
							$msg .= "$('input[name=\"data_regresso_pausa\"]').attr('class', 'avisoInput');";
							$msg .= "$('#dias').html('Verifique a data do regresso da pausa.');";
							$msg .= "$('input[name=\"data_inicio_pausa\"]').attr('class', '');";
						$msg .= "}else if(days_passed<=0){";
							$msg .= "$('input[name=\"data_inicio_pausa\"]').attr('class', 'avisoInput');";
							$msg .= "$('input[name=\"data_regresso_pausa\"]').attr('class', 'avisoInput');";
							$msg .= "$('#dias').html('A data de inicio deve ser menor do que a data de regresso.');";
						$msg .= "}else if(days_hoje<0){";
							$msg .= "$('input[name=\"data_inicio_pausa\"]').attr('class', 'avisoInput');";
							$msg .= "$('#dias').html('A data de inicio deve ser hoje ou um dia anterior.');";
						$msg .= "}else{";
							$msg .= "$('input[name=\"data_inicio_pausa\"]').attr('class', '');";
							$msg .= "$('input[name=\"data_regresso_pausa\"]').attr('class', '');";
							$msg .= "$('#dias').html(days_passed+' dias de pausa.');";
							$msg .= "$('#linkPausa').attr('href', 'cadastrarMatricula.php?id=".base64_encode($ID)."&op=pausa&data1='+data1+'&data2='+data2);";
						$msg .= "}";
					$msg .= "}";
				$msg .= "</script>";
				$msg .= "Para pausar está matricula, insira as datas de início e regresso:<br>";
				$msg .= "Data de inicio ".inputData("formularioMatricula", "data_inicio_pausa", null, date('d/m/Y'))."<br>";
				$msg .= "Data de regresso ".inputData("formularioMatricula", "data_regresso_pausa", null)."<br>";
				$msg .= "<span id='dias'></span><br><br>";
				$msg .= "<a class='aSubmit btnEnviar' id='linkPausa' href='#' onmouseover='getLink();'>Enviar</a> ";
				$msg .= "<a class='aSubmit btnDeletar' href='#' onclick=\"infoApagar();\">Cancelar</a>";
				info($msg, "green", null, "none", "pausa");
				$cod_form1 .= "<a title='Pausar matrícula $ID' href='#' ".info(null, null, null, null, null, "pausa")."><img src='img/matricula_pause.png'></a>";
			}
			
			$cod_form1 .= arqVisualizar("itensForm3", "Visualizar / Esconder restante do cadastro do cliente");
			
			if(getCredencialUsuario("pesquisaConta.php")){
				$cod_form1 .= $cod;
			}
			if($statusMatricula==1 or $statusMatricula==2){
				$msg = "Deseja realmente inativar está matricula?<br><br>";
				$msg .= "<a class='aSubmit' href='cadastrarMatricula.php?op=inativar&id=".base64_encode($ID)."'>Sim</a> ";
				$msg .= "<a class='aSubmit' href='#' onclick=\"infoApagar();\">Não</a>";
				info($msg, "green", null, "none", "confirmar");
				$cod_form1 .= "<a title='Inativar matrícula $ID' href='#' ".info(null, null, null, null, null, "confirmar")."><img src='img/deletar.png'></a>";
			}elseif($statusMatricula==0){
				$msg = "Para ativar está matrícula adicione um novo plano.";
				info($msg, "green", null, "none", "confirmar");
				$cod_form1 .= "<a href='#thImg' title='Ativar matrícula.' onclick='maisPlano(\"true\"); $(\".confirmar\").show();'><img src='img/inserir.png'></a>";
			}
			$cod_form1 .= "</div>";
			$cod_form1 .= "</th>";
		$cod_form1 .= "</tr>";
	}else{
		$cod_form1 .= "<input type='hidden' name='op' value='novo'>";
		$id = $formato = $tipo = $nome = $razao_social = $cpf_cnpj = $rg_ie = $data_nascimento = $email = $observacoes = 
		$fone1 = $fone2 = $endereco = $numero = $bairro = $cidade = $estado = $cep = $referencia = $matriculaObservacoes = 
		$status = $latitude = $longitude = null;
		$tabs1 = 'Nova Matricula';
	}

	echo "<form name='formularioMatricula' method='post' action='cadastrarMatricula.php' enctype='multipart/form-data' onSubmit='return (filtroClienteFornecedor() && filtroMatricula() ? true: false);'>";
	echo "<div id='tabs' style='width:70%'>";
		echo "<ul>";
			echo "<li><a href='#tabs-1'>$tabs1</a></li>";
	    	echo "<li><a href='#tabs-2'>Ficha de Anamnese</a></li>";
	    	echo "<li><a href='#tabs-3'>Ficha de Avaliação física</a></li>";
	    	echo "<li><a href='#tabs-4'>Ficha de exercícios</a></li>";
		echo "</ul>";
	
		echo "<div id='tabs-1' style='width:100%;margin:0px; padding:0px;'>";
			echo "<table id='gradient-style' style='width:100%;margin:0px; padding:0px;'>";
				echo $cod_form1;
				

				$cliente = new cliente_fornecedor($id, $formato, $tipo, $nome, $razao_social, $cpf_cnpj, $rg_ie, $data_nascimento, $email,
				$fone1, $fone2, $endereco, $numero, $bairro, $cidade, $estado, $cep, $referencia, $observacoes, $latitude, $longitude, $status);
				echo $cliente->formulario(true);
				
				$style="";
				$class="";
				
				if($ID){
					
					echo "<tr>";
						if(getCredencialUsuario("pesquisaConta.php")){
							echo "<th id='thImg'><a href='#thImg' title='Adicionar um novo Plano / Assinatura.' onclick='maisPlano(\"true\");'><img width='30' src='img/mais.png'></a></th>";
						}else{
							echo "<th></th>";
						}
						echo "<input type='hidden' name='novoPlano' value='false'>";
						echo "<th colspan='5'>Planos / Assinaturas contratadas</th>";
					echo "</tr>";
					echo "<tr>";
						echo "<td></td>";
						echo "<td>Planos / Assinaturas</td>";
						echo "<td>Valor</td>";
						echo "<td>Data de inicio</td>";
						echo "<td>Data de termino</td>";
						echo "<td>Dias de treino</td>";
					echo "</tr>";
					$sql = query("select * from matricula_plano_assinatura where id_matricula='$ID'");
					for($i=0; $i<mysqli_num_rows($sql); $i++){
						extract(mysqli_fetch_assoc($sql));
						echo "<tr>";
							if(getCredencialUsuario("pesquisaConta.php")){
								echo "<td>";
									echo "<a href='#' title='Editar este plano.' ".pop("cadastrarMatriculaPlano.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=editar&plano=".base64_encode($id), 570, 230, false)."><img width='30' src='img/refresh.png'></a>";
									$sqlPausa = query("select * from matricula_pausa where id_matricula_plano_assinatura='$id'");
									if(mysqli_num_rows($sqlPausa)>0){
										$msg = "<table>";
										
										$msg .= "<tr>";
											$msg .= "<th colspan='5'>Histórico de pausa deste plano</th>";
										$msg .= "</tr>";
										$msg .= "<tr>";
											$msg .= "<th>Data de inicio</th>";
											$msg .= "<th>Data de regresso</th>";
											$msg .= "<th>Dias de pausa</th>";
											$msg .= "<th>Usuário</th>";
											$msg .= "<th>Data</th>";
										$msg .= "</tr>";
										for($j=0; $j<mysqli_num_rows($sqlPausa); $j++){
											$mp = mysqli_fetch_assoc($sqlPausa);
											$msg .= "<tr>";
												$msg .= "<td>".formataData($mp["data_inicio"])."</td>";
												$msg .= "<td>".formataData($mp["data_termino"])."</td>";
												$msg .= "<td>".subtrairDatas($mp["data_inicio"], $mp["data_termino"])."</td>";
												$msg .= "<td>".getNomeUsuarioCliente($id_usuario, "id", "usuario", false)."</td>";
												$msg .= "<td>".formataData($mp["data"], false, true)."</td>";
											$msg .= "</tr>";
										}
										$msg .= "</table>";
										info($msg, "green", null, "none", "visualizarPausa");
										echo "<a href='#visualizarPausa' title='Visualizar pausas deste plano.' ".info(null, null, null, null, null, "visualizarPausa").">";
										echo "<img width='30' src='img/historico.png'></a>";
									}
									if($i>0 and $status=="1"){
										$msg = "Você deseja realmente deletar este Plano / Assinatura e suas contas a receber?<br><br>";
										$msg .= "<a class='aSubmit' href='cadastrarMatricula.php?op=deletar&plano=".base64_encode($id)."'>Sim</a>";
										$msg .= "<a class='aSubmit' href='#' onclick=\"infoApagar();\">Não</a>";
										info($msg, "green", null, "none", "deletarPlano");
										echo "<a href='#deletarPlano' title='Deletar este plano.' ".info(null, null, null, null, null, "deletarPlano").">";
										echo "<img width='30' src='img/deletar.png'></a>";
										
										//echo "<a href='#' title='Deletar este plano.' ".pop("cadastrarMatriculaPlano.php?op=deletar&plano=".base64_encode($id), 570, 230)."><img width='30' src='img/deletar.png'></a>";	
									}
								echo "</td>";
							}else{
								echo "<td></td>";
							}
							
							$status==2 ? $style= "text-decoration: line-through;" : $style = ""; //status==2 deletado
							echo "<td style='white-space:nowrap; $style'>".registro($id_plano_assinatura, "plano_assinatura", "nome")."</td>";
							echo "<td style='white-space:nowrap; $style'>R$ ".real($valor)."</td>";
							echo "<td style='$style' >".formataData($data_inicio)." </td>";
							
							if($data_termino!="0000-00-00"){
								echo "<td style='$style'>".formataData($data_termino)."</td>";
								echo "<td style='$style'><b>".subtrairDatas($data_inicio, $data_termino)." dias </b> ";
								if($status==2){
									echo "Plano deletado";
								}
								echo "</td>";
							}else{
								//previsao para o fim do plano
								$fim_previsao = subtrairDatas(date('Y-m-d'), $data_previsao);
								//fim da pausa
								if(isset($mp)){
									$fim_pausa = subtrairDatas(date('Y-m-d'), $mp["data_termino"]);

								}else{
									$fim_pausa = -1;
								}
								
								//caso este plano tenha sido pausado
								if(isset($mp) and $fim_pausa>=0){
									if($fim_pausa>1){
										//$text = "Previsto para finalizar ".formataData($data_carencia)."<br>";
										$text = "Faltam <b>$fim_pausa dias</b> para finalizar o período de pausa.<br>";
									}elseif($fim_pausa==0){
										$text = "<b class='alerta'>Hoje finaliza a pausa do plano.</b>";
									}
									$text.= "Faltam <b>$fim_previsao dias</b> para finalizar o plano.";
								}else{
									//caso não tenha pausa
									if($fim_previsao>1){
										$text = "Previsto para finalizar ".formataData($data_previsao)."<br>";
										$text .= "Faltam <b>$fim_previsao dias</b> para finalizar o plano.";
									}elseif($fim_previsao==0){
										$text = "<b class='alerta'>Hoje finaliza o plano.</b>";
									}elseif($fim_previsao<0){
										$text = "Já se passou <b class='alerta'>".($fim_previsao * -1)." dias</b> que o plano foi finalizado.<br>";
										$text.= "O aluno está liberado para frequentar até ".formataData($data_carencia);
									}else{
										$text = null;
									}
								}

								echo "<td colspan='2' align='center'>$text</td>";
							}
							
						echo "</tr>";

						//resetar variavel da pausa para q n interfira nos outros historicos de planos
						unset($mp);
					}
					$style="display:none;";
					$class="class='avisoInput'";
				}

				echo "<tr class='trPlano' style='$style'>";
				echo "<td colspan='2'>Plano<br>";
				echo "<select $class name='planoAssinatura' onchange='showPlano(this.value); showDataTermino(this.value); showPlanoValor(this.value)'>";
				echo "<option value='0'>--</option>";
				$sql = query("select * from plano_assinatura where status='1'");
				for($i=0; $i<mysqli_num_rows($sql); $i++){
					extract(mysqli_fetch_assoc($sql));
					echo "<option value='$id'>$nome R$ ".real($valor)."</option>";
				}
				echo "</select>";
				echo "</td>";
					echo "<td colspan='4' id='tdPlano'>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan='6'> ";
					echo arqVisualizar("obsMatricula", "Clique para mostrar as observações desta matrícula.", 1);
					echo "Observações desta matrícula";
					echo "<div class='obsMatricula' style='display:none;'><textarea class='ckeditor' name='matriculaObservacoes'>$matriculaObservacoes</textarea></div>";
					echo "</td>";
				echo "</tr>";
				
				
				echo "<tr class='trPlano' style='$style'>";
					echo "<td colspan='2'>";
						echo "Data inicio do Plano<br>";
						/*
						if($ID){
							$sql = query("select max(id), data_inicio from matricula_plano_assinatura where status='1' and id_matricula='$ID'");
							extract(mysqli_fetch_assoc($sql));
							$dataInicio = formataData($data_inicio);
						}else{
							$dataInicio = date('d/m/Y');
						}
						*/
						$dataInicio = date('d/m/Y');
						echo inputData("formularioMatricula", "dataInicio", null, $dataInicio);
					echo "</td>";
					echo "<td colspan='3' id='tdDataTermino'>";
						echo "Data termino do Plano<br>";
						if($ID){
							$sql = query("select max(id), data_termino, data_previsao from matricula_plano_assinatura where status='1' and id_matricula='$ID'");
							extract(mysqli_fetch_assoc($sql));
							if($data_termino=="0000-00-00"){
								echo "Previsto para <b>".formataData($data_previsao)."</b>";
							}else{
								echo formataData($data_termino);
							}
						}
					echo "</td>";
					echo "<td colspan='1'>";
						echo "Valor do Plano<br>";
						if($ID){
							$sql = query("select max(id), valor from matricula_plano_assinatura where status='1' and id_matricula='$ID'");
							extract(mysqli_fetch_assoc($sql));
							$valorPlano = $valor;
						}else{
							$valorPlano = 0;
						}
						echo "<input type='text' name='valorPlano' value='".real($valorPlano)."' class='totalValor preco' ".mascara("Valor2")." style='width:80%;'>";
						echo "<div id='divPlanoPreco' style='display:inline-block;'></div>";
					echo "</td>";
				echo "</tr>";
				
				//tipo e forma de pagamento
				echo "<tr class='trPlano' style='$style'>";
					if($ID){
						$instrucao = "select * from conta where referido=(select max(id) from matricula_plano_assinatura where id_matricula='$ID') and tabela_referido='plano_assinatura'";
						$sql= query($instrucao);
						if(mysqli_num_rows($sql)>0){
							extract(mysqli_fetch_assoc($sql));
							if($parcelas){
								$display2= "";
							}
						}	
					}else{
						$id = $endidade = $id_referido = $referido = $tipo = $valor = $forma_pagamento = $parcelas = $nota_fiscal = $data = $id_usuario = null;
					}
					echo "<td colspan='5' align='right'>Forma de Pagamento</td>";
					echo "<td><select name='pgaForma' onchange=\"showParcela(this.value);\">".opcaoSelect("pagamento_forma", 1, "Ativo", $forma_pagamento)."</select></td>";
				echo "</tr>";
				echo "<tr class='trPlano' style='$style'>";
					$cod = "";
					$sql = query("select * from conta_itens where id_conta='$id'");
					if(mysqli_num_rows($sql)>0){
						extract(mysqli_fetch_assoc($sql));
						
						if($tipo_pagamento_sub){
							$display1= "";
							$sql= query("select * from pagamento_tipo_sub where id_pagamento_tipo in (select id_pagamento_tipo from pagamento_tipo_sub where id='$tipo_pagamento_sub' and status='Ativo')");
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
				
				echo "<tr class='trPlano' id='pagamentoTipoSub' style='$style $display1'>";
					echo $cod;
				echo "</tr>";
				
				echo "<tr class='trPlano' id='parcela' style='$style $display2'>";
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
				echo "<td colspan='5' align='right'><input type='submit' class='btnEnviar' value='Enviar'></td>";
				echo "<td align='left'><input type='reset' value='Cancelar'></td>";
				echo "</tr>";
				echo "</table>";
			echo "</div>";//div tabs1


			echo "<div id='tabs-2' style='width:100%;margin:0px; padding:0px;'>";
			echo "<table id='gradient-style' style='width:100%;margin:0px; padding:0px;'>";
						
				if($ID){
					echo "<tr>";
						$sqlAnamnese = query("select * from matricula_anamnese_atributo_padrao where status='1'");
						if(mysqli_num_rows($sqlAnamnese)){
							echo "<th><a href='#' ".pop("fichaAnamnese.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&id_matricula=".base64_encode($ID), 800, 600)." title='Adicionar uma novo Ficha de Anamnese.'><img width='30' src='img/mais.png'></a></th>";
							echo "<th colspan='5'>Fichas de Anamnese</th>";	
						}else{
							echo "<th><a href='administrativoAnamneseAtributoPadrao.php' title='Adicionar atributos para ficha de anamnese.'><img width='30' src='img/mais.png'></a></th>";
							echo "<th colspan='5'>Adicione atributos para poder cadastrar fichas de anamnese.</th>";
						}
					echo "</tr>";

					echo "<tr>";
						echo "<td colspan='6' style='vertical-align:top; text-align:center; width:50%;'>";
						$instrucao = "select * from matricula_anamnese where id_matricula='$ID'";
						$sql = query($instrucao);
						for($i=0; $i<mysqli_num_rows($sql); $i++){
							extract(mysqli_fetch_assoc($sql));
							echo "<span class='arquivo'>";
							echo "<a href='#' ".pop("fichaAnamnese.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=visualizar&id_matricula=".base64_encode($ID)."&id_matricula_anamnese=".base64_encode($id), 800,600);
							echo " title='Ficha de Anamnese: $id<br>Avaliador: ".registro($id_usuario, "usuario", "nome")."<br>Data: ".formataData($data)."' >";
							echo "<img style='width:30px' src='img/arq.png'></a>";
							echo "<br><a href='#' ".pop("fichaAnamnese.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=deletar&id_matricula=".base64_encode($ID)."&id_matricula_anamnese=".base64_encode($id), 400,200);
							echo ">Deletar</a>";
							echo "</span>";
						}
						echo "</td>";
					echo "</tr>";
					
				}else{
					echo "<tr><td></td></tr>";
				}
				echo "</table>";
			echo "</div>";
			echo "<div id='tabs-3' style='width:100%;margin:0px; padding:0px;'>";
				echo "<table id='gradient-style' style='width:100%;margin:0px; padding:0px;'>";
					if($ID){
						echo "<tr>";
							echo "<th><a href='#' ".pop("fichaAvaliacaoFisica.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&id_matricula=".base64_encode($ID), 800, 600)." title='Adicionar uma novo Ficha de Avaliação Física.'><img width='30' src='img/mais.png'></a></th>";
							echo "<th colspan='5'>Ficha de avaliação física</th>";
						echo "</tr>";
						echo "<tr>";						
							echo "<td colspan='6' style='vertical-align:top; text-align:center; width:50%;'>";
							$sqlAvaliacao = query("select * from matricula_avaliacao where id_matricula='$ID'");
							for($i=0; $i<mysqli_num_rows($sqlAvaliacao); $i++){
								extract(mysqli_fetch_assoc($sqlAvaliacao));
								echo "<span class='arquivo'>";
								echo "<a href='#' ".pop("fichaAvaliacaoFisica.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=visualizar&id_matricula=".base64_encode($ID)."&id_matricula_avaliacao=".base64_encode($id), 800,600);
								echo " title='Ficha de Avaliação Física: $id<br>Avaliador: ".registro($id_usuario, "usuario", "nome")."<br>Data: ".formataData($data)."' >";
								echo "<img style='width:30px' src='img/arq.png'></a>";
								echo "<br><a href='#' ".pop("fichaAvaliacaoFisica.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=deletar&id_matricula=".base64_encode($ID)."&id_matricula_avaliacao=".base64_encode($id), 400,200);
								echo ">Deletar</a>";
								echo "</span>";
							}
							echo "</td>";
						echo "</tr>";
					}else{
						echo "<tr><td></td></tr>";
					}
				echo "</table>";
			echo "</div>";
			echo "<div id='tabs-4' style='width:100%;margin:0px; padding:0px;'>";
				echo "<table id='gradient-style' style='width:100%;margin:0px; padding:0px;'>";
					if($ID){
						echo "<tr>";
							$sqlExercicio = query("select * from exercicio where status='1'");
							if(mysqli_num_rows($sqlExercicio)){
								echo "<th colspan='3'><a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&id_matricula=".base64_encode($ID)."&op=", 800, 600)." title='Adicionar uma novo Ficha de Exercicio.'><img width='30' src='img/mais.png'></a></th>";
								echo "<th colspan='3'><a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&id_matricula=".base64_encode($ID)."&op=impressao", 800, 600)." title='Visualizar impressão de todas as fichas ativas.'><img width='30' src='img/impressora.png'></a></th>";
								
							}else{
								echo "<th><a href='cadastrarExercicio.php' title='Adicionar exercício para ficha de exercícios.'><img width='30' src='img/mais.png'></a></th>";
								echo "<th colspan='5'>Adicione exercícios para poder cadastrar fichas de exercício.</th>";
							}
						echo "</tr>";
						
						
						echo "<tr>";
							echo "<td colspan='6' style='vertical-align:top; text-align:center; width:50%;'>";
							$instrucao = "select * from matricula_exercicio where id_matricula='$ID' and status='1'";
							$sql = query($instrucao);
							echo "<div style='width:70%; display:inline-block; vertical-align:top; text-align:center;'>";
							echo "Fichas Ativas<br>";
							for($i=0; $i<mysqli_num_rows($sql); $i++){
								extract(mysqli_fetch_assoc($sql));
								echo "<span class='arquivo'>";
								echo "<a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=visualizar&id_matricula=".base64_encode($ID)."&id_matricula_exercicio=".base64_encode($id), 800,600);
								echo " title='Ficha de Exercicio: $id $nome<br>Treinador: ".registro($id_usuario, "usuario", "nome")."<br>Data: ".formataData($data)."' >";
								echo "<img style='width:30px' src='img/arq.png'></a>";
								echo "<br><a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=inativar&id_matricula=".base64_encode($ID)."&id_matricula_exercicio=".base64_encode($id), 400,200);
								echo ">Deletar</a>";
								echo "</span>";
							}
							echo "</div>";
							
							$instrucao = "select * from matricula_exercicio where id_matricula='$ID' and status='0'";
							$sql = query($instrucao);
							echo "<div style='width:30%; display:inline-block; vertical-align:top; text-align:center;'>";
							echo "Fichas Inativas<br>";
							for($i=0; $i<mysqli_num_rows($sql); $i++){
								extract(mysqli_fetch_assoc($sql));
								echo "<span class='arquivo'>";
								echo "<a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=visualizar&id_matricula=".base64_encode($ID)."&id_matricula_exercicio=".base64_encode($id), 800,600);
								echo " title='Ficha de Exercicio: $id $nome<br>Treinador: ".registro($id_usuario, "usuario", "nome")."<br>Data: ".formataData($data)."' >";
								echo "<img style='width:30px' src='img/arq.png'></a>";
								echo "<br><a href='#' ".pop("fichaExercicio.php?empresa=".base64_encode($_COOKIE["id_empresa"])."&op=ativar&id_matricula=".base64_encode($ID)."&id_matricula_exercicio=".base64_encode($id), 400,200);
								echo ">Reinserir</a>";
								echo "</span>";
							}
							echo "</div>";
							echo "</td>";
						echo "</tr>";
					}else{
						echo "<tr><td></td></tr>";
					}
				echo "</table>";
			echo "</div>";
	echo "</div>";//div tabs

	echo "</form>";
}


if($op=="novo" or $op=="editar"){
	

	
	$valorPlano = str_replace(",", ".", $valorPlano);
	$tipo2 = "cliente";
	if (!isset($cadastrarCliente)) {
		$cadastrarCliente = "false";
	}

	function validacaoToken() {

		global $cod, $cont, $array, $_POST;

		echo "<form style='width:30%;' method='post' action='cadastrarMatricula.php' enctype='multipart/form-data' style='display:inline;'>";
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
		echo "<br>Para validar o Plano / Assinatura insira um token.<br>";
		echo "<input type='password' name='token'>";
		echo "<input type='submit' class='btnEnviar' class='btnEnviar' value='Enviar'>";
		echo "</form>";
	}

	$info = "";
	$cor = "green";
	$validaToken = false;
	//irá dizer se precisará de token ou não
	$cod = "";
	$msg = "";
	
	//verificar se a o cliente é cadastrado
	if (!$id_cliente_fornecedor and $cadastrarCliente == "false" and $op== "novo") {
		$validaToken = true;
		$cod .= "Cliente $contato não é cadastrado.<br>";
		$cod .= "Para matricular um cliente em um Plano / Assinatura é necessário cadastra-lo.<br>";
		$cod .= "Caso contrário será insira um token para validar a operação.";
	}
	//verifica se o valor total é menor do que o valor do plano
	if(!isset($novoPlano)){
		$novoPlano=false;
	}
	if($op=="novo" or $novoPlano=="true"){
		$sqlValorPlano = query("select valor as valorPlanoComparativo from plano_assinatura where id='$planoAssinatura'");
		if(mysqli_num_rows($sqlValorPlano)<1){
			info('Plano selecionado para este cliente incorreto.');
			die;
		}
		extract(mysqli_fetch_assoc($sqlValorPlano));
		if ($valorPlano < $valorPlanoComparativo) {
			$validaToken = true;
			$cod .= "Plano com valor R$ ".real($valorPlano)." abaixo do valor R$ ".real($valorPlanoComparativo).".<br>";
			$cod .= "Para matricular este aluno com o valor deste plano será necessário um token.<br>";
		}	
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

		//os dados foram confirmados do cadastro do cliente e o usuario deseja cadastrar o cliente
		//cadastrando cliente
		if (!isset($razao_social)) {
			$razao_social = "NULL";
		}
		if (!isset($cidade)) {
			$cidade = "NULL";
		}
		if(!isset($tipo) or !isset($doc1) or !isset($doc2) or !isset($observacoes)){
			$sql = query("select tipo, cpf_cnpj as doc1, rg_ie as doc2, observacoes from cliente_fornecedor where id='$id_cliente_fornecedor'");
			extract(mysqli_fetch_assoc($sql));
		}


		$cliente_fornecedor = new cliente_fornecedor($id_cliente_fornecedor, $tipo2, $tipo, $nome, 
		$razao_social, $doc1, $doc2, $data, $email, $telefone1, $telefone2, $endereco, $numero, $bairro, 
		$cidade, $estado, $cep, $referencia, $observacoes, $latitude, $longitude, 1);

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
		
		if($op=="novo" and !$validaToken){
				
			//verificando duplicidade de matricula
			if(is_numeric($idCliente)){
				$sql = query("select id as idMatricula from matricula where id_cliente = '$idCliente' and status='1'");
			}else{
				$sql = query("select id as idMatricula from matricula where id_cliente = '$contato' and status='1'");
				$idCliente = $contato;
			}
			if(mysqli_num_rows($sql)>0){
				extract(mysqli_fetch_assoc($sql));
				info("Já existe uma matricula com esse cliente.");
				matricula($idMatricula);
				include "templates/downLogin.inc.php";
				die;
			}
			
			//inserindo na matricula
			$instrucao = "insert into matricula ";
			$instrucao .= "(id_cliente, telefone, observacoes, id_usuario, status) values ";
			$instrucao .= "('$idCliente', '$telefoneC', '$matriculaObservacoes','".getIdCookieLogin($_COOKIE["login"])."', '1')";
			$sql = query($instrucao);
			$idMatricula = mysqli_insert_id($conexao);
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cadastrou uma nova Matrícula.";
			$tabela_afetada = "matricula";
			$chave_principal = $idMatricula;
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			$sql = query("select dias_validade, dias_aviso from plano_assinatura where id='$planoAssinatura'");
			extract(mysqli_fetch_assoc($sql));
			
			//inserindo na matricula_plano_assinatura
			$instrucao = "insert into matricula_plano_assinatura ";
			$instrucao .= "(id_matricula, id_plano_assinatura, valor, data_inicio, data_aviso, data_carencia, data_previsao, data_termino) values";
			$instrucao .= "('$idMatricula', '$planoAssinatura', '$valorPlano', '".formataDataInv($dataInicio)."', ";
			$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days - $dias_aviso days"))."', ";
			$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days + $dias_aviso days"))."', ";
			$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days"))."', ";
			$instrucao .= "'0000-00-00')";
			$sql = query($instrucao);
			
			$idMatriculaPlanoAssinatura = mysqli_insert_id($conexao);
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Cadastrou um Plano / Assinatura para Matrícula.";
			$tabela_afetada = "matricula_plano_assinatura";
			$chave_principal = $idMatriculaPlanoAssinatura;
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			
			if(!$pgaParcelas){
			$pgaParcelas = 1;
			}
			if($idCliente==0){
				$idCliente = $contato;
			}
			//abrindo conta
			$instrucao = "insert into conta ";
			$instrucao .= "(tabela_entidade, entidade, tabela_referido, referido, valor, status, forma_pagamento, parcelas, conta_plano, data, id_usuario) ";
			$instrucao .= "values ";
			//conta plano = 5 eh igual a Plano / Assinatura
			$instrucao .= "('cliente_fornecedor', '$idCliente', 'plano_assinatura', '$idMatriculaPlanoAssinatura','$valorPlano', '2','$pgaForma','$pgaParcelas', '5','".date('Y-m-d H:i:s')."','".getIdCookieLogin($_COOKIE["login"])."');";
			
			$sql = query($instrucao);
			
			$idConta = mysqli_insert_id($conexao);
			
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
				while (checkdate($mes, $dia, $ano)==false){
					$dia--;
				}
				$vencimento = $ano . "-" . $mes . "-" . $dia;
				if($i!=0){
					$instrucao .= ", ";
				}
				$instrucao .= "('$idConta','$pgaTipo','$subTipoPagamento', NULL, NULL,'$vencimento','".getIdCookieLogin($_COOKIE["login"])."')";
			
			$i++;
			} while($i<$pgaParcelas);
			
			$sql = query($instrucao);
			
			
			$info .= "Matricula cadastrada com sucesso.";
		
		
		}elseif($op=="editar" and !$validaToken){
			
			//corrigindo caso n seja cliente cadastrado
			if(!is_numeric($idCliente)){
				$idCliente = $contato;
				//atualizando contas caso troque o nome do cliente
				$instrucao = "update conta set entidade='$idCliente' where tabela_referido='plano_assinatura' and referido = any ";
				$instrucao .= "(select id from matricula_plano_assinatura where id_matricula='$idMatricula')";
				$sql = query($instrucao);
			}
			
			//inserindo na matricula
			$instrucao = "update matricula set ";
			$instrucao .= "id_cliente='$idCliente', telefone='$telefoneC', observacoes='$matriculaObservacoes' ";
			$instrucao .= "where id='$idMatricula'";
			$sql = query($instrucao);
			
			$info .= "Matricula Editada com sucesso!<br>";
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Editou uma Matrícula.";
			$tabela_afetada = "matricula";
			$chave_principal = $idMatricula;
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			if($novoPlano=="true"){
				
				$sql = query("select dias_validade, dias_aviso from plano_assinatura where id='$planoAssinatura'");
				extract(mysqli_fetch_assoc($sql));
				
				//verificando se o ultimo plano assinatura está ativo e inativando ele, caso contrario não faz nada
				$instrucao = "select id as idMatriculaPlano, data_termino from matricula_plano_assinatura where status='1' and id_matricula='$idMatricula' ";
				$instrucao .= "and id=(select max(id) from matricula_plano_assinatura where status='1' and id_matricula='$idMatricula')";
				$sql = query($instrucao);
				if(mysqli_num_rows($sql)>0){
					extract(mysqli_fetch_assoc($sql));
					if($data_termino=="0000-00-00"){
						$sql = query("update matricula_plano_assinatura set data_termino='".date("Y-m-d")."', status='0' where id='$idMatriculaPlano'");
					}	
				}
				
				//inserindo na matricula_plano_assinatura
				$instrucao = "insert into matricula_plano_assinatura ";
				$instrucao .= "(id_matricula, id_plano_assinatura, valor, data_inicio, data_aviso, data_carencia, data_previsao, data_termino) values";
				$instrucao .= "('$idMatricula', '$planoAssinatura', '$valorPlano', '".formataDataInv($dataInicio)."', ";
				$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days - $dias_aviso days"))."', ";
				$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days + $dias_aviso days"))."', ";
				$instrucao .= "'".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days"))."', ";
				$instrucao .= "'0000-00-00')";
				$sql = query($instrucao);
				
				$idMatriculaPlanoAssinatura = mysqli_insert_id($conexao);
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cadastrou um Plano / Assinatura para Matrícula.";
				$tabela_afetada = "matricula_plano_assinatura";
				$chave_principal = $idMatriculaPlanoAssinatura;
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
				if(!$pgaParcelas){
				$pgaParcelas = 1;
				}
				if($idCliente==0){
					$idCliente = $contato;
				}
				//abrindo conta
				$instrucao = "insert into conta ";
				$instrucao .= "(tabela_entidade, entidade, tabela_referido, referido, valor, status, forma_pagamento, parcelas, conta_plano, data, id_usuario) ";
				$instrucao .= "values ";
				//conta plano = 5 eh igual a Plano / Assinatura
				if(!$pgaParcelas){
					$pgaParcelas = 1;
				}
				$instrucao .= "('cliente_fornecedor', '$idCliente', 'plano_assinatura', '$idMatriculaPlanoAssinatura','$valorPlano', '2','$pgaForma','$pgaParcelas', '5','".date('Y-m-d H:i:s')."','".getIdCookieLogin($_COOKIE["login"])."');";
				
				$sql = query($instrucao);
				
				$idConta = mysqli_insert_id($conexao);
				
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
			
			}

		}
		
		info($info, $cor);
		if(isset($idMatricula)){
			matricula($idMatricula);
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarMatricula.php?op=visualizar&id=".base64_encode($idMatricula)."'>";
		}
	}
}elseif($op=="inativar"){
	
	$idMatricula = base64_decode($id);
	
	//instrucao para atualizar matricula_plano_assinatura
	$instrucao = "update matricula set ";
	$instrucao .= "status='0' ";
	$instrucao .= "where id='$idMatricula'";
	$sql = query($instrucao);
	
	$info =  "Matricula editada com sucesso.<br>";
	
	$id_usuario = getIdCookieLogin($_COOKIE["login"]);
	$dataAtual = date('Y-m-d H:i:s');
	$acao = "Inativou uma Matrícula.";
	$tabela_afetada = "matricula";
	$chave_principal = $idMatricula;
	insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	
	$sql = query("select max(id) as idPlano from matricula_plano_assinatura where id_matricula='$idMatricula'");
	extract(mysqli_fetch_assoc($sql));
	
	//instrucao para atualizar matricula_plano_assinatura
	$instrucao = "update matricula_plano_assinatura set ";
	$instrucao .= "data_carencia='".date('Y-m-d')."', ";
	$instrucao .= "data_termino='".date('Y-m-d')."' ";
	$instrucao .= "where id='$idPlano'";
	$sql = query($instrucao);
	
	info($info, "yellow");
	
	matricula($idMatricula);
	
}elseif($op=="pausa"){
	
	$id = base64_decode($id);
	
	$sql = query("select status from matricula where id='$id'");
	extract(mysqli_fetch_assoc($sql));
	if($status==2){
		info("Esta matricula já se encontra em pausa.", "yellow");
	}else{
		$sql = query("update matricula set status='2' where id='$id'");
		//calcular quantos dias a mais a matricula_plano_assinatura ganhará por está em pausa
		$maisDias = subtrairDatas(formataDataInv($data1), formataDataInv($data2));
		$sql = query("select id as idMatriculaPlanoAssinatura, id_plano_assinatura, data_previsao from matricula_plano_assinatura where id=(select max(id) from matricula_plano_assinatura where id_matricula='$id')");
		if(mysqli_num_rows($sql)){
			extract(mysqli_fetch_assoc($sql));
			
			$sql = query("select data_aviso, data_carencia, data_previsao from matricula_plano_assinatura where id='$idMatriculaPlanoAssinatura'");
			if(mysqli_num_rows($sql)){
				extract(mysqli_fetch_assoc($sql));
				$instrucao = "update matricula_plano_assinatura set ";
				$instrucao .= "data_aviso='".date('Y-m-d', strtotime($data_aviso." + $maisDias days"))."', ";
				$instrucao .= "data_carencia='".date('Y-m-d', strtotime($data_carencia." + $maisDias days"))."', ";
				$instrucao .= "data_previsao='".date('Y-m-d', strtotime($data_previsao." + $maisDias days"))."' ";
				$instrucao .= "where id='$idMatriculaPlanoAssinatura'";
				
				//echo $instrucao;
				$sql = query($instrucao);
				
				$instrucao = "insert into matricula_pausa (id_matricula_plano_assinatura, data_inicio, data_termino, id_usuario, data) values";
				$instrucao .= "('$idMatriculaPlanoAssinatura', '".formataDataInv($data1)."', '".formataDataInv($data2)."', '".getIdCookieLogin($_COOKIE["login"])."', '".date('Y-m-d H:i:s')."')";
				//echo $instrucao;
				$sql = query($instrucao);
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Pausou uma Matrícula.";
				$tabela_afetada = "matricula";
				$chave_principal = $id;
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
				//verificando se a data do regresso da pausa é menor do que a data de hoje
				//se for está convertendo a matricula para ativa novamente.
				if(subtrairDatas(date('Y-m-d'), formataDataInv($data2))<0){
					$sql = query("update matricula set status='1' where id='$id'");
					$msg = "Registrado a pausa da matricula. Entretanto a matricula foi posta novamente como play, ";
					$msg .= "pois a data de regresso ($data2) é menor do que a data de hoje (".date('d/m/Y').").";
				}else{
					$msg = "Matrícula pausa com sucesso.";
				}
				info($msg);
			}
		}
	}

	matricula($id);
	echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=cadastrarMatricula.php?op=visualizar&id=".base64_encode($id)."'>";

}elseif($op=="play"){
	
	$id = base64_decode($id);
	$instrucao = "select id_matricula_plano_assinatura as idMatriculaPlanoAssinatura, data_inicio as data1 from matricula_pausa where ";
	$instrucao .= "id=(select max(id) from matricula_pausa where ";
	$instrucao .= "id_matricula_plano_assinatura = ";
	$instrucao .= "any(select max(id) from matricula_plano_assinatura where id_matricula='$id'))";
	$sql = query($instrucao);
	if(mysqli_num_rows($sql)){
		extract(mysqli_fetch_assoc($sql));
		$data1=formataData($data1);
		$data2=date('d/m/Y');
		
		$sql = query("select status from matricula where id='$id'");
		extract(mysqli_fetch_assoc($sql));
		if($status==1){
			info("Esta matricula já se encontra em play.", "yellow");
		}else{
			$sql = query("update matricula set status='1' where id='$id'");
			//calcular quantos dias a mais a matricula_plano_assinatura ganhará por está em pausa
			$maisDias = subtrairDatas(formataDataInv($data1), formataDataInv($data2));
			$sql = query("select id as idMatriculaPlanoAssinatura, id_plano_assinatura, data_previsao from matricula_plano_assinatura where id=(select max(id) from matricula_plano_assinatura where id_matricula='$id')");
			if(mysqli_num_rows($sql)){
				extract(mysqli_fetch_assoc($sql));
				
				$sql = query("select dias_aviso, dias_carencia from plano_assinatura where id='$id_plano_assinatura'");
				if(mysqli_num_rows($sql)){
					extract(mysqli_fetch_assoc($sql));
					$instrucao = "update matricula_plano_assinatura set ";
					$instrucao .= "data_aviso='".date('Y-m-d', strtotime(formataDataInv($data1)." + $maisDias days - $dias_aviso days"))."', ";
					$instrucao .= "data_carencia='".date('Y-m-d', strtotime(formataDataInv($data1)." + $maisDias days + $dias_aviso days"))."', ";
					$instrucao .= "data_previsao='".date('Y-m-d', strtotime(formataDataInv($data1)." + $maisDias days"))."' ";
					$instrucao .= "where id='$idMatriculaPlanoAssinatura'";
					$sql = query($instrucao);
					
					$sql = query("select max(id) as idMatriculaPausa from matricula_pausa where id_matricula_plano_assinatura='$idMatriculaPlanoAssinatura'");
					extract(mysqli_fetch_assoc($sql));
					
					$instrucao = "update matricula_pausa set data_inicio='".formataDataInv($data1)."', data_termino='".formataDataInv($data2)."', ";
					$instrucao .= "id_usuario='".getIdCookieLogin($_COOKIE["login"])."', data='".date('Y-m-d')."' ";
					$instrucao .= "where id='$idMatriculaPausa'";
					$sql = query($instrucao);
					
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Play uma Matrícula.";
					$tabela_afetada = "matricula";
					$chave_principal = $id;
					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
					info("Matricula play com sucesso.");
				}
			}
		}
		
		
	}
	
	matricula($id);
	echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarMatricula.php?op=visualizar&id=".base64_encode($id)."'>";	

}elseif($op=="deletar"){
	
	$plano = base64_decode($plano);
	$msg = "";
	
	//atualizando conta		
	$instrucao = "update conta set status='4' where tabela_referido='plano_assinatura' and referido='$plano'";
	$sql = query($instrucao);
	
	extract(mysqli_fetch_assoc(query("select id as idConta from conta where tabela_referido='plano_assinatura' and referido='$plano'")));
	$id_usuario = getIdCookieLogin($_COOKIE["login"]);
	$dataAtual = date('Y-m-d H:i:s');
	$acao = "Deletou uma conta.";
	$tabela_afetada = "conta";
	$chave_principal = $idConta;
	insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	$msg .= "Contas a receber deletadas com sucesso.<br>";
	
	//atualizando matricula_plano_assinatura
	$instrucao = "update matricula_plano_assinatura set status='2', data_termino='".date('Y-m-d')."' where id='$plano'";
	$sql = query($instrucao);
	
	$id_usuario = getIdCookieLogin($_COOKIE["login"]);
	$dataAtual = date('Y-m-d H:i:s');
	$acao = "Deletou um Plano / Assinatura para Matrícula.";
	$tabela_afetada = "matricula_plano_assinatura";
	$chave_principal = $plano;
	insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	$msg .= "Plano deletado com sucesso.<br>";
	
	//checando se este é o ultimo plano desta matricula
	//e se estiver deletado a matricula será inativada
	$sql = query("select id_matricula from matricula_plano_assinatura where id='$plano'");
	extract(mysqli_fetch_assoc($sql));
	$sql = query("select max(id) as idMax from matricula_plano_assinatura where id_matricula=$id_matricula");
	extract(mysqli_fetch_assoc($sql));
	if($idMax==$plano){
		$sql = query("update matricula set status='0' where id='$id_matricula'");
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inativou a matricula ao deletar o último plano da mesma.";
		$tabela_afetada = "matricula";
		$chave_principal = $id_matricula;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		$msg .= "Matrícula inativada devido a falta de planos ativos.<br>";
	}
	
	
	info($msg);
	matricula($id_matricula);
	echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarMatricula.php?op=visualizar&id=".base64_encode($id_matricula)."'>";

}elseif($op=="visualizar"){
		
	matricula(base64_decode($id));

}else{
		
	matricula();

}


include "templates/downLogin.inc.php";
?>

