// JavaScript Document
//eventos q acontece dentro do formCadastraProduto
//para simplificar o codigo usei a funcao WITH
function detSubCat() {
	with (document.formCadastraServico) {
		var cat = categoria.value;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 1) {
				selectSubCat.innerHTML = "<center><img width='30' src='img/loading.gif'></center>";
			} else if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				selectSubCat.innerHTML = xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET", "inc/sub_categoria.inc.php?categoria=" + cat, true);
		xmlhttp.send();
	}//final do with
}

function mudaSubCat(valor){
	$("input[name='subcategoria']").val(valor);
}

function verificaNome(){
	var nome = $('#nome').val();
	var ID = $("input[name='id']").val();
	if(nome.length<5){
		$('#checkNomeSpan').attr("style", "display:inline-block;");
		$('#checkNomeSpan').html("<center><img class='check' src='img/check-no.png'></center>");
		$('#nome').attr("class", "avisoInput");
		$('#checkNomeInput').val("false");
	}else{
		if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}else{
			xmlhttp = new ActiveXobject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState == 1) {
				$('#checkNomeSpan').attr("style", "display:inline-block;");
				$('#checkNomeSpan').html("<center><img class='check' src='img/loading.gif'></center>");
				$('#checkNomeInput').val("false");
			} else if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				resposta = xmlhttp.responseText;
				if(resposta=="true"){
					$('#checkNomeSpan').attr("style", "display:inline-block;");
					$('#checkNomeSpan').html("<center><img class='check' src='img/check-ok.png'></center>");
					$('#nome').attr("class", "");
					$('#checkNomeInput').val("true");
				}else{
					$('#checkNomeSpan').attr("style", "display:inline-block;");
					$('#checkNomeSpan').html("<center><img class='check' src='img/check-no.png'></center>");
					$('#nome').attr("class", "avisoInput");
					$('#checkNomeInput').val("false");
				}
			}
		}
		xmlhttp.open("GET", "inc/servico_nome.inc.php?nome=" + nome + "&id=" + ID, true);
		xmlhttp.send();
	}
	
}

//funcao para adicionar ou remover item de produto
$(function() {
	function removerCampoProduto() {
		$(".removerCampoProduto").unbind("click");
		$(".removerCampoProduto").bind("click", function() {
			i = 0;
			$(".servico tr.campoProduto").each(function() {
				i++;
			});
			if (i > 1) {
				$(this).parent().parent().remove();
				document.getElementById('produtoQtd').value = parseInt(document.getElementById('produtoQtd').value) - 1;
			}
		});
	}

	removerCampoProduto();
	$(".adicionarCampoProduto").click(function() {
		novoCampo = $(".servico tr.campoProduto:first").clone();
		novoCampo.find("input").val("");

		//verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
		$('#produtoQtd').val(parseInt($('#produtoQtd').val()) + 1);
		var qtdProduto = $('#produtoQtd').val();
		while ($('#produtoId_' + qtdProduto).val() != undefined) {
			qtdProduto = parseInt(qtdProduto) + 1;
		}

		//editando id's das inputs
		novoCampo.find("input[name='id_produto[]']").attr("id", "produtoId_" + qtdProduto);
		novoCampo.find("input[name='produto[]']").attr("id", "produto_" + qtdProduto);
		novoCampo.find("input[name='quantidade[]']").attr("id", "produtoQtd_" + qtdProduto);
		novoCampo.find("input[name='quantidade[]']").attr("value", "00,00");
		novoCampo.find("select[name='volume[]']").attr("id", "produtoVolume_" + qtdProduto);
		novoCampo.find("input[name='produtoSubTotal[]']").attr("id", "produtoSubTotal_" + qtdProduto);
		novoCampo.find("input[name='produtoSubTotal[]']").attr("value", "00,00");
		novoCampo.find("input[name='produtoTotal[]']").attr("id", "produtoTotal_" + qtdProduto);
		novoCampo.find("input[name='produtoTotal[]']").attr("value", "00,00");
		novoCampo.find("div[class='suggestionsBox']").attr("id", "suggestions_" + qtdProduto);
		novoCampo.find("input[id='deletar']").attr("value", "X");
		novoCampo.find("div[class='suggestionList']").attr("id", "autoSuggestionsList_" + qtdProduto);
		novoCampo.insertAfter(".servico tr.campoProduto:last");

		removerCampoProduto();
	});
});

//funcao para o auto complite do produto
function buscarProduto(input, op) {
	//capturando o id da input
	var id = input.id.split("_");

	//op == false para procurar string com o nome do produto
	//op == true para procurar string com o id do produto
	if (input.value.length == 0) {
		// Hide the suggestion box.
		$('#suggestions_' + id[1]).hide();
	} else {
		$('#suggestions_' + id[1]).show();
		var cod = "<br><br><center><img width='30' src='img/loading.gif'></center>";
		$('#autoSuggestionsList_' + id[1]).html(cod);
		$.post("inc/servico_produtos.inc.php", {
			queryString : "" + input.value + "",
			id : "" + id[1] + "",
			op : "" + op + ""
		}, function(data) {
			if (data.length > 0) {
				$('#suggestions_' + id[1]).show();
				$('#autoSuggestionsList_' + id[1]).html(data);
				//setTimeout("$('.suggestionsBox').hide();", 10000);
			}
		});
	}
}

