<?php
include "templates/upLogin.inc.php";



$cod = "<div id='cupomDiv'  style='width:100%' style='z-index:0;'><p>";
$instrucao = "select id_conta as id from conta_itens where id_conta = any (select id from conta where tabela_referido='pdv' and status<>'4') and valor is null";
//$instrucao = "select valor, id, referido from conta where tabela_referido='pdv' and status<>'4' ";
//$instrucao.= "and valor>(select sum(valor) as valorParcial from conta_itens where id_conta=conta.id)";
$SQL = query($instrucao);
$comandas_texto['referido'][] = null;
$comandas_numero['referido'][] = null;
for($i = $j = $totalValorParcial = $totalValorParcialTexto = $totalValorParcialNumero = $check = 0; $i < mysqli_num_rows($SQL); $i++){
	extract(mysqli_fetch_assoc($SQL));
	$referido = registro($id, 'conta', 'referido');
	$valor = registro($id, 'conta', 'valor');
	$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
	$sql = query($instrucao);
	extract(mysqli_fetch_assoc($sql));
	
	$valorParcial = round($valorParcial, 2);
	if($valorParcial<$valor and !in_array($referido,$comandas_texto['referido']) and !in_array($referido,$comandas_numero['referido'])){
		
		$sqlTooltip = query("select id_produto, quantidade, preco, data from pdv_itens where id_pdv='$referido'");
		$tooltip = "title='<pre>";
		for($l=0; $l<mysqli_num_rows($sqlTooltip); $l++){
			extract(mysqli_fetch_assoc($sqlTooltip));
			$l!=0 ? $tooltip .="<br>" : false;
			$nomeProduto = str_replace("-", " ", registro($id_produto, "produto", "nome"));
			if(strlen($nomeProduto)>19){
				$nomeProduto = wordwrap($nomeProduto, 19, "!@#$",true);
				$nome_Produto = explode("!@#$", $nomeProduto);
				$nomeProduto = $nome_Produto[0];
			}
			$tooltip .= str_pad($nomeProduto, 20, ".").$quantidade." X R$ ".real($preco);
		}
		$tooltip .= "</pre>'";
		
		if($j==0){
			
		}

		$codComanda = "<a href='cadastrarPDV.php?pdv=".base64_encode($referido)."' $tooltip >";
		$codComanda.= "<div class='cupom ";
		
		

		$codComanda.= "'>";
			registro($referido, "pdv", "nome") ? $nome = registro($referido, "pdv", "nome") : $nome = "PDV $referido"; 
			$codComanda.= "<span align='center' style='font-size:25px;'>$nome</span>";
			$codComanda.= "<span style='font-size:13px;'>Total: R$ ".real($valor)."</span>";
			$codComanda.= "<span style='font-size:13px;'>Parcial: <b style='font-size:20px;'>R$ ".real($valor - $valorParcial)."</b></span>";
		$codComanda.= "</div>";
		$codComanda.= "</a>";

		if(is_numeric($nome) or strstr(strtolower($nome), "mesa")){
			$comandas_numero['referido'][] = $referido;
			$comandas_numero['nome'][] = $nome;
			$comandas_numero['cod'][] = $codComanda;
			$totalValorParcialNumero += $valor - $valorParcial;
		}else{
			$comandas_texto['referido'][] = $referido;
			$comandas_texto['nome'][] = $nome;
			$comandas_texto['cod'][] = $codComanda;
			$totalValorParcialTexto += $valor - $valorParcial;
		}
		

		
		$check++;
		$j++;
		$totalValorParcial += $valor - $valorParcial;
	}
}

if(isset($comandas_texto['nome'])){
	array_multisort($comandas_texto['nome'], $comandas_texto['cod']);
	foreach ($comandas_texto['cod'] as $key => $value) {
		$cod.= $value;
	}
	$cod.= "<br><br><div>Comandas em aberto: ".count($comandas_texto['cod'])."<br><span class='totalValor'>R$ ".real($totalValorParcialTexto)."</span></div>";
	$cod.= "</p></div>";
}

if(isset($comandas_numero['nome'])){
	array_multisort($comandas_numero['nome'], $comandas_numero['cod']);
	foreach ($comandas_numero['cod'] as $key => $value) {
		$cod.= $value;
	}
	$cod.= "<br><br><div>Comandas em aberto: ".count($comandas_numero['cod'])."<br><span class='totalValor'>R$ ".real($totalValorParcialNumero)."</span></div>";
	$cod.= "</p></div>";
}


$cod.= "<br><br><div>Total de comandas em aberto no geral: $check<br><span class='totalValor'>R$ ".real($totalValorParcial)."</span></div>";
if($check<1){
	$cod.= "<div class='msg'>Não existem comandas em aberto.</div>";
}
$cod.= "</p></div>";
echo $cod;
echo "<a href='popup/despachar.php'><div class='cupom'>Visualizar itens <br>para o despache</div></a>";
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=pesquisaPDV.php'>";

include "templates/downLogin.inc.php";
?>
