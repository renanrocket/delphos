<?php
include_once "../templates/upLoginImp.inc.php";
?>

<script type="text/javascript">
	function check(valor, idMatricula, id){
		$.post("../inc/ajaxFichaExercicio.inc.php", {
			idMatriculaExercicioItem : "" + valor + "",
			idMatricula : "" + idMatricula + ""
		}, function(data) {
			if (data.length > 0) {
				//if($("#check_"+valor).is(':checked')){
				if(data=="1"){
					$("#exercicio_"+id).attr("class", "riscado");
				}else{
					$("#exercicio_"+id).attr("class", "");
				}
			}
		});
		
	}
	function filtro(){
		var valida = true;
		var alerta = "";
		
		if($("input[name='nomeFicha']").val()==""){
			alerta += "Por favor preencha o nome desta ficha. Por exemplo: Treino A.\n";
			$("input[name='nomeFicha']").attr("class", "avisoInput");
			valida = false;
		}else{
			$("input[name='nomeFicha']").attr("class", "");
		}
		
		if(!valida){
			alert(alerta);
		}
		return valida;
	}
	
	function showQtdExercicio(valor){
		if(valor<1 || valor>99){
			$("input[name='qtdExercicio']").val(1);
		}
		
		for(var i=1; i<=99; i++){
			if(i<=valor){
				$("#exercicio_"+i).show();
			}else{
				$("#exercicio_"+i).hide();
			}
		}
	}
	
	function showExercicio(valor, valor2){
		if(!valor){
			valor = $(".exercicioSelect_"+valor2).val();
		}
		var cod = "<img width='30' src='../img/loadingB.gif'>";
		$(".showExercicio").html(cod);
		$.post("../inc/ajaxFichaExercicio.inc.php", {
			idExercicio : "" + valor + ""
		}, function(data) {
			if (data.length > 0) {
				$(".showExercicio").html(data);
			}
		});
		
	}
</script>
<?php


//all
extract($_GET);
extract($_POST);

