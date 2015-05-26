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


$instrucao = "select * from produto where qtd_estoque<=qtd_minima and qtd_minima<>'0'";
$sql = query($instrucao);

for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
	
	extract(mysqli_fetch_assoc($sql));
	
	$cod = "<form method='get' action='../cadastrarProduto.php' target='_main' enctype='multipart/form-data'>";
	$cod .= "<input type='hidden' name='op' value='visualizar'>";
	$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
	$cod .= "<input type='submit' value='$id'>";
	$cod .= "</form>";
	
	$array[] = $cod;
	$array[] = showImagemProduto($id, 1, 1);
	$array[] = $nome." ".registro($id_marca, "marca", "nome")." ".registro($id_categoria, "categoria", "nome")." ".registro($id_subcategoria, "sub_categoria", "nome");
	$array[] = $qtd_minima;
	$array[] = $qtd_estoque;
	
}

$tag = array("ID", "Imagem" , "Nome", "Qtd. Mínima", "Qtd. Atual");

$tabela = new tabela;
if($i>1){
	$text1 = "Existem";
	$text2 = "produtos";
}else{
	$text1 = "Existe";
	$text2 = "produto";
}
$titulo = "<img class='iframeImg' src='../img/icones/pesquisaProduto.png'> $botaoAtualizar";
$titulo .= "<span>$text1 $i $text2 com quantidades abaixo<br>do permitido no estoque.</span>";
$tabela->setTitulo($titulo);
$tabela->setTag($tag);
$tabela->setValores($array);
$tabela->style = "width:100%; padding:0px; margin:0px;";
echo $tabela->showTabela();


include "../templates/downIframe.inc.php";
?>