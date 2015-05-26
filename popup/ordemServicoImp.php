<?php
include_once "../templates/upLoginImp.inc.php";
?>
<script type="text/javascript">
    function showDados(selecao){
        if(selecao=="true"){
        	$(".preco1").attr("colspan", "3");
        	$(".preco2").show();
            document.getElementById('link').value = document.getElementById('link').value + "&marca=sim" ;
        }else{
            $(".preco1").attr("colspan", "5");
        	$(".preco2").hide();
            document.getElementById('link').value = document.getElementById('linkOriginal').value;
        }
        
    }d
</script>
<?php

if(isset($_COOKIE["login"])){
	if(getCredencialUsuario("cadastrarOrdemServico.php")){
		$server = $_SERVER['SERVER_NAME']; 
	    $endereco = $_SERVER ['REQUEST_URI'];
	    $link = "http://" . $server . $endereco;
	    
	    echo "<div id='naoImp'>";
	        echo "Mostrar valores ocultos?<br>";
	        echo "<input type='radio' name='tipoImp' value='true' onclick=\"showDados(this.value);\">Mostrar ";
	        echo "<input type='radio' name='tipoImp' value='false' checked onclick=\"showDados(this.value);\">Não mostrar ";
	        echo "<br>";
	        echo "Link para enviar para o cliente:<br>";
	        echo "<input type='hidden' name='linkOriginal' id='linkOriginal' value='$link'>";
	        echo "<input type='text' style='width:50%;' name='link' id='link' value='$link'>";
	    echo "</div>";
	}
}



$idOrdemServico = base64_decode($_GET["os"]);


echo "<table id='tabelaImp'>";
echo "<tr>";
echo "<td colspan='6' class='tdNone' align='center' width='806' height='131'>";

echo cabecalho();

echo "</td>";
echo "</tr>";

$sql = query("select * from ordem_servico where id='$idOrdemServico'");
extract(mysqli_fetch_assoc($sql));
$statusOrdemServico = $status;

echo "<tr>";
echo "<th colspan='6'>";
echo "Ordem de Serviço $idOrdemServico";
echo "</th>";
echo "</tr>";

$sql = query("select * from ordem_servico where id='$idOrdemServico'");
extract(mysqli_fetch_assoc($sql));
$statusOrcamento = $status;
$precoServico = $valor;
$id_orcamento!=0 ? $referido = $id_orcamento : $referido = $idOrdemServico ;
$id_orcamento!=0 ? $tabela_referido = "orcamento" : $tabela_referido = "ordem_servico" ;

