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

if ($op == "orcamentoAnterior") {
	
	$instrucao = "select * from orcamento where data_emissao>='" . date('Y-m-d H:i:s', strtotime("- 1 month")) . "' and data_emissao<='$data2' and status='1'";
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarOrcamento.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$Sql = query("select quantidade, valor_produto from orcamento_itens where id_orcamento='$id'");
		for ($j = 0,$valor = 0; $j < mysqli_num_rows($Sql); $j++) {
			extract(mysqli_fetch_assoc($Sql));
			$valor += $quantidade * $valor_produto;
		}
		$array[] = "R$ ".real($valor);
		$total += $valor;
		$array[] = formataData($data_emissao);
		
	}
	
	$tag = array("ID", "Contato" , "Valor", "Data Emissão");
	
	$tabela = new tabela;
	if($i>1){
		$existe = "Existem";
		$orcamento = "orçamentos";
	}else{
		$existe = "Existe";
		$orcamento = "orçamento";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaOrcamento.png'> $botaoAtualizar";
	$titulo .= "<span>$existe $i $orcamento de 30 dias atrás<br>Aguardando Venda</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->setRodape("Valor total <b>R$ ".real($total)."</b>.");
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();

} elseif ($op == "orcamentoAtual") {

	$instrucao = "select * from orcamento where data_emissao>='$data1' and data_emissao<='$data2' and status='1'";
	$sql = query($instrucao);
	$aguardandoVenda = mysqli_num_rows($sql);
	
	$instrucao = "select * from orcamento where data_emissao>='$data1' and data_emissao<='$data2' and status='2'";
	$sql = query($instrucao);
	$vendido = mysqli_num_rows($sql);
	
	$instrucao = "select * from orcamento where data_emissao>='$data1' and data_emissao<='$data2' and (status='1' or status='2')";
	$sql = query($instrucao);
	
	for($i = $total  = $totalVendido = $totalAguardando = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarOrcamento.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$Sql = query("select quantidade, valor_produto from orcamento_itens where id_orcamento='$id'");
		for ($j = 0,$valor = 0; $j < mysqli_num_rows($Sql); $j++) {
			extract(mysqli_fetch_assoc($Sql));
			$valor += $quantidade * $valor_produto;
		}
		$array[] = "R$ ".real($valor);
		$total += $valor;
		$array[] = formataData($data_emissao);
		if ($status == 1) {
			$totalAguardando += $valor;
			$array[] = "<img style='width:2em;' title='Aguardando venda' src='../img/aguardando_venda.png'>";
		} elseif ($status == 2) {
			$totalVendido += $valor;
			$array[] = "<img style='width:2em;' title='Vendido' src='../img/vendido.png'>";
		}
	}
	
	$tag = array("ID", "Contato" , "Valor", "Data Emissão", "Status");
	
	$tabela = new tabela;
	if($i>1){
		$existe = "Existem";
		$orcamento = "orçamentos";
	}else{
		$existe = "Existe";
		$orcamento = "orçamento";
	}
	$mes = date('m');
	switch ($mes) {
		case "01" :
			$mes = "Janeiro";
			break;
		case "02" :
			$mes = "Fevereiro";
			break;
		case "03" :
			$mes = "Março";
			break;
		case "04" :
			$mes = "Abril";
			break;
		case "05" :
			$mes = "Maio";
			break;
		case "06" :
			$mes = "Junho";
			break;
		case "07" :
			$mes = "Julho";
			break;
		case "08" :
			$mes = "Agosto";
			break;
		case "09" :
			$mes = "Setembro";
			break;
		case "10" :
			$mes = "Outubro";
			break;
		case "11" :
			$mes = "Novembro";
			break;
		case "12" :
			$mes = "Dezembro";
			break;
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaOrcamento.png'> $botaoAtualizar";
	$titulo .= "<span>$existe $i $orcamento emitidos<br>no mês de $mes / ".date('Y')."</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	if($aguardandoVenda>1){
		$existe = "Existem";
		$orcamento = "orçamentos";
	}else{
		$existe = "Existe";
		$orcamento = "orçamento";
	}
	$texto = "$existe $aguardandoVenda (<b>R$ ".real($totalAguardando)."</b>) $orcamento Aguardando venda.<br>";
	if($vendido>1){
		$existe = "Existem";
		$orcamento = "orçamentos";
	}else{
		$existe = "Existe";
		$orcamento = "orçamento";
	}
	$texto .= "$existe $vendido $orcamento (<b>R$ ".real($totalVendido)."</b>) Vendidos.<br>";
	$tabela->setRodape("$texto Valor total <b>R$ ".real($total)."</b>.");
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();
	
}

include "../templates/downIframe.inc.php";
?>