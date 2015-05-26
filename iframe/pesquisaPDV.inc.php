<script type="text/javascript">

	var tempo =<?php echo $_GET["tempo"] ?>000;
	function loading() {
		$("#gradient-style").html("<tr><td align='center'>Carregando...</td></tr>")
	}

	function resetTimeout() {
		loading();
		clearTimeout(timeout);
		timeout = setTimeout("location.reload(true);", 1);
	}
	
	var timeout = setTimeout("location.reload(true);", tempo);
	var timerefresh = setTimeout("loading();", tempo - 1000);
	
</script>
<?php
include "../templates/upIframe.inc.php";


//all

extract($_GET);
$botaoAtualizar =  "<span style='float:right; display:inline-block;'><a href='javascript:void(0);' onclick='resetTimeout();'><img width='30' src='../img/refresh.png'></a></span>";
$data1 = date('Y-m') . "-01 00:00:00";
$data2 = date('Y-m') . "-31 23:59:59";

if ($op == "pdv") {
	
	$instrucao = "select id_conta as id from conta_itens join conta on conta.id=conta_itens.id_conta ";
	$instrucao.= "where conta.tabela_referido='pdv' and conta.status<>'4' and conta_itens.valor is null";
	//$instrucao = "select id_conta as id from conta_itens where id_conta = any (select id from conta where tabela_referido='pdv' and status<>'4') and valor is null";
	$sql = query($instrucao);
	
	for($i = $total = $qtd = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		$referido = registro($id, 'conta', 'referido');
		$valor = registro($id, 'conta', 'valor');
		
		$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
		$sqlParcial = query($instrucao);
		extract(mysqli_fetch_assoc($sqlParcial));
		$valorParcial = round($valorParcial, 2);
		if($valorParcial<$valor){
			$cod = "<form method='get' action='../cadastrarPDV.php' target='_main' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='pdv' value='".base64_encode($referido)."'>";
			$cod .= "<input type='submit' value='$referido'>";
			$cod .= "</form>";
			
			$array[] = $cod;
			$array[] = registro($referido, "pdv", "nome");
			$array[] = "R$ ".real($valor);
			$array[] = "R$ ".real($valor - $valorParcial);
			$qtd++;
		}
	}
	
	$tabela = new tabela;
	$tag = array("ID", "PDV" , "Valor<br>total", "Valor<br>parcial");
	if($qtd>1){
		$text1 = "Existem";
		$text2 = "PDV's abertos.";
	}else{
		$text1 = "Existe";
		$text2 = "PDV aberto.";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaPDV.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $qtd $text2</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();

}


include "../templates/downIframe.inc.php";
?>