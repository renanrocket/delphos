<?php

include_once "../templates/upLoginImp.inc.php";

echo "
<script type='text/javascript' src='../js/funcoes.js'></script>
		<script type='text/javascript' src='../js/mascara.js'></script>
        <script type='text/javascript' src='../js/jquery.js'></script>
		";




//all
echo "<script src='../js/cadastrarMatriculaFiltroPop.js' type='text/javascript'></script>";

extract($_GET);
extract($_POST);
$cont = count($_POST);
$array = array_keys($_POST);
$plano= base64_decode($plano);
$info = "<h1>";



if(isset($_GET["op"])){
	
	if($op=="editar"){
		echo "<form method='post' action='cadastrarMatriculaPlano.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='plano' value='".base64_encode($plano)."'>";
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<table>";
			echo "<tr>";
			echo "<td>Plano<br>";
			echo "<select name='planoAssinatura' onchange='showPlano(this.value); showDataTermino(this.value); showPlanoValor(this.value)'>";
			$sql = query("select id_plano_assinatura from matricula_plano_assinatura where id='$plano'");
			extract(mysqli_fetch_assoc($sql));
			$sql = query("select * from plano_assinatura where status='1'");
			for($i=0; $i<mysqli_num_rows($sql); $i++){
				extract(mysqli_fetch_assoc($sql));
				if($id_plano_assinatura==$id){
					echo "<option value='$id' selected='yes'>$nome R$ ".real($valor)."</option>";
					$script =  "<iframe src='../inc/cadastrarMatricula.inc.php?op=plano&Plano=$id' width='240' height='70' frameborder='0' scrolling='yes' marginheight='0' marginwidth='0'></iframe>";
				}else{
					echo "<option value='$id'>$nome R$ ".real($valor)."</option>";
				}
				
			}
			echo "</select>";
			echo "</td>";
				echo "<td colspan='4' id='tdPlano'>";
				echo $script;
			echo "</td>";
			echo "</tr>";
			
			$sql = query("select * from matricula_plano_assinatura where id='$plano'");
			extract(mysqli_fetch_assoc($sql));
			echo "<tr>";
			echo "<td>";
				echo "Data inicio do Plano / Assinatura<br>";
				echo "<input type='text' name='dataInicio' ".mascara("Data", "10", "value='".formataData($data_inicio)."'").">";
			echo "</td>";
			echo "<td colspan='3' id='tdDataTermino'>";
				echo "Data termino do Plano / Assinatura<br>";
				if($data_termino!="0000-00-00"){
					echo "<input type='text' name='dataTermino' ".mascara("Data", "10", "value='".formataData($data_termino)."'").">";
				}else{
					echo "<input type='hidden' name='dataTermino' ".mascara("Data", "10", "value='".formataData($data_termino)."'").">";
				}
			echo "</td>";
			echo "<td colspan='1'>";
				echo "Valor do Plano<br>";
				echo "<input type='text' name='valorPlano' value='".real($valor)."' class='totalValor preco' ".mascara("Valor2").">";
				echo "<div id='divPlanoPreco' style='display:inline-block;'></div>";
			echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td colspan='4'></td>";
			echo "<td align='center'><input type='submit' value='Enviar' class='aSubmit btnEnviar'></td>";
		echo "</tr>";
			
		echo "</table>";
		echo "</form>";
	}
	
	
}elseif(isset($_POST["op"])){
	
	if($op=="editar"){
		
		$valorPlano = str_replace(",", ".", $valorPlano);
		
		$valida = true;
		$refresh = "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarMatriculaPlano.php?op=editar&plano=".base64_encode($plano)."'>";
		//filtro da data
		if(strlen($dataInicio)<10){
			echo "<h3>Data Inválida!</h3>";
			echo $refresh;
			$valida = false;
		}
		
		function validacaoToken() {

			global $cod, $cont, $array, $_POST;
		
			echo "<form method='post' action='cadastrarMatriculaPlano.php' enctype='multipart/form-data' style='display:inline;'>";
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
			echo "<h1>";
			echo $cod;
			echo "<br>Para validar o Plano / Assinatura insira um token.<br>";
			echo "</h1>";
			echo "<input type='password' name='token'>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'>";
			echo "</form>";
		}
		
		$cor = "green";
		$validaToken = false;
		//irá dizer se precisará de token ou não
		$cod = "";
		$msg = "";
		
		//verifica se o valor total é menor do que o valor do plano
		$sqlValorPlano = query("select valor as valorPlanoComparativo from plano_assinatura where id='$planoAssinatura'");
		extract(mysqli_fetch_assoc($sqlValorPlano));
		if ($valorPlano < $valorPlanoComparativo) {
			$validaToken = true;
			$cod .= "Plano com valor de R$ ".real($valorPlano)." abaixo do valor de R$ ".real($valorPlanoComparativo).".<br>";
			$cod .= "Para validar esta operação é necessário inserir um token.";
		}
		
		if ($validaToken and !isset($token) and $valida) {//precisa de token
			//pergunta se realmente quer cadastrar o cliente
			echo validacaoToken();
		} elseif($valida) {// nao precisa de token ou token existe
		
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
					$validaToken = true;
					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$acao = "Tentou usar um token.";
					$tabela_afetada = NULL;
					$chave_principal = NULL;
		
					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				}
			}
			
			if(!$validaToken){
				
				//instrucao para atualizar conta
				$instrucao = "select valor from matricula_plano_assinatura where id='$plano'";
				$sql = query($instrucao);
				extract(mysqli_fetch_assoc($sql));
				
				$sql = query("select dias_validade, dias_aviso, dias_carencia from plano_assinatura where id=(select id_plano_assinatura from matricula_plano_assinatura where id='$plano')");
				extract(mysqli_fetch_assoc($sql));
				
				if(!isset($dataTermino)){
					$dataTermino = "00/00/0000";
				}
				//instrucao para atualizar matricula_plano_assinatura
				$instrucao = "update matricula_plano_assinatura set ";
				$instrucao .= "id_plano_assinatura='$planoAssinatura', ";
				$instrucao .= "data_aviso='".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days - $dias_aviso days"))."', ";
				$instrucao .= "data_carencia='".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days + $dias_carencia days"))."', ";
				$instrucao .= "data_previsao='".date('Y-m-d', strtotime(formataDataInv($dataInicio)." + $dias_validade days"))."', ";
				$instrucao .= "data_inicio='".formataDataInv($dataInicio)."', data_termino='".formataDataInv($dataTermino)."', ";
				$instrucao .= "valor='$valorPlano' where id='$plano'";
				$sql = query($instrucao);
				
				$info .=  "Plano editado com sucesso.";
				
				$idMatriculaPlanoAssinatura = $plano;
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Editou um Plano / Assinatura para Matrícula.";
				$tabela_afetada = "matricula_plano_assinatura";
				$chave_principal = $idMatriculaPlanoAssinatura;
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
				//condição para atualizar conta
				if($valor!=$valorPlano){
					
					$pgaParcelas = 1;
					$pgaTipo = 1;
					//editando conta
					$instrucao = "update conta set ";
					$instrucao .= "valor='$valorPlano', ";
					$instrucao .= "parcelas='$pgaParcelas', data='".date('Y-m-d H:i:s')."', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where referido='$plano' and tabela_referido='plano_assinatura'";
					
					$sql = query($instrucao);
					
					$idConta = mysqli_fetch_row(query("select id from conta where tabela_referido='plano_assinatura' and referido='$plano'"));
					
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
					
					$info .= "Conta editada com sucesso.";
					$info .= "<br>As contas e valores recebidos (se houver) foram resetados.";
					$info .= "<br><div style='font-size:20px; color:red;'>AVISO!</div>";
					$info .= "Por favor, se havia algum valor recebido, certifique-se foi realmente recebido na conta desta matrícula.";
				}
				
				
				echo $info."</h1>";
				
				echo "<script language=\"JavaScript\" type=\"text/javascript\">";
					echo "window.opener.location.reload();";
				echo "</script>";
			}
		}

	}
	
}

	
//end all

include_once "../templates/downLoginImp.inc.php";

?>
