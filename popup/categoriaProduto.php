<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
	
	//caso o post da categoria e o get da categoriaID n existir ou seja caso o usuario abrir a pagina pela primeira vez
	if (empty($_POST["categoria"]) and empty($_GET["categoriaID"])){
		echo "<form method='post' action='categoriaProduto.php' enctype='multipart/form-data'>";
			echo "Digite o nome da nova categoria:<br>";
			echo "<input type='text' name='categoria'><br>";
			echo "Uma breve descri&ccedil;&atilde;o<br>";
			echo "<textarea name='descricao'></textarea><br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	} elseif(!empty($_POST["categoria"]) and empty($_GET["categoriaID"])){// caso o usuario tiver clicado para inserir uma nova categoria
		extract($_POST);// categoria descricao
		if (mysqli_num_rows(query("select * from categoria where nome='$categoria'"))){//verifica se a categoria q ele qr inserir ja existe
			echo "J&aacute; existe uma categoria com esse nome $categoria.";
			echo "<meta http-equiv=Refresh content='3; URL=categoriaProduto.php'>";
		}else{// caso n exista conflito de nomes de categoria inserir a categoria
			$sql = query("insert into categoria (nome, descricao) values ('$categoria', '$descricao')");
			if (mysqli_affected_rows($conexao)){
				echo "Categoria $categoria inserida no Banco de Dados.<br>";
				echo "<meta http-equiv=Refresh content='3; URL=categoriaProduto.php'>";
			}else{
				include_once "inc/msgErro.php";
			}
		}
	}
	if (empty($_GET["categoriaID"])){// exibir as categoria existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
	//exibir as categorias existentes.
		echo "<table>";
		echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Nome da categoria</td>";
		echo "<td>Descri&ccedil;&atilde;o</td>";
		echo "<td>Status</td>";
		echo "</tr>";
		
		$sql= query("select * from categoria order by status");
		
		
		while ($reg = mysqli_fetch_assoc($sql)){
			extract($reg); // id nome descricao
			echo "<tr>";
			echo "<form method='get' action='categoriaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='categoriaID' value='$id'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "<td>$nome</td>";
			echo "<td>$descricao</td>";
			echo "<td>$status</td>";
			echo "</form>";
			echo "</tr>";
		}
		echo "</table>";
	}else{//formulario para editar  categoria
		extract($_GET); //categoriaID
		
		$sql= query("select * from categoria where id='$categoriaID'");
		extract(mysqli_fetch_assoc($sql)); //id nome descricao
		echo "<form method='post' action='categoriaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "Nome da categoria:<br>";
			echo "<input type='text' name='categoria' value='$nome'><br>";
			echo "Uma breve descri&ccedil;&atilde;o<br>";
			echo "<textarea name='descricao'>$descricao</textarea><br>";
			echo "Status<br>";
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
	extract($_POST);// op id categoria descricao status
	
	if (mysqli_num_rows(query("select * from categoria where nome='$categoria' and id<>'$id'"))){//verifica se a categoria q ele qr inserir ja existe
		echo "J&aacute; existe uma categoria com esse nome $categoria.<br>";
		echo "<meta http-equiv=Refresh content='3; URL=categoriaProduto.php'>";
	}else{// caso n exista conflito de nomes de categoria inserir a categoria
		$sql = query("update categoria set nome='$categoria', descricao='$descricao', status='$status' where id='$id'");
		if (mysqli_affected_rows($conexao)){
			echo "Categoria $categoria atualizada no Banco de Dados.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=categoriaProduto.php'>";
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