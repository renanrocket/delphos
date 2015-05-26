<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
	
	//caso o post da subcategoria e o get da subcategoriaID n existir ou seja caso o usuario abrir a pagina pela primeira vez
	if ((empty($_POST["volume"]) or empty($_POST["abreviatura"])) and empty($_GET["volumeID"])){
		echo "<form method='post' action='volume.php' enctype='multipart/form-data'>";
			echo "Digite o nome do volume:<br>";
			echo "<input type='text' name='volume'><br>";
			echo "Abreviatura:<br>";
			echo "<input type='text' name='abreviatura'><br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	} elseif(!empty($_POST["volume"]) and !empty($_POST["abreviatura"]) and empty($_GET["volumeID"])){// caso o usuario tiver clicado para inserir uma nova volume
		extract($_POST);// volume categoria descricao
		if (mysqli_num_rows(query("select * from produto_volume where nome='$volume'"))){//verifica se a subcategoria q ele qr inserir ja existe
			echo "Já existe um volume com esse nome $volume.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
		}elseif( mysqli_num_rows(query("select * from produto_volume where abreviatura='$abreviatura'"))){
			echo "Já existe um volume ($volume) com essa abreviatura $abreviatura.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
		}else{// caso n exista conflito de nomes de volume inserir a volume
			$sql = query("insert into produto_volume (nome, abreviatura, status) values ('$volume', '$abreviatura', 'Ativo')");
			if (mysqli_affected_rows($conexao)>0){
				echo "O volume $volume foi inserido no Banco de Dados.<br>";
				echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
			}else{
				include_once "inc/msgErro.inc";
			}
		}
	}
	if (empty($_GET["volumeID"])){// exibir as volume existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
	//exibir as volume existentes.
		echo "<table>";
		echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Volume</td>";
		echo "<td>Abreviatura</td>";
		echo "<td>Status</td>";
		echo "</tr>";
		
		$sql= query("select * from produto_volume");
		
		
		while ($reg = mysqli_fetch_assoc($sql)){
			extract($reg); // id volume abreviatura status
			echo "<tr>";
			echo "<form method='get' action='volume.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='volumeID' value='$id'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "<td>$nome</td>";
			echo "<td>$abreviatura</td>";
			echo "<td>$status</td>";
			echo "</form>";
			echo "</tr>";
		}
		echo "</table>";
	}else{//formulario para editar  subcategoria
		extract($_GET); //volumeID
		
		$sql= query("select * from produto_volume where id='$volumeID'");
		extract($reg = mysqli_fetch_assoc($sql)); //id nome id_categoria descricao
		echo "<form method='post' action='volume.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "Volume:<br>";
			echo "<input type='text' name='volume' value='$nome'><br>";
			echo "Abreviatura:<br>";
			echo "<input type='text' name='abreviatura' value='$abreviatura'><br>";
			echo "Status<br>";
			echo "<select name='status'>";
				if($status=="Ativo"){
					echo "<option value='Ativo' selected='yes'>Ativo</option>";
					echo "<option value='Inativo'>Inativo</option>";
				}elseif($status=="Inativo"){
					echo "<option value='Ativo'>Ativo</option>";
					echo "<option value='Inativo' selected='yes'>Inativo</option>";
				}
			echo "</select>";
			echo "<br><input type='submit' class='btnEnviar' value='Enviar'>";
		echo "</form>";
	
	}
}else{
	extract($_POST);// op id volume abreviatura status
	
	if (mysqli_num_rows(query("select * from produto_volume where nome='$volume' and id<>'$id'"))){//verifica se a subcategoria q ele qr inserir ja existe
		echo "Já existe um volume com esse nome $volume.<br>";
		echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
	}elseif( mysqli_num_rows(query("select * from produto_volume where abreviatura='$abreviatura' and id<>'$id'"))){
			echo "Já existe um volume ($volume) com essa abreviatura $abreviatura.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
	}else{// caso n exista conflito de nomes de subcategoria inserir a subcategoria
		$instrucao = "update produto_volume set nome='$volume', abreviatura='$abreviatura', status='$status' where id='$id'";
		$sql = query($instrucao);
		if (mysqli_affected_rows($conexao)>0){
			echo "O volume $volume atualizada no Banco de Dados.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=volume.php'>";
		}else{
			include_once "../inc/msgErro.inc";
		}
	}
}


	
//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script> 