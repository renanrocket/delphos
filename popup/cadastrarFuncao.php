<?php
		include_once "../templates/upLoginImp.inc.php";
	echo "
		<script type='text/javascript' src='../js/funcoes.js'></script>
		<script type='text/javascript' src='../js/mascara.js'></script>
        <script type='text/javascript' src='../js/jquery.js'></script>
		";

?>

<form name='incluiFuncao' method='post' action='cadastrarFuncao2.php' enctype='multipart/form-data'>
	<table>
		<tr>
			<td>
				Nome Função <br>
				<?php
					if ($_POST){
						extract($_POST);
						echo "<input type='hidden' name='op' value='editar'>";
						echo "<input type='hidden' name='grava_id' value='$id_funcao'>";
					}else{
						echo "<input type='hidden' name='op' value='novo'>";
						$id_funcao = $nome_funcao = "";
					}
					
					echo "$id_funcao <input type='text' name='grava_funcao' value='$nome_funcao'>";

				?>
			</td>
		</tr>
		<tr>
			<td>
				<input type='submit' class='btnEnviar' value='Enviar'>
			</td>
		</tr>
	</table>
</form>

<?php
	
	$conn = TConnection::open(ALCUNHA);
	$sql = new TSqlSelect;
	$sql->setEntity('funcao');
	$sql->addColumn('*');
	$result = $conn->query($sql->getInstruction());
	
	echo "<table>";
	
	for($i=0; $i<$result->rowCount(); $i++){
		
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		
		if($status=="Ativo"){
			$cod = "<input type='hidden' name='op' value='deletar'><input type='hidden' name='id_funcao' value='$id'><input type='submit' value='X'>";
		}else{
			$cod = "<input type='hidden' name='op' value='ativar'><input type='hidden' name='id_funcao' value='$id'><input type='submit' value='Ativar'>";
		}
		
		echo "<tr>
		<form method='post' action='cadastrarFuncao.php' enctype='multipart/form-data'>
		<td>
		<input name='id_funcao' type='submit' value='$id'>
		</td>
		<td>
		<input type='hidden' name='nome_funcao' value='$nome'>
		$nome
		</td>
		</form>
		<form method='get' action='cadastrarFuncao2.php' enctype='multipart/form-data'>
		<td>
		$cod
		</td>
		</form>
		</tr>";
	}
	
	echo "</table>";

	include_once "../templates/downLoginImp.inc.php";
?>	
