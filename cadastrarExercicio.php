<?php
include "templates/upLogin.inc.php";

extract($_POST);
extract($_GET);

function formExercicio($ID = null){
	
	echo "<script type='text/javascript' src='js/cadastrarExercicio.js'></script>";
	echo "<form method='post' action='cadastrarExercicio.php' enctype='mulitpar/form-data' onsubmit='return filtro();'>";
	
	if($ID){
		$sql = query("select * from exercicio where id='$ID'");
		extract(mysqli_fetch_assoc($sql));
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='id' value='$ID'>";
		
		$sqlMusculo = query("select * from  exercicio_musculo where id_exercicio='$ID'");
		$qtdMusculo = mysqli_num_rows($sqlMusculo);
	}else{
		echo "<input type='hidden' name='op' value='novo'>";
		$nome = $beneficios = $status = null;
		$qtdMusculo = 0;
	}
	
	echo "<table id='gradient-style'>";
		echo "<tr>";
			echo "<th>";
			echo "Exercicio $ID <span style='float:right;'>";
			if($ID){
				if($status){
					echo "<a title='Inativar este exercício.' href='cadastrarExercicio.php?op=inativar&id=".base64_encode($ID)."'><img style='width:30px;' src='img/deletar.png'></a>";
				}else{
					echo "<a title='Ativar este exercício.' href='cadastrarExercicio.php?op=ativar&id=".base64_encode($ID)."'><img style='width:30px;' src='img/inserir.png'></a>";
				}
			}
			echo "</span></th>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td>Nome do exercício<br><input type='text' name='nome' value='$nome'></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td>Benefícios <textarea class='ckeditor' name='beneficios'>$beneficios</textarea></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td>";
			echo "<a href='#' title='Adicionar Musculo' ".pop("musculo.php")."><img style='width:30px' src='img/mais.png'></a> ";
			echo " Musculos envolvidos neste exercicio ";
			$qtdMusculo == 0 ? $valor = 1 : $valor = $qtdMusculo;
			echo "<input type='text' name='qtdMusculo' onchange='showMusculo(this.value);' value='$valor' ".mascara("Integer", "2", "style='width:30px;'")."><br>";
			for($i=1; $i<=99; $i++){
				if($i<=$qtdMusculo){
					extract(mysqli_fetch_assoc($sqlMusculo));
					$style = "style='width:auto;'";
				}elseif($i<=$valor){
					$id_musculo = null;
					$style = "style='width:auto;'";
				}else{
					$id_musculo = null;
					$style = "style='width:auto; display:none;'";
				}
				
				echo "<select name='listaMusculo[]' $style id='musculo_".$i."'>";
					echo opcaoSelect("musculos", "nome", 1, $id_musculo, null, "order by nome", false, false);
				echo "</select>";
				
			}
			echo "</td>";
		echo "</tr>";
		
		if($ID){
			
			$sql = query("select * from exercicio_imagem where id_exercicio='$ID'");
			$imagensQtd = mysqli_num_rows($sql);
			
			echo "<tr>";
				echo "<th><a href='#' ".pop("exercicio_imagem_upload.php?id=$ID", 600, 700)." title='Adicionar imagens para $nome'><img class='imgHelp' src='img/mais.png'></a> Imagens deste exercicio</th>";
			echo "<tr>";
			if($imagensQtd>0){
				echo "<tr>";
				echo "<td align='center'>";
				
				echo showImagemExercicio($ID, 8, 0, true);
				
				echo "</td>";
				echo "</tr>";
			}
			
		}
		
		echo "<tr>";
			echo "<th style='text-align:right'><input type='submit' class='btnEnviar' value='Enviar'><input type='reset' value='Cancelar'></th>";
		echo "</tr>";
		
	echo "</table>";
	
	echo "</form>";
}



if(!isset($op)){
	
	formExercicio();
	
}else{
		
		
	if($op=="novo"){
		
		//insert no exercicio		
		$instrucao = "insert into exercicio ";
		$instrucao.= "(nome, beneficios) values ";
		$instrucao.= "('$nome', '$beneficios') ";
		$sql = query($instrucao);
		
		
		$idExercicio = mysqli_insert_id($conexao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Cadastrou um novo exercício.";
		$tabela_afetada = "exercicio";
		$chave_principal = $idExercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		if(isset($listaMusculo)){
			//insert no exercicio_musculo
			$instrucao = "insert into exercicio_musculo ";
			$instrucao.= "(id_exercicio, id_musculo) values ";
			for($i=0; $i<$qtdMusculo; $i++){
				if($i!=0){
					$instrucao.= ", ";
				}
				$instrucao.= "('$idExercicio', '".$listaMusculo[$i]."')";
			}
			$sql = query($instrucao);
		}
		
		
		$msg = "Exercício $nome cadastrado com sucesso.";
		info($msg, "green");
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarExercicio.php?op=visualizar&id=".base64_encode($idExercicio)."'>";
		
	}elseif($op=="editar"){
		$idExercicio = base64_decode($id);
		
		//insert no exercicio		
		$instrucao = "update exercicio set ";
		$instrucao.= "nome='$nome', beneficios='$beneficios'";
		$instrucao.= "where id='$idExercicio'";
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou um exercício.";
		$tabela_afetada = "exercicio";
		$chave_principal = $idExercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		if(isset($listaMusculo)){
			//insert no exercicio_musculo
			$sql = query("delete from exercicio_musculo where id_exercicio='$idExercicio'");
			$instrucao = "insert into exercicio_musculo ";
			$instrucao.= "(id_exercicio, id_musculo) values ";
			for($i=0; $i<$qtdMusculo; $i++){
				if($i!=0){
					$instrucao.= ", ";
				}
				$instrucao.= "('$idExercicio', '".$listaMusculo[$i]."')";
			}
			$sql = query($instrucao);
		}
		
	
		$msg = "Exercício $nome editado com sucesso.";
		info($msg, "yellow");
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarExercicio.php?op=visualizar&id=".base64_encode($idExercicio)."'>";
	
	}elseif($op=="inativar"){
		
		$idExercicio = base64_decode($id);
		
		//insert no exercicio		
		$instrucao = "update exercicio set ";
		$instrucao.= "status='0'";
		$instrucao.= "where id='$idExercicio'";
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inativou um exercício.";
		$tabela_afetada = "exercicio";
		$chave_principal = $idExercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		$msg = "Exercício inativado com sucesso.";
		info($msg, "red");
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarExercicio.php?op=visualizar&id=".base64_encode($idExercicio)."'>";
		
	}elseif($op=="ativar"){
		
		$idExercicio = base64_decode($id);
		
		//insert no exercicio		
		$instrucao = "update exercicio set ";
		$instrucao.= "status='1'";
		$instrucao.= "where id='$idExercicio'";
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Ativou um exercício.";
		$tabela_afetada = "exercicio";
		$chave_principal = $idExercicio;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		$msg = "Exercício ativado com sucesso.";
		info($msg, "green");
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarExercicio.php?op=visualizar&id=".base64_encode($idExercicio)."'>";
	}else{
		$idExercicio = base64_decode($id);
	}
	
	formExercicio($idExercicio);
}


include "templates/downLogin.inc.php";
?>

