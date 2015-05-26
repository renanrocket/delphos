<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
	
	//caso o post da marca e o get da marcaID n existir ou seja caso o usuario abrir a pagina pela primeira vez
	if (empty($_POST["marca"]) and empty($_GET["marcaID"])){
		echo "<form method='post' action='marcaProduto.php' enctype='multipart/form-data'>";
			echo "Digite o nome da nova marca:<br>";
			echo "<input type='text' name='marca'><br>";
			echo "Uma breve descri&ccedil;&atilde;o<br>";
			echo "<textarea name='descricao'></textarea><br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	} elseif(!empty($_POST["marca"]) and empty($_GET["marcaID"])){// caso o usuario tiver clicado para inserir uma nova marca
		extract($_POST);// marca descricao
		if (mysqli_num_rows(query("select * from marca where nome='$marca'"))){//verifica se a marca q ele qr inserir ja existe
			echo "J&aacute; existe uma marca com esse nome $marca.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=marcaProduto.php'>";
		}else{// caso n exista conflito de nomes de marca inserir a marca
			$sql = query("insert into marca (nome, descricao) values ('$marca', '$descricao')");
			if (mysqli_affected_rows($conexao)){
				echo "Marca $marca inserida no Banco de Dados.<br>";
				echo "<meta http-equiv=Refresh content='3; URL=marcaProduto.php'>";
			}else{
				include_once "inc/msgErro.php";
			}
		}
	}
	if (empty($_GET["marcaID"])){// exibir as marca existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
	//exibir as marcas existentes.
		echo "<table>";
		echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Nome da marca</td>";
		echo "<td>Descri&ccedil;&atilde;o</td>";
		echo "<td>Status</td>";
		echo "</tr>";
		
		$sql= query("select * from marca");
		
		
		while ($reg = mysqli_fetch_assoc($sql)){
			extract($reg); // id nome descricao
			echo "<tr>";
			echo "<form method='get' action='marcaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='marcaID' value='$id'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "<td>$nome</td>";
			echo "<td>$descricao</td>";
			echo "<td>$status</td>";
			echo "</form>";
			echo "</tr>";
		}
		echo "</table>";
	}else{//formulario para editar  marca
		extract($_GET); //marcaID
		
		$sql= query("select * from marca where id='$marcaID'");
		extract($reg = mysqli_fetch_assoc($sql)); //id nome descricao
		echo "<form method='post' action='marcaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "Nome da marca:<br>";
			echo "<input type='text' name='marca' value='$nome'><br>";
			echo "Uma breve descri&ccedil;&atilde;o<br>";
			echo "<textarea name='descricao'>$descricao</textarea><br>";
			echo "<select name='status'>";
			if($status=="Ativo"){
				echo "<option value='Ativo' selected='yes'>Ativo</option>";
				echo "<option value='Inativo'>Inativo</option>";
			}else{
				echo "<option value='Ativo'>Ativo</option>";
				echo "<option value='Inativo' selected='yes'>Inativo</option>";
			}
			echo "</select><br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	
	}
}else{
	extract($_POST);// op id marca descricao
	if (mysqli_num_rows(query("select * from marca where nome='$marca' and id<>'$id'"))){//verifica se a marca q ele qr inserir ja existe
		echo "J&aacute; existe uma marca com esse nome $marca.<br>";
		echo "<meta http-equiv=Refresh content='3; URL=marcaProduto.php'>";
	}else{// caso n exista conflito de nomes de marca inserir a marca
		$sql = query("update marca set nome='$marca', descricao='$descricao', status='$status' where id='$id'");
		if (mysqli_affected_rows($conexao)){
			echo "Marca $marca atualizada no Banco de Dados.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=marcaProduto.php'>";
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