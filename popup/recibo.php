<?php

include_once "../templates/upLoginImp.inc.php";

//funcao exibe o recibo que ja esta inserido no sistema
function recibo($ID){
	
	//calcular o total recebido ate agora
	$sql = query("select valor as recebido, id_conta, data_pagamento from conta_itens where id='$ID'");
	extract(mysqli_fetch_assoc($sql));

	$sql = query("select * from conta where id='$id_conta'");
	extract(mysqli_fetch_assoc($sql));
	$reg = mysqli_fetch_row($sql);
	$data = explode("-", $data_pagamento);
	$data2 = explode(" ", $data[2]);

	switch ($status) {
		case '2' :
			$status = "Recebemos do(a)";
			$ass = registro($id_usuario, "usuario", "nome");
			if(registro($id_usuario, "usuario", "ass_digital_usar")){
	        	$ass_img = "<img style='border-bottom: solid 1px black;' src='../".registro($id_usuario, "usuario", "ass_digital_end_min")."'>";
	        }else{
	        	$ass_img = "<br><br>__________________________________________";
	        }
			break;

		case '3' :
			$status = "Pagamos ao";
            if(is_numeric($entidade)){
                $ass = registro($entidade, $tabela_entidade, "nome");
            }else{
                $ass = $entidade;
            }
            $ass_img = "";
			break;
		default:
			$status = $ass = $ass_img = "";
	}
	
	
	if(!isset($_COOKIE["login"])){
		$vezes = 1;
	}else{
		$vezes = 2;
	}
	for ($i = 0; $i < $vezes; $i++) {
		if($i>0){
			echo "<hr style='border-top: 1px dashed #f00; border-bottom: none; color: #fff; background-color: #fff; height: 4px;'>";
		}
		echo "<div style='margin-bottom:30px; margin-top:30px; width:700px; border: 2px solid #999; border-radius: 20px;'>";
			echo "<table>";
				echo "<tr>";
					echo "<td colspan='5' class='tdNone' align='center' width='806' height='131'>";
						echo cabecalho();
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td align='center' class='tdNone'>";
						echo "<table align='center'>";
							echo "<tr>";
								echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; da Conta</td>";
								echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$id_conta</td>";
								echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; do Recibo</td>";
								echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$ID</td>";
								if(is_numeric($referido) and $tabela_referido=='orcamento'){
									echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; Orçamento</td>";
									echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$referido</td>";
                                    $sqlDesc = query("select descReal from orcamento where id='$referido'");
                                    extract(mysqli_fetch_assoc($sqlDesc));
								}elseif(is_numeric($referido) and $tabela_referido=='pdv'){
									echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; PDV</td>";
									echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$referido</td>";
								}elseif(is_numeric($referido) and $tabela_referido=='ordem_servico'){
									echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; Ordem de Serviço</td>";
									echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$referido</td>";
								}elseif(is_numeric($referido) and $tabela_referido=='plano_assinatura'){
									echo "<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>N&ordm; Plano / Assinatura</td>";
									echo "<td align='left' class='tdNone' style='vertical-align:middle;'>$referido</td>";
								}else{
									echo "<td align='center' colspan='2' class='tdNone'><br></td>";
								}
								echo "<td align='center' style='border:none; font-size:25px; font-weight: bold; width:500px;'>R$ " . real($recebido);
                                if($descReal){
                                    echo "<span style='display: inline-block;  background-color: white;  border: none;  font-size: 10px;  padding-left: 20px;'>VOCÊ ECONOMIZOU<br>";
                                    echo "R$ ".real($descReal)."</span>";
                                }
                                echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='7' align='left'><span class='spanRecibo'>$status Sr.(s)</span><center style='font-weight:bold; font-variant:small-caps;'>";
							if(is_numeric($entidade)){
								echo registro($entidade, $tabela_entidade, "nome");
							}else{
								echo $entidade;
							}
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td colspan='7' align='left'><span class='spanRecibo'>A quantida de</span><center style='font-weight:bold; font-variant:small-caps;'>" . extenso($recebido, false) . "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td colspan='7' align='left'><span class='spanRecibo'>Referente a</span><center style='font-weight:bold; font-variant:small-caps;'>";
								if($valor>$recebido){
									echo "Parte do pagamento de ";
								}
								if(is_numeric($referido)){
									if($tabela_referido=="orcamento"){
										$instrucao = "select id_item, tabela_item, quantidade as qtd from orcamento_itens where id_orcamento='$referido'";
									}elseif($tabela_referido=="pdv"){
										$instrucao = "select id_produto as id_item, quantidade as qtd from pdv_itens where id_pdv='$referido'";
										$tabela_item = "produto";
									}elseif($tabela_referido=="ordem_servico"){
										$instrucao = "select id_servico as id_item, quantidade as qtd from ordem_servico where id='$referido'";
										$tabela_item = "servico";
									}elseif($tabela_referido=="plano_assinatura"){
										$instrucao = "select id_plano_assinatura as id_item, data_inicio, data_termino, data_previsao from matricula_plano_assinatura where id='$referido'";
										$tabela_item = "plano_assinatura";
										$qtd = null;
									}
									$sql = query($instrucao);
									for($j=0;$j<mysqli_num_rows($sql); $j++){
										if($j>0){
											echo ", ";
										}
										extract(mysqli_fetch_assoc($sql));
										if(is_numeric($id_item)){
											$produto = registro($id_item, $tabela_item, "nome");
											if($tabela_item!="plano_assinatura"){
												$vol = registro(registro($id_item, $tabela_item, "id_volume"), "produto_volume", "abreviatura");
											}
										}else{
											$produto = $id_item;
											$vol = "und";
										}
										echo $produto;
										if($qtd){
											echo " (x $qtd $vol)";
										}else{
											echo "(no período de ".formataData($data_inicio);
											if($data_termino!="0000-00-00"){
												echo " à ".formataData($data_termino).")";
											}else{
												echo " previsto para terminar em ".formataData($data_previsao).")";
											}
										}
									}
								}else{
								    echo $referido;
								}
                                if($descReal){
                                    echo " com desconto de R$ ".real($descReal);
                                }
								echo "</td>";
							echo "</tr>";
							switch ($data[1]) {
								case "01" :
									$data[1] = "Janeiro";
									break;
								case "02" :
									$data[1] = "Fevereiro";
									break;
								case "03" :
									$data[1] = "Março";
									break;
								case "04" :
									$data[1] = "Abril";
									break;
								case "05" :
									$data[1] = "Maio";
									break;
								case "06" :
									$data[1] = "Junho";
									break;
								case "07" :
									$data[1] = "Julho";
									break;
								case "08" :
									$data[1] = "Agosto";
									break;
								case "09" :
									$data[1] = "Setembro";
									break;
								case "10" :
									$data[1] = "Outubro";
									break;
								case "11" :
									$data[1] = "Novembro";
									break;
								case "12" :
									$data[1] = "Dezembro";
									break;
							}
							echo "<tr>";
                            extract(mysqli_fetch_assoc(query("select nome as cidade from cidades where cod_cidades=(select cidade from empresa where usarTimbrado='1')")));
                            extract(mysqli_fetch_assoc(query("select sigla as estado from estados where cod_estados=(select estado from empresa where usarTimbrado='1')")));
								echo "<td colspan='7' style='border:none; text-align:center;'>$cidade ($estado) $data2[0] de $data[1] de $data[0]<br>";
									echo "$ass_img<br>";
									echo $ass;
								echo "</td>";
							echo "</tr>";
						echo "</table>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</div>";

	}
}

//all
recibo(base64_decode($_GET["recibo"]));
//end all

include_once "../templates/downLoginImp.inc.php";
?>