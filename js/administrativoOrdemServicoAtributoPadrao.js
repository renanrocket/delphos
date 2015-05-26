//parametro Seleção de itens = id atributoSub show subItens
function showSubItens(valor){
	
	if(valor=="Seleção de itens"){
		$('#subItens').show();
		$("#qtdItem").val(2);
	}else{
		$('#subItens').hide();
		$("#qtdItem").val(0);
	}
}

//parametro ativo=id ativo show ou inativo = id inativo show 
function mudaAtributos(valor){
	if(valor=="Ativos"){
		$('.ativo').show();
		$('.inativo').hide();
	}else if(valor=="Inativos"){
		$('.ativo').hide();
		$('.inativo').show();
	}
	
}

function mudaQtdItens(qtd){
	if(qtd<3){
		$('#qtdItem').val(2);
		qtd=2;
	}
	
	for(var i = 1; i <= 99; i++){
		if(i <= qtd){
			$('#selecao_'+ i).show();
		}else{
			$('#selecao_'+ i).hide();
		}
	}
	
}
