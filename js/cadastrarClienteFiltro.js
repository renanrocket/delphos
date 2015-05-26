function lookupOff() {
	$('.suggestionsBox').hide();
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

function clienteShow(stringCliente, tipoBusca) {

	if (stringCliente.value.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$('#suggestions').show();
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#autoSuggestionsList').html(cod);
		$.post("inc/cliente_fornecedor.inc.php", {
			search : "" + stringCliente.value + "",
			tipo : "" + tipoBusca + ""
		}, function(data) {
			if (data.length > 0) {
				$('#autoSuggestionsList').html(data);
				setTimeout("$('#suggestions').hide();", 10000);
			}
		});
	}
}

function preencherCliente(id, tipo, nome, razao_social, cpf_cnpj, rg_ie, data_nascimento, email, fone1, fone2, endereco, numero, bairro, cidade, CIDADE, estado, cep, ref) {
	
	//chamada da funcao para setar o tipo de pessoa no orcamento
	tipoPessoaDoc(tipo);

	if (tipo == "f") {
		var Tipo = "Fisica";
		var script = "<input type='radio' value='f' name='tipo' checked='yes' onclick=\"tipoPessoaDoc(this.value);\">Pessoa Física ";
		script += "<input type='radio' value='j' name='tipo' onclick=\"tipoPessoaDoc(this.value);\">Pessoa Jurídica";
		$("#tipo_pessoa").html(script);
	} else {
		var Tipo = "Juridica";
		$('input[name$="razao_social"]').val(razao_social);
		var script = "<input type='radio' value='f' name='tipo' onclick=\"tipoPessoaDoc(this.value);\">Pessoa Física ";
		script += "<input type='radio' value='j' name='tipo' checked='yes' onclick=\"tipoPessoaDoc(this.value);\">Pessoa Jurídica";
		$("#tipo_pessoa").html(script);	
	}

	$('input[name$="contato"]').val(nome);
	$('input[name$="telefoneC"]').val(fone1);
	
	$('input[name$="id_cliente_fornecedor"]').val(id);
	$('input[name$="nome"]').val(nome);
	$('input[name$="doc1"]').val(cpf_cnpj);
	$('input[name$="doc2"]').val(rg_ie);
	$('input[name$="data"]').val(data_nascimento);
	$('input[name$="email"]').val(email);
	$('input[name$="telefone1"]').val(fone1);
	$('input[name$="telefone2"]').val(fone2);
	$('#endereco').html("Endereço<br><textarea name=\"endereco\" style=\"height:90%;\">" + endereco + "</textarea>");
	$('input[name$="numero"]').val(numero);
	$('input[name$="bairro"]').val(bairro);
	$('#tdCidades').html("Cidade<br><select name='cidade' id='cidades'><option value='" + cidade + "'>" + CIDADE + "</option></select>");
	document.getElementById('estados').options[estado].selected = "yes";
	$('input[name$="cep"]').val(cep);
	$('input[name$="referencia"]').val(ref);


	if (id == "") {
		$('#obsCadastro').val("");
		//$('#obsCadastro').html("<textarea name=\"observacoes\"></textarea>");
	} else {

		$.post("inc/cliente_fornecedor.inc.php", {
			search : "" + id + "",
			tipo : "obs"
		}, function(data) {
			if (data.length > 0) {	
				$('#obsCadastro').html(data);
				//$('#obsCadastro').html("<textarea name=\"observacoes\">" + data + "</textarea>");
			}
		});
	}
	
	
	mudaForm();
	$("input[name='cadastrarCliente']").val("false");// essa linha de codigo deve estar debaixo da funcao mudaForm();
	lookupOff();
}
/*
function tipoPessoaDoc(valor) {
	if (valor == "j") {
		document.getElementById('nome').innerHTML = "Nome da Empresa<br><input type='text' name='nome' style='width:99%;'>";
		document.getElementById('doc1').innerHTML = "CNPJ<br><input type='text' name='doc1' style='width:99%;' maxlength='18' onKeyDown='Mascara(this,Cnpj);' onKeyPress='Mascara(this,Cnpj);' onKeyUp='Mascara(this,Cnpj);'>";
		document.getElementById('doc2').innerHTML = "IE<br><input type='text' name='doc2' style='width:99%;' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>";
		document.getElementById('datadoc').innerHTML = "Fundação";
		document.getElementById('razaoSocial').style.display = "";
	} else {
		document.getElementById('nome').innerHTML = "nome<br><input type='text' name='nome'style='width:99%;' >";
		document.getElementById('doc1').innerHTML = "CPF<br><input type='text' name='doc1' style='width:99%;' maxlength='14' onKeyDown='Mascara(this,Cpf);' onKeyPress='Mascara(this,Cpf);' onKeyUp='Mascara(this,Cpf);'>";
		document.getElementById('doc2').innerHTML = "RG<br><input type='text' name='doc2' style='width:99%;' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>";
		document.getElementById('datadoc').innerHTML = "Nascimento";
		document.getElementById('razaoSocial').style.display = "none";
	}
}
*/
function tipoPessoaDoc(valor) {
	if (valor == "j") {
		$("#cliente").html("<div>Nome da Empresa<br><input type='text' name='nome' onBlur='cloneInput(this.value, \"contato\");'></div><div>Razão Social<br><input type='text' name='razao_social'></div>");
		var cod = "CNPJ<br><input type='text' name='doc1' maxlength='18' onKeyDown='Mascara(this,Cnpj);' onKeyPress='Mascara(this,Cnpj);' onKeyUp='Mascara(this,Cnpj);' onBlur='verificaDoc(\"j\");'>";
		cod += "<span id='checkDoc1' style='display:none;'></span>";
		cod += "<input type='hidden' name='verificaDoc1' value='true'>";
		$("#doc1").html(cod); 
		$("#doc2").html("Inscrição Estadual<br><input type='text' name='doc2' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>");
		$("#data").html("Data de Fundação");
	} else {
		$("#cliente").html("Cliente<br><input type='text' name='nome' onBlur='cloneInput(this.value, \"contato\");'>");
		var cod = "CPF<br><input type='text' name='doc1' maxlength='14' onKeyDown='Mascara(this,Cpf);' onKeyPress='Mascara(this,Cpf);' onKeyUp='Mascara(this,Cpf);' onBlur='verificaDoc(\"f\");'>";
		cod += "<span id='checkDoc1' style='display:none;'></span>";
		cod += "<input type='hidden' name='verificaDoc1' value='true'>";
		$("#doc1").html(cod);
		$("#doc2").html("RG<br><input type='text' name='doc2' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>");
		$("#data").html("Data de Nascimento");
	}
}

function mudaForm(){
	$("input[name='cadastrarCliente']").val("true");
	$("span[class='itensForm1']").attr("style", "display:none;");
	$("tr[class='itensForm1']").attr("style", "display:none;");
	$("span[class='itensForm2']").attr("style", "");
	$("tr[class='itensForm2']").attr("style", "");
	$("span[class='itensForm3']").attr("style", "");
	$("tr[class='itensForm3']").attr("style", "");
}

function cloneInput(valor, nomeInput){
	$("input[name='"+ nomeInput +"']").val(valor);
}

function verificaDoc(tipo){
	var doc = $("input[name='doc1']").val();
	var idCliente = $("input[name='id_cliente_fornecedor']").val();
	if (doc.length != 0) {
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#checkDoc1').show();
		$("input[name='doc1']").attr("style", "width:75%");
		$('#checkDoc1').attr("style", "display:inline-block;");
		$('#checkDoc1').html(cod);
		$.post("inc/cliente_fornecedorDoc1.inc.php", {
			doc : "" + doc + "",
			tipo : "" + tipo + "",
			id : "" + idCliente + ""
		}, function(data) {
			if (data.length > 0) {
				if(data=="true"){
					$("input[name='doc1']").attr("class", "");
					$("input[name='verificaDoc1']").val("true");
					$('#checkDoc1').show();
					$('#checkDoc1').html("<center><img class='check' src='img/check-ok.png'></center>");
				}else{
					$("input[name='doc1']").attr("class", "avisoInput");
					$("input[name='verificaDoc1']").val("false");
					$('#checkDoc1').show();
					$('#checkDoc1').html("<center><img class='check' src='img/check-no.png'></center>");
				}
			}
		});
	}
}

function filtroClienteFornecedor(){
	var alerta = "";
	var valida = true;
	var cadastrar = ($("input[name='cadastrarCliente']").val()== "true" ? true : false);
	if($("input[name='contato']")!= undefined){
		if ($("input[name='contato']").val().length == 0 && !cadastrar) {
			alerta += "Preencha o campo Contato.\n";
			$("input[name='contato']").attr("class", "avisoInput");
			valida = false;
		} else {
			$("input[name='contato']").attr("class", "");
		}
		if ($("input[name='telefoneC']").val().length < 14 && !cadastrar) {
			alerta += "Preencha o campo Telefone do contato.\n";
			$("input[name='telefoneC']").attr("class", "avisoInput");
			valida = false;
		} else {
			$("input[name='telefoneC']").attr("class", "");
		}
	}
	
	//caso queira cadastrar o nome
	if($("input[name='nome']").val().length==0 && cadastrar){
		alerta += "Preencha o campo nome.\n";
		$("input[name='nome']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='nome']").attr("class", "");
	}
	
	if($("input[name='razao_social']") != undefined && $("input[name='tipo']").val()=="j" && cadastrar){
		if($("input[name='razao_social]").val().length==0){
			alerta += "Preencha o campo nome.\n";
			$("input[name='razao_social']").attr("class", "avisoInput");
			valida = false;
		}
	} else {
		$("input[name='razao_social']").attr("class", "");
	}
	
	/*if($("input[name='doc1']").val().length==0 && cadastrar){
		alerta += "Preencha o campo Documento 1.\n";
		$("input[name='doc1']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='doc1']").attr("class", "");
	}
	
	if($("input[name='verificaDoc1']").val()=="false" && cadastrar){
		alerta += "Verifique o Documento 1.\n";
		$("input[name='verificaDoc1']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='verificaDoc1']").attr("class", "");
	}*/
	
	if(($("input[name='email']").val().indexOf("@")<0 || $("input[name='email']").val().indexOf(".")<0) && $("input[name='email']").val().length>0 && cadastrar){
		alerta += "Verifique se o e-mail foi digitado corretamente.\n";
		$("input[name='email']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='email']").attr("class", "");
	}
	
	if($("input[name='endereco']").val().length<5 && cadastrar){
		alerta += "Preencha o campo Endereço.\n";
		$("textarea[name='endereco']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("textarea[name='endereco']").attr("class", "");
	}
	
	if($("input[name='telefone1']").val().length < 14 && cadastrar){
		alerta += "Preencha o campo Telefone 1.\n";
		$("input[name='telefone1']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='telefone1']").attr("class", "");
	}
	
	if($("#estados").val()=="0" && cadastrar){
		alerta += "Selecione o Estado.\n";
		$("#estados").attr("class", "avisoInput");
		valida = false;
	} else {
		$("#estados").attr("class", "");
	}
	
	if($("#cidades").val()=="0" && cadastrar){
		alerta += "Selecione a Cidade.\n";
		$("#cidades").attr("class", "avisoInput");
		valida = false;
	} else {
		$("#cidades").attr("class", "");
	}
	
	if($("input[name='data']").val().length>0 && $("input[name='data']").val().length<10 && cadastrar){
		alerta += "Verifique se a data foi digitada corretamente.\n";
		$("input[name='data']").attr("class", "avisoInput");
		valida = false;
	} else {
		$("input[name='data']").attr("class", "");
	}
	
	
	if(alerta.length>0){
		alert(alerta);
	}
	return valida;
}