if (!$id_cliente) {
	echo "<tr>";
	echo "<td colspan='5'><span>Cliente</span>$cliente</td>";
	echo "<td colspan='1'><span>Telefone</span>$fone</td>";
	echo "</tr>";
} else {
	$Sql = query("select * from cliente_fornecedor where id='$id_cliente'");
	extract(mysqli_fetch_assoc($Sql));
	if ($tipo == "f") {
		echo "
		<tr>
			<td colspan='5'><span>Cliente</span>$nome</td>
			<td colspan='1'><span>CPF</span>$cpf_cnpj</td>
		</tr>";
		$data = "Data Nascimento";
		$doc2 = "RG";
	} else {
		echo "
		<tr>
			<td colspan='3'><span>Cliente</span>$nome</td>
			<td colspan='2'><span>Razão Social</span>$razao_social</td>
			<td colspan='1'><span>CNPJ</span>$cpf_cnpj</td>
		</tr>";
		$data = "Data Fundação";
		$doc2 = "IE";
	}

	echo "
		<tr>
			<td colspan='5'><span>E-mail</span>$email</td>
			<td colspan='1'><span>$doc2</span>$rg_ie</td>
		</tr>
		<tr>
			<td colspan='5' rowspan='2' valign='top'><span>Endereço</span>" . nl2br($endereco) . "</td>
			<td colspan='1'><span>Telefone 1</span>$fone1</td>
		</tr>
		<tr>
			<td colspan='1'><span>Telefone 2</span>$fone2</td>
		</tr>
		<tr>
			<td colspan='3'><span>Bairro</span>$bairro</td>
			<td colspan='1'><span>Numero</span>$numero</td>
			<td colspan='1'><span>Cidade</span>" . registro($cidade, "cidades", "nome", "cod_cidades") . " - " . registro($estado, "estados", "sigla", "cod_estados") . "</td>
			<td colspan='1'><span>$data</span>" . formataData($data_nascimento) . "</td>
		</tr>";
}
echo "<tr>";
echo "<td scope='col' colspan='2' align='center'><span>Data de abertura</span>" . formataData($data_venda) . "</td>";
echo "<td scope='col' colspan='3' align='center'><span>Data de previsão de entrega</span>" . formataData($data_previsao) . "</td>";
echo "<td scope='col' colspan='1' align='center'><span>Data de finalização</span>" . formataData($data_concluida) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td scope='col' colspan='4' align='center'><span>Tipo de Entrega</span>";
if ($entrega_tipo == "entregar") {
	echo "O cliente solicitou que entregasse o serviço no endereço do cadastro do cliente.";
} else {
	echo "O cliente concordou em buscar o serviço.";
}
echo "</td>";
echo "<td scope='col' colspan='2' align='center'><span>Produziram está Ordem de Serviço</span>";
$sqlProduziram = query("select * from ordem_servico_usuario where id_ordem_servico='$idOrdemServico' and id_ordem_servico!='0'");
for ($i = 0; $i < mysqli_num_rows($sqlProduziram); $i++) {
	extract(mysqli_fetch_assoc($sqlProduziram));
	echo registro($id_usuario_contribuidor, "usuario", "nome") . " ";
	echo registro(registro($id_usuario_contribuidor, "usuario", "funcao"), "funcao", "nome") . "<br>";
}
echo "</td>";
echo "</tr>";


echo "<tr>";
echo "<th colspan='6'>Serviço</th>";
echo "</tr>";

