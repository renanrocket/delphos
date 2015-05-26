<?php
include "templates/upLogin.inc.php";

extract($_GET);
!isset($ordem) ? $ordem = "id_subcategoria" : false;

$qtdTotal = $valorCompraTotal = $valorVendaTotal = 0;

$sql = query("select id_categoria as idCategoria from produto group by id_categoria");
for($i = $id_categoria = 0; $i<mysqli_num_rows($sql); $i++){
	extract(mysqli_fetch_assoc($sql));
	if($id_categoria!=$idCategoria){
			
		$sqlProduto = query("select * from produto where id_categoria='$idCategoria' order by $ordem");
		for($j=0; $j<mysqli_num_rows($sqlProduto); $j++){
			extract(mysqli_fetch_assoc($sqlProduto));
			$titulo = registro($idCategoria, "categoria", "nome");
			$cod = "<form method='get' action='cadastrarProduto.php' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='op' value='visualizar'>";
			$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
			$cod .= "<input type='submit' value='$id'>";
			$cod .= "</form>";
			$array[] = $cod;
			$array[] = $nome;
			$array[] = registro($id_subcategoria, "sub_categoria", "nome");
			$array[] = registro($id_marca, "marca", "nome");
			$array[] = $qtd_minima;
			$array[] = $qtd_estoque;
			if(getCredencialUsuario("cadastrarProduto.php")){
				$array[] = "R$ ".real($valor_compra)."<br><span style='font-size:11px;'>R$ ".real($valor_compra*$qtd_estoque)."</span>";
				$array[] = "R$ ".real(precoProduto($id, true))."<br><span style='font-size:11px;'>R$ ".real(precoProduto($id, true)*$qtd_estoque)."</span>";	
			}
			
			if($qtd_estoque>0){
				$qtdTotal += $qtd_estoque;
				$valorCompraTotal += $qtd_estoque * $valor_compra;
				$valorVendaTotal += $qtd_estoque * precoProduto($id, true);
			}
		}
		$tag[] = "<a href='relatorioEstoque.php?ordem=id'>ID</a>";
		$tag[] = "<a href='relatorioEstoque.php?ordem=nome'>Nome do produto</a>";
		$tag[] = "<a href='relatorioEstoque.php?ordem=id_subcategoria'>Sub categoria</a>";
		$tag[] = "<a href='relatorioEstoque.php?ordem=id_marca'>Marca</a>";
		$tag[] = "<a href='relatorioEstoque.php?ordem=qtd_minima'>Qtd. mínima</a>";
		$tag[] = "<a href='relatorioEstoque.php?ordem=qtd_estoque'>Qtd. em estoque</a>";
		if(getCredencialUsuario("cadastrarProduto.php")){
			$tag[] = "<a href='relatorioEstoque.php?ordem=valor_compra'>Valor de compra</a>";
			$tag[] = "<a href='#'>Valor de venda</a>";
		}
		
		$tabela = new tabela;
		$tabela->setTitulo($titulo);
		$tabela->setTag($tag);
		$tabela->setValores($array);
		echo $tabela->showTabela();
		
	}
	
	unset($array);
	unset($tag);
}

$tabela = new tabela;
$tabela->setTitulo("Resumo Geral");
$tabela->setTag(array("Quantidade de itens no estoque", "Valor de compra", "Valor de venda"));
$tabela->setValores(array($qtdTotal, "R$ ".real($valorCompraTotal), "R$ ".real($valorVendaTotal)));
echo $tabela->showTabela();

include "templates/downLogin.inc.php";
?>

