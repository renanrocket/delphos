<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
	
	//caso o post da subcategoria e o get da subcategoriaID n existir ou seja caso o usuario abrir a pagina pela primeira vez
	if (empty($_POST["subcategoria"]) and empty($_GET["subcategoriaID"])){
		echo "<form method='post' action='subCategoriaProduto.php' enctype='multipart/form-data'>";
			echo "Digite o nome da nova subcategoria:<br>";
			echo "<input type='text' name='subcategoria'><br>";
			echo "Categoria<a href='categoriaProduto.php' title='Cadastrar nova categoria'><img class='imgHelp' src='../img/mais.png'></a><br>
			<select name='categoria'>";
			echo "<option value=''>--</option>";
			$sql= query("select id, nome from categoria order by nome");
			while($row= mysqli_fetch_assoc($sql)){
				extract($row);
				echo "<option value='$id'>$nome</option>";
			}				
			echo "</select><br>";
			echo "Uma breve descri&ccedil;&atilde;o<br>";
			echo "<textarea name='descricao'></textarea><br>";
			echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
		echo "</form>";
	} elseif(!empty($_POST["subcategoria"]) and empty($_GET["subcategoriaID"])){// caso o usuario tiver clicado para inserir uma nova subcategoria
		extract($_POST);// subcategoria categoria descricao
		if (mysqli_num_rows(query("select * from sub_categoria where nome='$subcategoria'"))){//verifica se a subcategoria q ele qr inserir ja existe
			echo "J&aacute; existe uma subcategoria com esse nome $subcategoria.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=subCategoriaProduto.php'>";
		}else{// caso n exista conflito de nomes de subcategoria inserir a subcategoria
			$sql = query("insert into sub_categoria (nome, id_categoria, descricao) values ('$subcategoria', '$categoria', '$descricao')");
			if (mysqli_affected_rows($conexao)>0){
				echo "Sub Categoria $subcategoria inserida no Banco de Dados.<br>";
				echo "<meta http-equiv=Refresh content='3; URL=subCategoriaProduto.php'>";
			}else{
				include_once "inc/msgErro.php";
			}
		}
	}
	if (empty($_GET["subcategoriaID"])){// exibir as subcategoria existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
	//exibir as subcategorias existentes.
		echo "<table>";
		echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Nome da subcategoria</td>";
		echo "<td>Categoria associada</td>";
		echo "<td>Descri&ccedil;&atilde;o</td>";
		echo "<td>Status</td>";
		echo "</tr>";
		
		$sql= query("select * from sub_categoria");
		
		
		while ($reg = mysqli_fetch_assoc($sql)){
			extract($reg); // id nome id_categoria descricao
			echo "<tr>";
			echo "<form method='get' action='subCategoriaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='subcategoriaID' value='$id'>";
			echo "<td><input type='submit' value='$id'></td>";
			echo "<td>$nome</td>";
			$Sql= query("select nome from categoria where id='$id_categoria'");
			$reg= mysqli_fetch_row($Sql);
			echo "<td>$reg[0]</td>";
			echo "<td>$descricao</td>";
			echo "<td>$status</td>";
			echo "</form>";
			echo "</tr>";
		}
		echo "</table>";
	}else{//formulario para editar  subcategoria
		extract($_GET); //subcategoriaID
		
		$sql= query("select * from sub_categoria where id='$subcategoriaID'");
		extract($reg = mysqli_fetch_assoc($sql)); //id nome id_categoria descricao
		echo "<form method='post' action='subCategoriaProduto.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "Nome da subcategoria:<br>";
			echo "<input type='text' name='subcategoria' value='$nome'><br>";
			echo "Categoria<a href='categoriaProduto.php' title='Cadastrar nova categoria'><img class='imgHelp' src='../img/mais.png'></a><br>
			<select name='categoria'>";
			echo "<option value=''>--</option>";
			$sql= query("select id, nome from categoria order by nome");
			while($row= mysqli_fetch_assoc($sql)){
				extract($row);
				if ($id_categoria == $id){
					echo "<option value='$id' selected='yes'>$nome</option>";
				}else{
					echo "<option value='$id'>$nome</option>";
				}
			}				
			echo "</select><br>";
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
	extract($_POST);// op id subcategoria categoria descricao
	
	if (mysqli_num_rows(query("select * from sub_categoria where nome='$subcategoria' and id<>'$id'"))){//verifica se a subcategoria q ele qr inserir ja existe
		echo "J&aacute; existe uma subcategoria com esse nome $subcategoria.<br>";
		echo "<meta http-equiv=Refresh content='3; URL=subCategoriaProduto.php'>";
	}else{// caso n exista conflito de nomes de subcategoria inserir a subcategoria
		$instrucao = "update sub_categoria set nome='$subcategoria', id_categoria='$categoria', descricao='$descricao', status='$status' where id='$id'";
		$sql = query($instrucao);
		if (mysqli_affected_rows($conexao)>0){
			echo "Sub Categoria $subcategoria atualizada no Banco de Dados.<br>";
			echo "<meta http-equiv=Refresh content='3; URL=subCategoriaProduto.php'>";
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