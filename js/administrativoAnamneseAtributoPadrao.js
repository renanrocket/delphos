function showTipoItemSub(valor){
	if (valor == "Seleção de itens"){
		$("#select").show();
	}else{
		$("#select").hide();
	}
}
function showEscolhas(valor){
	if(valor<3 || valor=="" || valor>99){
		if(valor>99){
			alert("O número maximo de escolhas é de 99.");
		}
		valor = 2;
		$("input[name='qtdEscolha']").val(valor);
	}
	for(var i = 1; i<=99; i++){
		if(i<=valor){
			$("#divEscolha_" + i).show();	
		}else{
			$("#divEscolha_" + i).hide();
		}
	}
}
function showAtributos(valor){
	if(valor=="1"){
		$(".ativo").show();
		$(".inativo").hide();
	}else{
		$(".ativo").hide();
		$(".inativo").show();
	}
}
function filtro(){
	var valida = true;
	var alerta = "";
	
	if($("input[name='pergunta']").val()==""){
		valida = false;
		alerta += "Você precisa digitar uma pergunta.\n";
		$("input[name='pergunta']").attr("class", "avisoInput");
	}else{
		$("input[name='pergunta']").attr("class", "");
	}
	for(var i=1; i<=$("input[name='qtdEscolha']").val(); i++){
		if($("input[name='escolha_" + i + "']").val()=="" && $("select[name='tipo_item']").val()=="Seleção de itens"){
			valida = false;
			alerta += "Você precisa digitar o valor da escolha.\n";
			$("input[name='escolha_" + i + "']").attr("class", "avisoInput");
		}else{
			$("input[name='escolha_" + i + "']").attr("class", "");
		}
	}
	
	if(alerta.length>0){
		alert(alerta);
	}
	
	return(valida);
}