//'$nomeCompleto', '$id', '$preco1', '$inputId', '$cod_barra', '$nome'
function preencherProdutoServico(IDENTIDADE, ID, NOME, SUBTOTAL) {

	$("#produtoId_" + IDENTIDADE).val(ID);
	$("#produto_" + IDENTIDADE).val(NOME);
	$("#produtoSubTotal_" + IDENTIDADE).val(SUBTOTAL);

	lookupOff();
}

function turnZero(input) {
	var valor = input.value;
	if (valor == "") {
		valor = 0;
	}
	$('#' + input.id).val(valor);
}

function lookupOff() {
	$('.suggestionsBox').hide();
}

function calcularPreco(input, idReferencia, idAlterar) {

	id = input.id.split('_');
	if ($('#' + idReferencia + id[1]).val() == "") {
		valorReferencia = "0";
	} else {
		valorReferencia = ($('#' + idReferencia + id[1]).val()).replace(",", ".");
	}
	if (input.value == "") {
		total = "00,00";
	} else {
		total = (parseFloat(valorReferencia) * parseFloat((input.value).replace(",", "."))).toFixed(2);
	}
	total = total.replace(".", ",");
	$('#' + idAlterar + id[1]).val(total);

	calcularProdutoPrecoTotal();
}

//quantidade, preco, identidade
function calcularPreco2(inputValor, referenciaValor, verificaValor) {

	var id = inputValor.id.split("_");

	if (verificaValor) {
		$('#' + referenciaValor + id[1]).val() == "" ? preco = "0" : preco = $('#' + referenciaValor + id[1]).val();
		inputValor.value == "" ? quantidade = "0" : quantidade = inputValor.value;
	} else {
		inputValor.value == "" ? preco = "0" : preco = inputValor.value;
		$('#' + referenciaValor + id[1]).val() == "" ? quantidade = "0" : quantidade = $('#' + referenciaValor + id[1]).val();
	}
	if (quantidade <= 0) {
		$('#precoTotal_' + id[1]).val(preco);
	} else {
		$('#precoTotal_' + id[1]).val(((parseInt(quantidade) * parseFloat(preco.replace(",", ".")).toFixed(2)).toFixed(2)).replace(".", ","));
	}

}

/*
 $(function() {
 $('#calcularProduto').click(function() {
 var cont = $('#produtoQtd').val();
 var subT = "";
 var total = "";
 var quant;
 var subTotal;
 subT = total = "0";
 for (var i = 1; i <= cont; i++) {
 if ($('#produtoSubTotal_' + i).val() != undefined) {
 if ($('#produtoSubTotal_' + i).val() != "") {
 subT = parseFloat(subT) + parseFloat(($('#produtoSubTotal_' + i).val()).replace(",", "."));
 }
 if ($('#produtoTotal_' + i).val() != "") {
 total = parseFloat(total) + parseFloat(($('#produtoTotal_' + i).val()).replace(",", "."));
 }
 }
 }
 $('#subTotalProduto').val((parseFloat(subT).toFixed(2)).replace(".", ","));
 $('#totalProduto').val(parseFloat(total).toFixed(2).replace(".", ","));

 });
 });
 */
function calcularProdutoPrecoTotal() {
	var cont = parseInt($('#produtoQtd').val()) + 50;
	var subT = "";
	var total = "";
	var quant;
	var subTotal;
	subT = total = "0";
	for (var i = 1; i <= cont; i++) {
		if ($('#produtoSubTotal_' + i).val() != undefined) {
			if ($('#produtoSubTotal_' + i).val() != "") {
				subT = parseFloat(subT) + parseFloat(($('#produtoSubTotal_' + i).val()).replace(",", "."));
			}
			if ($('#produtoTotal_' + i).val() != "") {
				total = parseFloat(total) + parseFloat(($('#produtoTotal_' + i).val()).replace(",", "."));
			}
		}
	}
	$('#subTotalProduto').val((parseFloat(subT).toFixed(2)).replace(".", ","));
	$('#totalProduto').val(parseFloat(total).toFixed(2).replace(".", ","));

}

function showUsuarioValor(input) {
	//capturando o id da input
	var id = input.id.split("_");

	var cod = "<center><img width='30' src='img/loading.gif'></center>";
	$('#usuarioValorTd_' + id[1]).html(cod);
	$.post("inc/servico_usuario_valor.inc.php", {
		tabela : "" + input.value + "",
	}, function(data) {
		$('#usuarioValorTd_' + id[1]).html(data);
	});
}

