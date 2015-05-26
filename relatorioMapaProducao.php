<?php
include "templates/upLogin.inc.php";

function legendaOS($full = false){

	if($full){
		$instrucao = "select * from ordem_servico_status where status='1'";
	}else{
		$instrucao = "select * from ordem_servico_status where status='1' and id<>'2' and id<>'3'";
	}
	$divisor = 3;
	
	$sql = query($instrucao);
	$cod = "Legenda dos status das Ordens de Serviço<br>";
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		extract(mysqli_fetch_assoc($sql));
		if($i%$divisor==0){
			$cod .= "<br><br><br><br>";
		}
		
		$curva = "10px";
		
		$cod .= "<span style='";
		$cod .= "padding: 20px;";
		$cod .= "margin: 5px;";
		$cod .= "line-height: 25px;";
		$cod .= "width: 200px;";
		
		
		/*$cod .= "background: -moz-linear-gradient(top, $cor_bg, $cor_font);";
		$cod .= "background: -webkit-gradient(linear, 0% 0%, 0% 100%, from($cor_bg), to($cor_font));";
		$cod .= "-moz-border-radius: $curva $curva $curva $curva;";
		$cod .= "-webkit-border-radius: $curva $curva $curva $curva;";*/
		$cod .= "background-color: $cor_bg;";
		if($cor_bg=="white" or $cor_bg=="#ffffff"){
			$cod .= "border: solid 1px black;";	
		}
		$cod .= "border-radius: $curva $curva $curva $curva;";
		$cod .= "' align='center'>";
		$cod .= "<a href='relatorioMapaProducao.php?statusSelect=".base64_encode($id)."' title='$descricao' style='";
		$cod .= "color:$cor_font;";
		$cod .= "text-shadow: 1px 1px 1px $cor_bg;";
		$cod .= "'>$nome</a></span>";
		
	}
	
	echo $cod;
}

echo legendaOS();

extract($_GET);

if(!isset($statusSelect)){
	$statusSelect=1;
}else{
	$statusSelect=base64_decode($statusSelect);
}
$sqlStatus = query("select * from ordem_servico_status where status='1' and id='$statusSelect'");
extract(mysqli_fetch_assoc($sqlStatus));
$select = "<form method='get' action='relatorioMapaProducao.php' enctype='multipart/form-data'>"; 
$select .= "<select name='statusSelect' onchange='this.form.submit();' style='background-color:$cor_bg; color:$cor_font; width:auto;'>";
//$sqlStatus = query("select * from ordem_servico_status where id<>'2' and id<>'3'");
$sqlStatus = query("select * from ordem_servico_status where id<>'2' and id<>'3'");
for($i=0; $i<mysqli_num_rows($sqlStatus); $i++){
	extract(mysqli_fetch_assoc($sqlStatus));
	if($statusSelect==$id){
		$select .= "<option style='background-color:$cor_bg; color:$cor_font;' value='".base64_encode($id)."' selected='yes'>$nome</option>";	
	}else{
		$select .= "<option style='background-color:$cor_bg; color:$cor_font;' value='".base64_encode($id)."'>$nome</option>";
	}
	
}
$select .= "</select>";
$select .= "</form>";

$sql = query("select * from ordem_servico where status='$statusSelect' order by data_previsao");

for($i = 0; $i<mysqli_num_rows($sql); $i++){
		
	extract(mysqli_fetch_assoc($sql));
	
	$cod = "<form method='get' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
	$cod .= "<input type='hidden' name='op' value='visualizar'>";
	$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
	$cod .= "<input type='submit' value='$id'>";
	$cod .= "</form>";
	
	$array[] = str_replace(" ", "<br>", formataData($data_previsao));
	$array[] = $cod;
	$array[] = $cliente;
	$array[] = registro($id_servico, "servico", "nome");
	$array[] = $quantidade;
	if(getCredencialUsuario("pesquisaConta")){
		$array[] = "R$ ".real($quantidade * $valor);
	}
	
}
mysqli_num_rows($sql)==0? $array = null: false;

if(getCredencialUsuario("pesquisaConta")){
	$tag = array("Previsão de entrega", "ID", "Cliente", "Serviço", "Quantidade", "Preço");
}else{
	$tag = array("Previsão de entrega", "ID", "Cliente", "Serviço", "Quantidade");
}


$tabela = new tabela;
$tabela->setTitulo("Selecione o status da ordem de serviço $select");
$tabela->setTag($tag);
$tabela->setValores($array);
echo $tabela->showTabela();

include "templates/downLogin.inc.php";
?>

