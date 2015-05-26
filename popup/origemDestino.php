<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
	
	//caso o post da origem_destino e o get da origem_destinoID n existir ou seja caso o usuario abrir a pagina pela primeira vez
	if (empty($_POST["origem_destino"]) and empty($_GET["origem_destinoID"])){
		echo "<form method='post' action='origemDestino.php' enctype='multipart/form-data'>";
			echo "Digite o nome do novo Origem / Destino:<br>";
			echo "<input type='text' name='origem_destino'><br>";
			echo "Pode-se retirar produtos deste local indeterminadamente?<br>";
			echo "Por exemplo: Fornecedor Externo é um local em que pode-se tirar produtos indeterminadamente.";
			echo "<input type='radio' name='ex' value='1' checked>Sim <input type='radio' name='ex' value='0'>Não<br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	} elseif(!empty($_POST["origem_destino"]) and empty($_GET["origem_destinoID"])){// caso o usuario tiver clicado para inserir uma nova origem_destino
		extract($_POST);// origem_destino descricao
		if (mysqli_num_rows(query("select * from origem_destino where o_d='$origem_destino'"))){//verifica se a origem_destino q ele qr inserir ja existe
			echo "J&aacute; existe uma Local com esse nome $origem_destino.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=origemDestino.php'>";
		}else{// caso n exista conflito de nomes de origem_destino inserir a origem_destino
			$sql = query("insert into origem_destino (o_d, ex) values ('$origem_destino', '$ex')");
			if (mysqli_affected_rows($conexao)){
				echo "O local $origem_destino inserida no Banco de Dados.<br>";
				echo "<meta http-equiv=Refresh content='3; URL=origemDestino.php'>";
			}else{
				include_once "../inc/msgErro.php";
			}
		}
	}
	if (empty($_GET["origem_destinoID"])){// exibir as origem_destino existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
	//exibir as origem_destinos existentes.
		echo "<table>";
		echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Nome do Local</td>";
		echo "<td>Permite ser exportado:</td>";
		echo "</tr>";
		
		$sql= query("select * from origem_destino");
		
		
		while ($reg = mysqli_fetch_assoc($sql)){
			extract($reg); // id nome descricao
			echo "<tr>";
			echo "<form method='get' action='origemDestino.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='origem_destinoID' value='$id'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "<td>$o_d</td>";
			echo "<td>";
			if($ex=='1')
				echo "Sim";
			else
				echo "Não";
			echo "</td>";
			echo "</form>";
			echo "</tr>";
		}
		echo "</table>";
	}else{//formulario para editar  origem_destino
		extract($_GET); //origem_destinoID
		
		$sql= query("select * from origem_destino where id='$origem_destinoID'");
		extract($reg = mysqli_fetch_assoc($sql)); //id nome descricao
		echo "<form method='post' action='origemDestino.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "Nome do Local:<br>";
			if(is_numeric($o_d)){
				echo registro($o_d, "cliente_fornecedor", "nome")." ".registro($o_d, "cliente_fornecedor", "razao_social")." <br>Para editar esse nome, edite o cadastro do fornecedor de ID: $o_d";	
			}else{
				echo "<input type='text' name='origem_destino' value='$o_d'><br>";
			}
			echo "Pode-se retirar produtos deste local indeterminadamente?<br>";
			echo "Por exemplo: Fornecedor Externo é um local em que pode-se tirar produtos indeterminadamente.";
			if($ex=='1'){
				echo "<input type='radio' name='ex' value='1' checked>Sim <input type='radio' name='ex' value='0'>Não<br>";	
			}else{
				echo "<input type='radio' name='ex' value='1'>Sim <input type='radio' name='ex' value='0' checked>Não<br>";
			}
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	
	}
}else{
	extract($_POST);// op id origem_destino descricao
	
	if (mysqli_num_rows(query("select * from origem_destino where o_d='$origem_destino' and id<>'$id'"))){//verifica se a origem_destino q ele qr inserir ja existe
		echo "J&aacute; existe um local com esse nome $origem_destino.<br>";
		echo "<meta http-equiv=Refresh content='3; URL=origemDestino.php'>";
	}else{// caso n exista conflito de nomes de origem_destino inserir a origem_destino
		$sql = query("update origem_destino set o_d='$origem_destino', ex='$ex' where id='$id'");
		if (mysqli_affected_rows()){
			echo "Local $origem_destino atualizada no Banco de Dados.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=origem_destino.php'>";
		}else{
			include_once "../inc/msgErro.php";
		}
	}
}


	
//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script> 