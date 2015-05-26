// JavaScript Document
//funcao para corrigir problema de voltar historico navegador e a quantidade produto ser diferente de 1
window.onload = function() {
	if($('input[name$="op"]').val()=="novo"){
		$('#qtdProd').val(1);
	}
};
//funcao para o auto complite do produto
	function lookup(input, op) {
		//capturando o id da input
		var id = input.id.split("_");
		
		//op == false para procurar string com o nome do produto
		//op == true para procurar string com o id do produto
		
		if(input.value.length == 0) {
			// Hide the suggestion box.
			$('#suggestions_'+id[1]).hide();
		} else {
			$('#suggestions_'+id[1]).show();
			var cod = "<center><img width='30' src='img/loading.gif'></center>";
			$('#autoSuggestionsList_'+id[1]).html(cod);
			$.post("inc/orcamento_produtos.inc.php", {queryString: ""+input.value+"", id: ""+id[1]+"", op: ""+op+""}, function(data){
				if(data.length >0) {
					$('#suggestions_'+id[1]).show();
					$('#autoSuggestionsList_'+id[1]).html(data);
					//setTimeout("$('.suggestionsBox').hide();", 10000);
				}
			});
		}
	} // lookup
	
	function ajax(valor, op) {

		if (op == "cidades") {
			if (valor == 0) {
				// Hide the suggestion box.
				$('#tdCidades').html("Cidade<br><select name='cidades' id='cidades' disabled></select>");
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
	
	function ajaxTipoPagamentoSub(valor, tipo_pagamento_sub) {
		$('#pagamentoTipoSub').hide();
		var cod = "<center><img width='30' src='img/loading.gif'></center>";
		$('#pagamentoTIpoSub').html(cod);
		$.post("inc/ajaxTipoPagamentoSub.inc.php", {
			variavel : "" + valor + "",
			tipoPagamentoSub: "" + tipo_pagamento_sub + ""
		}, function(data) {
			if (data.length > 0) {
				$('#pagamentoTipoSub').show();
				$('#pagamentoTipoSub').html(data);
			}
		});
	}
	
	function clienteShow(stringCliente) {
		
		if(stringCliente.value.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$('#suggestions').show();
			var cod = "<center><img width='30' src='img/loading.gif'></center>";
			$('#autoSuggestionsList').html(cod);
			$.post("inc/orcamento_clientes.inc.php", {search: ""+stringCliente.value+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
					setTimeout("$('#suggestions').hide();", 10000);
				}
			});
		}
	} // lookup
	
	function preencherCliente(id, tipo, nome, razao_social, cpf_cnpj, rg_ie, data_nascimento, email, fone1, fone2, endereco, numero, bairro, cidade, CIDADE, estado, cep){
		
		//chamada da funcao para setar o tipo de pessoa no orcamento
		tipoPessoaDoc(tipo);
		
		if(tipo=="f"){
			var Tipo = "Fisica";
		}else{
			var Tipo = "Juridica";
			$('input[name$="razao_social"]').val(razao_social);
		}
		
		
		$('#tipo_pessoa').html("<input type='hidden' name='tipoPessoa' value='" + tipo + "'> Or&ccedil;amento para: Pessoa " + Tipo);
		$('input[name$="contato"]').val(nome);
		$('input[name$="telefoneC"]').val(fone1);
		$('input[name$="cliente"]').val(nome);
		$('input[name$="id_cliente"]').val(id);
		$('input[name$="doc1"]').val(cpf_cnpj);
		$('input[name$="email"]').val(email);
		$('input[name$="doc2"]').val(rg_ie);
		$('#endereco').html("Endereço<br><textarea name='endereco' style='height:90%;'>"+ endereco +"</textarea>");
		$('input[name$="telefone1"]').val(fone1);
		$('input[name$="telefone2"]').val(fone2);
		$('input[name$="bairro"]').val(bairro);
		$('input[name$="numero"]').val(numero);
		$('input[name$="data"]').val(data_nascimento);
		document.getElementById('estados').options[estado].selected = "true";
		$('#tdCidades').html("Cidade<br><select name='cidades' id='cidades'><option value='" + cidade + "'>"+ CIDADE +"</option></select>");
		$('input[name$="cep"]').val(cep);
		
		
		lookupOff();
	}
	
	function turnZero(variavel){
		if(variavel==NULL){
			variavel= "";
		}
		return variavel;
	}
	
	function lookupOff(){
		$('.suggestionsBox').hide();
	}
	
	function preencher(NOME, ID, PRECO, INPUTID, COD, NOME2) {
		$('#id_'+INPUTID).val(ID);
		$('#produto_'+INPUTID).val(NOME);
		$('#subTotal_'+INPUTID).val(PRECO);
		setTimeout("$('#suggestions_"+INPUTID+"').hide();", 200);
	}
	
	
	//funcao para adicionar ou remover item de produto
	$(function () {
		function removeCampo() {
			$(".removerCampo").unbind("click");
			$(".removerCampo").bind("click", function () {
				i=0;
				$(".produtos tr.campoProduto").each(function () {
					i++;
				});
				if (i>1) {
					$(this).parent().parent().remove();
					document.getElementById('qtdProd').value = parseInt(document.getElementById('qtdProd').value) - 1;
				}
			});
		}
		removeCampo();
		$(".adicionarCampo").click(function () {
			novoCampo = $(".produtos tr.campoProduto:first").clone();
			novoCampo.find("input").val("");
			
			//verifica se o infame do usuario apaga um campo q n eh o ultimo e corrige o erro causado pelo usuario
			$('#qtdProd').val(parseInt($('#qtdProd').val()) + 1);
			var qtdProduto = $('#qtdProd').val();
			while($('#id_' + qtdProduto).val()!= undefined){
				qtdProduto = parseInt(qtdProduto) + 1;
			}
			
			//editando id's das inputs
			novoCampo.find("input[name='id[]']").attr("id","id_"+qtdProduto);
			novoCampo.find("input[name='produto[]']").attr("id","produto_"+qtdProduto);
			novoCampo.find("input[name='quantidade[]']").attr("id","quantidade_"+qtdProduto);
			novoCampo.find("input[name='subTotal[]']").attr("id","subTotal_"+qtdProduto);
			novoCampo.find("input[name='itemTotal[]']").attr("id","itemTotal_"+qtdProduto);
			novoCampo.find("div[class='suggestionsBox']").attr("id","suggestions_"+qtdProduto);
				novoCampo.find("input[id='deletar']").attr("value","X");
			novoCampo.find("div[class='suggestionList']").attr("id","autoSuggestionsList_"+qtdProduto);
            novoCampo.insertAfter(".produtos tr.campoProduto:last");
			
			removeCampo();
		});
	});
	
	//determina o preco total do item ao alterar o valor da quantidade
	function precoTotalItem(quant){
		var id = quant.id.split("_");
		var quantidade = document.getElementById('quantidade_'+id[1]).value;
		if (quantidade.length>0){
			
			a = parseInt(quantidade);
			b = parseFloat(document.getElementById('subTotal_'+id[1]).value);
			var total = (a*b).toFixed(2);
			if (isNaN(total)==false){
				$('#itemTotal_'+id[1]).val(total);	
			}
		}
	}
	
	//determina todos os valores do orçamento
	$(function(){
		$('#calcular').click(function(){
			var cont = $('#qtdProd').val();
			var subT = 0;
			var total = 0;
			var quant;
			var subTotal;
			
			for (var i =1; i<=cont; i++){
				if ($('#subTotal_'+i).val()!=undefined){
					quant = parseInt($('#quantidade_'+i).val());
					subTotal = parseFloat($('#subTotal_'+i).val());					
					subT += parseFloat($('#subTotal_'+i).val());
					if (isNaN(quant*subT)==false){
						$('#itemTotal_'+i).val((quant*subTotal).toFixed(2));	
					}
					total += parseFloat($('#itemTotal_'+i).val());
				}
			}
			if (isNaN(subT)==false){
				$('input[name="totalSubTotal"]').val(subT.toFixed(2));
			}
			if (isNaN(total)==false){
				$('input[name="totalItemTotal"]').val(total.toFixed(2));
			}
			
		});
	});
	
	function showParcela(valor){
		if (valor=="2"){
			document.getElementById('parcela').style.display = '';
		}else{
			document.getElementById('parcela').style.display = 'none';
			document.getElementById('parcelaSelect').value = '';
		}
	}

function tipoPessoaDoc(valor) {
	if (valor == "j") {
		document.getElementById('cliente').innerHTML = "<div>Nome da Empresa<br><input type='text' name='cliente'></div><div>Raz&atilde;o Social<br><input type='text' name='razao_social'></div>";
		document.getElementById('doc1').innerHTML = "CNPJ<br><input type='text' name='doc1' maxlength='18' onKeyDown='Mascara(this,Cnpj);' onKeyPress='Mascara(this,Cnpj);' onKeyUp='Mascara(this,Cnpj);'>";
		document.getElementById('doc2').innerHTML = "Inscri&ccedil;&atilde;o Estadual<br><input type='text' name='doc2' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>";
		document.getElementById('data').innerHTML = "Data de Fundação";
	} else {
		document.getElementById('cliente').innerHTML = "Cliente<br><input type='text' name='cliente'>";
		document.getElementById('doc1').innerHTML = "CPF<br><input type='text' name='doc1' maxlength='14' onKeyDown='Mascara(this,Cpf);' onKeyPress='Mascara(this,Cpf);' onKeyUp='Mascara(this,Cpf);'>";
		document.getElementById('doc2').innerHTML = "RG<br><input type='text' name='doc2' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'>";
		document.getElementById('data').innerHTML = "Data de Nascimento";
	}
}

function filtro() {
	with (document.formulario) {
		var alerta = "";
		var valida = true;
		/*
		
		##tentar corrigir esse erro depois
		
		if(window.tipoPessoa[1]===undefined){
			if (document.formulario.tipoPessoa.value=="j") {
				var tamanho = 18;
				var doc = "CNPJ";
			} else {
				var tamanho = 14;
				var doc = "CPF";
			}
		}else{
			if (document.formulario.tipoPessoa[1].checked) {
				var tamanho = 18;
				var doc = "CNPJ";
			} else {
				var tamanho = 14;
				var doc = "CPF";
			}
		}*/
		

		if (contato.value.length == 0) {
			alerta += "Preencha o campo Contato.\n";
			contato.className = "avisoInput";
			valida = false;
		} else {
			contato.className = "";
		}
		if (telefoneC.value.length < 14) {
			alerta += "Preencha o campo Telefone do Contato.\n";
			telefoneC.className = "avisoInput";
			valida = false;
		} else {
			telefoneC.className = "";
		}
		/*
		if (doc1.value.length > 0 && doc1.value.length < tamanho) {
			alerta += "Verifique se o " + doc + " informado esta correto.\n"
			doc1.className = "avisoInput";
			valida = false;
		} else {
			doc1.className = "";
		}
		*/
		if (pgaForma.value == "") {
			alerta += "Escolha a forma de pagamento.\n";
			pgaForma.className = "avisoInput";
			valida = false;
		} else {
			pgaForma.className = "";
		}
		if (pgaTipo.value == "") {
			alerta += "Escolha o tipo de pagamento.\n";
			pgaTipo.className = "avisoInput";
			valida = false;
		} else {
			pgaTipo.className = "";
		}
		if (pgaForma.value == 2 && pgaParcelas.value == "") {
			alerta += "Determine o numero de parcelamento.\n";
			pgaParcelas.className = "avisoInput";
			valida = false;
		} else {
			pgaParcelas.className = "";
		}
		//filtro de checagem de id de produto
		for (var i = 1; i <= qtdProd.value; i++) {
			if (document.getElementById('id_' + i) != null) {
				/*if (document.getElementById('id_' + i).value.length == 0) {
					if (alerta.indexOf("Existe um campo de produto nao preenchido.\n") < 0) {
						alerta += "Existe um campo de produto nao preenchido.\n";
					}
					document.getElementById('id_' + i).className = "avisoInput";
					valida = false;
				} else {
					document.getElementById('id_' + i).className = "";
				}*/
				if (document.getElementById('produto_' + i).value.length == 0) {
					if (alerta.indexOf("Existe um campo de produto nao preenchido.\n") < 0) {
						alerta += "Existe um campo de produto nao preenchido.\n";
					}
					document.getElementById('produto_' + i).className = "avisoInput";
					valida = false;
				} else {
					document.getElementById('produto_' + i).className = "";
				}
				if (document.getElementById('quantidade_' + i).value.length == 0) {
					if (alerta.indexOf("Existe um campo de produto nao preenchido.\n") < 0) {
						alerta += "Existe um campo de produto nao preenchido.\n";
					}
					document.getElementById('quantidade_' + i).className = "avisoInput";
					valida = false;
				} else {
					document.getElementById('quantidade_' + i).className = "";
				}
			}
		}

		//verificando duplicidade de produtos
		for (var i = 1; i <= qtdProd.value; i++) {
			if (document.getElementById('id_' + i) != null) {

				for (var j = 1; j <= qtdProd.value; j++) {

					if (document.getElementById('id_' + j) != null) {

						if (document.getElementById('id_' + j).value == document.getElementById('id_' + i).value && j != i && document.getElementById('id_' + i).value!="") {
							if (alerta.indexOf("Existem produtos duplicados.\n") < 0) {
								alerta += "Existem produtos duplicados.\n";
							}
							document.getElementById('produto_' + j).className = document.getElementById('produto_' + i).className = "avisoInput";
							valida = false;
						} else {
							if(document.getElementById('id_' + j).className != "avisoInput"){
								document.getElementById('id_' + j).className = "";
							}
							if(document.getElementById('id_' + i).className != "avisoInput"){
								document.getElementById('id_' + i).className = "";
							}
						}

						if (document.getElementById('produto_' + j).value == document.getElementById('produto_' + i).value && j != i) {
							if (alerta.indexOf("Existem produtos duplicados.\n") < 0) {
								alerta += "Existem produtos duplicados.\n";
							}
							document.getElementById('produto_' + j).className = document.getElementById('produto_' + i).className = "avisoInput";
							valida = false;
						} else {
							if(document.getElementById('id_' + j).className != "avisoInput"){
								document.getElementById('id_' + j).className = "";
							}
							if(document.getElementById('id_' + i).className != "avisoInput"){
								document.getElementById('id_' + i).className = "";
							}
						}
						
						

					}
				}
			}
		}

		if (valida == true) {
			return true;
		} else {
			alert(alerta);
			return false;
		}
	}
}