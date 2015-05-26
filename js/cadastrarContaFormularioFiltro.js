
$(function(){
	var valPar = document.getElementById('parcelas').value;

	for (var i = 1; i <= valPar; i++) {
		if (document.getElementById('tipoPga_' + i).value == "2") {
			document.getElementById('spanTipoPagamentoSub_' + i).style.display = 'inline';
		} else {
			document.getElementById('spanTipoPagamentoSub_' + i).style.display = 'none';
			document.getElementById('spanTipoPagamentoSub_' + i).value = '';
		}
	}
});
	
function showTipoPagamentoSub(tipopga, elemento) {
	
	id= elemento.id.split("_");
	
	$('#spanTipoPagamentoSub_' + id[1]).show();
	var cod = "<center><img width='30' src='img/loading.gif'></center>";
	$('#spanTipoPagamentoSub_' + id[1]).html(cod);
	$.post("inc/ajaxTipoPagamentoSub.inc.php", {
		variavel : "" + tipopga + "",
		id : "" + id[1] + ""
	}, function(data) {
		if (data.length > 0) {
			$('#spanTipoPagamentoSub_' + id[1]).html(data);
		}else{
			var cod = "<select name='tipo_pagamento_sub' id='tipoPagamentoSub_"+id[1]+"' style='display:none;'></select>";
			$('#spanTipoPagamentoSub_' + id[1]).html(cod);
		}
	});
	
}

function mudaTipoPagamentoSub(valor, identidade){
	$("#tipoPagamentoSub_"+identidade).val(valor);
}

function sugestaoContaPlano(plano){
	
	$('#contaPlanoSugestoes').show();
	var cod = "<center><img width='30' src='img/loading.gif'></center>";
	$('#contaPlanoLista').html(cod);
	$.post("inc/ajaxContaPlano.inc.php", {
		plano : "" + plano + ""
	}, function(data) {
		if (data.length > 0) {
			$('#contaPlanoLista').html(data);
		}else{
			$('#contaPlanoSugestoes').hide();
		}
	});
	
}

function preencherPlano(idPlano, plano){
	
	$("input[name='idPlanoConta']").val(idPlano);
	$("input[name='planoConta']").val(plano);
	$("input[name='cadastrarPlano']").val("false");
	lookupOff();
	
}

function planoOperacao(plano){
	
	$.post("inc/ajaxContaPlanoOperacao.inc.php", {
		plano : "" + plano + ""
	}, function(data) {
		if (data.length > 0) {
			$("input[name='cadastrarPlano']").val(data);
		}
	});
	
}

function lookupOff(){
	$(".suggestionsBox").hide();
}

function mudarOp(identidade, op){
	$("#op2_" + identidade).val(op);
}

function filtro(){

	var valida = true;
	var alerta = "";

	function testeVariavel(variavel, valor, msg){
		if(variavel.val()==valor){
			alerta += msg;
			variavel.addClass("avisoInput");
			valida = false;
		}else{
			variavel.removeClass("avisoInput");
		}
	}

	testeVariavel($("input[name='entidade']"), "","Digite o nome do Cliente/Fornecedor.\n");
	testeVariavel($("input[name='referido']"), "","Digite a referencia desta conta.\n");
	testeVariavel($("input[name='valorTotal']"), "00,00","O valor da conta não pode ser 00,00.\n");
	testeVariavel($("input[name='valorTotal']"), "","O valor da conta não pode ser 00,00.\n");
	testeVariavel($("select[name='status']"), "0","Escolha o status da conta.\n");
	testeVariavel($("select[name='formaPagamento']"), "0","Escolha a forma de pagamento da conta.\n");

	if(!valida){
		alert(alerta);
	}
	return valida;
}

