<?php

include_once "../templates/upLoginImp.inc.php";

?>
<script type="text/javascript">
    function showFormaPagamento(selecao){
        if(selecao=="true"){
            document.getElementById('outrasformasPga').style.display="";
            document.getElementById('link').value = document.getElementById('link').value + "&marca=sim" ;
        }else{
            document.getElementById('outrasformasPga').style.display="none";
            document.getElementById('link').value = document.getElementById('linkOriginal').value;
        }
        
    }
</script>
<?php


//all

if(isset($_COOKIE["login"])){
    $server = $_SERVER['SERVER_NAME']; 
    $endereco = $_SERVER ['REQUEST_URI'];
    $link = "http://" . $server . $endereco;
    
    echo "<div id='naoImp'>";
        echo "Mostrar outras formas de pagamento na ordem de serviço?<br>";
        echo "<input type='radio' name='tipoImp' value='true' onclick=\"showFormaPagamento(this.value);\">Mostrar ";
        echo "<input type='radio' name='tipoImp' value='false' checked onclick=\"showFormaPagamento(this.value);\">Não mostrar ";
        echo "<br>";
        /*echo "Link para enviar para o cliente:<br>";
        echo "<input type='hidden' name='linkOriginal' id='linkOriginal' value='$link'>";
        echo "<input type='text' style='width:50%;' name='link' id='link' value='$link'>";*/
    echo "</div>";
}

$sql = query("select * from orcamento where id ='".base64_decode($_GET["orcamento"])."'");
extract(mysqli_fetch_assoc($sql));
$statusOrcamento = $status;
$observacoes_orcamento = $observacoes;

echo "<table id='tabelaImp'>";
	echo "<tr>";
		echo "<td colspan='5' class='tdNone' align='center' width='806' height='131'>";
			
			echo cabecalho();
		
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	if($statusOrcamento=="1"){
		$data = "Data da emissão ".formataData($data_emissao);
		$cod = "Orçamento";
	}elseif($statusOrcamento=="2"){
		$data = "Data da venda ".formataData($data_venda);
		$cod = "Venda";
	}else{
		$data = $cod = "";
	}
		echo "<th colspan='5' align='center'>$cod ".base64_decode($_GET["orcamento"])." $data</th>";
	echo "</tr>";
if(!$id_cliente){
	echo "<tr>";
		echo "<td colspan='4'><span>Cliente</span>$cliente</td>";
		echo "<td colspan='1'><span>Telefone</span>$fone</td>";
	echo "</tr>";
}else{
	$Sql = query("select * from cliente_fornecedor where id='$id_cliente'");
	extract(mysqli_fetch_assoc($Sql));
	if($tipo=="f"){
		echo "
		<tr>
			<td colspan='4'><span>Cliente</span>$nome</td>
			<td colspan='1'><span>CPF</span>$cpf_cnpj</td>
		</tr>";
		$data = "Data Nascimento";
		$doc2 = "RG";
	}else{
		echo "
		<tr>
			<td colspan='2'><span>Cliente</span>$nome</td>
			<td colspan='2'><span>Razão Social</span>$razao_social</td>
			<td colspan='1'><span>CNPJ</span>$cpf_cnpj</td>
		</tr>";
		$data = "Data Fundação";
		$doc2 = "IE";
	}
	
	
	echo "
		<tr>
			<td colspan='4'><span>E-mail</span>$email</td>
			<td colspan='1'><span>$doc2</span>$rg_ie</td>
		</tr>
		<tr>
			<td colspan='4' rowspan='2' valign='top'><span>Endereço</span>".nl2br($endereco)."</td>
			<td colspan='1'><span>Telefone 1</span>$fone1</td>
		</tr>
		<tr>
			<td colspan='1'><span>Telefone 2</span>$fone2</td>
		</tr>
		<tr>
			<td colspan='2'><span>Bairro</span>$bairro</td>
			<td colspan='1'><span>Numero</span>$numero</td>
			<td colspan='1'><span>Cidade</span>".registro($cidade, "cidades", "nome", "cod_cidades")." - ".registro($estado, "estados", "sigla", "cod_estados")."</td>
			<td colspan='1'><span>$data</span>".formataData($data_nascimento)."</td>
		</tr>";
}

