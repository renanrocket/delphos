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

if ($op == "dia") {
	
	$instrucao = "select * from ordem_servico where data_venda>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
	$instrucao .= " and data_venda<='" . date('Y-m-d H:i:s') . "' and data_concluida is null";
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarOrdemServico.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$array[] = $quantidade." ".registro($id_servico, "servico", "nome");
		if (getCredencialUsuario("pesquisaConta.php")){
			$valor = $valor * $quantidade;
			$array[] = "R$ ".real($valor);
			$total += $valor;
		}
		
	}
	
	$tabela = new tabela;
	if(getCredencialUsuario("pesquisaConta.php")){
		$tag = array("ID", "Contato" , "Serviço", "Valor");
		$tabela->setRodape("Valor total <b>R$ ".real($total)."</b>.");
	}else{
		$tag = array("ID", "Contato" , "Serviço");
	}
	if($i>1){
		$text1 = "Existem";
		$text2 = "Ordens de serviço criadas";
	}else{
		$text1 = "Existe";
		$text2 = "Ordem de serviço criada";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaOrdemServico.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2 nas últimas 24horas</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();

} elseif ($op == "concluida") {
	
	
	$instrucao = "select * from ordem_servico where data_concluida>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
	$instrucao .= " and data_concluida<='" . date('Y-m-d H:i:s') . "'";
	
	
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarOrdemServico.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$array[] = $quantidade." ".registro($id_servico, "servico", "nome");
		if (getCredencialUsuario("pesquisaConta.php")){
			$valor = $valor * $quantidade;
			$array[] = "R$ ".real($valor);
			$total += $valor;
		}
		
	}
	
	$tabela = new tabela;
	if(getCredencialUsuario("pesquisaConta.php")){
		$tag = array("ID", "Contato" , "Serviço", "Valor");
		$tabela->setRodape("Valor total <b>R$ ".real($total)."</b>.");
	}else{
		$tag = array("ID", "Contato" , "Serviço");
	}
	if($i>1){
		$text1 = "Existem";
		$text2 = "Ordens de serviço concluídas";
	}else{
		$text1 = "Existe";
		$text2 = "Ordem de serviço concluída";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaOrdemServico.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2 nas últimas 24horas</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();
	
	
} elseif ($op == "hoje") {
	
	
	$instrucao = "select * from ordem_servico where data_previsao>='" . date('Y-m-d') . " 00:00:00'";
	$instrucao .= " and data_previsao<='" . date('Y-m-d') . " 23:59:59' and data_concluida is null";
	
	
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarOrdemServico.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$array[] = $quantidade." ".registro($id_servico, "servico", "nome");
		if (getCredencialUsuario("pesquisaConta.php")){
			$valor = $valor * $quantidade;
			$array[] = "R$ ".real($valor);
			$total += $valor;
		}
		
	}
	
	$tabela = new tabela;
	if(getCredencialUsuario("pesquisaConta.php")){
		$tag = array("ID", "Contato" , "Serviço", "Valor");
		$tabela->setRodape("Valor total <b>R$ ".real($total)."</b>.");
	}else{
		$tag = array("ID", "Contato" , "Serviço");
	}
	if($i>1){
		$text1 = "Existem";
		$text2 = "Ordens de serviço";
	}else{
		$text1 = "Existe";
		$text2 = "Ordem de serviço";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaOrdemServico.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2 para ser concluída hoje.</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();
}

include "../templates/downIframe.inc.php";
?>