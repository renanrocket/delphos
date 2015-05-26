<?php
include "templates/upLogin.inc.php";

extract($_GET);
extract($_POST);

function anamnese_atributo_padrao($ID = null){
	
	echo "<script type='text/javascript' src='js/administrativoAnamneseAtributoPadrao.js'></script>";

	echo "<form method='post' action='administrativoAnamneseAtributoPadrao.php' enctype='multpart/form-data' onsubmit='return filtro();'>";

	if ($ID) {
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='id' value='".base64_encode($ID)."'>";
		$sql = query("select * from matricula_anamnese_atributo_padrao where id='$ID'");
		extract(mysqli_fetch_assoc($sql));
	} else {
		echo "<input type='hidden' name='op' value='novo'>";
		$tipo_item = $obrigatoriedade = $nome = $status = null;
	}
	
	echo "<table id='gradient-style'>";
	echo "<tr>";
	echo "<th colspan='3'>Insira uma pergunta padrão para a avaliação de Anamnese</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'>Pergunta<br><input type='text' name='pergunta' value='$nome'></td>";
	echo "<td>Obrigatóriedade<br>";
	echo "<select name='obrigatoriedade'>";
	if ($obrigatoriedade) {
		echo "<option value='1' selected='yes'>Sim</option>";
		echo "<option value='0'>Não</option>";
	} else {
		echo "<option value='1'>Sim</option>";
		echo "<option value='0' selected='yes'>Não</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'>Tipo de pergunta<br>";
	echo "<select name='tipo_item' onchange='showTipoItemSub(this.value)'>";
	if ($tipo_item == "Texto curto") {
		echo "<option value='Texto curto' selected='yes'>Texto curto</option>";
	} else {
		echo "<option value='Texto curto'>Texto curto</option>";
	}
	if ($tipo_item == 'Seleção de itens') {
		$styleSelect = "";
		echo "<option value='Seleção de itens' selected='yes'>Seleção de itens</option>";
	} else {
		$styleSelect = "style='display:none;'";
		echo "<option value='Seleção de itens'>Seleção de itens</option>";
	}
	if ($tipo_item == 'Texto com parágrafo') {
		echo "<option value='Texto com parágrafo' selected='yes'>Texto com parágrafo</option>";
	} else {
		echo "<option value='Texto com parágrafo'>Texto com parágrafo</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "<td>";


	$sql = query("select * from matricula_anamnese_atributo_padrao_sub where id_matricula_anamnese_atributo_padrao='$ID'");
	if(mysqli_num_rows($sql)==1){
		extract(mysqli_fetch_assoc($sql));
		$qtdEscolha='2';
	}elseif(mysqli_num_rows($sql)>0){
		$qtdEscolha = mysqli_num_rows($sql);
		$valor = null;
	}else{
		$qtdEscolha = '2';
		$valor = null;
	}
	
	echo "<span id='select' $styleSelect>";
	echo "<div>Defina pelo menos 2 escolhas: <input type='text' ".mascara("Integer")." name='qtdEscolha' value='$qtdEscolha' onchange='showEscolhas(this.value)' style='width:40px;'></div>";
	$sql = query("select * from matricula_anamnese_atributo_padrao_sub where id_matricula_anamnese_atributo_padrao='$ID'");
	if(mysqli_num_rows($sql)==0){
		$linhas = 2;
	}else{
		$linhas = mysqli_num_rows($sql);
	}
	for($i=1; $i<100; $i++){
		if($i<=mysqli_num_rows($sql)){
			extract(mysqli_fetch_assoc($sql));
		}else{
			$valor = "Escolha $i";
		}
		if($i<=$linhas){
			echo "<div id='divEscolha_".$i."'><input type='text' name='escolha[]' value='$valor' style='width:50%;'></div>";
		}else{
			echo "<div id='divEscolha_".$i."' style='display:none;'><input type='text' name='escolha[]' value='$valor' style='width:50%;'></div>";
		}
	}
	
	
	echo "</span>";

	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th colspan='3' style='text-align: right;'><input type='submit' class='btnEnviar' value='Enviar'> <input type='reset' value='Cancelar'></th>";
	echo "</tr>";
	echo "</form>";
	if($ID){
		echo "</table>";
	}
	
}

function anamnese_atributo_padrao2(){
	echo "<tr>";
		echo "<th colspan='3'>Atributos <select style='width:auto;' onchange='showAtributos(this.value)'><option value='1'>Ativos</option><option value='0'>Inativos</option></select></th>";
	echo "</tr>";
	
	$sql = query("select * from matricula_anamnese_atributo_padrao order by status");
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		if($status){
			echo "<tr class='ativo'>";
			$operacao = "inativar";
		}else{
			$operacao = "ativar";
			echo "<tr class='inativo' style='display:none;'>";
		}
		
		
			echo "<form method='get' action='administrativoAnamneseAtributoPadrao.php' ectype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='hidden' name='id' value='".base64_encode($id)."'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "</form>";
			$obrigatoriedade == 0 ? $obrigatoriedade = "Não obriatório" : $obrigatoriedade = "Obrigatório";
			echo "<td>$nome | $tipo_item | $obrigatoriedade ";
			$sqlSub = query("select * from matricula_anamnese_atributo_padrao_sub where id_matricula_anamnese_atributo_padrao='$id'");
			echo "<br><span>";
			for($j=0; $j<mysqli_num_rows($sqlSub); $j++){
				if($j==0){
					echo "Escolhas: ";
				}
				extract(mysqli_fetch_assoc($sqlSub));
				echo $valor." | ";
				
			}
			echo "</span";
			echo "</td>";
			
			if($operacao=='ativar'){
				$operacao2='Ativar está pergunta.';
				$img='inserir';
			}else{
				$operacao2='Inativar está pergunta.';
				$img='deletar';
			}
			
			echo "<td><a title='$operacao2' href='administrativoAnamneseAtributoPadrao.php?op=".$operacao."&id=".base64_encode($id)."'><img src='img/$img.png'></a></td>";
			
		echo "</tr>";
	}
	echo "<table>";
}

if (!isset($op)) {
	
	anamnese_atributo_padrao();
	anamnese_atributo_padrao2();
	
}else{
	
	if($op=="visualizar"){
		anamnese_atributo_padrao(base64_decode($id));
	}elseif($op=="novo"){
			
		$instrucao = "insert into matricula_anamnese_atributo_padrao ";
		$instrucao .= "(tipo_item, obrigatoriedade, nome) ";
		$instrucao .= "values ";
		$instrucao .= "('$tipo_item', '$obrigatoriedade', '$pergunta')";
		
		$sql = query($instrucao);
		$idAnamneseAtributo = mysqli_insert_id($conexao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Cadastrou um novo atributo para ficha de Anamnese.";
		$tabela_afetada = "matricula_anamnese_atributo_padrao";
		$chave_principal = $idAnamneseAtributo;
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		if($tipo_item=="Seleção de itens"){
			
			$instrucao = "insert into matricula_anamnese_atributo_padrao_sub ";
			$instrucao .= "(id_matricula_anamnese_atributo_padrao, valor) ";
			$instrucao .= "values ";
			for($i=0; $i<$qtdEscolha; $i++){
				if($i!=0){
					$instrucao .= ", ";
				}
				$instrucao .= "('$idAnamneseAtributo', '".$_POST["escolha"][$i]."')";
			}
			
			$sql = query($instrucao);
		}
		
	}elseif($op=="editar"){
			
		$id = base64_decode($id);
		
		$instrucao = "update matricula_anamnese_atributo_padrao set ";
		$instrucao .= "tipo_item = '$tipo_item', obrigatoriedade = '$obrigatoriedade', nome = '$pergunta' ";
		$instrucao .= "where id='$id'";
		
		$sql = query($instrucao);
		$idAnamneseAtributo = $id;
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou um atributo para ficha de Anamnese.";
		$tabela_afetada = "matricula_anamnese_atributo_padrao";
		$chave_principal = $idAnamneseAtributo;
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		if($tipo_item=="Seleção de itens"){
				
			$instrucao = "delete from matricula_anamnese_atributo_padrao_sub ";
			$instrucao .= "where id_matricula_anamnese_atributo_padrao='$id'";
			$sql = query($instrucao);
			
			$instrucao = "insert into matricula_anamnese_atributo_padrao_sub ";
			$instrucao .= "(id_matricula_anamnese_atributo_padrao, valor) ";
			$instrucao .= "values ";
			for($i=0; $i<$qtdEscolha; $i++){
				if($i!=0){
					$instrucao .= ", ";
				}
				$instrucao .= "('$idAnamneseAtributo', '".$_POST["escolha"][$i]."')";
			}
			
			$sql = query($instrucao);
		}
	}elseif($op=="inativar"){
		
		$id = base64_decode($id);
		
		$instrucao = "update matricula_anamnese_atributo_padrao set ";
		$instrucao .= "status='0' ";
		$instrucao .= "where id='$id'";
		
		$sql = query($instrucao);
		$idAnamneseAtributo = $id;
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inativou um atributo para ficha de Anamnese.";
		$tabela_afetada = "matricula_anamnese_atributo_padrao";
		$chave_principal = $idAnamneseAtributo;
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
	}elseif($op=="ativar"){
				
		$id = base64_decode($id);
		
		$instrucao = "update matricula_anamnese_atributo_padrao set ";
		$instrucao .= "status='1' ";
		$instrucao .= "where id='$id'";
		
		$sql = query($instrucao);
		$idAnamneseAtributo = $id;
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inativou um atributo para ficha de Anamnese.";
		$tabela_afetada = "matricula_anamnese_atributo_padrao";
		$chave_principal = $idAnamneseAtributo;
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);	
		
	}
	
	if($op!= "visualizar"){
		anamnese_atributo_padrao();
		anamnese_atributo_padrao2();
	}
	
}

include "templates/downLogin.inc.php";
?>