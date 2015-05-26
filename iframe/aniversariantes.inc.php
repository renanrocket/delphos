<script type="text/javascript">
	var tempo =<?php echo $_GET["tempo"] ?>000;
	function loading() {
		$("#gradient-style").html("<tr><td align='center'>Carregando...</td></tr>");
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

for($j=0, $dia=date('d'); $dia<=31; $dia++){

	$instrucao = "select * from usuario where data_nascimento<>'0000-00-00' order by data_nascimento";
	$sql = query($instrucao);
	for($i=0; $i<mysqli_num_rows($sql); $i++){
	
		extract(mysqli_fetch_assoc($sql));
		$checkData = explode("-", $data_nascimento);

		if($checkData[1]==date('m') and $checkData[2]==$dia){
			
			$cod = "<form method='post' action='../cadastrarUsuario.php' target='_main' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='op' value='visualizar'>";
			$cod .= "<input type='hidden' name='id' value='$id'>";
			$cod .= "<input type='submit' value='". ($id - 1) ."'>";
			$cod .= "</form>";
			
			$idade = subtrairDatas($data_nascimento, date('Y-m-d'), "anos");
			$aniversario = date('Y').substr($data_nascimento, -6);
			
			if(getCredencialUsuario("pesquisaClienteFornecedor")){
				$array[] = $cod;
			}else{
				$array[] = $id;
			}
				$array[] = showImagemUsuario($id, 1, 1);
				$array[] = $nome;

			if(subtrairDatas(date('Y-m-d'), $aniversario)>1){
				$texto = "Faltam ".subtrairDatas(date('Y-m-d'), $aniversario)." dias";
			}elseif(subtrairDatas(date('Y-m-d'), $aniversario)>0){
				$texto = "Falta ".subtrairDatas(date('Y-m-d'), $aniversario)." dia";
			}else{
				$texto = "Hoje";
			}
			
			$array[] = formataData($aniversario)." (".$idade." Anos)<br>".$texto;
			
			$j++;
		}
	}


	$instrucao = "select * from cliente_fornecedor where data_nascimento<>'0000-00-00' order by data_nascimento";
	$sql = query($instrucao);
	
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		$checkData = explode("-", $data_nascimento);
				
		if($checkData[1]==date('m') and $checkData[2]==$dia){
			
			$cod = "<form method='get' action='../cadastrarClienteFornecedor.php' target='_main' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='op' value='visualizar'>";
			$cod .= "<input type='hidden' name='id_cliente_fornecedor' value='".base64_encode($id)."'>";
			$cod .= "<input type='submit' value='$id'>";
			$cod .= "</form>";
			
			$idade = subtrairDatas($data_nascimento, date('Y-m-d'), "anos");
			$aniversario = date('Y').substr($data_nascimento, -6);
			
			if(getCredencialUsuario("pesquisaClienteFornecedor")){
				$array[] = $cod;
			}else{
				$array[] = $id;
			}

			$array[] = showImagemClienteFornecedor($id, 1, 1);
			$array[] = $nome;
			
			if(subtrairDatas(date('Y-m-d'), $aniversario)>1){
				$texto = "Faltam ".subtrairDatas(date('Y-m-d'), $aniversario)." dias";
			}elseif(subtrairDatas(date('Y-m-d'), $aniversario)>0){
				$texto = "Falta ".subtrairDatas(date('Y-m-d'), $aniversario)." dia";
			}else{
				$texto = "Hoje";
			}
			
			$array[] = formataData($aniversario)." (".$idade." Anos)<br>".$texto;
			
			$j++;
		}
	}	
	
}

$tabela = new tabela;
$tag = array("ID", "Foto" , "Nome", "Data de aniversário");
if($j>1){
	$text1 = "Existem";
	$text2 = "aniversariantes";
}else{
	$text1 = "Existe";
	$text2 = "aniversariante";
}
$titulo = "<img class='iframeImg' src='../img/icones/pesquisaClienteFornecedor.png'> $botaoAtualizar";
$titulo .= "<span>$text1 $j $text2 este mês.</span>";
$tabela->setTitulo($titulo);
$tabela->setTag($tag);
$tabela->setValores($array);
$tabela->style = "width:100%; padding:0px; margin:0px;";
echo $tabela->showTabela();


include "../templates/downIframe.inc.php";
?>