//para usuarios que possão visualizar os valores da ordem de serviço
if (getCredencialUsuario("cadastrarOrcamento.php") or getCredencialUsuario("cadastrarConta.php")) {

	echo "<tr>";
	echo "<td colspan='5' class='preco1'>";
	echo "<span>Serviço</span>" . registro($id_servico, "servico", "nome");
	echo "<div class='suggestionsBox' id='sugestoes' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"fechar();\"></span>";
	echo "<div class='suggestionList' id='sugestoesLista'></div></div>";
	echo "</td>";
	echo "<td  style='width:100px;'><span>Quantidade</span>$quantidade</td>";
	echo "<td colspan='1' style='display:none;' class='preco2'><span>Sub Total</span>" . real($precoServico) . "</td>";
	$servicoTotal = $quantidade * $precoServico;
	echo "<td colspan='1' style='display:none;' class='preco2' ><span>Total</span>" . real($servicoTotal) . "</td>";
	echo "</tr>";
	echo "<tr style='display:none;' class='preco2'>";
	$instrucao = "select id, forma_pagamento, parcelas from conta where tabela_referido='$tabela_referido' and referido='$referido'";
	$sql_conta = query($instrucao);
	extract(mysqli_fetch_assoc($sql_conta));
	$sql_conta_log = query("select tipo_pagamento, tipo_pagamento_sub from conta_itens where id_conta='$id'");
	extract(mysqli_fetch_assoc($sql_conta_log));
	echo "<td id='td1' colspan='6'>
								<table>
									<tr>
										<td class='tdNone'>Forma de Pagamento</td>
										<td class='tdNone'>" . registro($forma_pagamento, "pagamento_forma", "forma_pagamento") . "</td>
									</tr>
									<tr>
										<td class='tdNone'>Tipo de pagamento</td>
										<td class='tdNone'>" . registro($tipo_pagamento, "pagamento_tipo", "tipo_pagamento") . "</td>
									</tr>";
	if ($tipo_pagamento_sub) {
		echo "
										<tr>
											<td class='tdNone'>Sub Tipo de pagamento</td>
											<td class='tdNone'>" . registro($tipo_pagamento_sub, "pagamento_tipo_sub", "sub_tipo_pagamento") . "</td>
										</tr>";
	}
	if ($parcelas > 1) {
		echo "
											<tr>
												<td class='tdNone'>Parcelas</td>
												<td class='tdNone'>$parcelas</td>
											</tr>
										";
	}
	echo "</table></td></tr>";

	if (isset($_GET["marca"])) {
		$display = "";
	} else {
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
	for ($i = 0; $i < $linha; $i++) {
		extract(mysqli_fetch_assoc($sql));
		if ($i > 0) {
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
	for ($i = 0; $i < $linha; $i++) {
		extract(mysqli_fetch_assoc($sql));
		if ($i > 0) {
			echo "<tr>";
		}
		$instrucao = "select sub_tipo_pagamento from pagamento_tipo_sub where id_pagamento_tipo='$id' and status='Ativo'";
		$sqlSubTipoPagamento = query($instrucao);
		$cod = "";
		for ($j = 0; $j < mysqli_num_rows($sqlSubTipoPagamento); $j++) {
			extract(mysqli_fetch_assoc($sqlSubTipoPagamento));
			if ($j == 0) {
				$cod = "( ";
			}
			$cod .= "$sub_tipo_pagamento";
			if ($j <> mysqli_num_rows($sqlSubTipoPagamento) and $j <> mysqli_num_rows($sqlSubTipoPagamento) - 1) {
				$cod .= ", ";
			}
			if ($j == mysqli_num_rows($sqlSubTipoPagamento) - 1) {
				$cod .= ")";
			}
		}
		echo "<td class='tdNone' colspan='2'>$tipo_pagamento $cod</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</tr>";
	echo "</td>";
} else {
	//para usuarios que não podem visualizar os valores da ordem de servico
	echo "<tr>";
	echo "<th scope='col' colspan='5' align='center'>Serviço</th>";
	echo "<th scope='col' align='center'>Quantidade</th>";
	echo "</tr>";

	echo "<tr>";
	echo "<td colspan='5'>";
	echo "<input type='hidden' name='idServico' value='$id_servico'>";
	echo "<input type='text' name='servico' value='" . registro($id_servico, "servico", "nome") . "' class='inputValor'>";
	echo "</td>";
	echo "<td><input type='text' name='quantidade' value='$quantidade' class='inputValor'></td>";
	echo "<input type='hidden' name='subTotal' value='" . real($precoServico) . "'>";
	$servicoTotal = $quantidade * $precoServico;
	echo "<input type='hidden' name='servicoTotal' value='" . real($servicoTotal) . "'>";
	echo "</tr>";
	echo "<tr>";
	$instrucao = "select * from conta where entidade='$busca' and referido='$referido' and tabela_referido='$tabela_referido'";
	$sql = query($instrucao);
	if (mysqli_num_rows($sql) > 0) {
		extract(mysqli_fetch_assoc($sql));
	} else {
		$id = $endidade = $referido = $tipo = $valor = $forma_pagamento = $parcelas = $nota_fiscal = $data = $id_usuario = null;
	}
	echo "<input type='hidden' name='pgaForma' value='$forma_pagamento'>";
	echo "<input type='hidden' name='pgaTipo' value='$tipo_pagamento_sub'>";
	$sql = query("SELECT * FROM pagamento_tipo_sub WHERE id_pagamento_tipo IN (SELECT id_pagamento_tipo FROM pagamento_tipo_sub WHERE id='$tipo_pagamento_sub' and status='Ativo')");
	if (mysqli_num_rows($sql) > 0) {
		extract(mysqli_fetch_assoc($sql));
		echo "<input type='hidden' name='subTipoPagamento' value='$id'>";
	}
	echo "<input type='hidden' name='pgaParcelas' value='$parcelas'>";

}



$sqlAtributos = query("select * from ordem_servico_atributo_padrao where status='1' order by id");
$divisor = 1;
$colspan = 6 / $divisor;
while (mysqli_num_rows($sqlAtributos) % $divisor != 0 and $divisor < 6) {
	$divisor++;
	$colspan = 6 / $divisor;
}
for ($i = 0; $i < mysqli_num_rows($sqlAtributos); $i++) {
	if ($i == 0) {
		echo "<tr>";
		echo "<th scope='col' colspan='6' align='center'>Atributos da Ordem de Serviço</th>";
		echo "</tr>";
	}

	if ($i % $divisor == 0) {
		if ($i != 0) {
			echo "</tr>";
		}
		echo "<tr>";
	}
	extract(mysqli_fetch_assoc($sqlAtributos));
	$idAtributo = $id;
	$caminho = "../".END_ARQ."arq_ordem_servico/" . $idOrdemServico . "/" . $tipo_item . "_" . $idAtributo . "/";
	
	$instrucao = "select valor as valorAtributo from ordem_servico_atributo where id_ordem_servico='$idOrdemServico' ";
	$instrucao .= "and id_ordem_servico_padrao='$id'";
	$sqlAtributosValor = query($instrucao);
	mysqli_num_rows($sqlAtributosValor) > 0 ? extract(mysqli_fetch_assoc($sqlAtributosValor)) : $valorAtributo = null;
	//"Texto curto", "Texto com parágrafo", "Arquivo", "Seleção de itens", "Imagem"
	if ($tipo_item == "Texto curto" or $tipo_item == "Seleção de itens") {
		$cod = $valorAtributo;
	} elseif ($tipo_item == "Texto com parágrafo") {
		$cod = str_replace("</p>", "<br>", str_replace("<p>", "", $valorAtributo));
	} elseif ($tipo_item == "Arquivo") {
		$cod = "";
		if ($idOrdemServico) {
			if (is_dir($caminho)) {
				$diretorio = dir($caminho);
			} else {
				mkdir($caminho, 0755, true);
				chmod($caminho, 0755);
				$diretorio = dir($caminho);
			}
			$valorAtributo = 0;
			while ($arquivo = $diretorio -> read()) {
				if (!is_dir($arquivo)) {
					$cod .= "<p style='line-height:30px; vertical-align: middle;'><img src='../img/arq.png' style='width:30px;'> $arquivo </p>";
					$valorAtributo++;
				}
			}
			$diretorio -> close();
		}
	} elseif ($tipo_item == "Imagem") {
		$cod = "";
		if ($idOrdemServico) {
			if (is_dir($caminho)) {
				$diretorio = dir($caminho);
			} else {
				mkdir($caminho, 0755, true);
				chmod($caminho, 0755);
				$diretorio = dir($caminho);
			}
			$valorAtributo = 0;
			while ($arquivo = $diretorio -> read()) {
				if (!is_dir($arquivo)) {
					$cod .= "<img src=\"../".END_ARQ."arq_ordem_servico/$idOrdemServico/".$tipo_item."_".$idAtributo."/$arquivo\" style=\"height:100px;\">";
					$valorAtributo++;
				}
			}
			$diretorio -> close();
		}
	}
	echo "<td scope='col' colspan='$colspan' align='center' style='text-transform:capitalize;'>";
	echo "<span>$nome</span>";
	echo $cod;
	echo "</td>";
	$cod = "";
	

}


echo "<tr>";
echo "<td colspan='6' align='center' class='tdNone'>";
$codOS = "OS " . base64_decode($_GET["os"]);
$codLinha = strlen($codOS);
$codLinha2 = 30 - ($codLinha);
str_pad("0", $codLinha2, "0");
$texto = null;
$cod = str_pad($texto, $codLinha2, "0") . $codOS;

echo "<iframe src='../inc/codBarras.inc.php?cod=$cod&w=300&h=50' id='codBarrasH'></iframe>";


echo "</td>";
echo "</tr>";
echo "</table>";

echo "<div id='marca'>".registro($statusOrdemServico, "ordem_servico_status", "nome")."</div>";

include_once "../templates/downLoginImp.inc.php";
?> 