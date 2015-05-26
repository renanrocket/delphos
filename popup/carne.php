<?php

include_once "../templates/upLoginImp.inc.php";

//all
$sql = query("select * from conta where id='".base64_decode($_GET["carne"])."'");
extract(mysqli_fetch_assoc($sql));
if(is_numeric($entidade)){
	$entidade = registro($entidade, $tabela_entidade, "nome");
}
$total = $valor;
$recebido = $aReceber = $subtrator = 0;

$sql = query("select valor, data_vencimento, data_pagamento from conta_itens where id_conta='$id'");
for($i=1, $cod=""; $i<=$parcelas; $i++){
	
	
	extract(mysqli_fetch_assoc($sql));

	if($valor==0 or $valor=="NULL"){
		$valorParcela = ($total - $aReceber) / ($parcelas - $subtrator);
		$data_pagamento = "";
	}else{
		$subtrator++;
		$valorParcela = $valor;
		$aReceber += $valorParcela;
	}
	
	$recebido += $valorParcela;
	$faltaQuitar = $total - $recebido;
	
	//linha	
	echo "<hr style='margin-bottom: -3px; border-top: 1px dashed #f00; border-bottom: none; color: #fff; background-color: #fff; $cod'>";
	
	echo "<table style='margin-bottom:0px;' >";
		echo "<tr>";
			echo "<td colspan='4' class='tdNone' align='center' width='806'>";
			echo cabecalho();
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td style='width:190px;'>";
				echo "<span>Cliente</span>";
				echo "$entidade";
				echo "<br>";
				echo "<div style='width:95px; display:inline-block;'>";
				echo "<span>Data Vencimento</span>";
				echo formataData($data_vencimento);
				echo "</div>";
				echo "<div style='width:95px; display:inline-block;'>";
				echo "<span>Data Quitado</span>";
				echo "_____/_____/_____";
				echo "</div>";
				echo "<br>";
				echo "<div style='width:95px; display:inline-block;'>";
				echo "<span>Valor Parcela</span>";
				echo "R$ ".real($valorParcela);
				echo "</div>";
				echo "<div style='width:95px; display:inline-block;'>";
				echo "<span>Falta Quitar</span>";
				echo "R$ ".real($faltaQuitar);
				echo "</div>";
			echo "</td>";
			echo "<td valign='top' style='border-right:1px dashed #f00; width:58px;'>";
				echo "<span>Assinatura </span>";
			echo "</td>";
			echo "<td style='border-left:none;'>";
				echo "<span>Cliente</span>";
				echo "$entidade";
				echo "<br>";
				echo "<div style='width:50%; display:inline-block;'>";
				echo "<span>Data Vencimento</span>";
				echo formataData($data_vencimento);
				echo "</div>";
				echo "<div style='width:50%; display:inline-block;'>";
				echo "<span>Data Quitado</span>";
				echo "_____/_____/_____";
				echo "</div>";
				echo "<br>";
				echo "<div style='width:50%; display:inline-block;'>";
				echo "<span>Valor Parcela</span>";
				echo "R$ ".real($valorParcela);
				echo "</div>";
				echo "<div style='width:50%; display:inline-block;'>";
				echo "<span>Falta Quitar</span>";
				echo "R$ ".real($faltaQuitar);
				echo "</div>";
			echo "</td>";
			echo "<td valign='top' style='width:58px;'>";
				echo "<span>Assinatura</span>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	if($i%3==0 || $i==$parcelas){
		echo "<hr style='margin-bottom: -3px; border-top: 1px dashed #f00; border-bottom: none; color: #fff; background-color: #fff;'>";
		$cod = "page-break-before:always;";
	}else{
		$cod = "";
	}

}



//end all

include_once "../templates/downLoginImp.inc.php";
?>