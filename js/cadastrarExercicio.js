function showMusculo(valor){
	if(valor<1){
		$("input[name='qtdMusculo']").val(1);
		valor = 1;
	}
	
	for(var i=1; i<=99; i++){
		if(i<=valor){
			$("#musculo_"+i).show();
		}else{
			$("#musculo_"+i).hide();
		}
	}
}

function filtro(){
	
	var valida = true;
	var alerta = "";
	
	if($("input[name='nome']").val()==""){
		alerta += "Por favor preencha o nome do exercício.\n";
		$("input[name='nome']").attr("class", "avisoInput");
		valida = false;
	}else{
		$("input[name='nome']").attr("class", "");
	}
	
	for(var i=1; i<=$("input[name='qtdMusculo']").val(); i++){
		for(var j=1; j<=$("input[name='qtdMusculo']").val(); j++){
			if($("#musculo_"+i).val() == $("#musculo_"+j).val() && j!=i){
				if(alerta.indexOf("Existem musculos duplicados.\n")<0){
					alerta += "Existem musculos duplicados.\n";
				}
				$("#musculo_"+i).attr("class", "avisoInput");
				valida = false;
			}else{
				$("#musculo_"+i).attr("class", "");
			}
		}
	}
	
	
	if(!valida){
		alert(alerta);
	}
	
	return valida;
	
	
}
