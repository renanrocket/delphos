<?php
include "templates/upLogin.inc.php";

echo "<center>";
	echo "<table id='gradient-style' summary='Resultado da pesquisa'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th scope='col'>ID</th>";
				echo "<th scope='col'>Nome Completo</th>";
				echo "<th scope='col'>Login</th>";
				echo "<th scope='col'>Função</th>";
				echo "<th scope='col'>Data de Admissão</th>";
				echo "<th scope='col'>Telefone 1</th>";
				echo "<th scope='col'>Status</th>";
			echo "</tr>";
		echo "</thead>";
		$sql = query("select id, nome, login, funcao, data_admissao, telefone1, status from usuario where id<>1 order by status, nome");
		$linha = mysqli_num_rows($sql);
		echo "<tfoot>";
			echo "<tr>";
				echo "<td colspan='7' align='center'>Existem $linha usuário(s)</td>";
			echo "</tr>";
		echo "</tfoot>";

		echo "<tbody>";
for($i=0; $i<$linha; $i++){
	extract(mysqli_fetch_assoc($sql));
	echo "<tr>";
	echo "<form method='get' action='cadastrarUsuario.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='op' value='visualizar'>";
		echo "<input type='hidden' name='ID' value='".base64_encode($id)."'>";
		$usuarioId= $id - 1;
		echo "<td><input type='submit' value='$usuarioId'></td>";
		echo "<td>$nome</td>";
		echo "<td>$login</td>";
		echo "<td>".registro($funcao, "funcao", "nome")."</td>";
		echo "<td>".formataData($data_admissao)."</td>";
		echo "<td style='white-space:nowrap;'>$telefone1</td>";
		echo "<td>$status</td>";
	echo "</form>";
	echo "</tr>";
}

echo "</tbody>
	</table>
</center>";

include "templates/downLogin.inc.php";
?>

