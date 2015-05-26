//função de completar produto


function produtoShow(stringProduto) {
	
	if(stringProduto.value.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$('#suggestions').show();
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#autoSuggestionsList').html(cod);
		$.post("inc/pdv_itens.inc.php", {
			queryString: stringProduto.value,
			op: "produto"
		}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
		
	}
} // lookup

function preencher(NOME, COD) {
	$('#produto').val(NOME);
	$('#cod').val(COD);
	lookupOff();
	$('#form_pdv').submit();
}

function lookupOff(){
	$('.suggestionsBox').hide();
}

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

function salvarNome(nome, tipo, identidade){
	if(nome.length>0){
		if(tipo=="nome"){
			$("#checkNome").html("<img class='check' src='img/loading.gif'>");
		}else{
			$("#checkObservacoes").html("<img class='check' src='img/loading.gif'>");
		}
		$.post("inc/ajaxPdvNome.inc.php", {
			nome : "" + nome + "",
			tipo : "" + tipo + "",
			identidade : "" + identidade + ""
		}, function(data) {
			if(tipo=="nome"){
				if(data){
					$("#checkNome").html("<img class='check' src='img/check-no.png'>" + data);	
					$("input[name='pdvNome']").attr("class", "avisoInput");
					$("input[name='pdvNome']").val("");
				}else{
					$("input[name='pdvNome']").attr("class", "");
					$("#checkNome").html("<img class='check' src='img/check-ok.png'>");	
				}
			}else{
				$("#checkObservacoes").html("<img class='check' src='img/check-ok.png'>");	
			}
			
		});
	}
}

