//<!--

function filtro() {
	with (document.formularioUsuario) {
		var alerta = "";
		var confirma = "";
		var valida = true;

		function existir(variavel, texto) {
			if (variavel.value == "" || (variavel.value.match(/^\s+$/)) || variavel.value == 0) {
				alerta += texto;
				variavel.className = "avisoInput";
				valida = false;
			} else {
				variavel.className = "";
			}
		}

		function existirC(variavelC, textoC) {
			if (variavelC.value == "" || (variavelC.value.match(/^\s+$/)) || variavelC.value == 0) {
				confirma += textoC;
				variavelC.className = "avisoInput";
				valida = false;
			} else {
				variavelC.className = "";
			}
		}

		function existirTamanho(variavelT, tamanho, textoT) {
			if (variavelT.value.length > 0 && variavelT.value.length < tamanho) {
				confirma += textoT;
				variavelT.className = "avisoInput";
				valida = false;
			} else {
				variavelT.className = "";
			}
		}

		//existir(razao_social, "Preencha o campo Razao Social.\n");
		existir(nome, "Por favor insira o nome completo do usuario.\n");
		existir(login, "O usuario precisa de um login.\n");
		existir(funcao, "Marque a funcao do usuario.\n");
		existir(email, "Por favor informe um e-mail.\n");
		
		existirC(admissao, "Por favor informe a data de admissao.\n");
		existirC(telefone1, "Deseja salvar o usuario sem informar pelo menos um telefone de contato?\n");
		existirC(endereco, "Deseja salvar o usuario sem informar o endereco do usuario?\n");
		existirC(estados, "Deseja salvar o usuario sem informar o estado?\n");
		existirC(salario, "Deseja salvar o usuario sem determinar o salario base?\n");
		existirC(rg, "Deseja salvar o usuario informar a identidade RG?\n");
		existirC(cpf, "Deseja salvar o usuario informar o CPF?\n");

		existirTamanho(telefone1, 14, "Verifique se o telefone 1 esta correto.\n");
		existirTamanho(telefone2, 14, "Verifique se o telefone 2 esta correto.\n");
		existirTamanho(admissao, 10, "Verifique a data de admissao (DD/MM/AAAA).\n");
		existirTamanho(nascimento, 10, "Verifique a data de nascimento (DD/MM/AAAA).\n");
		existirTamanho(cpf, 10, "Verifique o CPF esta correto (DD/MM/AAAA).\n");


		var checked = false;
		var form = document.formularioUsuario;

		for ( i = 19; i < form.length; i++) {
			if (form[i].checked) {
				checked = true;
			}
		}
		if (!checked) {
			alerta += "É obrigatório selecionar no mínimo uma ferramenta!\n";
			valida = false;
		}
		
		
		if (valida == true) {
			return true;
		} else {
			if (alerta != "" && confirma == "") {
				alert(alerta);
				return false;
			} else if (alerta != "" && confirma != "") {
				alert(alerta);
				alert(confirma);
				return false;
			} else if (alerta == "" && confirma != "") {
				return confirm(confirma);
			}
		}
	}
}

function cadastrar_pesquisar(){
	if($("#cadastro").is(":checked")){
		$('input[name="ferramentaCP[]"]').attr("checked",true);
	}else{
		$('input[name="ferramentaCP[]"]').attr("checked",false);
	}
}

/*
function pesquisar(){
	if($("#pesquisa").is(":checked")){
		$('input[name$="ferramentaP[]"]').attr("checked",true);
	}else{
		$('input[name$="ferramentaP[]"]').attr("checked",false);
	}
}
*/

function relatorio(){
	if($("#relatar").is(":checked")){
		$('input[name="ferramentaR[]"]').attr("checked",true);
	}else{
		$('input[name="ferramentaR[]"]').attr("checked",false);
	}
}

function recursosHumanos(){
	if($("#rh").is(":checked")){
		$('input[name="ferramentaRH[]"]').attr("checked",true);
	}else{
		$('input[name="ferramentaRH[]"]').attr("checked",false);
	}
}

function admin(){
	if($("#administrar").is(":checked")){
		$('input[name="ferramentaA[]"]').attr("checked",true);
	}else{
		$('input[name="ferramentaA[]"]').attr("checked",false);
	}
}


function ajax(valor, op) {
	if (op == "cidades") {
		if (valor == 0) {
			// Hide the suggestion box.
			$('#tdCidades').html("Cidade*<br><select name='cidades' id='cidades' disabled></select>");
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

function mudaAss(){
	var ass = $("input[name='ass_digital']").val();
	if(ass=="1"){
		$("input[name='assDigital']").attr("checked", false);
		$("input[name='ass_digital']").val(0);
	}else{
		$("input[name='assDigital']").attr("checked", true);
		$("input[name='ass_digital']").val(1);
	}
}

//-->