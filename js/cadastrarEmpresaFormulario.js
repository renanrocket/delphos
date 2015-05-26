// JavaScript Document

//<!--
function logoInput(){
	
	document.getElementById('logo').innerHTML = "Logomarca<br><input type='file' name='imgsrc' id='imgsrc' accept='image/*'><br><span style='font-size:11px;'>Recomenda-se imagem do tamaho de até 182px X 100px</span>";
		
}


function ajax(valor, op) {
	

	if (op == "cidades") {
		if (valor == 0) {
			// Hide the suggestion box.
			$('#tdCidades').html("Cidade<br><select name='cidade' id='cidade' disabled></select>");
		} else {
			var cod = "<center><img width='30' src='img/loading.gif'></center>";
			$('#tdCidades').html(cod);
			$.post("inc/ajaxCidadeEstadoCep.inc.php", {
				variavel : "" + valor + "",
				op : "" + op + ""
			}, function(data) {
				if (data.length > 0) {
					$('#tdCidades').html(data);
				}
			});
		}
	} else if (op == "cep") {
		if (valor == 0) {
			// Hide the suggestion box.
			$('#tdCep').html("CEP<br><input type='text' name='cep' value='' onKeyDown='Mascara(this,Cep);' onKeyPress='Mascara(this,Cep);' onKeyUp='Mascara(this,Cep);'  maxlength='14'>");
		} else {
			var cod = "<center><img width='30' src='img/loading.gif'></center>";
			$('#tdCep').html(cod);
			$.post("inc/ajaxCidadeEstadoCep.inc.php", {
				variavel : "" + valor + "",
				op : "" + op + ""
			}, function(data) {
				if (data.length > 0) {
					$('#tdCep').html(data);
				}
			});
		}
	}

}



//-->