function fichaExercicio($id_matricula, $ID = null, $top = true, $down = true, $downInput = true){
	
	echo "<form method='post' action='fichaExercicio.php?id_matricula=".base64_encode($id_matricula)."' enctype='multipart/form-data' onsubmit='return filtro();'>";
		
		if(!$ID){
			echo "<input type='hidden' name='op' value='novo'>";
			$qtdExercicio = 1;
		}else{
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id_matricula_exercicio' value='".base64_encode($ID)."'>";
			$instrucao = "select * from matricula_exercicio_item where id_matricula_exercicio='$ID'";
			$sql = query($instrucao);
			$qtdExercicio = mysqli_num_rows($sql);
		}
		
		
		
		if($top){
			echo "<table>";
			
			
			if($_GET["op"]=="impressao"){
				echo "<tr>";
					$style1 = "style='border:none; text-align:right;'";
					$style2 = "style='border:none; border-bottom: solid 1px black;'";
					$sqlTimbrado = query("select * from empresa where usarTimbrado='1'");
					echo "<td colspan='3' rowspan='5' style='border:none; align:center;'>";
						if(mysqli_num_rows($sqlTimbrado)){
							extract(mysqli_fetch_assoc($sqlTimbrado));
							echo "<img src='../".$imgsrc."' height='100'>";
						}
					echo "</td>";
					$sql = query("select * from matricula where id='$id_matricula'");
					extract(mysqli_fetch_assoc($sql));
					echo "<td $style1>Aluno:</td>";
					echo "<td colspan='2' $style2>";
						echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td $style1>Objetivo:</td>";
					echo "<td colspan='2' $style2>";
						$sqlObjetivo = query("select objetivo from matricula_avaliacao where id_matricula='$id_matricula' order by id desc");
						if(mysqli_num_rows($sqlObjetivo)){
							extract(mysqli_fetch_assoc($sqlObjetivo));
							echo $objetivo;
						}
					echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td $style1>Instrutor:</td>";
					echo "<td colspan='2' $style2>";
						$sqlInstrutor = query("select id_usuario from matricula_exercicio where id_matricula='$id_matricula' order by id desc");
						if(mysqli_num_rows($sqlInstrutor)){
							extract(mysqli_fetch_assoc($sqlInstrutor));
							echo registro($id_usuario, "usuario", "nome");
						}
					echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td $style1>Plano:</td>";
					echo "<td colspan='2' $style2>";
						$sqlPlano = query("select id_plano_assinatura, data_inicio, data_previsao from matricula_plano_assinatura where id_matricula='$id_matricula' and status='1'");
						if(mysqli_num_rows($sqlPlano)){
							extract(mysqli_fetch_assoc($sqlPlano));
							echo registro($id_plano_assinatura, "plano_assinatura", "nome");
							echo " inicio ".formataData($data_inicio)." e previsto para finalizar ".formataData($data_previsao);
						}
					echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td $style1>Restrições:</td>";
					echo "<td colspan='2' $style2></td>";
				echo "</tr>";	
			}else{
				
				echo "<tr><td colspan='6'>";
					cabecalho();
				echo "</td></tr>";
			
				$sql = query("select * from matricula where id='$id_matricula'");
				extract(mysqli_fetch_assoc($sql));
				
				echo "</tr>";
					echo "<td colspan='1'><span>ID Matrícula:</span>$id_matricula</td>";
					echo "<td colspan='1'><span>ID Ficha:</span>$ID</td>";
					echo "<td colspan='2'><span>Nome:</span>";
					echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
					echo "</td>";
					echo "<td colspan='1'><span>Telefone:</span>$telefone</td>";
					
					if($ID){
						$sql = query("select * from matricula_exercicio where id='$ID'");
						extract(mysqli_fetch_assoc($sql));
						if(!$downInput){
							$sqlNome = query("select id, nome from matricula_exercicio where id_matricula='$id_matricula'");
							for($j = $QTDExercicio = 0, $Nome = null; $j<mysqli_num_rows($sqlNome); $j++){
								extract(mysqli_fetch_assoc($sqlNome));
								$Nome.=" ".$nome;
								$sqlQtd = query("select * from matricula_exercicio_item where id_matricula_exercicio='$id'");
								$QTDExercicio += mysqli_num_rows($sqlQtd);
							}
							$nome = $Nome;
							
						}
					}else{
						$nome = $data = $id_usuario = null;
					}
					echo "<td colspan='1'><span>Treino</span>";
					echo "<input type='text' name='nomeFicha' value='$nome'>";
					echo "</td>";
					
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan='4'><span>Treinador:</span>".registro($id_usuario, "usuario", "nome")."</td>";
					echo "<td colspan='1'><span>Data criação da ficha:</span>".formataData($data)."</td>";
					echo "<td><span>Qtd Exercício</span>";
					
					echo "<input style='width:30px;' ".mascara("Integer", 2)." type='text' name='qtdExercicio' value='";
					echo !$downInput ? $QTDExercicio : $qtdExercicio;
					echo "' onblur='showQtdExercicio(this.value);'>";
					
					echo "</td>";
				echo "</tr>";
			}
		}
		
			echo "<tr>";
				echo "<th width='40'></th>";
				echo "<th colspan='3'>Exercício</th>";
				echo "<th colspan='1'>Sequência</th>";
				echo "<th colspan='1'>Carga</th>";
			echo "</tr>";
			
			$instrucao = "select * from matricula_exercicio_item where id_matricula_exercicio='$ID'";
			$sql = query($instrucao);
			for($i=1; $i<=99;$i++){
				if($i<=$qtdExercicio){
					$style="";
				}else{
					$style="style='display:none;'";
				}
					
				if($ID and $i<=$qtdExercicio){
					extract(mysqli_fetch_assoc($sql));
					if($checked){
						$checked = "checked='yes'";
						$class = "class='riscado'";
					}else{
						$checked = "";
						$class = "";
					}
					$td = "<td class='$id' align='center'><input type='checkbox' name='checked[]' onclick='check(this.value, \"$id_matricula\", \"$i\")' id='check_".$id."' value='$id' $checked></td>";
				}else{
					$id = $id_exercicio = $carga = $sequencia = null;
					$td = "<td class='$id'></td>";
					$class = "";
				}
				
				echo "<tr id='exercicio_$i' $class $style>";
				echo $td;
				echo "<td align='center' class='$id' colspan='3' onmouseover='showExercicio(\"$id_exercicio\", \"$id\");'>";
					if(getCredencialUsuario("cadastrarMatricula.php")){
						echo "<select name='exercicio[]' id='naoImp' class='exercicioSelect_$id' onchange='showExercicio(\"$id_exercicio\", \"$id\");'>";
							echo opcaoSelect("exercicio", "nome", 1, $id_exercicio, false, "order by nome", 0, false);
						echo "</select>";
						echo "<div id='imp'>".registro($id_exercicio, "exercicio", "nome")."</div>";
					}else{
						echo "<input type='hidden' name='exercicio[]' value='$id_exercicio'>";
						registro($id_exercicio, "exercicio", "nome");
					}
				echo "</td>";
				
				echo "<td align='center' class='$id' colspan='1'>";
					if(getCredencialUsuario("cadastrarMatricula.php")){
						echo "<input type='text' name='sequencia[]' value='$sequencia'>";
					}else{
						echo "<input type='hidden' name='sequencia[]' value='$sequencia'>";
						echo $sequencia;
					}
				echo "</td>";
				
				echo "<td align='center' class='$id' colspan='1'>";
					echo "<input type='text' name='carga[]' value='$carga'>";
				echo "</td>";
					
					
				echo "</tr>";
			}
		
			if(getCredencialUsuario("cadastrarMatricula.php") and $downInput){
				echo "<tr style='display:table-row;'>";
					echo "<td style='border:none;'></td>";
					echo "<td style='border:none;'></td>";
					echo "<td style='border:none;'></td>";
					echo "<td style='border:none;'></td>";
					echo "<td style='border:none;'></td>";
					echo "<td id='naoImp' style='border:none; text-align:right;'>";
						echo "<input type='submit' class='btnEnviar' value='Enviar'> ";
						echo "<input type='reset' value='Cancelar'>";
					echo "</td>";
				echo "</tr>";
				
			}		
			
		
	echo "</form>";
	
	if($down){
		echo "</table>";
		echo "<center class='showExercicio'></center>";
	}
		
}

