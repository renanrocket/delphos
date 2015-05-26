<?php
include "funcoes.inc.php";


//deletar esse codigo apois migração do app.ado
file_exists("../conecta.php") ?
include "../conecta.php" : $conexao = null;
//fim

extract($_POST);

$sql = query("select * from cliente where (nome like '%$valor%' or razao_social like '%$valor%' or cnpj like '%$valor%') and status='1' limit 0,1");
$linha = mysqli_num_rows($sql);

if ($linha) {
	for ($i = 0; $i < $linha; $i++) {
		extract(mysqli_fetch_assoc($sql));
		if ($i == 0) {
			echo "<ul>";
		}
		echo "<li onclick='preencher(\"$id\", \"$nome\")'><img src='$imgsrc' style='height:3em; vertical-align: middle;'>$nome</li>";
		if ($linha == ($i + 1)) {
			echo "</ul>";
		}
	}
} else {
	echo "<ul>";
	echo "<li>Não foi encontrado nenhuma<br>empresa com esse nome.</li>";
	echo "</ul>";
}
?>