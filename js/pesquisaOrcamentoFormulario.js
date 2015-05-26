function showPesquisa(tipo){
	
	document.getElementById('id').style.display = "none";
	document.getElementById('cliente').style.display = "none";
	document.getElementById('produto').style.display = "none";
	document.getElementById('status').style.display = "none";
	document.getElementById('usuario').style.display = "none";
	document.getElementById('data').style.display = "none";
	
	
	document.getElementById(tipo).style.display = "";
	
	if(tipo=="cliente" || tipo=="produto" || tipo=="status" || tipo=="usuario"){
		document.getElementById('data').style.display = "";	
	}
	
}

function filtro(){
	
	var valida = true;
	var info = "";
	
	with(document.formulario){
		
		if(data1.value.length>0 && data1.value.length<10){
			valida = false;
			info += "Data 1 esta invalida.\n";
		}
		if(data2.value.length>0 && data2.value.length<10){
			valida = false;
			info += "Data 2 esta invalida.\n";
		}
		if(status.value==0){
			valida = false;
			info += "Por favor selecione o status do orcamento.\n";
		}
					
	}
	
	if(info.length>0){
		alert(info);
	}
	return valida;
	
		

}
