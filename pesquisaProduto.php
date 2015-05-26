<?php
include "templates/upLogin.inc.php";

?>
<script type="text/javascript">

function show(escolha){
	with(document.form){
		if (escolha != "nome"){
			Nome.style.display = "none";
		}else if (escolha == "nome"){
			Nome.style.display= "";
		}
		if (escolha != "categoria"){
			Categoria.style.display = "none";
		}else if (escolha == "categoria"){
			Categoria.style.display= "";
		}
		if (escolha != "subcategoria"){
			Subcategoria.style.display = "none";
		}else if (escolha == "subcategoria"){
			Subcategoria.style.display= "";
		}
	}
}


</script>
<?php



//all

extract($_POST);
extract($_GET);

if(!isset($op)){
	
	echo "<form method='get' action='pesquisaProduto.php' enctype='multipart/form-data'>";
		echo "Pesquisar produto por: <select name='op' style='width:auto;'><option value='cod_id'>Codigo de Barra / Identidade</option><option value='nome'>Nome / Marca / Categoria / Sub Categoria</option></select><br>";
		echo "<input type='text' name='busca' style='width:auto;' autofocus><br>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";	
	
	
}else{
	
	if($op=="cod_id"){
		$instrucao = "select id, nome, id_volume, qtd_estoque, contabilizar_estoque from produto where cod_barra='$busca' or id='$busca'";
	}elseif($op=="nome"){
		$instrucao = "select id, nome, id_volume, qtd_estoque, contabilizar_estoque from produto ";
		$instrucao .= "where nome like '%$busca%' or modelo like '%$busca%' ";
		$instrucao .= "or id_marca = any (select id from marca where nome like '%$busca%') ";
		$instrucao .= "or id_categoria = any (select id from categoria where nome like '%$busca%') ";
		$instrucao .= "or id_subcategoria = any (select id from sub_categoria where nome like '%$busca%') ";
	}
	
	$sql = query($instrucao);
	
	$valoresArray = null;
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$script  = "<form method='get' action='cadastrarProduto.php' enctype='multipart/form-data'>";
		$script .= "<input type='hidden' name='op' value='visualizar'>";
		$script .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$script .= "<input type='submit' value='$id'>";
		$script .= "</form>";
		
		$valoresArray[] = $script;
		$valoresArray[] = $nome;
		$valoresArray[] = "R$ ".real(precoProduto($id, true));
		if($contabilizar_estoque){
			$valoresArray[] = real($qtd_estoque)." ".registro($id_volume, "produto_volume", "nome");	
		}else{
			$valoresArray[] = "Produto não contabilizado.";
		}
		
		
	}
	
	$tabela = new tabela;
	$tabela->setTitulo("Produtos encontrados");
	$tabela->setTag(array("ID", "Nome", "Preço", "Qtd em estoque"));
	$tabela->setValores($valoresArray);
	echo $tabela->showTabela();
	

}

	
//end all

include "templates/downLogin.inc.php";
?>