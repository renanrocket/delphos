//funcao para o auto complite do item
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

function buscarServico(valor) {
	if (valor.length == 0) {
		$("#sugestoes").hide();
	} else {
		$("#sugestoes").show();
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$("#sugestoesLista").html(cod);
		$.post("inc/ordem_servico.inc.php", {
			queryString : "" + valor + ""
		}, function(data) {
			if (data.length > 0) {
				$('#sugestoesLista').html(data);
				//setTimeout("$('.suggestionsBox').hide();", 10000);
			}
		});
	}
}


//verificar qual tipo do item e se caso for serviço preenche com o preço de acordo com sua quantidade
function calculaPrecoQuantidade() {

	var cod = "<img width='30' src='img/loading.gif'>";
	$("#spanSubTotal").html(cod);
	$("input[name='subTotal']").attr("style", "width:30%;");
	$.post("inc/cadastrarOrdemServicoPrecoServico.inc.php", {
		idServico : "" + $("input[name='idServico']").val() + "",
		qtd : "" + $("input[name='quantidade']").val() + ""
	}, function(data) {
		if (data.length > 0) {
			$("#spanSubTotal").html("");
			$("input[name='subTotal']").attr("style", "width:75%;");
			$("input[name='subTotal']").val(data);
			calcularTotal();
		}
	});

}

function fechar() {
	$('.suggestionsBox').hide();
}

function preencher(ID, NOME, PRECO) {
	$("input[name='idServico']").val(ID);
	$("input[name='servico']").val(NOME);
	$("input[name='quantidade']").val(1);
	Preco = PRECO;
	$("input[name='subTotal']").val((parseFloat(Preco.replace(",", ".")).toFixed(2)).toString().replace(".", ","));
	setTimeout("$('#sugestoesLista').hide();", 200);
	calcularTotal();
	fechar();
}

//determina todos os valores da ordem de serviço
function calcularTotal() {
	if($("input[name='subTotal']").val().length<1){
		$("input[name='subTotal']").val("00,00");
	}
	if($("input[name='quantidade']").val().length<1){
		$("input[name='quantidade']").val("1");
	}
	var quant = parseFloat($("input[name='quantidade']").val().replace(",","."));
	var subTotal = parseFloat($("input[name='subTotal']").val().replace(",","."));

	
	total = (subTotal * quant).toFixed(2);
	$("input[name='servicoTotal']").val(total.toString().replace(".", ","));


}

function historico(){
	hist = $("input[name='historicoVisualizacao']").val();
	if(hist=="0"){
		$("input[name='historicoVisualizacao']").val("1");
		$(".historico").show();
	}else{
		$("input[name='historicoVisualizacao']").val("0");
		$(".historico").hide();
	}
}

function filtroOrdemServico(){
	valida = true;
	alerta = "";
	
	if($("input[name='dataPrevisao']").val().length<10){
		alerta+= "Por favor verifique a data de previsão de entrega.\n";
		$("input[name='dataPrevisao']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='dataPrevisao']").attr("class", "");
	}
	
	if($("input[name='dataPrevisaoHora']").val().length<5){
		alerta+= "Por favor verifique a hora de previsão de entrega.\n";
		$("input[name='dataPrevisaoHora']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='dataPrevisaoHora']").attr("class", "");
	}
	var dataHora = $("input[name='dataPrevisaoHora']").val().split(":");
	if(parseInt(dataHora[0])>24 || parseInt(dataHora[1])>59){
		alerta+= "Por favor verifique a hora de previsão de entrega.\n";
		$("input[name='dataPrevisaoHora']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='dataPrevisaoHora']").attr("class", "");
	}
	
	/*
	qtdAtributos = parseInt($("input[name='qtdAtributos']").val());
	for(var i = 0; i<qtdAtributos; i++){
		if($("#obrigatoriedade_"+ i).val()==1 && $("#valorAtributo_"+i).val().length<1){
			alerta+= "O atributo "+$("#nome_"+i).val()+" é obrigatório.\n";
			$("#valorAtributo_"+i).attr("class", "avisoInput");
			valida= false;
		}else{
			$("#valorAtributo_"+i).attr("class", "");
		}
	}
	*/
	
	if($("input[name='idServico']").val().length<1){
		alerta+= "Por favor, selecione um serviço.\n";
		$("input[name='servico']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='servico']").attr("class", "");
	}
	if($("input[name='quantidade']").val().length<1){
		alerta+= "Quantidade inválida.\n";
		$("input[name='quantidade']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='quantidade']").attr("class", "");
	}
	if($("select[name='pgaForma']").val()<1){
		alerta+= "Forma de pagamento inválida.\n";
		$("select[name='pgaForma']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("select[name='pgaForma']").attr("class", "");
	}
	if($("select[name='pgaTipo']").val()<1){
		alerta+= "Tipo de pagamento inválida.\n";
		$("select[name='pgaTipo']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("select[name='pgaTipo']").attr("class", "");
	}
	
	if(!valida){
		alert(alerta);
	}
	
	return valida;
}
