<?php

extract($_POST);
header('content-type: text/html; charset=ISO-8859-1');
include "funcoes.inc.php";
if(isset($_COOKIE["id_empresa"])){
	//includes do php para todas as paginas
	$conn = TConnection::open("gestor");

	$criterio = new TCriteria;
	$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

	$sql = new TSqlSelect;
	$sql->setEntity("cliente");
	$sql->addColumn('alcunha');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		define("ALCUNHA", $alcunha);
	}

	//deletar esse codigo apois migração do app.ado
	file_exists("../conecta.php") ?
	include "../conecta.php" : $conexao = null;
	$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
	extract(mysqli_fetch_assoc($sql));
	include "../".$conectasrc;
	//fim
}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}

$sql= query("select * from pagamento_tipo_sub where id_pagamento_tipo='$variavel'");
$linha = mysqli_num_rows($sql);

if(!isset($id)){
	if($linha>0){
		echo "<td colspan='5' align='right'>Sub tipo de pagamento</td>";
		echo "<td colspan='1'>";
			echo "<select name='subTipoPagamento' onchange='mudaTipoPagamentoSub(this.value, '1');'>";
			echo "<option value=''>--</option>";
			for($i=0; $i<$linha; $i++){
				extract(mysqli_fetch_assoc($sql));
				if($id==$tipoPagamentoSub){
					echo "<option value='$id_pagamento_tipo' selected='yes'>$sub_tipo_pagamento</option>";	
				}else{
					echo "<option value='$id_pagamento_tipo'>$sub_tipo_pagamento</option>";	
				}
				
			}
			echo "</select>";
		echo "</td>";
	}
}else{
	if($linha>0){
		echo "<select name='tipo_pagamento_sub' id='tipoPagamentoSub_$id' onchange=\"mudaTipoPagamentoSub(this.value, '$id');\">";
		echo opcaoSelect("pagamento_tipo_sub", 2, "Ativo", null, null, "and id_pagamento_tipo='$variavel'");
		echo "</select>";
	}
}




?>