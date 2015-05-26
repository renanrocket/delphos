<?php
include "../templates/upIframe.inc.php";


?>

<script type="text/javascript">
	var tempo =<?php echo $_GET["tempo"] ?>000;
	function loading() {
		$("#atualiza").html("<tr><td align='center'>Carregando...</td></tr>");
		$("#gradient-style").html("<tr><td align='center'>Carregando...</td></tr>");
	}
	function resetTimeout() {
		loading();
		clearTimeout(timeout);
		timeout = setTimeout("location.reload(true);", 1);
	}
	var timeout = setTimeout("location.reload(true);", tempo);
	var timerefresh = setTimeout("loading();", tempo - 1000);

	var intervaloAlerta = setInterval(function(){
		if($(".alerta").attr("id")=="alerta_red" || $(".alerta").attr("id")==undefined){
			$(".alerta").attr("id", "alerta_red1");
		}

		if($(".alerta").attr("id")=="alerta_white"){
			$(".alerta").attr("id", "alerta_red");
		}
		if($(".alerta").attr("id")=="alerta_red1"){
			$(".alerta").attr("id", "alerta_white");
		}
		
	}, 400);
	
</script>

<?php

//all

extract($_GET);
$botaoAtualizar =  "<span style='float:right; display:inline-block;'><a href='javascript:void(0);' onclick='resetTimeout();'><img width='30' src='../img/refresh.png'></a></span>";
$mais15Dias = date('Y-m-d', strtotime("+ 15 days"));

if ($op == "grafico") {
	
	$instrucao = "select conta.id, conta.valor from conta join conta_itens on conta.id=conta_itens.id_conta where (conta_itens.id_caixa_movimento is null) and conta.status='3'";
	//$instrucao = "select id, valor from conta where id = any(select id_conta from conta_itens where id_caixa_movimento is null) and status='3'";
	
	$sqlConta = query($instrucao);
	for($i = $aPagar = 0; $i<mysqli_num_rows($sqlConta); $i++){
		extract(mysqli_fetch_assoc($sqlConta));
		$instrucao = "select sum(valor) as parcial from conta_itens where id_conta='$id'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		$aPagar += $valor - $parcial;
	}
	if(mysqli_num_rows($sqlConta)<1){
		$aPagar = 0;
	}
	
	$instrucao = "select conta.id, conta.valor from conta join conta_itens on conta.id=conta_itens.id_conta where (conta_itens.id_caixa_movimento is null) and conta.status='2'";
	//$instrucao = "select id, valor from conta where id = any(select id_conta from conta_itens where id_caixa_movimento is null) and status='2'";
	$sqlConta = query($instrucao);
	for($i = $aReceber = 0; $i<mysqli_num_rows($sqlConta); $i++){
		extract(mysqli_fetch_assoc($sqlConta));
		$instrucao = "select sum(valor) as parcial from conta_itens where id_conta='$id'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		$aReceber += $valor - $parcial;
	}
	if(mysqli_num_rows($sqlConta)<1){
		$aReceber = 0;
	}
	
	
	$sqlMovimento = query("select sum(valor) as credito from caixa_movimento where debito_credito='1'");
    extract(mysqli_fetch_assoc($sqlMovimento));
    $sqlMovimento = query("select sum(valor) as debito from caixa_movimento where debito_credito='0'");
    extract(mysqli_fetch_assoc($sqlMovimento));
    $total = $credito - $debito;
	    
	//tipo = tipo do grafico Radar | Bar | Doughnut | Line | Pie | PolarArea
	echo "<script>$(function (){ $(\"body\").attr(\"style\", \"margin:0px; padding:0px; background-color: white;\")});</script>";
	echo "<div style='background-color:white; width:95%; height:510px; padding:5px; padding-top:10px; padding-bottom:0px;' id='atualiza'><br>$botaoAtualizar";
	echo grafico("Radar", array("A pagar (R$)", "A receber (R$)", "Caixa (R$)"), array($aPagar, $aReceber, $total), null, array(500,500));
	echo "</div>";

} elseif ($op == "pagar" or $op == "receber") {
	
	if($op == "pagar"){
		$status = 3;
	}else{
		$status = 2;
	}
	

	$instrucao = "select conta_itens.id_conta, conta.valor as valorTotal, conta.id as id, conta.entidade, conta.tabela_entidade ";
	$instrucao .= " from conta_itens join conta on conta.id=conta_itens.id_conta where ";
	$instrucao .= "conta_itens.id_caixa_movimento is null and conta_itens.data_vencimento<='$mais15Dias' ";
	$instrucao .= "and conta.status='$status' ";
	$instrucao .= "order by data_vencimento";
	/*$instrucao = "select * from conta_itens where id_caixa_movimento is null and data_vencimento<='$mais15Dias' ";
	$instrucao .= "and id_conta = any (select id from conta where status='$status')";
	$instrucao .= "order by data_vencimento";*/
	$sqlContaItem = query($instrucao);
	
	$ID_CONTA = array();
	for($i = $total = 0; $i<mysqli_num_rows($sqlContaItem); $i++){
		
		extract(mysqli_fetch_assoc($sqlContaItem));
		
		/*$sql = query("select * from conta where id='$id_conta'");
		extract(mysqli_fetch_assoc($sql));
		
		$valorTotal = $valor;*/
		if(!in_array($id_conta, $ID_CONTA)){
			$ID_CONTA[] = $id_conta;
			$cod = "<form method='get' action='../pesquisaConta2.php' target='_main' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='op' value='visualizar'>";
			$cod .= "<input type='hidden' name='conta' value='".base64_encode($id)."'>";
			$cod .= "<input type='submit' value='$id'>";
			$cod .= "</form>";
			
			$array[] = $cod;
			$array[] = is_numeric($entidade) ? registro($entidade, $tabela_entidade, "nome") : $entidade;
			
			$instrucao = "select data_vencimento from conta_itens where id_conta='$id' and id_caixa_movimento is null";
			$sqlPlano = query($instrucao);
			if(mysqli_num_rows($sqlPlano)== 0){
				$parcelas = 1;
			}else{
				$parcelas = mysqli_num_rows($sqlPlano);
				extract(mysqli_fetch_assoc($sqlPlano));
			}
			$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
			$sqlPlano = query($instrucao);
			extract(mysqli_fetch_assoc($sqlPlano));
			
			$valorParcela = ($valorTotal - $valorParcial) / $parcelas;
			
			$array[] = "R$ ".real($valorParcela);
			if(subtrairDatas(date('Y-m-d'), $data_vencimento)<=0){
				$array[] = "<b class='alerta'>".formataData($data_vencimento)."</b><br>".(subtrairDatas(date('Y-m-d'), $data_vencimento) * -1)." dias vencido";
			}else{
				$array[] = formataData($data_vencimento);
			}
		}
		
		
	}
	
	$tabela = new tabela;
	$tag = array("ID", "Contato" , "Valor da Parcela", "Data Vencimento");
	if($i>1){
		$text1 = "Existem";
		$text2 = "contas";
	}else{
		$text1 = "Existe";
		$text2 = "conta";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaConta.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2 a $op<br>nos próximos 15 dias.</span>";

	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();

}

include "../templates/downIframe.inc.php";
?>