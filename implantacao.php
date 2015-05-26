<?php
include "templates/upLogin.inc.php";

extract($_GET);
extract($_POST);

$conn = TConnection::open(ALCUNHA);

$sql = new TSqlSelect;
$sql->setEntity("servico");
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->addColumn('id_categoria');
$result = $conn->query($sql->getInstruction());
if($result->rowCount()){
	for($i=0;$i<$result->rowCount();$i++){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('id_servico', '=', $id));

		$sql = new TSqlSelect;
		$sql->setEntity("servico_preco");
		$sql->addColumn('valor');
		$sql->setCriteria($criterio);

		$result2 = $conn->query($sql->getInstruction());
		$row = $result2->fetch(PDO::FETCH_ASSOC);
		extract($row);

		$nome = $nome.' #P#';

		$sql = new TSqlInsert;
		$sql->setEntity('produto');
		$sql->setRowData('nome', $nome);
		$sql->setRowData('id_marca', '1');
		$sql->setRowData('id_categoria', $id_categoria);
		$sql->setRowData('valor_compra', '1');
		$sql->setRowData('mlpor', $valor*100);

		$result3 = $conn->query($sql->getInstruction());

		$sql = new TSqlInsert;
		$sql->setEntity('produto_tributacao');
		$sql->setRowData('id_produto', $id);
		$sql->setRowData('nome', '');
		$sql->setRowData('tipo_valor', '0');
		$sql->setRowData('valor', '0');

		$result3 = $conn->query($sql->getInstruction());

	}

}
echo $sql->getInstruction();

include "templates/downLogin.inc.php";
?>