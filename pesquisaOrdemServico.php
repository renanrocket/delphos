<?php
include "templates/upLogin.inc.php";

$dataInicial = $dataFinal = null;
extract($_POST);
extract($_GET);

if(isset($op)){
	
	$data1 = formataDataInv($dataInicial);
	$data2 = formataDataInv($dataFinal);
	if($data1=="0000-00-00" and $data2=="0000-00-00"){
		$data2=date('Y-m-d');
	}

	//verificando qual das datas é maior e ajustando
	$dataMaior = strtotime($data1);
	$dataMenor = strtotime($data2);
	if ($dataMaior - $dataMenor > 0) {
		//troca
		$DATA = $data1;
		$data1 = $data2;
		$data2 = $DATA;
	}
	
	$instrucao = "select * from ordem_servico where ";
	if($op=="numero"){
		$instrucao .= "id='$busca'";
	}elseif($op=="cliente"){
		$instrucao .= "(cliente like '%$busca%' or id_cliente = any";
		$instrucao .= "(select id from cliente_fornecedor where nome like '%$busca%' or razao_social like '%$busca%')) ";
		
	}elseif($op=="servico"){
		$instrucao .= "id_servico = any (select id from servico where nome like '%$busca%')";
	}
	if($op!="numero"){
		$instrucao .= "and $data>='$data1 00:00:00' and $data<='$data2 23:59:59'";
	}
	
	$sql = query($instrucao);
	
	for($i = 0, $array = null; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = $cliente;
		$array[] = $fone;
		$array[] = registro($id_servico, "servico", "nome");
		$array[] = $quantidade;
		$array[] = formataData($data_venda);
		$array[] = formataData($data_previsao);
		$array[] = formataData($data_concluida);
		
	}
	
	$tag = array("ID", "Cliente", "Telefone", "Serviço", "Quantidade", "Data da venda", "Data previsão de entrega", "Data de conclusão");
	
	$tabela = new tabela;
	$tabela->setTitulo("Resultado para busca de Ordem de Serviço");
	$tabela->setTag($tag);
	$tabela->setValores($array);
	echo $tabela->showTabela();
	
}else{
	
	
	echo "<form method='get' name='formulario' style='width:20%;' action='pesquisaOrdemServico.php' enctype='multipart/form-data'>";
	echo "Selecione a forma de busca:<br>";
	echo "<select name='op'>";
	echo "<option value='numero'>Por numero</option>";
	echo "<option value='cliente'>Por cliente</option>";
	echo "<option value='servico'>Por serviço</option>";
	echo "</select><br>";
	echo "Buscar:";
	echo "<input type='text' name='busca'><br>";
	echo "Está entre as datas:";
	echo "<select name='data'>";
	echo "<option value='data_venda'>de venda</option>";
	echo "<option value='data_previsao'>de previsão de entrega</option>";
	echo "<option value='data_concluida'>de conclusão</option>";
	echo "</select><br>";
	echo inputData("formulario", "dataInicial", null);
	echo "<br>à<br>";
	echo inputData("formulario", "dataFinal", null);
	echo "<br><input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";



}


include "templates/downLogin.inc.php";
?>

