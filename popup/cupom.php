<?php
include_once "../templates/upLoginImp.inc.php";

?>
<body onload="javascript:self.print()"></body>
<?php

//all
extract($_GET);
if(isset($op)){
	$conn = TConnection::open(ALCUNHA);
	$item = $cupom;
	if($op=='des'){
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id', '=', base64_decode($item)));

		$sql = new TSqlUpdate;
		$sql->setEntity('pdv_itens');
		$sql->setRowData('despachado', 1);
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		$historico_msg = "Despachou o item ".registro(base64_decode($item), 'pdv_itens', 'quantidade')." x ".registro(registro(base64_decode($item), 'pdv_itens', 'id_produto'), 'produto', 'nome');
		$historico = new historico(null, 'pdv_itens', base64_decode($item), $historico_msg);
		$historico->update();
	}elseif($op=='des-voltar'){
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id', '=', base64_decode($item)));

		$sql = new TSqlUpdate;
		$sql->setEntity('pdv_itens');
		$sql->setRowData('despachado', 0);
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		$historico_msg = "Voltou a ação de despachar do item ".registro(base64_decode($item), 'pdv_itens', 'quantidade')." x ".registro(registro(base64_decode($item), 'pdv_itens', 'id_produto'), 'produto', 'nome');
		$historico = new historico(null, 'pdv_itens', base64_decode($item), $historico_msg);
		$historico->update();
	}
}

cupom($cupom, $tipo);

//end all

include_once "../templates/downLoginImp.inc.php";
?>