// JavaScript Document
//<!--
function filtro(){
	with(document.formCadastraServico){
		var alerta = "";
		var confirma = "";
		var valida = true;
		if (nome.value=="" || (nome.value.match(/^\s+$/))){
			alerta += "Digite o nome para este servico.\n";
			nome.className = "avisoInput";
			valida = false;
		}else{
			nome.className = "";
		}
		if (nome.value.length<5){
			alerta += "Nome deste servico esta muito curto.\n";
			nome.className = "avisoInput";
			valida = false;
		}else{
			nome.className = "";
		}
		
		if (categoria.value==0){
			alerta += "Selecione a categoria do produto.\n";
			categoria.className = "avisoInput";
			valida = false;
		}else{
			categoria.className = "";
		}
		/*
		if (subcategoria.value.length==0){
			alerta += "Selecione a sub categoria do produto.\n";
			subcategoria.className = "avisoInput";
			valida = false;
		}else{
			subcategoria.className = "";
		}*/
		/*
		if (descricao.value.length==0){
			alerta += "Descricao do servico esta vazia.\n";
			descricao.className = "avisoInput";
			valida = false;
		}else{
			descricao.className = "";
		}
		*/
		
		//filtro Produto
		var qtdProduto = parseInt(produtoQtd.value) + 50;
		for(var i = 1; i <= qtdProduto ; i++){
			
			if ($('#produto_' + i).val() == undefined) {
				i ++;
			}else{
				if((i>1) || (i==1 && qtdProduto>51)){
					if($('#produto_' + i).val() == ""){
						alerta += "Existe um campo produto vazio.\n";
						$('#produto_' + i).attr("class", "avisoInput");
						valida = false;
					}else{
						$('#produto_' + i).attr("class", "");
					}
				}
				if($('#produto_' + i).val() != ""){
					
					if((parseFloat(($('#produtoQtd_' + i).val()).replace(",", "."))).toFixed(2) <= 0){
						if(alerta.indexOf("Existe um campo quantidade do produto invalido.\n")<0){
							alerta += "Existe um campo quantidade do produto invalido.\n";	
						}
						$('#produtoQtd_' + i).attr("class", "avisoInput");
						valida = false;
					}else{
						$('#produtoQtd_' + i).attr("class", "");
					}
					if(parseInt($('#produtoVolume_' + i).val()) <= 0){
						if(alerta.indexOf("Existe um campo volume do produto invalido.\n")<0){
							alerta += "Existe um campo volume do produto invalido.\n";	
						}
						$('#produtoVolume_' + i).attr("class", "avisoInput");
						valida = false;
					}else{
						$('#produtoVolume_' + i).attr("class", "");
					}
					if(parseFloat(($('#produtoSubTotal_' + i).val()).replace(",", ".")) <= 0){
						if(alerta.indexOf("Existe um campo Sub Total do produto invalido.\n")<0){
							alerta += "Existe um campo Sub Total do produto invalido.\n";	
						}
						$('#produtoSubTotal_' + i).attr("class", "avisoInput totalValor preco");
						valida = false;
					}else{
						$('#produtoSubTotal_' + i).attr("class", "totalValor preco");
					}
					if(parseFloat(($('#produtoTotal_' + i).val()).replace(",", ".")) <= 0){
						if(alerta.indexOf("Existe um campo Total do produto invalido.\n")<0){
							alerta += "Existe um campo Total do produto invalido.\n";	
						}
						$('#produtoTotal_' + i).attr("class", "avisoInput totalValor preco");
						valida = false;
					}else{
						$('#produtoTotal_' + i).attr("class", "totalValor preco");
					}
					
					//verificando duplicidade de produto
					for (var j = 1; j <= qtdProduto; j++) {
						if ($('#produtoId_' + j) != null) {
	
							if ($('#produtoId_' + j).val() == $('#produtoId_' + i).val() && j != i && $('#produtoId_' + i).val()!="") {
								if (alerta.indexOf("Existem produtos duplicados.\n") < 0) {
									alerta += "Existem produtos duplicados.\n";
								}
								$('#produto_' + j).attr("class", "avisoInput");
								$('#produto_' + i).attr("class", "avisoInput");
								valida = false;
							}
							if ($('#produto_' + j).val() == $('#produto_' + i).val() && j != i) {
								if (alerta.indexOf("Existem produtos duplicados.\n") < 0) {
									alerta += "Existem produtos duplicados.\n";
								}
								$('#produto_' + j).attr("class", "avisoInput");
								$('#produto_' + i).attr("class", "avisoInput");
								valida = false;
							}
						}
					}
					
					/*
					//checando se os valores das comissoes são maiores do que o preço da unidade final do servico
					var Total = parseFloat($('#totalProduto').val().replace(",", "."));
					for (var l = 1; l <= qtdProduto; l++) {
						if ($('#produtoId_' + l) != null && $('#preco_'+ i) != null) {
							//no caso do tipo de comissao ser do tipo valor
							if($('#precoTipo_'+ l).val()=="1"){
								variavel1 = $('#precoValorRealInput_'+ l).val();
								Total = Total + parseFloat((variavel1).replace(",", "."));
							}else{//no caso do tipo de comissao ser do tipo por
								variavel1 = $('#precoValorPorInput_'+ l).val();
								variavel2 = $('#preco_'+ i).val();
								Total = Total + (parseFloat((variavel1).replace(",", ".")) * parseFloat((variavel2).replace(",", ".")))/100;
							}  
						}
					}
					alert(Total);
					if(Total>= parseFloat($('#preco_'+i).val().replace(",","."))){
						$("input[name='precoValorReal[]']").attr("class", "avisoInput totalValor preco");
						$("input[name='precoValorPor[]']").attr("class", "avisoInput totalValor porcentagem");
					}else{
						$("input[name='precoValorReal[]']").attr("class", "totalValor preco");
						$("input[name='precoValorPor[]']").attr("class", "totalValor porcentagem");
					}
					*/
				}else{
					$('#produtoQtd_' + i).attr("class", "");
					$('#produtoVolume_' + i).attr("class", "");
					$('#produtoSubTotal_' + i).attr("class", "totalValor preco");
					$('#produtoTotal_' + i).attr("class", "totalValor preco");
				}
			}
			
		}
		
		//filtro das regras de comissao
		var qtdComissao = parseInt($('#comissaoQtd').val()) + 50;
		for(i = 1; i <= qtdComissao; i++){
			

			if ($('#usuarioValor_' + i).val() == undefined) {
				i ++;
			}else{
				
				//evitar duplicidade
				for (var j = 1; j <= qtdProduto; j++) {
					if ($('#usuarioTabela_' + j) != null) {
						if( ( $('#usuarioTabela_'+i).val() == $('#usuarioTabela_'+j).val() ) 
							&&
							 ( $('#usuarioValor_'+i).val() == $('#usuarioValor_'+j).val() 
							 && i!=j) ){
							if(alerta.indexOf("Existem regras de comissao duplicadas.\n")<0){
								alerta+= "Existem regras de comissao duplicadas.\n";	
							}
							valida = false;
							$('#usuarioTabela_'+i).attr("class", "avisoInput");
							$('#usuarioTabela_'+j).attr("class", "avisoInput");
							$('#usuarioValor_'+i).attr("class", "avisoInput");
							$('#usuarioValor_'+j).attr("class", "avisoInput");
						}else{
							$('#usuarioTabela_'+i).attr("class", "");
							$('#usuarioTabela_'+j).attr("class", "");
							$('#usuarioValor_'+i).attr("class", "");
							$('#usuarioValor_'+j).attr("class", "");
						}
					}
				}				
				var real = ($('#precoValorRealInput_' + i).val()).replace(",", ".");
				var por = ($('#precoValorPorInput_' + i).val()).replace(",", ".");
				
				if(i>1){
					if($('#precoTipo_' + i).val() == "1"){
						if( parseFloat(real) <= 0 ){
							alerta += "Existe um valor de regra de comissao invalida.\n";
							valida = false;
							$('#precoValorRealInput_' + i).attr("class", "avisoInput totalValor preco");
						}else{
							$('#precoValorRealInput_' + i).attr("class", "totalValor preco");
						}
					}else if ($('#precoTipo_' + i).val() == "0"){
						if( parseFloat(por) <= 0 ){
							alerta += "Existe um valor de regra de comissao invalida.\n";
							valida = false;
							$('#precoValorPorInput_' + i).attr("class", "avisoInput totalValor porcentagem");
						}else{
							$('#precoValorPorInput_' + i).attr("class", "totalValor porcentagem");
						}
					}//fim do else do precoTipo
				}else{
					if($('#usuarioValor_' + i).val() != "0"){
						if($('#precoTipo_' + i).val() == "1"){
							if( parseFloat(real) <= 0 ){
								alerta += "Existe um valor de regra de comissao invalida.\n" + i;
								valida = false;
								$('#precoValorRealInput_' + i).attr("class", "avisoInput totalValor preco");
							}else{
								$('#precoValorRealInput_' + i).attr("class", "totalValor preco");
							}
						}else if ($('#precoTipo_' + i).val() == "0"){
							if( parseFloat(por) <= 0 ){
								alerta += "Existe um valor de regra de comissao invalida.\n";
								valida = false;
								$('#precoValorPorInput_' + i).attr("class", "avisoInput totalValor porcentagem");
							}else{
								$('#precoValorPorInput_' + i).attr("class", "totalValor porcentagem");
							}
						}//fim do else do precoTipo
					}//fim do if
				}//fim do else de i>1
				
			}//fim do else undefined
		}//fim da porra toda
		
		var PrecoQtd = parseInt($('#precoQtd').val()) + 50;
		for(i = 1; i <= PrecoQtd; i++){
			for(j=1; j<=PrecoQtd; j++){
				if($('#quantidadePreco_' + i).val() != undefined){
				
					var quantidadePreco = parseInt($('#quantidadePreco_' + i).val());
					var preco = parseFloat( ($('#preco_' + i).val()).replace(",",".") );
					var precoTotalProduto = parseFloat( ($('#totalProduto').val()).replace(",", ".") );
					
					if(preco <= 0){
						if(alerta.indexOf("Você precisa por um preço para este serviço.\n")<0){
							alerta += "Você precisa por um preço para este serviço.\n";	
						}
						valida = false;
						$('#preco_' + i).attr("class", "avisoInput totalValor preco");
					}else if(preco <= precoTotalProduto){
						if(alerta.indexOf("Você esta prestes a cadastrar um servico com valor igual ou inferior ao da materia prima para ser fabricado o mesmo.\n")<0){
							alerta += "Você esta prestes a cadastrar um servico com valor igual ou inferior ao da materia prima para ser fabricado o mesmo.\n";	
						}
						valida = false;
						$('#preco_' + i).attr("class", "avisoInput totalValor preco");
						$('#totalProduto').attr("class", "avisoInput totalValor inputValor preco");
					}else if($('#quantidadePreco_' + i).val() == $('#quantidadePreco_' + j).val() && i!=j){
						alerta += "Existem valores dublicados nas quantidades de precos de produtos.\n";
						valida = false;
						$('#quantidadePreco_' + i).attr("class", "avisoInput");
						$('#quantidadePreco_' + j).attr("class", "avisoInput");
					}else{
						$('#quantidadePreco_' + i).attr("class", "");
						$('#preco_' + i).attr("class", "totalValor preco");
						$('#totalProduto').attr("class", "totalValor inputValor preco");
					}
					
					
				}
			}
			
		}
		
		//detectando se existe alguma comissao fixa maior que o preco unico final do produto
		for(i=1; i<=qtdComissao; i++){
			
			if($('#precoValorRealInput_' + i).val() != undefined){
				
				var valorTipoComissao = parseInt( $('#precoTipo_' + i).val() );
				var valorComissao = parseFloat( ( $('#precoValorRealInput_' + i).val() ).replace(",", ".") );	
				
				for(j=1; j<=PrecoQtd; j++){
					
					if($('#preco_' + j).val() != undefined){
						
						var valorPreco = parseFloat( ( $('#preco_' + j).val() ).replace(",", ".") );
						
						if(valorTipoComissao==1){//significa q a opção de comissao esta setada como preço fixo
							if(valorComissao>=valorPreco){//se o preço da comissao maior do que o preço do servico então avisar
								if(alerta.indexOf("Existem valores de comissão maior do que o preço do serviço.\n")<0){
									alerta += "Existem valores de comissão maior do que o preço do serviço.\n";
								}
								$('#precoValorRealInput_' + i).attr("class", "avisoInput totalValor preco");
								$('#preco_' + j).attr("class", "avisoInput totalValor preco");
								valida = false;
							}else{
								$('#precoValorRealInput_' + i).attr("class", "totalValor preco");
								$('#preco_' + j).attr("class", "totalValor preco");
							}
						}//fim da checagem se o tipo de comissao é igual a 1
					}//fim do undefined do j
				}//fim do loop do j
			}//fim do undefined do i
		}//fim do loop do i
		
		//verifica se o nome é válido;
		if($('#checkNomeInput').val()=="false"){
			$('#checkNomeSpan').attr("style", "display:inline-block;");
			$('#checkNomeSpan').html("<center><img style='width:26px; line-height:26px;' src='img/check-no.png'></center>");
			$('#nome').attr("class", "avisoInput");
			valida=false;
			alerta+= "O nome do servico esta invalido.\n";
		}
		
		if (valida==false){
			alert(alerta);
			return false;
		}else{
			return true;
		}
	}
}

//-->