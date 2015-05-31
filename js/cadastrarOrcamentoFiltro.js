// JavaScript Document
//funcao para corrigir problema de voltar historico navegador e a quantidade item ser diferente de 1

window.onload = function() {
	if ($('input[name$="op"]').val() == "novo") {
		$('#qtdItem').val(1);
	}
};

//funcao para o auto complite do item
function lookup(valorInput, identidade) {
	//capturando o id da input
	var ID = identidade.split("_");
	
	if (valorInput.length == 0) {
		// Hide the suggestion box.
		$('#suggestions_' + ID[1]).hide();
	} else {
		$('#suggestions_' + ID[1]).show();
		var cod = "<center><img style='width:30px; margin-right:30px;' src='img/loading.gif'></center>";
		$('#autoSuggestionsList_' + ID[1]).html(cod);
		$.post("inc/orcamento_itens.inc.php", {
			queryString : "" + valorInput + "",
			id : "" + ID[1] + ""
		}, function(data) {
			if (data.length > 0) {
				$('#autoSuggestionsList_' + ID[1]).html(data);
			}else{
				$('#autoSuggestionsList_' + ID[1]).html("<table style='margin-right:30px;'><tr><td>Nenhum resultado encontrado.</td></tr></table>");
				//setTimeout("$('.suggestionsBox').hide();", 3000);
			}
		});
	}
}

function ajaxTipoPagamentoSub(valor, tipo_pagamento_sub) {
	$('#pagamentoTipoSub').show();
	var cod = "<td colspan='6'><center><img width='30' src='img/loading.gif'></center></td>";
	$('#pagamentoTipoSub').html(cod);
	$.post("inc/ajaxTipoPagamentoSub.inc.php", {
		variavel : "" + valor + "",
		tipoPagamentoSub : "" + tipo_pagamento_sub + ""
	}, function(data) {
		if (data.length > 0) {
			$('#pagamentoTipoSub').show();
			$('#pagamentoTipoSub').html(data);
		}else{
			$('#spanTipoPagamento').html("");
			$('#pagamentoTipoSub').hide();
		}
	});
}

function showParcela(valor) {
	if (valor == "2") {
		document.getElementById('parcela').style.display = '';
	} else {
		document.getElementById('parcela').style.display = 'none';
		document.getElementById('parcelaSelect').value = '';
	}
}

function turnZero(variavel) {
	if (variavel == NULL) {
		variavel = "";
	}
	return variavel;
}

function lookupOff() {
	$('.suggestionsBox').hide();
}

function preencher(TIPOITEM, ID, NOME, PRECO, INPUTID) {
	$('#tabelaItem_' + INPUTID).val(TIPOITEM);
	$('#idItem_' + INPUTID).val(ID);
	$('#item_' + INPUTID).val(NOME);
	$('#item_' + INPUTID).attr("class", "inputValor");
	$('#quantidade_' + INPUTID).val(1);
	Preco = PRECO.toString();
	$('#subTotal_' + INPUTID).val((parseFloat(Preco.replace(",", ".")).toFixed(2)).toString().replace(".", ","));
	setTimeout("$('#suggestions_" + INPUTID + "').hide();", 200);
	calcularTotal();
	
}

