// JavaScript Document
//<!--
function reset(){
	$("input[name='ml']").val("00,00");
	$("input[name='mlpor']").val("00,00");
	$("input[name='descMaxpor']").val("00,00");
	$("input[name='descMax']").val("00,00");
}

function filtro(){
	
	alerta = "";
	valida = true;
	
	if($("select[name='marca']").val()==0){
		alerta += "Por favor selecione a marca.\n";
		$("select[name='marca']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("select[name='marca']").attr("class", "");
	}
	if($("select[name='categoria']").val()==0){
		alerta += "Por favor selecione a categoria.\n";
		$("select[name='categoria']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("select[name='categoria']").attr("class", "");
	}
	if($("input[name='verificaNome']").val()=="false" || $("input[name='nome']").val().length< 5){
		alerta += "Por favor certifique-se que digitou o nome correto.\n";
		$("input[name='nome']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='nome']").attr("class", "");
	}
	
	if(parseFloat($("input[name='valorCompra']").val().replace(",", "."))<=0){
		alerta += "O valor de compra está inválido.\n";
		$("input[name='valorCompra']").attr("class", "preco totalValor avisoInput");
		reset();
		valida = false;
	}else{
		$("input[name='valorCompra']").attr("class", "preco totalValor ");
	}
	
	var tributacao = 0;
	
	for(var i = 1; i<= $("#qtdTributacao").val(); i++){
		
		if($("#tipoValor_"+i)=="0"){
			tributacao = tributacaoReal + parseFloat($("#tributacaoValor_" + i).val().replace(",", "."));
		}else if($("#tipoValor_"+i)=="1"){
			tributacao = tributacaoPor + parseFloat($("input[name='valorCompra']").val().replace(",",".")) * parseFloat($("#tributacaoValor_" + i).val().replace(",", "."));
		}
		
		if($("#tributacao_" + i).val().length<2 && $("#qtdTributacao").val()>1){
			if(alerta.indexOf("O nome da tributação está inválido.\n")<=0){
				alerta += "O nome da tributação está inválido.\n";
			}
			$("#tributacao_" + i).attr("class", "avisoInput");
			reset();
			valida = false;
		}else{
			$("#tributacao_" + i).attr("class", "");
		}
		if(parseFloat($("#tributacaoValor_" + i).val().replace(",", "."))<=0 && $("#qtdTributacao").val()>1){
			if(alerta.indexOf("O valor da tributação está inválido.\n")<=0){
				alerta += "O valor da tributação está inválido.\n";
			}
			$("#tributacaoValor_" + i).attr("class", "preco totalValor avisoInput");
			reset();
			valida = false;
		}else{
			$("#tributacaoValor_" + i).attr("class", "preco totalValor ");
		}
	}
	
	var precoCustoTotal = tributacao + parseFloat($("input[name='valorCompra']").val().replace(",","."));
	
	if(parseFloat($("input[name='ml']").val().replace(",","."))<precoCustoTotal){
		alerta += "Valor de venda menor do que o valor de custo total (R$ "+ precoCustoTotal.toFixed(2).toString().replace(".",",") +").\n";
		valida = false;
		$("input[name='ml']").attr("class", "preco totalValor avisoInput");
	}else{
		$("input[name='ml']").attr("class", "preco totalValor");
	}
	if(parseFloat($("input[name='descMax']").val().replace(",","."))<precoCustoTotal){
		alerta += "Valor de venda com desconto menor do que o valor de custo total (R$ "+ precoCustoTotal.toFixed(2).toString().replace(".",",") +").\n";
		valida = false;
		$("input[name='descMax']").attr("class", "preco totalValor avisoInput");
	}else{
		$("input[name='descMax']").attr("class", "preco totalValor");
	}
	if(parseFloat($("input[name='descMax']").val().replace(",","."))>parseFloat($("input[name='ml']").val().replace(",","."))){
		alerta += "Valor de venda com desconto maior do que o valor de venda sem desconto.\n";
		valida = false;
		$("input[name='descMax']").attr("class", "preco totalValor avisoInput");
	}else{
		$("input[name='descMax']").attr("class", "preco totalValor");
	}
	
		
	if (valida==true){
		return true;
	}else{
		alert(alerta);
		return false;
	}
}

//-->