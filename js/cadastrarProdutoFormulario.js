// JavaScript Document
//funcao para adicionar ou remover item de item
$(function() {

	$(".removerCampo").click(function() {
		$('#qtdTributacao').val(parseInt($('#qtdTributacao').val()) - 1);
		if ($("#qtdTributacao").val() < 1) {
			$("#qtdTributacao").val(1);
		}
		for (var i = 1; i <= 99; i++) {
			if (i <= $('#qtdTributacao').val()) {
				$('#trTributacao_' + i).show();
			} else {
				$('#trTributacao_' + i).hide();
			}
		}

		/*
		 i = 0;
		 $(".produto tr.tributacao").each(function() {
		 i++;
		 });
		 if (i > 1) {
		 $(this).parent().parent().remove();
		 $('#qtdTributacao').val(parseInt($('#qtdTributacao').val()) - 1);
		 }*/
	});

	//removeCampo();
	$(".adicionarCampo").click(function() {

		$('#qtdTributacao').val(parseInt($('#qtdTributacao').val()) + 1);

		for (var i = 1; i <= 99; i++) {
			if (i <= $('#qtdTributacao').val()) {
				$('#trTributacao_' + i).show();
			} else {
				$('#trTributacao_' + i).hide();
			}
		}

		/*
		 novoCampo = $(".produto tr.tributacao:first").clone();
		 novoCampo.find("input").val("");
		 //verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
		 $('#qtdTributacao').val(parseInt($('#qtdTributacao').val()) + 1);
		 var qtdTributacao = $('#qtdTributacao').val();
		 while ($('#tributacao_' + qtdTributacao).val() != undefined) {
		 qtdTributacao = parseInt(qtdTributacao) + 1;
		 }

		 //editando id's das inputs
		 novoCampo.find("input[name='tributacao[]']").attr("id", "tributacao_" + qtdTributacao);

		 novoCampo.find("select[name='tipo_valor[]']").attr("id", "tipoValor_" + qtdTributacao);
		 //document.getElementById('tipoValor_' + qtdTributacao).options["0"].selected = "yes";

		 novoCampo.find("input[name='tributacaoValor[]']").attr("id", "tributacaoValor_" + qtdTributacao);
		 novoCampo.find("input[name='tributacaoValor[]']").attr("class", "totalValor preco");

		 novoCampo.insertAfter(".produto tr.tributacao:last");
		 //$(".produto tr.tributacao:last").append(novoCampo);
		 removeCampo();
		 */
	});
});

function mudaTributacao(input){
	id = input.id.split("_");
	if($("#tipoValor_"+id[1]).val()=="0"){
		$("#tributacaoValor_"+id[1]).attr("class", "totalValor preco");
	}else{
		$("#tributacaoValor_"+id[1]).attr("class", "totalValor porcentagem");
	}
	totalCusto();
}

//selecionar categoria muda a subcategoria
$(function(){
	$("html").on('change', "select[name='categoria']", subCatShow);
});

function subCatShow() {
	with (document.formCadastraProduto) {
		var cat = categoria.value;

		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$(selectSubCat).html(cod);
		$.get("inc/sub_categoria.inc.php", {
			categoria : "" + cat + ""
		}, function(data) {
			if (data.length > 0) {
				$(selectSubCat).html(data);
			}
		});
	}
}
function mudaSubCat(valor){
	$("input[name='subcategoria']").val(valor);
}

function mudaValor(valor){
	if(valor=="1"){
		
		$("input[name='qtd_minima']").attr("class", "");
		$("input[name='qtd_estoque']").attr("class", "");
		$("select[name='volume']").attr("class", "");
		$("input[name='qtd_minima']").removeAttr("readonly");
		$("input[name='qtd_estoque']").removeAttr("readonly");
		
		
		
	}else if(valor=="0"){

		
		$("input[name='qtd_minima']").val("0");
		$("input[name='qtd_estoque']").val("0");
		$("input[name='qtd_minima']").attr("class", "inputValor");
		$("input[name='qtd_estoque']").attr("class", "inputValor");
		$("select[name='volume']").attr("class", "inputValor");
		$("input[name='qtd_minima']").attr("readonly", "true");
		$("input[name='qtd_estoque']").attr("readonly", "true");
		
	}
}

function mudaPonto(valor){
	if(valor=="1"){
		$("input[name='ponto_valor']").attr("class", "totalValor ponto");
		//$("input[name='ponto_valor']").attr("readonly", "");
	}else if(valor=="0"){
		$("input[name='ponto_valor']").attr("class", "totalValor inputValor ponto");
		//$("input[name='ponto_valor']").attr("readonly", "yes");
	}
}

