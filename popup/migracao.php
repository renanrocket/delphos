<?php
include "../templates/upLoginImp.inc.php";
$sql = query("select * from produto");
for($i=0; $i<mysqli_num_rows($sql); $i++){

	extract(mysqli_fetch_assoc($sql));
	$mlpor = $mlpor * 100 / $valor_compra;
	$nome = str_replace('"', 'P', $nome);
	$descricao = "NCM/SH ".$descricao;

	$sql2 = query("select id as idCategoria from categoria where nome='$id_categoria'");
	if(!mysqli_num_rows($sql2)){
		$sql2 = query("insert into categoria set nome='$id_categoria'");
		$id_categoria = mysqli_insert_id($conexao);
	}
	
	$sql2 = query("select id as idVolume from produto_volume where nome='$id_volume'");
	if(!mysqli_num_rows($sql2)){
		$sql2 = query("insert into produto_volume set nome='$id_volume', abreviatura='$id_volume'");
		$id_volume = mysqli_insert_id($conexao);
	}


	$sql2 = query("update produto set descMaxpor='10', mlpor='$mlpor', id_volume='$id_volume', id_categoria='$id_categoria', status='1', nome='$nome', descricao='$descricao' where id='$id'");
	echo "Produto $id atualizado $nome $descricao $mlpor<br>";

}

include "../templates/downLoginImp.inc.php";

?>