//funcao para adicionar ou remover item de item
$(function() {
	function removeCampo() {
		$(".removerCampo").unbind("click");
		$(".removerCampo").bind("click", function() {
			i = 0;
			$(".itens tr.campoItem").each(function() {
				i++;
			});
			if (i > 1) {
				$(this).parent().parent().remove();
				document.getElementById('qtdItem').value = parseInt(document.getElementById('qtdItem').value) - 1;
			}
		});
	}

	removeCampo();
	$(".adicionarCampo").click(function() {
		lookupOff();
        novoCampo = $(".itens tr.campoItem:first").clone();
        novoCampoL = $(".itens tr.campoItem:last").clone();
        novoCampo.find("input").val("");
        novoCampoL.find("input").val("");
		//verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
		$('#qtdItem').val(parseInt($('#qtdItem').val()) + 1);
		var qtdItem = $('#qtdItem').val();
		while ($('#id_' + qtdItem).val() != undefined) {
			qtdItem = parseInt(qtdItem) + 1;
		}

		//editando id's das inputs
		/*novoCampo.find(".info").attr("class", "info lupa_" + qtdItem);
		novoCampo.find(".infoBack").attr("class", "infoBack lupa_" + qtdItem);
		novoCampo.find(".link").attr("onclick", "$('.lupa_" + qtdItem + "').show();");*/

		novoCampo.find(".link").attr("onclick", "lookup($('#item_" + qtdItem + "').val(), 'item_" + qtdItem + "');");

		novoCampo.find("input[name='tabelaItem[]']").attr("id", "tabelaItem_" + qtdItem);
		novoCampo.find("input[name='tabelaItem[]']").attr("value", "item");

		novoCampo.find("input[name='idItem[]']").attr("id", "idItem_" + qtdItem);

		novoCampo.find("input[name='item[]']").attr("id", "item_" + qtdItem);
		novoCampo.find("input[name='item[]']").attr("class", "");

		novoCampo.find("input[name='quantidade[]']").attr("id", "quantidade_" + qtdItem);
		novoCampo.find("input[name='quantidade[]']").attr("value", "0");

		novoCampo.find("td[class='tdSubTotal']").attr("id", "tdSubTotal_" + qtdItem);
		var script = "<input type=\"text\" name=\"subTotal[]\" value=\"00,00\" class=\"totalValor preco\" ";
		script += "id=\"subTotal_" + qtdItem + "\" onkeydown=\"Mascara(this,Valor2); calcularTotal(); verificaTipoItem(this);\" ";
		script += "onkeypress=\"Mascara(this,Valor2); calcularTotal(); verificaTipoItem(this);\" ";
		script += "onkeyup=\"Mascara(this,Valor2); calcularTotal(); verificaTipoItem(this);\" autocomplete=\"off\" >";
		novoCampo.find("td[class='tdSubTotal']").html(script);

		novoCampo.find("input[name='subTotal[]']").attr("id", "subTotal_" + qtdItem);
		novoCampo.find("input[name='subTotal[]']").attr("value", "00,00");

		novoCampo.find("input[name='itemTotal[]']").attr("id", "itemTotal_" + qtdItem);
		novoCampo.find("input[name='itemTotal[]']").attr("value", "00,00");

		novoCampo.find("div[class='suggestionsBox']").attr("id", "suggestions_" + qtdItem);
		novoCampo.find("input[id='deletar']").attr("value", "X");
		novoCampo.find("div[class='suggestionList']").attr("id", "autoSuggestionsList_" + qtdItem);
        novoCampo.insertAfter(".itens tr.campoItem:last");
        novoCampoL.insertAfter(".itens tr.campoItem:last");

		removeCampo();
	});
});

//determina o preco total do item ao alterar o valor da quantidade
function precoTotalItem(quant) {
	var id = quant.id.split("_");
	var quantidade = parseFloat($('#quantidade_' + id[1]).val());
	if (quantidade > 0) {
		subTotalValor = parseFloat($('#subTotal_' + id[1]).val().replace(",", "."));
		var total = (quantidade * subTotalValor).toFixed(2);
		if (isNaN(total) == false) {
			$('#itemTotal_' + id[1]).val(total.toString().replace(".", ","));
		}
	}
}