echo "
		<tr>
			<td colspan='5' valign='top' align='center'>
				<table>
					<tr>
						<td class='orcSta orcTop' style='width:396px;' colspan='2'>Itens</td>
						<td class='orcMid orcTop'>Quantidade</td>
						<td class='orcMid orcTop' style='width:100px;'>Sub Total</td>
						<td class='orcEnd orcTop' style='width:100px;'>Total Item</td>
					</tr>";
					$sql_itens_orcamento = query("select * from orcamento_itens where id_orcamento='".base64_decode($_GET["orcamento"])."'");
					$linha = mysqli_num_rows($sql_itens_orcamento);
					$qtdItemTotal = 0;
					$totalSubTotal = 0;
					$totalItemTotal = 0;
					$class1= "orcSta";
					$class2= "orcMid";
					if($id_cliente){
					    $max=2;
					}else{
					    $max=8;
					}
					
					for ($i=0; $i<$linha; $i++){
						extract(mysqli_fetch_assoc($sql_itens_orcamento));
						if (($linha-1 == $i) and ($linha>=$max)){
							$class1.= " orcTop";
							$class2.= " orcTop";
						}
						if ($i%2==0){
							$class3= "tdClara";
						}else{
							$class3= "tdEscura";
						}
						$itemTotal = $quantidade * $valor_produto;
						//verificando se o orcamento esta com produtos previamente cadastrados
						if(is_numeric($id_item) and $tabela_item!="item"){
							$id_item = registro($id_item, $tabela_item, "nome")." ". ($tabela_item=="produto" ? registro($id_item, $tabela_item, "modelo") : "");
						}else{
							$id_item = $id_item;
						}
						echo "
							<tr>
								<td class='$class1 $class3' colspan='2'>$id_item<br>$descricao_item</td>
								<td class='$class2 $class3'>$quantidade</td>
								<td class='$class2 $class3'>R$ ".real($valor_produto)."</td>
								<td class='$class2 $class3'>R$ ".real($itemTotal)."</td>
							</tr>";
						$qtdItemTotal +=$quantidade;
						$totalSubTotal += $valor_produto;
						$totalItemTotal += $itemTotal;
						
						if (($i+1==$linha) and $linha<$max){
							
							for ($j=$max-$linha; $j>0;$j--){
								if ($j==1){
									$class1.= " orcTop";
									$class2.= " orcTop";
								}
								if ($j%2==0){
									$class3= "tdClara";
								}else{
									$class3= "tdEscura";
								}
								echo "
									<tr>
										<td class='$class1 $class3' colspan='2'><br></td>
										<td class='$class2 $class3'></td>
										<td class='$class2 $class3'></td>
										<td class='$class2 $class3'></td>
									</tr>";
							}
						}
					}
                    if($descReal){
                        echo "
                        <tr>
                            <td class='tdNone'></td>
                            <td class='orcSta' align='right'>SUB TOTAL</td>
                            <td class='orcMid'>$qtdItemTotal</td>
                            <td class='orcMid'>R$ ".real($totalSubTotal)."</td>
                            <td class='orcMid'>R$ ".real($totalItemTotal)."</td>
                        </tr>
                        <tr>
                            <td class='tdNone'></td>
                            <td class='orcSta' align='right'>DESCONTO</td>
                            <td class='orcMid'></td>
                            <td class='orcMid'>$descPor %</td>
                            <td class='orcMid'>R$ ".real($descReal)."</td>
                        </tr>
                        <tr>
                            <td class='tdNone'></td>
                            <td class='orcSta' align='right'>TOTAL</td>
                            <td class='orcMid'></td>
                            <td class='orcMid'></td>
                            <td class='orcMid' style='font-weight:bold; font-size:19px;'>R$ ".real($totalItemTotal-$descReal)."</td>
                        </tr>";
                    }else{
                        echo "
                        <tr>
                            <td class='tdNone'></td>
                            <td class='orcSta' align='right'>TOTAL</td>
                            <td class='orcMid'>$qtdItemTotal</td>
                            <td class='orcMid'>R$ ".real($totalSubTotal)."</td>
                            <td class='orcMid' style='font-weight:bold; font-size:19px;'>R$ ".real($totalItemTotal)."</td>
                        </tr>";
                    }

					$sql_conta = query("select id, forma_pagamento, parcelas from conta where tabela_referido='orcamento' and referido='".base64_decode($_GET["orcamento"])."'");
					extract(mysqli_fetch_assoc($sql_conta));
					$sql_conta_log = query("select tipo_pagamento, tipo_pagamento_sub from conta_itens where id_conta='$id'");
					extract(mysqli_fetch_assoc($sql_conta_log));
				echo "</table>";
			echo "</td>";
		echo "</tr>";
		
		if($observacoes_orcamento){
			echo "<tr>";
				echo "<td colspan='5'><span>Observações</span>$observacoes_orcamento</td>";
			echo "</tr>";	
		}
		
		/*
		$sql_itens_orcamento = query("select * from orcamento_itens where id_orcamento='".base64_decode($_GET["orcamento"])."'");
		echo "<tr>";
		echo "<td id='td1' colspan='5'>";
		echo "</td>";
		echo "</tr>";
		*/
		
		echo "<tr>";
			echo "<td id='td1' colspan='5'>";
				echo "<table>
					<tr>
						<td class='tdNone'>Forma de Pagamento</td>
						<td class='tdNone'>".registro($forma_pagamento, "pagamento_forma", "forma_pagamento")."</td>
					</tr>
					<tr>
						<td class='tdNone'>Tipo de pagamento</td>
						<td class='tdNone'>".registro($tipo_pagamento, "pagamento_tipo", "tipo_pagamento")."</td>
					</tr>";
					if($tipo_pagamento_sub){
						echo"
						<tr>
							<td class='tdNone'>Sub Tipo de pagamento</td>
							<td class='tdNone'>".registro($tipo_pagamento_sub, "pagamento_tipo_sub", "sub_tipo_pagamento")."</td>
						</tr>";
					}
					if ($parcelas>1){
						echo "
							<tr>
								<td class='tdNone'>Parcelas</td>
								<td class='tdNone'>$parcelas</td>
							</tr>
						";
					}
					if($statusOrcamento=="1"){//aguardando venda
						$data= date('d/m/Y', strtotime("+30 days", strtotime($data_emissao)));
						echo "
							<tr>
								<td class='tdNone'>Orçamento válido até</td>
								<td class='tdNone'>$data</td>
							</tr>";
						
					}
					echo "</table></td></tr>";
		
		if(isset($_GET["marca"])){
            $display = "";
        }else{
            $display = "display:none";
        }
		echo "<tr>";
            echo "<td id='outrasformasPga' style='$display' colspan='5'>";
            echo "<table>";
                echo "<tr>";
                    echo "<td  class='tdNone' colspan='3' align='center'>Aceitamos também</td>";
                echo "</tr>";
                echo "<tr>";
                $sql = query("select forma_pagamento, descricao from pagamento_forma where id<>'$forma_pagamento' and status='Ativo'");
                $linha = mysqli_num_rows($sql);
                echo "<td class='tdNone' rowspan='$linha'>As seguintes Formas de pagamento:</td>";
                for($i=0; $i<$linha; $i++){
                    extract(mysqli_fetch_assoc($sql));
                    if($i>0){
                        echo "<tr>";
                    }
                    echo "<td class='tdNone'>$forma_pagamento</td>";
                    echo "<td class='tdNone'>$descricao</td>";
                    echo "</tr>";
                }
                echo "<tr>";
                $sql = query("select parcelaMax from pagamento_parcela");
                extract(mysqli_fetch_assoc($sql));
                echo "<td class='tdNone'>Parcelamos em até:</td>";
                echo "<td class='tdNone' colspan='2'>$parcelaMax Vezes</td>";
                echo "</tr>";
                echo "<tr>";
                $sql = query("select id, tipo_pagamento from pagamento_tipo where id<>'$tipo_pagamento' and status='Ativo'");
                $linha = mysqli_num_rows($sql);
                echo "<td class='tdNone' rowspan='$linha'>Os seguintes tipos de pagamento:</td>";
                for($i=0; $i<$linha; $i++){
                    extract(mysqli_fetch_assoc($sql));
                    if($i>0){
                        echo "<tr>";
                    }
                    $instrucao = "select sub_tipo_pagamento from pagamento_tipo_sub where id_pagamento_tipo='$id' and status='Ativo'";
                    $sqlSubTipoPagamento = query($instrucao);
                    $cod = "";
                    for($j=0; $j<mysqli_num_rows($sqlSubTipoPagamento); $j++){
                        extract(mysqli_fetch_assoc($sqlSubTipoPagamento));
                        if($j==0){
                            $cod = "( ";
                        }
                        $cod .= "$sub_tipo_pagamento";
                        if($j<>mysqli_num_rows($sqlSubTipoPagamento) and $j<>mysqli_num_rows($sqlSubTipoPagamento)-1){
                            $cod .= ", ";
                        }
                        if($j==mysqli_num_rows($sqlSubTipoPagamento)-1){
                            $cod .= ")";
                        }
                    }
                    echo "<td class='tdNone' colspan='2'>$tipo_pagamento $cod</td>";
                    echo "</tr>";
                }
            echo "</table>";
        echo "</tr>";
        
        if(registro($id_usuario, "usuario", "ass_digital_usar")){
        	$ass_img = "<img src='../".registro($id_usuario, "usuario", "ass_digital_end_min")."'>";
        }else{
        	$ass_img = "";
        }
		
        echo "</tr>
		<tr>
			<td colspan='2' class='tdNone' style='text-align: center;vertical-align: bottom;'>$ass_img</td>
			<td colspan='1' class='tdNone'><br><br><br><br><br></td>
			<td colspan='2' class='tdNone'></td>
		</tr>
		<tr>
			<td colspan='2' class='tdAss' width='150'>".registro($id_usuario, "usuario", "nome")."<span>Representante Comercial</span></td>
			<td colspan='1' class='tdNone'></td>
			<td colspan='2' class='tdAss' width='150'>$cliente</td>
		</tr>
	</table>
";

//end all

include_once "../templates/downLoginImp.inc.php";

?>