function totalCusto(){
	
	var ValorCompra = parseFloat($("input[name='valorCompra']").val().replace(",", "."));
	var ValorTributacaoReal = 0.0;
	var ValorTributacaoPor = 0.0;
	
	for(var i = 1; i<= parseInt($("#qtdTributacao").val()); i++){
		if($("#tipoValor_" + i).val()=="0"){
			ValorTributacaoReal += parseFloat(ValorTributacaoReal) + parseFloat($("#tributacaoValor_"+i).val().replace(",", "."));
		}else if($("#tipoValor_" + i).val()=="1"){
			ValorTributacaoPor += (parseFloat(ValorCompra) * parseFloat($("#tributacaoValor_"+i).val().replace(",", "."))) / 100;
		}
	}
	var TributacaoTotal = parseFloat(ValorTributacaoReal) + parseFloat(ValorTributacaoPor);
	$("input[name='tributacaoTotal']").val((TributacaoTotal.toFixed(2)).replace(".", ","));
	var ValorCusto = parseFloat(ValorCompra) + parseFloat(ValorTributacaoReal) + parseFloat(ValorTributacaoPor);
	$("input[name='valorCusto']").val((ValorCusto.toFixed(2)).replace(".", ","));
	
}

function valorReal(desconto){
	
	$("input[name='valorCompra']").attr("readonly", "true");
	$("input[name='valorCompra']").attr("class", "totalValor preco inputValor");
	$("input[name='tributacao[]']").attr("readonly", "true");
	$("input[name='tributacao[]']").attr("class", "inputValor");
	$("select[name='tipo_valor[]']").attr("readonly", "true");
	$("select[name='tipo_valor[]']").attr("class", "inputValor");
	$("input[name='tributacaoValor[]']").attr("readonly", "true");
	$("input[name='tributacaoValor[]']").attr("class", "totalValor preco inputValor");
	
	var custo = parseFloat($("input[name='valorCusto']").val().replace(",", "."));
	var mlpor = parseFloat($("input[name='mlpor']").val().replace(",", "."));
	var ml = parseFloat($("input[name='ml']").val().replace(",", ".")); 
	var descMaxpor = parseFloat($("input[name='descMaxpor']").val().replace(",", "."));
	
	if(desconto=="false"){
		var VALOR = (custo * mlpor / 100).toFixed(2);
		if(VALOR=="NaN"){
			VALOR = "00.00";
		}
		$("input[name='ml']").val(VALOR.toString().replace(".", ",")); 
	}else if (desconto == "true"){
		var VALOR = (ml - ml * descMaxpor / 100).toFixed(2);
		if(VALOR=="NaN"){
			VALOR = "00.00";
		}
		$("input[name='descMax']").val(VALOR.toString().replace(".", ",")); 
	}
	
}
function valorPor(desconto){
	
	$("input[name='valorCompra']").attr("readonly", "true");
	$("input[name='valorCompra']").attr("class", "totalValor preco inputValor");
	$("input[name='tributacao[]']").attr("readonly", "true");
	$("input[name='tributacao[]']").attr("class", "inputValor");
	$("select[name='tipo_valor[]']").attr("readonly", "true");
	$("select[name='tipo_valor[]']").attr("class", "inputValor");
	$("input[name='tributacaoValor[]']").attr("readonly", "true");
	$("input[name='tributacaoValor[]']").attr("class", "totalValor preco inputValor");
	
	var custo = parseFloat($("input[name='valorCusto']").val().replace(",", "."));
	var ml = parseFloat($("input[name='ml']").val().replace(",", ".")); 
	var descMax = parseFloat($("input[name='descMax']").val().replace(",", "."));
	
	if(desconto=="false"){
		var VALOR = (ml * 100 / custo).toFixed(2);
		if(VALOR=="NaN"){
			VALOR = "00.00";
		}
		$("input[name='mlpor']").val(VALOR.toString().replace(".", ",")); 
	}else if (desconto == "true"){
		var VALOR = (100 - descMax * 100 / ml).toFixed(2);
		if(VALOR=="NaN"){
			VALOR = "00.00";
		}
		$("input[name='descMaxpor']").val(VALOR.toString().replace(".", ",")); 
	}
	
}

function verificaNomeProduto(nome){
	
	$("input[name='nome']").attr("style", "width:80%;");

	if (nome.length < 5) {
		// Hide the suggestion box.
		$("input[name='nome']").attr("class", "avisoInput");
		$("input[name='verificaNome']").val("false");
		$('#checkNome').html("<center><img class='check' src='img/check-no.png'></center>");
	} else {
		
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#checkNome').html(cod);
		$.post("inc/produto_nome.inc.php", {
			nome : "" + nome + ""
		}, function(data) {
			if (data.length > 0) {
				if(data=="false"){
					$("input[name='nome']").attr("class", "avisoInput");
					$("input[name='verificaNome']").val("false");
					$('#checkNome').html("<center><img class='check' src='img/check-no.png'></center>");
				}else if(data=="true"){
					$("input[name='nome']").attr("class", "");
					$("input[name='verificaNome']").val("true");
					$('#checkNome').html("<center><img class='check' src='img/check-ok.png'></center>");
				}
			}
		});
	}
}
//-->