$id_matricula = base64_decode($id_matricula);

if(!isset($op)){
	
	fichaExercicio($id_matricula);

}else{
	
	if($op=="novo"){
		
		//insert na matricula_exercicio
		$instrucao = "insert into matricula_exercicio ";
		$instrucao .= "(id_matricula, nome, id_usuario, data) values";
		$instrucao .= "('$id_matricula', '$nomeFicha', '".getIdCookieLogin($_COOKIE["login"])."', '".date('Y-m-d H:i:s')."')";
		$sql = query($instrucao);
		
		$id_matricula_exercicio = mysqli_insert_id($conexao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inseriu uma ficha de exercicio.";
		$tabela_afetada = "matricula_exercicio";
		$chave_principal = $id_matricula_exercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		//insert na matricula_exercicio_item
		$instrucao = "insert into matricula_exercicio_item ";
		$instrucao .= "(id_matricula_exercicio, id_exercicio, carga, sequencia) values ";
		for($i=0; $i<$qtdExercicio; $i++){
			if($i<>0){
				$instrucao .= ", ";
			}
			$instrucao .= "('$id_matricula_exercicio', '".$exercicio[$i]."', '".$carga[$i]."', '".$sequencia[$i]."')";
		}
		$sql = query($instrucao);
		

	}elseif($op=="editar"){
			
		$id_matricula_exercicio = base64_decode($id_matricula_exercicio);	
		
		//update na matricula_exercicio
		$instrucao = "update matricula_exercicio set ";
		$instrucao .= "nome='$nomeFicha' where id='$id_matricula_exercicio'";
		
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou uma ficha de exercicio.";
		$tabela_afetada = "matricula_exercicio";
		$chave_principal = $id_matricula_exercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		//deletando matricula_exercicio_item
		$instrucao = "delete from matricula_exercicio_item where id_matricula_exercicio='$id_matricula_exercicio'";
		$sql = query($instrucao);
		
		//insert na matricula_exercicio_item
		$instrucao = "insert into matricula_exercicio_item ";
		$instrucao .= "(id_matricula_exercicio, id_exercicio, carga, sequencia) values ";
		for($i=0; $i<$qtdExercicio; $i++){
			if($i<>0){
				$instrucao .= ", ";
			}
			$instrucao .= "('$id_matricula_exercicio', '".$exercicio[$i]."', '".$carga[$i]."', '".$sequencia[$i]."')";
		}
		$sql = query($instrucao);
		
		
	}elseif($op=="visualizar"){
		$id_matricula_exercicio = base64_decode($id_matricula_exercicio);
	}elseif($op=="inativar"){
			
		$id_matricula_exercicio = base64_decode($id_matricula_exercicio);
		
		if(!isset($confirm)){
			echo "<form method='post' action='fichaExercicio.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='id_matricula' value='".base64_encode($id_matricula)."'>";
				echo "<input type='hidden' name='id_matricula_exercicio' value='".base64_encode($id_matricula_exercicio)."'>";
				echo "<input type='hidden' name='confirm' value='1'>";
				echo "<input type='hidden' name='op' value='inativar'>";
				echo "<h1>Você deseja mesmo deletar essa ficha de exercicio?</h1>";
				echo "<input class='aSubmit' type='submit' value='Sim'><br><br>";
				echo "<a href='#' class='aSubmit' onclick='window.close();'>Não</a>";
			echo "</form>";
		}else{
			//update na matricula_exercicio
			$instrucao = "update matricula_exercicio set ";
			$instrucao .= "status='0' where id='$id_matricula_exercicio'";
			$sql = query($instrucao);
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Inativou uma ficha de exercicio.";
			$tabela_afetada = "matricula_exercicio";
			$chave_principal = $id_matricula_exercicio;
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			echo "<script language=\"JavaScript\" type=\"text/javascript\">";
				echo "window.opener.location.reload();";
				echo "window.close()"; 
			echo "</script>";
		}
		
	}elseif($op=="ativar"){
		
		$id_matricula_exercicio = base64_decode($id_matricula_exercicio);	
		
		//update na matricula_exercicio
		$instrucao = "update matricula_exercicio set ";
		$instrucao .= "status='1' where id='$id_matricula_exercicio'";
		
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Ativou uma ficha de exercicio.";
		$tabela_afetada = "matricula_exercicio";
		$chave_principal = $id_matricula_exercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
			echo "window.opener.location.reload();";
			echo "window.close()"; 
		echo "</script>";
	}elseif($op=="impressao"){
		
		$sql = query("select * from matricula_exercicio where status='1' and id_matricula='$id_matricula'");
		$linha = mysqli_num_rows($sql);
		for($i=0; $i<$linha; $i++){
			extract(mysqli_fetch_assoc($sql));
			if($i==0){
				$top = true;
			}else{
				$top = false;
			}
			
			if(($i+1)==$linha){
				$down = true;
			}else{
				$down = false;
			}
			fichaExercicio($id_matricula, $id, $top, $down, false);
		}
		
		
	}
	
	if($op!="inativar" and $op!="ativar" and $op!="impressao"){
		if(!isset($id_matricula_exercicio)){
			$id_matricula_exercicio =null;
		}
		fichaExercicio($id_matricula, $id_matricula_exercicio);
	}
	

}


//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script>