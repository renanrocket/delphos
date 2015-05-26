function display(variavel, valor) {
	if (valor == variavel) {
		return document.getElementById(variavel).style.display = "";
	} else {
		return document.getElementById(variavel).style.display = "none";
	}
}

function escolha(valor) {
	display("entidade", valor);
	display("referido", valor);
	display("doc", valor);
	display("planoConta", valor);
}

function showNome(nome, op) {

	if (op == "completarEntidade" || op == "completarReferido" || op == "completarContaPlano") {
		
		if(op == "completarEntidade"){
			var idDocumento = "sugestoes1";
		}else if(op == "completarReferido"){
			var idDocumento = "sugestoes2";
		}else if(op == "completarContaPlano"){
			var idDocumento = "sugestoes3";
		}

		if (nome.length == 0) {
			document.getElementById(idDocumento).style.display = 'none';
		} else {

			document.getElementById(idDocumento).style.display = '';
			var cod = "<center><img width='30' src='img/loading.gif'></center>";

			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 1) {
					document.getElementById(idDocumento + 'Lista').innerHTML = cod;
				} else if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById(idDocumento + 'Lista').innerHTML = xmlhttp.responseText;
				}
			}

			xmlhttp.open("GET", "inc/contaAutoCompletar.inc.php?nome=" + nome + "&op="+ op, true);
			xmlhttp.send();
		}
	} else if (op == "preencherEntidade") {

		document.getElementById('entidadeValue').value = nome;
		document.getElementById('sugestoes1').style.display = 'none';

	} else if (op == "preencherReferido") {

		document.getElementById('referidoValue').value = nome;
		document.getElementById('sugestoes2').style.display = 'none';

	} else if (op == "preencherContaPlano") {

		document.getElementById('contaPlanoValue').value = nome;
		document.getElementById('sugestoes3').style.display = 'none';

	}
}
function lookupOff(){
	$('.suggestionsBox').hide();
}
function filtro(){
	
	var valida = true;
	var alerta = "";

	if ($('#tipo').val() == 0){
		alerta += "Selecione o tipo de conta que voce deseja pesquisar.\n";
		valida = false;
		$('#tipo').attr("class", "avisoInput");
	}else{
		$('#tipo').attr("class", "");
	}
	
	if(valida){
		return true;
	}else{
		alert(alerta);
		return false;
	}
	
}