<?php
include_once "../templates/upLoginImp.inc.php";

?>
<style type="text/css">
	body{
		background-color: #47A3DA;
		color: white;
	}
</style>
<?php
//all
extract($_GET);
$conn = TConnection::open(ALCUNHA);


if(isset($op)){
	if($op=='des'){
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id', '=', base64_decode($item)));

		$sql = new TSqlUpdate;
		$sql->setEntity('pdv_itens');
		$sql->setRowData('despachado', 1);
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		echo "<span class='msg-top'><a href='despachar.php?op=des-voltar&item=$item'>Deseja voltar a última ação?</a></span>";
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


$criterio = new TCriteria;
$criterio->add(new TFilter('despachado', '=', '0'));
$criterio->setProperty('order', 'id_pdv');

$sql = new TSqlSelect;
$sql->setEntity('pdv_itens');
$sql->addColumn('*');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

for($i=0; $i<$result->rowCount(); $i++){
	$row = $result->fetch(PDO::FETCH_ASSOC);
	extract($row);
	echo "<div class='despachado-item'>";
		$info = new info;
		$info->cor = '#e2d000';
		$info->display = 'none';
		$info->class= "msg_$i";
		$info->msg = "Deseja despachar o item  ".registro($id_produto, 'produto', 'nome')." (x $quantidade und)";
		$info->msg.= " do PDV ".registro($id_pdv, 'pdv', 'nome')."?";
		$info->msg.= "<br><a href='despachar.php?op=des&item=".base64_encode($id)."' ".pop('../cupom.php?tipo=pdv_itens&cupom='.base64_encode($id), null, null, 'page', $info->getJsApagar())." class='aSubmit'>Sim</a>";
		$info->msg.= "<a href='#' class='aSubmit' onclick='".$info->getJsApagar()."'>Não</a>";
		echo $info->getInfo();
		echo "<span>";
			echo "<a href='#' title='Despachar' ".$info->getJs()."><img src='../img/arq_show.png'></a>";
		echo "</span>";
		echo "<span>PDV ";
			echo registro($id_pdv, 'pdv', 'nome');
		echo "</span>";
		echo "<span>ITEM ";
			echo $quantidade." x ".registro($id_produto, 'produto', 'nome');
		echo "</span>";
		if($observacoes){
			echo "<span>OBS ";
				echo $observacoes;
			echo "</span>";
		}
		echo "<span>";
			echo primeiroUltimo(registro($id_usuario, 'usuario', 'nome'))." ".formataData($data);
		echo "</span>";

		echo "<span>";
		$contador = new contador('cont_'.$i, subtrairDatas($data, date('Y-m-d H:i:s'), 'seg'));
		echo $contador->getHtml();
		echo "</span>";
	echo "</div>";
}

echo "<meta HTTP-EQUIV='refresh' CONTENT='60;URL=despachar.php'>";

//end all

include_once "../templates/downLoginImp.inc.php";
?>