function showPrecoValor(input) {

	var id = input.id.split("_");
	if (input.value == '1') {
		$('#precoValorReal_' + id[1]).show();
		$('#precoValorPor_' + id[1]).hide();
	} else {
		$('#precoValorReal_' + id[1]).hide();
		$('#precoValorPor_' + id[1]).show();
	}

}

$(function() {
	function removerRegraComissao() {
		$(".removerRegraComissao").unbind("click");
		$(".removerRegraComissao").bind("click", function() {
			i = 0;
			$(".servico tr.regraComissao").each(function() {
				i++;
			});
			if (i > 1) {
				$(this).parent().parent().remove();
				document.getElementById('comissaoQtd').value = parseInt(document.getElementById('comissaoQtd').value) - 1;
			}
		});
	}

	removerRegraComissao();

	$('.adicionarRegraComissao').click(function() {
		novoCampo = $(".servico tr.regraComissao:first").clone();
		novoCampo.find("input").val("");

		//verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
		$('#comissaoQtd').val(parseInt($('#comissaoQtd').val()) + 1);
		var qtdComissao = $('#comissaoQtd').val();
		while ($('#usuarioTabela_' + qtdComissao).val() != undefined) {
			qtdComissao = parseInt(qtdComissao) + 1;
		}

		//editando id's das inputs
		novoCampo.find("select[name='usuarioTabela[]']").attr("id", "usuarioTabela_" + qtdComissao);
		novoCampo.find("td[class='usuarioValor']").attr("id", "usuarioValorTd_" + qtdComissao);
		novoCampo.find("select[name='usuarioValor[]']").attr("id", "usuarioValor_" + qtdComissao);
		novoCampo.find("select[name='precoTipo[]']").attr("id", "precoTipo_" + qtdComissao);
		novoCampo.find("select[name='precoTipo[]']").attr("value", "0");
		novoCampo.find("div[class='precoValorReal']").attr("id", "precoValorReal_" + qtdComissao);
		novoCampo.find("div[class='precoValorReal']").attr("style", "display:none;");
		novoCampo.find("input[name='precoValorReal[]']").attr("value", "00,00");
		novoCampo.find("input[name='precoValorReal[]']").attr("id", "precoValorRealInput_" + qtdComissao);
		novoCampo.find("div[class='precoValorPor']").attr("id", "precoValorPor_" + qtdComissao);
		novoCampo.find("div[class='precoValorPor']").attr("style", "display:inline-block;");
		novoCampo.find("input[name='precoValorPor[]']").attr("value", "0");
		novoCampo.find("input[name='precoValorPor[]']").attr("id", "precoValorPorInput_" + qtdComissao);
		novoCampo.find("input[name='precoValorMax[]']").attr("value", "00,00");
		novoCampo.find("input[name='precoValorMax[]']").attr("id", "precoValorMaxInput_" + qtdComissao);
		novoCampo.find("input[name='penalidade[]']").attr("value", "0");
		novoCampo.find("input[name='penalidade[]']").attr("id", "penalidadeInput_" + qtdComissao);
		novoCampo.insertAfter(".servico tr.regraComissao:last");

		removerRegraComissao();
	});
});

$(function() {
	function removerRegraPreco() {
		$(".removerRegraPreco").unbind("click");
		$(".removerRegraPreco").bind("click", function() {
			i = 0;
			$(".servico tr.regraPreco").each(function() {
				i++;
			});
			if (i > 1) {
				$(this).parent().parent().remove();
				document.getElementById('precoQtd').value = parseInt(document.getElementById('precoQtd').value) - 1;
			}
		});
	}

	removerRegraPreco();

	$('.adicionarRegraPreco').click(function() {
		novoCampo = $(".servico tr.regraPreco:first").clone();
		novoCampo.find("input").val("");

		//verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
		$('#precoQtd').val(parseInt($('#precoQtd').val()) + 1);
		var qtdPreco = $('#precoQtd').val();
		while ($('#quantidadePreco_' + qtdPreco).val() != undefined) {
			qtdPreco = parseInt(qtdPreco) + 1;
		}

		//editando id's das inputs
		novoCampo.find("input[name='quantidadePreco[]']").attr("id", "quantidadePreco_" + qtdPreco);
		novoCampo.find("input[name='quantidadePreco[]']").attr("value", "0");
		novoCampo.find("input[name='preco[]']").attr("id", "preco_" + qtdPreco);
		novoCampo.find("input[name='preco[]']").attr("value", "00,00");
		novoCampo.find("input[name='precoTotal[]']").attr("id", "precoTotal_" + qtdPreco);
		novoCampo.find("input[name='precoTotal[]']").attr("value", "00,00");
		novoCampo.insertAfter(".servico tr.regraPreco:last");

		removerRegraPreco();
	});
});

//-->