//determina todos os valores do orçamento
function calcularTotal() {
	var cont = $('#qtdItem').val();
    var subT = 0;
    var total = 0;
    var descPor = 0;
    var descReal = $('input[name="totalDescontoReal"]').val().replace(",", ".");
	var quant;
	var subTotal;

	for (var i = 1; i <= cont; i++) {
		if ($('#subTotal_' + i).val() != undefined) {
			quant = parseFloat($('#quantidade_' + i).val());
			subTotal = parseFloat($('#subTotal_' + i).val().replace(",", "."));
			subT += parseFloat($('#subTotal_' + i).val().replace(",", "."));
			// subtotalGlobal
			if (isNaN(quant * subT) == false) {
				$('#itemTotal_' + i).val((quant * subTotal).toFixed(2).toString().replace(".", ","));
			}
			total += parseFloat($('#itemTotal_' + i).val().replace(",", "."));
			//totalGlobal
		}
	}

    descPor = parseFloat(100-((total - descReal) * 100 / total));
    descReal = parseFloat(descReal);
    total = total - descReal;

    if (isNaN(descPor) == false) {
        $('input[name="totalDescontoPor"]').val(descPor.toFixed(2).replace(".", ","));
    }
    if (isNaN(descReal) == false) {
        $('input[name="TotalDescontoReal"]').val(descReal.toFixed(2).replace(".", ","));
    }
	if (isNaN(subT) == false) {
		$('input[name="totalSubTotal"]').val(subT.toFixed(2).replace(".", ","));
	}
	if (isNaN(total) == false) {
		$('input[name="totalItemTotal"]').val(total.toFixed(2).replace(".", ","));
	}

}
function calcularTotalDescPor(){

    var cont = $('#qtdItem').val();
    var subT = 0;
    var total = 0;
    var descPor = $('input[name="totalDescontoPor"]').val().replace(",", ".");
    var descReal = 0
    var quant;
    var subTotal;

    for (var i = 1; i <= cont; i++) {
        if ($('#subTotal_' + i).val() != undefined) {
            quant = parseFloat($('#quantidade_' + i).val());
            subTotal = parseFloat($('#subTotal_' + i).val().replace(",", "."));
            subT += parseFloat($('#subTotal_' + i).val().replace(",", "."));
            // subtotalGlobal
            if (isNaN(quant * subT) == false) {
                $('#itemTotal_' + i).val((quant * subTotal).toFixed(2).toString().replace(".", ","));
            }
            total += parseFloat($('#itemTotal_' + i).val().replace(",", "."));
            //totalGlobal
        }
    }

    descReal = parseFloat(total * descPor/100);
    descPor = parseFloat(descPor);
    total = total - descReal;

    if (isNaN(descPor) == false) {
        $('input[name="totalDescontoPor"]').val(descPor.toFixed(2).replace(".", ","));
    }
    if (isNaN(descReal) == false) {
        $('input[name="totalDescontoReal"]').val(descReal.toFixed(2).replace(".", ","));
    }
    if (isNaN(subT) == false) {
        $('input[name="totalSubTotal"]').val(subT.toFixed(2).replace(".", ","));
    }
    if (isNaN(total) == false) {
        $('input[name="totalItemTotal"]').val(total.toFixed(2).replace(".", ","));
    }

}

//verificar qual tipo do item e se caso for serviço preenche com o preço de acordo com sua quantidade
function verificaTipoItem(input) {

	var id = input.id.split("_");
	var quantidade = input.value;
	var tipoItem = $('#tabelaItem_' + id[1]).val();

	if (tipoItem == "servico") {
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#tdSubTotal_' + id[1]).html(cod);
		$.post("inc/cadastrarOrcamentoPrecoServico.inc.php", {
			idServico : "" + $('#idItem_' + id[1]).val() + "",
			qtd : "" + quantidade + "",
			idInput : "" + id[1] + ""
		}, function(data) {
			if (data.length > 0) {
				$('#tdSubTotal_' + id[1]).html(data);
				calcularTotal();
			}
		});
	}

}

function filtroOrcamento() {
	with (document.formulario) {
		var alerta = "";
		var valida = true;

		if (pgaForma.value == "0") {
			alerta += "Escolha a forma de pagamento.\n";
			pgaForma.className = "avisoInput";
			valida = false;
		} else {
			pgaForma.className = "";
		}
		if (pgaTipo.value == "0") {
			alerta += "Escolha o tipo de pagamento.\n";
			pgaTipo.className = "avisoInput";
			valida = false;
		} else {
			pgaTipo.className = "";
		}
		if (pgaForma.value == "2" && pgaParcelas.value == "") {
			alerta += "Determine o numero de parcelamento.\n";
			pgaParcelas.className = "avisoInput";
			valida = false;
		} else {
			pgaParcelas.className = "";
		}
		//filtro de checagem de id de Item
		for (var i = 1; i <= qtdItem.value; i++) {
			if (document.getElementById('idItem_' + i) != null) {
				if ($('#item_' + i).val().length == 0) {
					if (alerta.indexOf("Existe um campo de item nao preenchido.\n") < 0) {
						alerta += "Existe um campo de item nao preenchido.\n";
					}
					$('#item_' + i).attr("class", "avisoInput");
					valida = false;
				} else {
					$('#item_' + i).attr("class", "");
				}
				if (parseInt($('#quantidade_' + i).val()) == 0) {
					if (alerta.indexOf("Existe um campo de item nao preenchido.\n") < 0) {
						alerta += "Existe um campo de item nao preenchido.\n";
					}
					$('#quantidade_' + i).attr("class", "avisoInput");
					valida = false;
				} else {
					$('#quantidade_' + i).attr("class", "");
				}
			}
		}
        if($('input[name="totalItemTotal"]').val()<0){
            alerta += "Orçamento com total negativo.\n Verifique o desconto.\n";
            valida = false;
        }

	}
	

	if (valida == true) {
		return true;
	} else {
		alert(alerta);
		return false;
	}
}