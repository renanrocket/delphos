<?php
include_once "../templates/upLoginImp.inc.php";
?>
<script type='text/javascript' src='../js/funcoes.js'></script>
<script type='text/javascript' src='../js/mascara.js'></script>
<script type='text/javascript' src='../js/jquery.js'></script>


<script type="text/javascript">
	function filtro(){
		
		valida = true;
		
		<?php
			
			$sql = query("select * from matricula_anamnese_atributo_padrao where status='1'");
			for($i=0; $i<mysqli_num_rows($sql); $i++){
				extract(mysqli_fetch_assoc($sql));
				if($obrigatoriedade==1){
					echo "if($(\"input[name='atributo_\"+ $id +\"']\")==undefined){
						valida = false;
						alerta = \"Existem campos obrigatorios que nao foram preenchidos.\";
						$(\"#id_\"+ $id ).attr(\"class\", \"avisoInput\");
					}else if ($(\"input[name='atributo_\"+ $id +\"']\").val()==\"\"){
						valida = false;
						alerta = \"Existem campos obrigatorios que nao foram preenchidos.\";
						$(\"#id_\"+ $id ).attr(\"class\", \"avisoInput\");
					}else{
						$(\"#id_\"+ $id ).attr(\"class\", \"\");
					}";
				}
			}
		?>
		
		if(!valida){
			alert(alerta);
		}
		return valida;
		
	}
</script>


<?php



//all
extract($_GET);
extract($_POST);

function fichaAnamnese($ID = null){
	
	global $id_matricula;
	
	!is_numeric($id_matricula) ? $id_matricula = base64_decode($id_matricula) : $id_matricula;
	
	echo "<form method='post' action='fichaAnamnese.php?id_matricula=".base64_encode($id_matricula)."' enctype='multipart/form-data' onsubmit='return filtro();'>";
	
	if($ID){
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='id_matricula_anamnese' value='$ID'>";
	}else{
		echo "<input type='hidden' name='op' value='novo'>";
	}
	
	echo "<table style='margin-bottom:0px;'>";
		echo "<tr>";
			echo "<td colspan='6'>";
				cabecalho();
			echo "</td>";
		echo "<tr>";
		
		$sql = query("select * from matricula where id='$id_matricula'");
		extract(mysqli_fetch_assoc($sql));
		
		echo "</tr>";
			echo "<td colspan='1'><span>ID Matrícula:</span>$id_matricula</td>";
			echo "<td colspan='1'><span>ID Ficha:</span>$ID</td>";
			echo "<td colspan='1'><span>Nome:</span>";
			echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
			echo "</td>";
			echo "<td colspan='1'><span>Telefone:</span>$telefone</td>";
			
			if($ID){
				$sql = query("select * from matricula_anamnese where id='$ID'");
				extract(mysqli_fetch_assoc($sql));
			}else{
				$data = $id_usuario = null;
			}
			echo "<td colspan='1'><span>Avaliador:</span>".registro($id_usuario, "usuario", "nome")."</td>";
			echo "<td colspan='1'><span>Data da avaliação:</span>".formataData($data)."</td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th colspan='6'>Ficha de Anamnese</th>";
		echo "</tr>";
		$divisor = 2;
		$sql = query("select * from matricula_anamnese_atributo_padrao where status='1'");
		for($i=0; $i<mysqli_num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			$idAtributo = $id;
			if($i%$divisor==0){
				if($i!=0){
					echo "</tr>";
				}
				echo "<tr>";
			}
			if($i%$divisor==0 and ($i+1) == mysqli_num_rows($sql)){
				$colspan = 6;
			}else{
				$colspan = 3;
			}
			echo "<td colspan='$colspan' id='id_".$idAtributo."' style='width:50%;'>";
				echo "<span>$nome</span>";
				if(!$ID){
					$Valor = null;
				}else{
					$instrucao = "select valor as Valor from matricula_anamnese_atributo where ";
					$instrucao .= "id_matricula_anamnese='$ID' and id_matricula_anamnese_atributo_padrao='$idAtributo'";
					$sqlSubValor = query($instrucao);
					if(mysqli_num_rows($sqlSubValor)>0){
						extract(mysqli_fetch_assoc($sqlSubValor));
					}else{
						$Valor = null;
					}
					
				}
				if($tipo_item=="Texto curto"){
					echo "<input type='text' name='atributo_".$idAtributo."' value='$Valor' style='width:98%'>";
				}elseif($tipo_item=="Texto com parágrafo"){
					echo "<span id='imp' style='background-color:white; border:none;'>$Valor</span>";
					echo "<textarea id='naoImp' name='atributo_".$idAtributo."' style='width:98%'>$Valor</textarea>";
				}elseif($tipo_item=="Seleção de itens"){
					$sqlSub = query("select * from matricula_anamnese_atributo_padrao_sub where id_matricula_anamnese_atributo_padrao='$id'");
					$script = "<select id='naoImp' name='atributo_".$idAtributo."'>";
					for($j=0; $j<mysqli_num_rows($sqlSub); $j++){
						extract(mysqli_fetch_assoc($sqlSub));
						if($Valor==$valor){
							echo "<div id='imp'><span id='imp' class='chackbox checkboxChecado'></span> $valor |</div>";
							$script .= "<option value='$valor' selected='yes'>$valor</option>";
						}else{
							echo "<div id='imp'><span class='chackbox'></span> $valor |</div>";
							$script .= "<option value='$valor'>$valor</option>";
						}
					}
					$script .= "</select>";
					echo $script;
				}
				
			echo "</td>";
		}
		
		echo "<tr>";
			echo "<th colspan='6'>Termo de responsabilidade</th>";
		echo "</tr>";
		echo "<tr>";
			echo "<td colspan='6' style='text-align:center;'>";
			echo "<div>Eu (";
			echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
			echo ") ";
			echo " declaro que todas as informações acima são verdadeiras e que não omiti nenhuma outra relacionada a minha saúde.";
			echo "<br><br><br>_____________________________________________________<br>";
			echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
			echo "</div></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td colspan='6' id='naoImp' style='border:none; text-align:right;'>";
				echo "<input type='submit' class='btnEnviar' value='Enviar'> ";
				echo "<input type='reset' value='Cancelar'>";
			echo "</td>";
		echo "</tr>";
		
		echo "</form>";
		
	echo "</table>";
	
	
}

if(!isset($op)){
	
	fichaAnamnese();

}else{
	$idAnamnese = base64_decode($id);

	if($op=="novo"){
		
		$id_matricula = base64_decode($id_matricula);
		
		//insercao no matricula_anamnese
		$instrucao = "insert into matricula_anamnese ";
		$instrucao .= "(id_matricula, data, id_usuario) values ";
		$instrucao .= "('$id_matricula', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
		$sql = query($instrucao);
		$id_matricula_anamnese = mysqli_insert_id($conexao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inseriu uma nova ficha de anamnese.";
		$tabela_afetada = "matricula_anamnese";
		$chave_principal = $id_matricula_anamnese;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

		//insercao no matricula_anamnese_atributo
		$instrucao = "insert into matricula_anamnese_atributo ";
		$instrucao .= "(id_matricula_anamnese, id_matricula_anamnese_atributo_padrao, valor) values ";
		$cont = count($_POST);
		$array = array_keys($_POST);
		for($i=1; $i<$cont; $i++){
			if($i!=1){
				$instrucao .= ", ";
			}
			$idAtributo = explode("_", $array[$i]);
			$instrucao .= "('$id_matricula_anamnese', '".$idAtributo[1]."','".$_POST[$array[$i]]."')";	
		}
		$sql = query($instrucao);

	}elseif($op=="editar"){
		$id_matricula = base64_decode($id_matricula);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou uma ficha de anamnese.";
		$tabela_afetada = "matricula_anamnese";
		$chave_principal = $id_matricula_anamnese;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);

		//deletando no matricula_anamnese_atributo
		$instrucao = "delete from matricula_anamnese_atributo where ";
		$instrucao .= "id_matricula_anamnese='$id_matricula_anamnese'";
		$sql = query($instrucao);
		
		//inserindo no matricula_anamnese_atributo
		$instrucao = "insert into matricula_anamnese_atributo ";
		$instrucao .= "(id_matricula_anamnese, id_matricula_anamnese_atributo_padrao, valor) values ";
		$cont = count($_POST);
		$array = array_keys($_POST);
		for($i=2; $i<$cont; $i++){
			if($i!=2){
				$instrucao .= ", ";
			}
			$idAtributo = explode("_", $array[$i]);
			$instrucao .= "('$id_matricula_anamnese', '".$idAtributo[1]."','".$_POST[$array[$i]]."')";	
		}
		$sql = query($instrucao);
		
	}elseif($op=="deletar"){
		$id_matricula = base64_decode($id_matricula);
		$id_matricula_anamnese = base64_decode($id_matricula_anamnese);
		if(!isset($confirm)){
			echo "<form method='post' action='fichaAnamnese.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='id_matricula' value='".base64_encode($id_matricula)."'>";
				echo "<input type='hidden' name='id_matricula_anamnese' value='".base64_encode($id_matricula_anamnese)."'>";
				echo "<input type='hidden' name='confirm' value='1'>";
				echo "<input type='hidden' name='op' value='deletar'>";
				echo "<h1>Você deseja mesmo deletar essa ficha de anamnese?</h1>";
				echo "<input class='aSubmit' type='submit' value='Sim'><br><br>";
				echo "<a href='#' class='aSubmit' onclick='window.close();'>Não</a>";
			echo "</form>";
		}else{
			//update na matricula_anamnese
			$instrucao = "delete from matricula_anamnese ";
			$instrucao .= "where id='$id_matricula_anamnese'";
			$sql = query($instrucao);
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Deletou uma ficha de anamnese.";
			$tabela_afetada = "matricula_anamnese";
			$chave_principal = $id_matricula_anamnese;
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			echo "<script language=\"JavaScript\" type=\"text/javascript\">";
				echo "window.opener.location.reload();";
				echo "window.close()"; 
			echo "</script>";
		}
	}
	if(!is_numeric($id_matricula_anamnese)){
		$id_matricula_anamnese = base64_decode($id_matricula_anamnese);
	}
	if($op!="deletar"){
		fichaAnamnese($id_matricula_anamnese);	
	}
	
}


//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script>