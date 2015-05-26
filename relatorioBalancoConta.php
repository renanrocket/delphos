<?php
include "templates/upLogin.inc.php";

extract($_GET);

if(!isset($data1) and !isset($data2)){
    
    echo "<form name='formulario' method='get' action='relatorioBalancoConta.php' enctype='multipart/form-data'>";
        echo "Intervalo da pesquisa:<br>";
        echo inputData("formulario", "data1", null)."<br>à<br>".inputData("formulario", "data2", null);
        echo "<br><input type='submit' class='btnEnviar' value='Enviar'>";
    echo "</form>";
    
}else{
    
    $data1 = formataDataInv($_GET["data1"]);
    $data2 = formataDataInv($_GET["data2"]);
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
    

	echo "<table id='gradient-style'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th colspan='8'>Balanço de contas do periodo de ".formataData($data1)." à ".formataData($data2)."</th>";
			echo "</tr>";
		echo "</thead>";
		
		$instrucao = "select * from conta_itens where id_conta = any(select id from conta where status='2') and data_vencimento>='$data1 00:00:00' and data_vencimento<='$data2 23:59:59'";
		$sqlReceber = query($instrucao);
		$linhaReceber = mysqli_num_rows($sqlReceber);
		$instrucao = "select * from conta_itens where id_conta = any(select id from conta where status='3') and data_vencimento>='$data1 00:00:00' and data_vencimento<='$data2 23:59:59'";
		$sqlPagar = query($instrucao);
		$linhaPagar = mysqli_num_rows($sqlPagar);
		
		/*
		echo "<tfoot>";
			echo "<tr>";
				echo "<th colspan='4'>$linhaReceber contas a receber</th>";
				echo "<th colspan='4'>$linhaPagar contas a pagar</th>";
			echo "</tr>";
		echo "</tfoot>";
		*/
		
		$linhaReceber>=$linhaPagar ? $linha = $linhaReceber : $linha = $linhaReceber;
		
		echo "<tbody>";
			echo "<tr>";
				echo "<th colspan='4'>Contas a Receber</th>";
				echo "<th colspan='4'>Contas a Pagar</th>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>ID Conta</th>";
				echo "<th>Cliente</th>";
				echo "<th>Referente</th>";
				echo "<th>Valor</th>";
				echo "<th>ID Conta</th>";
				echo "<th>Fornecedor</th>";
				echo "<th>Referente</th>";
				echo "<th>Valor</th>";
			echo "</tr>";
			
		function tableCorpo(){
				
			global $id_conta, $valor, $valorQuitado, $valorTotal;
			
			echo "<td>";
				echo "<form method='get' action='pesquisaConta2.php' enctype='multipart/form-data'>";
					echo "<input type='hidden' name='conta' value='".base64_encode($id_conta)."'>";
					echo "<input type='submit' value='$id_conta'>";
				echo "</form>";
			echo "</td>";
			
			$sqlER = query("select tabela_entidade, entidade, tabela_referido, referido from conta where id='$id_conta'");
			extract(mysqli_fetch_assoc($sqlER));
			if(is_numeric($entidade)){
			}
			echo "<td style='white-space:normal;'>";
				echo is_numeric($entidade) ? registro($entidade, $tabela_entidade, "nome") : $entidade;
			echo "</td>";
			echo "<td style='white-space:normal;'>";
				if(is_numeric($referido)){
					if($tabela_referido=="orcamento"){
						echo "Orçamento ";
					}elseif($tabela_referido=="plano_assinatura"){
						echo "Plano / assinatura ";
					}elseif($tabela_referido=="pdv"){
						echo "PDV ";
					}elseif($tabela_referido=="ordem_servico"){
						echo "Ordem de serviço ";
					}
				}
				echo $referido;
			echo "</td>";
			$valorTotal = registro($id_conta, "conta", "valor");
			extract(mysqli_fetch_assoc(query("select sum(valor) as valorQuitado from conta_itens where id_conta='$id_conta'")));
			$parcelasRestantes = mysqli_num_rows(query("select id from conta_itens where id_conta='$id_conta' and valor is null"));
			if($valor==0){
				$parcelasRestantes == 0 ? $parcelasRestantes = 1 : false;
				if($valorTotal == $valorQuitado){
					$valor = round((($valorTotal) / $parcelasRestantes), 2);
				}else{
					$valor = round((($valorTotal - $valorQuitado) / $parcelasRestantes), 2);
				}
			}
			
			echo "<td>R$ ".real($valor)."</td>";
		}

		$idConta = array();
		for($i = $totalReceber = $totalPagar = $valorTotal = $valorQuitado = $valorQR = $valorFQR = $valorQP = $valorFQP = 0; $i<$linha; $i++){
				
			echo "<tr>";
			
			
			if($i<$linhaReceber){
				extract(mysqli_fetch_assoc($sqlReceber));
				tableCorpo();
				$totalReceber += $valor;
				if(!in_array($id_conta, $idConta)){
					$valorFQR += $valorTotal - $valorQuitado;
					$valorQR += $valorQuitado;
				}
				$idConta[] = $id_conta;
			}else{
				echo "<td colspan='4'></td>";
			}
			if($i<$linhaPagar){
				extract(mysqli_fetch_assoc($sqlPagar));
				tableCorpo();
				$totalPagar += $valor;
				if(!in_array($id_conta, $idConta)){
					$valorFQP += $valorTotal - $valorQuitado;
					$valorQP += $valorQuitado;
				}
				$idConta[] = $id_conta;
			}else{
				echo "<td colspan='4'></td>";
			}
			
			echo "</tr>";
		}
			echo "<tr>";
				echo "<th colspan='3'>Total a receber</th>";
				echo "<th>R$ ".real($totalReceber)."</th>";
				echo "<th colspan='3'>Total a pagar</th>";
				echo "<th>R$ ".real($totalPagar)."</th>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<td colspan='3'>Total quitado a receber</td>";
				echo "<td>R$ ".real($valorQR)."</td>";
				echo "<td colspan='3'>Total quitado a pagar</td>";
				echo "<td>R$ ".real($valorQP)."</td>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<td colspan='3'>Total falta quitar a receber</td>";
				echo "<td>R$ ".real($valorFQR)."</td>";
				echo "<td colspan='3'>Total falta quitar a pagar</td>";
				echo "<td>R$ ".real($valorFQP)."</td>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<td colspan='8' style='background:none;' align='center'>";
					$titulo = array("Total Receber", "Total Pagar", "Falta Receber", "Total Pagar");
					$valores = array($totalReceber, $valorFQR, $totalPagar, $valorFQP);
					echo grafico("Bar", $titulo, $valores, null , array(500, 200));
				echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<th colspan='8'>Meta de Vendas</th>";
			echo "</tr>";
			
			$minima = $totalPagar+30*$totalPagar/100;
			$razoavel = $totalPagar+60*$totalPagar/100;

			if($totalReceber>=$totalPagar){
				$exelente = $totalReceber+30*$totalReceber/100;
			}else{
				if($totalPagar+$totalReceber<=($totalPagar+60*$totalPagar/100)){
					$exelente = $totalPagar+90*$totalPagar/100;
				}else{
					$exelente = $totalPagar+$totalReceber;
				}
			}
			if($totalReceber+90*$totalReceber/100<=$exelente){
				$desafiadora = $exelente+30*$exelente/100;
			}else{
				$desafiadora = $totalReceber+90*$totalReceber/100;
			}
			
			echo "<tr>";
				echo "<td colspan='2' align='center'>Mínima<br>R$ ".real($minima)."</td>";
				echo "<td colspan='2' align='center'>Razoável<br>R$ ".real($razoavel)."</td>";
				echo "<td colspan='2' align='center'>Exelente<br>R$ ".real($exelente)."</td>";
				echo "<td colspan='2' align='center'>Desafiadora<br>R$ ".real($desafiadora)."</td>";
			echo "</tr>";
			
		echo "</tbody>";
	
	echo "</table>";
	
	
	
}
include "templates/downLogin.inc.php";
?>

