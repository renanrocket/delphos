function filtroMatricula(){
	var valida = true;
	var alerta = "";
	
	if($("select[name='planoAssinatura']").val()=="0" && $("input[name='op']").val()=="novo"){
		valida = false;
		alerta += "Por favor selecione o plano para está matrícula.\n";
		$("select[name='planoAssinatura']").attr("class", "avisoInput");
	}else{
		$("select[name='planoAssinatura']").attr("class", "");
	}
	if($("select[name='pgaForma']").val()=="0" && $("input[name='op']").val()=="novo"){
		valida = false;
		alerta += "Por favor selecione o plano para está matrícula.\n";
		$("select[name='pgaForma']").attr("class", "avisoInput");
	}else{
		$("select[name='pgaForma']").attr("class", "");
	}
	if($("select[name='pgaTipo']").val()=="0" && $("input[name='op']").val()=="novo"){
		valida = false;
		alerta += "Por favor selecione o plano para está matrícula.\n";
		$("select[name='pgaTipo']").attr("class", "avisoInput");
	}else{
		$("select[name='pgaTipo']").attr("class", "");
	}
	
	if(!valida){
		alert(alerta);
	}
	return valida;
}

function showPlano(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#tdPlano').html(cod);
		$.get("inc/cadastrarMatricula.inc.php?op=plano", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				$('#tdPlano').html(data);
			}
		});
	}
}
function showDataTermino(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#tdDataTermino').html(cod);
		$.get("inc/cadastrarMatricula.inc.php?op=dataTermino", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				
				$('#tdDataTermino').html(data);
			}
		});
	}
}

function showPlanoValor(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$("input[name='valorPlano']").attr("style", "width:60%;");
		$('#divPlanoPreco').show();
		$('#divPlanoPreco').html(cod);
		$.get("inc/cadastrarMatricula.inc.php?op=valorPlano", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				$("input[name='valorPlano']").val(data);
				$("input[name='valorPlano']").attr("style", "width:80%;");
				$('#divPlanoPreco').hide();
				$('#visualizarHistorico').attr("style", "position:absolute; left:97%;"); //corrigindo o bug do navegador do renan
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

function maisPlano(valor){
	
	if(valor=="true"){
		$("input[name='novoPlano']").val("true");
		$(".trPlano").show();
		$("#thImg").html("<a href='#thImg' title='Deletar o novo Plano / Assinatura.' onclick='maisPlano(\"false\");'><img width='30' src='img/menos.png'></a>");
	}else{
		$("input[name='novoPlano']").val("false");
		$(".trPlano").hide();
		$("#thImg").html("<a href='#thImg' title='Adicionar um novo Plano / Assinatura.' onclick='maisPlano(\"true\");'><img width='30' src='img/mais.png'></a>");
	}
	
}

$('input[name="dataInicio"]').live('blur', function(){
	$("#tdDataTermino").html($('input[name="dataInicio"]').val());
});
