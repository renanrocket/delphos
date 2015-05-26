<?php

/*
if(isset($_GET["pdv"])){
	//setcookie("pdv_".base64_decode($_GET["pdv"]), "1", time()+3600*24*90);//+hora+dia+mes 3600=1hora, 24=1dia, 90=3meses
	setcookie("pdv_".base64_decode($_GET["pdv"]), "1", time()+3600);//+hora, 3600=1hora
}
*/

include "templates/upLogin.inc.php";
//scripts exclusivos para essa pagina
echo "<script src='js/pdvFiltro.js' type='text/javascript'></script>";


//comandos para usar quando for programar a parte da impressao
/*
echo "<input type='button' value='Print' onclick=\"document.getElementById('PDFtoPrint').focus(); document.getElementById('PDFtoPrint').contentWindow.print();\">";

echo "<iframe src='texto.txt' id='PDFtoPrint'></iframe>";

echo "<a href='javascript:self.print()'>IMPRIMIR</a>";
*/

//restaurar zoom
echo '<meta name="viewport" content="width=device-width, user-scalable=no">';
echo '<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">';



$op = "visualizar";
extract($_POST);
extract($_GET);


$valida = true;
if(isset($_POST["op"]) or isset($_GET["op"])){
	
	//filtro apenas para op do post
	if(isset($_POST["op"])){
		if($quantidade<1){
			$valida = false;
		}
		
		if($produto==""){//no caso do uso da maquina de leitura cod de barra
			$instrucao = "select * from produto where cod_barra='$cod' and cod_barra<>''";
		}else{//no caso de n usar a maquina de leitura de cod de barra
			if($cod==""){
				$code = "(cod_barra='' or cod_barra is null)";
			}else{
				$code = "cod_barra='$cod'";
			}
			$instrucao = "select * from produto where $code and nome='$produto'";
		}
		
		if(mysqli_num_rows(query($instrucao))<1){
			$valida = false;
		}else{
			extract(mysqli_fetch_assoc(query($instrucao)));
			$idProduto = $id;
			$preco = precoProduto($idProduto, true);
		}
	}
	
	
	
	if($valida){
		if($op=="novo"){
			
			//instrucao empresa
			$sql = query("select id as empresa from empresa where usarTimbrado='1'");
			if(mysqli_num_rows($sql)==1){
				extract(mysqli_fetch_assoc($sql));
			}
				
			//instrucao do pdv
			$instrucao = "insert into pdv (nome, id_empresa) values ('$pdvNome', '$empresa')";
			$sql = query($instrucao);
			$idPdv = mysqli_insert_id($conexao);
			
			//instrucao do pdv_item
			$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
			$instrucao .= "values ('$idPdv', '$idProduto', '$quantidade', '$preco', '$observacoes', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
			$sql = query($instrucao);
			$total = $preco * $quantidade;

			$sql = query("select * from administrativo where taxonomia='dezporcento' and valor='1'");
			if(mysqli_num_rows($sql)>0){
				//10 por cento ativo
				$dez = round($total * 10/100,2);
				$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
				$instrucao .= "values ('$idPdv', '0', '1', '$dez', '', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
				$sql = query($instrucao);
				$total+=$dez;
			}

			//dando baixa no estoque
			$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
			extract(mysqli_fetch_assoc($sqlProduto));
			if($contabilizar_estoque){
				$qtd_estoque -= $quantidade;
				$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");	
			}
			
			
			//instrucao da conta
			$instrucao = "insert into conta (tabela_entidade, entidade, tabela_referido, referido, status, valor, forma_pagamento, parcelas, conta_plano, data, id_usuario, empresa) ";
			//conta plano = 3 é um PDV
			$instrucao .= "values ('pdv', '$pdvNome', 'pdv', '$idPdv', '2', '".($total)."', '1', '1', '3','".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."', '$empresa')";
			$sql = query($instrucao);
			$idConta = mysqli_insert_id($conexao);
			
			//instrucao da conta_itens
			$instrucao = "insert into conta_itens (id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
			$instrucao .= "values ('$idConta', '1', '0', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
			$sql = query($instrucao);
			
		}elseif($op=="editar"){
			
			$idPdv = base64_decode($pdv);

			//instrucao empresa
			$sql = query("select id as empresa from empresa where usarTimbrado='1'");
			if(mysqli_num_rows($sql)==1){
				extract(mysqli_fetch_assoc($sql));
			}
			
			//instrucao do pdv
			$instrucao = "update pdv set nome='$pdvNome', id_empresa='$empresa' where id='$idPdv'";
			$sql = query($instrucao);
			
			//instrucao do pdv_item
			$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
			$instrucao .= "values ('$idPdv', '$idProduto', '$quantidade', '$preco', '$observacoes', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
			$sql = query($instrucao);
			$instrucao = "select preco, quantidade from pdv_itens where id_pdv='$idPdv' and id_produto<>0";
			$sql = query($instrucao);
			for($total = $i =0, $linha= mysqli_num_rows($sql); $i<$linha; $i++){
				extract(mysqli_fetch_assoc($sql));
				$total += $preco * $quantidade;	
			}
			$preco = $total;

			//10 por cento ativo
			$sql = query("select * from administrativo where taxonomia='dezporcento' and valor='1'");
			if(mysqli_num_rows($sql)>0){
				$sql = query("delete from pdv_itens where id_pdv='$idPdv' and id_produto='0'");
				$dez = round($total * 10/100,2);
				$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
				$instrucao .= "values ('$idPdv', '0', '1', '$dez', '', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
				$sql = query($instrucao);
				$preco +=$dez;
			}
			
			//dando baixa no estoque
			$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
			extract(mysqli_fetch_assoc($sqlProduto));
			if($contabilizar_estoque){
				$qtd_estoque -= $quantidade;
				$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");	
			}
			
			
			
			
			//verificando se a conta ja foi paga e se o cliente está acrescentando outra produto neste pdv
			$sql = query("select id as idConta, parcelas from conta where referido='$idPdv' and tabela_referido='pdv'");
			extract(mysqli_fetch_assoc($sql));
			$sql = query("select sum(valor) as valorPago from conta_itens where id_conta='$idConta'");
			if(mysqli_num_rows($sql)){
				extract(mysqli_fetch_assoc($sql));
				
				if(round($valorPago,2)==round(registro($idConta, "conta", "valor"),2) and round($valorPago,2)<round($preco,2)){

					$instrucao = "insert into conta_itens ";
					$instrucao .= "(id_conta, tipo_pagamento, tipo_pagamento_sub, data_vencimento, id_usuario) ";
					$instrucao .= "values ('$idConta', '1', '0','".date('Y-m-d')."', '".getIdCookieLogin($_COOKIE["login"])."')";
					$sql = query($instrucao);
					
					//mudando a forma de pagamento se caso ela for a vista
					$instrucao = "update conta set forma_pagamento='2', parcelas='".($parcelas+1)."' where id='$idConta'";
					$sql = query($instrucao);
					
				}
			}
			//instrucao da conta
			$instrucao = "update conta set valor='$preco', empresa='$empresa' where id='$idConta'";
			$sql = query($instrucao);


		}elseif($op=="deletar"){
			
			/*
			* 
			* operação deletar apenas para usuários com credênciais ou para aqueles que inserirem o token
			* 
			*/


			if(verificaCredencialToken() or isset($validaToken)){
				$idPdv = base64_decode($pdv);
				$idProduto = 0;
				//dando alta no estoque
				$sqlProduto = query("select id_produto as idProduto, quantidade from pdv_itens where id='$idPdvItem'");
				if(mysqli_num_rows($sqlProduto)>0){
					extract(mysqli_fetch_assoc($sqlProduto));
					$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
					if($sqlProduto = mysqli_fetch_assoc($sqlProduto)){
						extract($sqlProduto);
					}else{
						$contabilizar_estoque = null;
					}
				}else{
					$contabilizar_estoque = null;
				}

				if($contabilizar_estoque){
					$qtd_estoque += $quantidade;
					$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");

					$id_usuario = getIdCookieLogin($_COOKIE["login"]);
					$dataAtual = date('Y-m-d H:i:s');
					$link = "<a href=\"cadastrarProduto.php?op=visualizar&id=".base64_encode($idProduto);
					$link.= "\" title=\"".registro($idProduto, "produto", "nome")."\">Item</a>";
					$acao = "Devolveu um produto ao estoque ao cancelar um $link do PDV.";
					$tabela_afetada = "pdv";
					$chave_principal = $idPdv;
					
					insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				}
				

				//intrucao do pdv_item
				$instrucao = "delete from pdv_itens where id='$idPdvItem'";
				$sql = query($instrucao);

				
				$instrucao = "select quantidade, preco from pdv_itens where id_pdv='$idPdv' and id_produto<>0";
				$sql = query($instrucao);
				for($i=$total=0; $i<mysqli_num_rows($sql); $i++){
					extract(mysqli_fetch_assoc($sql));
					$total+= $preco * $quantidade;
				}
				
				$preco = turnZero($total);//torna o preço 0 caso ele seja ""
				
				//10 por cento ativo
				$sql = query("select * from administrativo where taxonomia='dezporcento' and valor='1'");
				/*
				Esse if é diferente dos outros ifs dos 10porcento pois se o cliente não quiser pagar mais
				os 10 por cento o operador pode deleta-lo e fazendo isso ele não será mais incluido.
				até o momento dele inserir outro produto ao pdv
				*/
				if(mysqli_num_rows($sql)>0 and $idProduto<>0){
					$sql = query("delete from pdv_itens where id_pdv='$idPdv' and id_produto='0'");
					$dez = round($total * 10/100,2);
					$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
					$instrucao .= "values ('$idPdv', '0', '1', '$dez', '', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
					$sql = query($instrucao);
					$preco +=$dez;
				}

				//verificando se o total pago é menor do que o preço atual
				$instrucao = "select sum(valor) as valorPago from conta_itens where id_conta = any";
				$instrucao.= "(select id from conta where referido='$idPdv' and tabela_referido='pdv')";
				$sql = query($instrucao);
				if(mysqli_num_rows($sql)){
					extract(mysqli_fetch_assoc($sql));
					//se o total pago for maior que o preco atual deletar todos os pagamentos
					if($valorPago>$preco){
						$instrucao2 = str_replace("select sum(valor) as valorPago", "select id_caixa_movimento", $instrucao);
						$instrucao = "delete from caixa_movimento where id=any(".$instrucao2.")";
						$sql = query($instrucao);

						$instrucao2 = str_replace("select id_caixa_movimento from conta_itens", "update conta_itens set id_caixa_movimento=NULL, valor=NULL, data_pagamento=NULL", $instrucao2);
						$sql = query($instrucao2);
						info("Valor total da conta é menor que o valor lançado a receber.<br>Por esse motivo resetamos todas as contas a receber deste PDV.");
					}
				}
				//instrucao da conta
				$instrucao = "update conta set valor='$preco' where referido='$idPdv' and tabela_referido='pdv'";
				$sql = query($instrucao);
				/*
				$instrucao = "select id as idConta from conta where referido='$idPdv' and entidade='PDV'";
				$sql = query($instrucao);
				extract(mysqli_fetch_assoc($sql));
				
				//instrucao da conta_itens
				$instrucao = "update conta_itens set valor='$preco' where id='$idConta'";
				$sql = query($instrucao);
				*/ 
				$validaToken = true;
				
			}else{
				
				$validaToken = false;
				if(isset($token)){
					
					$hoje =  date('Y-m-d H:i:s');
					$instrucao = "select * from tokens where token='".md5($token)."' and data_validade>='$hoje' and vezes_permitido > any (select vezes_usado from tokens where token='".md5($token)."' and data_validade>='$hoje')";
					$sqlToken = query($instrucao);
					$linhaToken = mysqli_num_rows($sqlToken);
				

					if($linhaToken>0){
						
						$idPdv = base64_decode($pdv);

						//dando alta no estoque
						$sqlProduto = query("select id_produto as idProduto, quantidade from pdv_itens where id='$idPdvItem'");
						extract(mysqli_fetch_assoc($sqlProduto));
						$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
						if($sqlProduto = mysqli_fetch_assoc($sqlProduto)){
							extract($sqlProduto);
						}

						if($contabilizar_estoque){
							$qtd_estoque += $quantidade;
							$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");

							$id_usuario = getIdCookieLogin($_COOKIE["login"]);
							$dataAtual = date('Y-m-d H:i:s');
							$link = "<a href=\"cadastrarProduto.php?op=visualizar&id=".base64_encode($idProduto);
							$link.= "\" title=\"".registro($idProduto, "produto", "nome")."\">Item</a>";
							$acao = "Devolveu um produto ao estoque ao cancelar um $link do PDV.";
							$tabela_afetada = "pdv";
							$chave_principal = $idPdv;
							
							insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
						}
				
						//intrucao do pdv_item
						$instrucao = "delete from pdv_itens where id='$idPdvItem'";
						$sql = query($instrucao);
						$instrucao = "select quantidade, preco from pdv_itens where id_pdv='$idPdv' and id_produto<>0";
						$sql = query($instrucao);
						for($i=$total=0; $i<mysqli_num_rows($sql); $i++){
							extract(mysqli_fetch_assoc($sql));
							$total+= $preco * $quantidade;
						}
						
						$preco = turnZero($total);//torna o preço 0 caso ele seja ""

						//10 por cento ativo
						$sql = query("select * from administrativo where taxonomia='dezporcento' and valor='1'");
						/*
						Esse if é diferente dos outros ifs dos 10porcento pois se o cliente não quiser pagar mais
						os 10 por cento o operador pode deleta-lo e fazendo isso ele não será mais incluido.
						até o momento dele inserir outro produto ao pdv
						*/
						if(mysqli_num_rows($sql)>0 and $idProduto<>0){
							$sql = query("delete from pdv_itens where id_pdv='$idPdv' and id_produto='0'");
							$dez = round($total * 10/100,2);
							$instrucao = "insert into pdv_itens (id_pdv, id_produto, quantidade, preco, observacoes, data, id_usuario) ";
							$instrucao .= "values ('$idPdv', '0', '1', '$dez', '', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
							$sql = query($instrucao);
							$preco +=$dez;
						}
						
						//verificando se o total pago é menor do que o preço atual
						$instrucao = "select sum(valor) as valorPago from conta_itens where id_conta = any";
						$instrucao.= "(select id from conta where referido='$idPdv' and tabela_referido='pdv')";
						$sql = query($instrucao);
						if(mysqli_num_rows($sql)){
							extract(mysqli_fetch_assoc($sql));
							//se o total pago for maior que o preco atual deletar todos os pagamentos
							if($valorPago>$preco){
								$instrucao2 = str_replace("select sum(valor) as valorPago", "select id_caixa_movimento", $instrucao);
								$instrucao = "delete from caixa_movimento where id=any(".$instrucao2.")";
								$sql = query($instrucao);

								$instrucao2 = str_replace("select id_caixa_movimento", "update conta_itens set id_caixa_movimento=NULL", $instrucao2);
								$sql = query($instrucao2);
							}
						}
						//instrucao da conta
						$instrucao = "update conta set valor='$preco' where referido='$idPdv' and tabela_referido='pdv'";
						$sql = query($instrucao);
					
						$regToken = mysqli_fetch_assoc($sqlToken);
						$regToken["vezes_usado"]++;
						$sqlToken = query("update tokens set vezes_usado='".$regToken["vezes_usado"]."' where token='".md5($token)."'");
						$validaToken = true;
						
						$id_usuario = getIdCookieLogin($_COOKIE["login"]);
						$dataAtual = date('Y-m-d H:i:s');
						$acao = "Usou um token.";
						$tabela_afetada = "tokens";
						$chave_principal = $regToken["id"];
						
						insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
						
					}else{
						
						$id_usuario = getIdCookieLogin($_COOKIE["login"]);
						$dataAtual = date('Y-m-d H:i:s');
						$acao = "Tentou usar um token.";
						$tabela_afetada = NULL;
						$chave_principal = NULL;
						
						insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
					}
				}
			}
			
		}
		
	}else{
		info("Produto não cadastrado ou quantidade inválida.", "red");
	}
		
}

if(!isset($_GET["pdv"])){
	$pdv = null;
}


if($op=="deletar" and !$validaToken){
	
	echo "<form method='get' action='cadastrarPDV.php' enctype='multipart/form-data' style='width:30%;'>";
		echo "<input type='hidden' name='pdv' value='$pdv'>";
		echo "<input type='hidden' name='op' value='deletar'>";
		echo "<input type='hidden' name='idPdvItem' value='$idPdvItem'>";
		echo "Para completar essa operação você deve inserir um Token.<br>";
		echo "Solicite um com a administração.<br>";
		echo "<input type='password' name='token'>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
	
}else{
	
	$sql = query("select * from pdv where id='".base64_decode($pdv)."'");
	
	if(mysqli_num_rows($sql)==1 or !$pdv){
		
		//pdv mobile
		?>
		<script type="text/javascript">
			function inserir(valor, identidade){
				var nomeInput = $("#foco").val();
				var valorAtual = $("input[name='"+nomeInput+"']").val();
				if(valor=="ESPAÇO"){
					valorAtual = valorAtual+" ";
					$("input[name='"+nomeInput+"']").val(valorAtual);
				}else if (valor=="<<"){
					valorAtual = valorAtual.substring(0, valorAtual.length-1);
					$("input[name='"+nomeInput+"']").val(valorAtual);
				}else{
					valorAtual = valorAtual+valor;
					$("input[name='"+nomeInput+"']").val(valorAtual);
				}
				if(nomeInput=="produto"){
					pesquisaProduto(valorAtual);
				}/*else if(nomeInput=="observacoes" && identidade>0){
					$.post("inc/pdv_itens.inc.php",{
						queryString : valor,
						id : identidade,
						op : "observacoes",
						obs : $("#observacoesMobile").val()	
					}, function (data){
					});
				}*/
			}
			function pesquisaProduto(valor){
				if(valor.length>0){
					var cod = "<center><img width='30' src='img/loading.gif'></center>";
					$('#sugestaoMobile').html(cod);
					$.post("inc/pdv_itens.inc.php",{
						queryString : valor,
						op : "mobile"	
					}, function (data){
						if(data.length){
							$("#sugestaoMobile").html(data);
						}else{
							$("#sugestaoMobile").html("<label>Nenhum produto encontrado.</label>");
						}
						
					});
				}else{
					$("#sugestaoMobile").html("<label>Digite o nome do produto.</label>");
				}
			}
			
			function preencherMobile(nome, cod){
				$("input[name='produto']").val(nome);
				$("input[name='cod']").val(cod);
				if($("select[name='pdvNome']").val()=="--"){
					$("select[name='pdvNome']").addClass("avisoInput");
				}else{
					$("select[name='pdvNome']").removeClass("avisoInput");
					$("#formMobile").submit();
				}
				
			}

			function checkPDV(valorPDV){
				if(valorPDV!="--"){
					$.post("inc/pdv_check.inc.php",{
						pdv : valorPDV,
						op : $("input[name='op']").val()
					}, function(data){
						if(data.length){
							$("#sugestaoMobile").html(data);			
						}
					});
				}
			}

			function teclado_change(foco){
				$("#foco").val(foco);
			}
		</script>
		<?php

		function inserir($valor){
			if(isset($_GET["pdv"])){
				$identidade = base64_decode($_GET["pdv"]);
			}else{
				$identidade = 0;
			}
			return "class='btnMobile' value='$valor' onclick='inserir(\"$valor\", \"$identidade\");'";
		}
		
		if(isset($_GET["pdv"])){
			echo "<form class='form' id='formMobile' method='post' action='cadastrarPDV.php?pdv=".$_GET["pdv"]."' enctype='multipart/form-data'>"; // o $_GET["pdv"] ja esta criptografado
			echo "<input type='hidden' name='op' value='editar'>";
			$sql = query("select nome as pdvNome from pdv where id='".base64_decode($_GET["pdv"])."'");
			extract(mysqli_fetch_assoc($sql));
		}else{
			
			$sql = query("select max(id)+1 as pdvNome from pdv");
			extract(mysqli_fetch_assoc($sql));
			if(!$pdvNome){
				$pdvNome = 1;
			}
			echo "<form class='form' id='formMobile' method='post' action='cadastrarPDV.php?pdv=".base64_encode($pdvNome)."' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='novo'>";
			$pdvNome = "PDV ".$pdvNome;
			$observacoes = null;
		}
		echo "<div class='column mobile'>";
		echo "<label>PDV</label>";

		echo "<select name='pdvNome' onchange=\"checkPDV(this.value)\">";
		echo "<option value='--'>--</option>";
		if(isset($_GET["pdv"])){
			for($i=1; $i<=100; $i++){
				if($pdvNome==$i or $pdvNome=="pdv ".$i or $pdvNome=="mesa ".$i){
					echo "<option value='$i' selected='yes'>$i</option>";
				}else{
					echo "<option value='$i'>$i</option>";
				}
			}
		}else{
			for($i=1; $i<=100; $i++){
				if(isset($_GET["pdvSelected"])){
					if($i==$_GET["pdvSelected"]){
						echo "<option value='$i' selected='yes'>$i</option>";		
					}else{
						echo "<option value='$i'>$i</option>";
					}
				}else{
					echo "<option value='$i'>$i</option>";
				}
			}
		}
		echo "</select>";
		echo "<input type='hidden' name='cod'>";
		echo "<label>Quantidade</label>";
		echo "<select name='quantidade'>";
		for($i=1; $i<=10; $i++){
			echo "<option value='$i'>$i</option>";
		}
		echo "</select>";

		echo "<input id='foco' type='hidden' name='foco' value='produto'>";

		echo "<label onclick=\"teclado_change('produto');\">Produto</label>";
		echo "<input type='text' name='produto' class='inputValor' id='produtoMobile' onclick=\"teclado_change('produto');\">";
		echo "<div id='sugestaoMobile'><label>Digite o nome do produto.</label></div>";
		
		echo "<label onclick=\"teclado_change('observacoes');\">Observações</label>";
		echo "<input type='text' name='observacoes' class='inputValor' id='observacoesMobile' onclick=\"teclado_change('observacoes');\">";

		echo "<div id='teclado'>";
			echo "<span>";
				echo "<input style='width:9%;' type='button' ".inserir("1").">";
				echo "<input style='width:9%;' type='button' ".inserir("2").">";
				echo "<input style='width:9%;' type='button' ".inserir("3").">";
				echo "<input style='width:9%;' type='button' ".inserir("4").">";
				echo "<input style='width:9%;' type='button' ".inserir("5").">";
				echo "<input style='width:9%;' type='button' ".inserir("6").">";
				echo "<input style='width:9%;' type='button' ".inserir("7").">";
				echo "<input style='width:9%;' type='button' ".inserir("8").">";
				echo "<input style='width:9%;' type='button' ".inserir("9").">";
				echo "<input style='width:9%;' type='button' ".inserir("0").">";
			echo "</span>";
			echo "<span>";
				echo "<input style='width:9%;' type='button' ".inserir("Q").">";
				echo "<input style='width:9%;' type='button' ".inserir("W").">";
				echo "<input style='width:9%;' type='button' ".inserir("E").">";
				echo "<input style='width:9%;' type='button' ".inserir("R").">";
				echo "<input style='width:9%;' type='button' ".inserir("T").">";
				echo "<input style='width:9%;' type='button' ".inserir("Y").">";
				echo "<input style='width:9%;' type='button' ".inserir("U").">";
				echo "<input style='width:9%;' type='button' ".inserir("I").">";
				echo "<input style='width:9%;' type='button' ".inserir("O").">";
				echo "<input style='width:9%;' type='button' ".inserir("P").">";
			echo "</span>";
			echo "<span>";
				echo "<input style='width:9%;' type='button' ".inserir("A").">";
				echo "<input style='width:9%;' type='button' ".inserir("S").">";
				echo "<input style='width:9%;' type='button' ".inserir("D").">";
				echo "<input style='width:9%;' type='button' ".inserir("F").">";
				echo "<input style='width:9%;' type='button' ".inserir("G").">";
				echo "<input style='width:9%;' type='button' ".inserir("H").">";
				echo "<input style='width:9%;' type='button' ".inserir("J").">";
				echo "<input style='width:9%;' type='button' ".inserir("K").">";
				echo "<input style='width:9%;' type='button' ".inserir("L").">";
				echo "<input style='width:9%;' type='button' ".inserir("Ç").">";
			echo "</span>";
			echo "<span>";
				echo "<input style='width:9%;' type='button' ".inserir("Z").">";
				echo "<input style='width:9%;' type='button' ".inserir("X").">";
				echo "<input style='width:9%;' type='button' ".inserir("C").">";
				echo "<input style='width:9%;' type='button' ".inserir("V").">";
				echo "<input style='width:9%;' type='button' ".inserir("B").">";
				echo "<input style='width:9%;' type='button' ".inserir("N").">";
				echo "<input style='width:9%;' type='button' ".inserir("M").">";
				echo "<input style='width:9%;' type='button' ".inserir("-").">";
			echo "</span>";
			echo "<span>";
				echo "<input style='width:70%;' type='button' ".inserir("ESPAÇO").">";
				echo "<input style='width:20%;' type='button' ".inserir("<<").">";
			echo "</span>";
		echo "</div>";

		echo "<br>";
		if(isset($_GET["pdv"])){
			echo "<table>";
			echo "<tr>";
				echo "<td align='center' style='font-size:20px;'>----------------------------------------</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='center' style='font-size:20px;'>PDV $pdvNome</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='center' style='font-size:20px;'>----------------------------------------</td>";
			echo "</tr>";
			//instrucao capturar itens do pdv
			$instrucao = "select * from pdv_itens where id_pdv='".base64_decode($_GET["pdv"])."'";
			$sql = query($instrucao);
			$tabela_item = "produto";
			for($i = $total = 0, $linha = mysqli_num_rows($sql); $i<$linha; $i++){
				extract(mysqli_fetch_assoc($sql));
				if(isset($valor_produto)){
					$preco = $valor_produto;
					$id_produto = $id_item;
				}
				echo "<tr>";
					echo "<td align='center' style='font-size:20px; text-transform: uppercase;'>";
						if($id_produto!=0){
							$nomeProduto = registro($id_produto, $tabela_item, "nome");
						}else{
							$nomeProduto = $id_produto;
						}
						echo cupomLista($nomeProduto, $quantidade, real($preco));
						if($observacoes){
							echo "<tr>";
								echo "<td align='center' style='font-size:20px;'>".wordwrap(strip_tags($observacoes), 30, "<br>",true)."</td>";
							echo "</tr>";
						}
					echo "</td>";
				echo "</tr>";
				$total += $preco * $quantidade;
			}
		 	echo "<tr>";
				echo "<td align='center' style='font-size:20px;'>----------------------------------------</td>";
			echo "</tr>";
		
			$cod = "TOTAL";
			//verificar se existem contas quitadas e faz a sobtração do total
			$instrucao = "select sum(valor) as valor from conta_itens where id_conta=(select id from conta where tabela_referido='pdv' and referido='".base64_decode($_GET["pdv"])."')";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			$valor = round($valor,2);//correção do bug da soma do mysql
			if($valor>0 and $total!=$valor){
				echo "<tr>";
					echo "<td align='center' id='total' style='font-size:15px;'>Valor Total R$ ".real($total)."</td>";
				echo "</tr>";
				$total = $total - $valor;
				echo "<tr>";
					echo "<td align='center' id='total' style='font-size:15px;'>Recebido R$ ".real($valor)."</td>";
				echo "</tr>";
				$cod = "Diferenca";
			}
			
			echo "<tr>";
				echo "<td align='center' id='total' style='font-size:22px;'>$cod R$ ".real($total)."</td>";
			echo "</tr>";

			
			echo "</table>";
		}
		

		echo "<br><br><br>";

		echo "</div>";
		echo "</form>";
		



		//pdv desktop;
		echo "<table class='desktop'>";
			echo "<tr>";
				echo "<td>";
					formPDV();
				echo "</td>";
				echo "<td align='center'>";
					cupom($pdv, "pdv");
					
					if(isset($pdv)){
						echo "<br><p>";
						echo "<a href='pesquisaPDV.php?op=aberto' class='aSubmit'>Gerar Comanda</a>";
						echo "<a href='#' class='aSubmit' ".pop("cupom.php?tipo=pdv&cupom=".$pdv, "310", "400").">Imprimir Cupom</a></p><p>";
						$instrucao = "select * from conta where referido='".base64_decode($pdv)."' and tabela_referido='pdv'";
						$sql = query($instrucao);
						extract(mysqli_fetch_assoc($sql));

						$sql = query("select sum(valor) as total from conta_itens where id_conta='$id'");
						extract(mysqli_fetch_assoc($sql));
						$instrucao = "select min(id) as id_conta_item, id_caixa_movimento, data_vencimento from conta_itens where id_conta='$id' and valor is null";
						$sql = query($instrucao);
						extract(mysqli_fetch_assoc($sql));
						$valor = round($valor,2);//correção do bug da soma do mysql
						$total = round($total,2);//correção do bug da soma do mysql
						$total = $valor-$total;
						
						if($total){
							echo "<script type='text/javascript'>\n";
								echo "function calcularTroco(valor){\n";
									echo "valor = valor.replace(',','.');\n";
									echo "var troco = (parseFloat(valor)-parseFloat($total)).toFixed(2);\n";
									echo "if(troco=='NaN' || troco<0){";
										echo "troco='00,00'";
									echo "}";
									echo "troco = troco.replace('.',',');\n";
									echo "$(\"input[name='troco']\").val(troco);\n";
								echo "}\n";
								echo "function filtroValor(){\n";
									echo "if($(\"#valor\").val()!=\"\"){\n";
										echo "$(\"#valor\").val(\"\");\n";
									echo "}else{\n";
										echo "$(\"#valor\").val(\"".real($total)."\");\n";
									echo "}\n";
								echo "}\n";
								echo "function filtroValor2(){\n";
									echo "if($(\"#valor\").val()==\"\"){\n";
										echo "$(\"#valor\").val(\"".real($total)."\");\n";
									echo "}\n";
								echo "}\n";
							echo "</script>\n";

							$idConta = $id;
							$msg = "<form style='display:block;' class='form' method='post' action='pesquisaConta2.php' enctype='multipart/form-data'>";
							$msg.= "<div class='column'>";
							$msg.= "<input type='hidden' name='tipoPagamento' value='1'>";
							$msg.= "<input type='hidden' name='dataVencimento' value='$data_vencimento'>";
							$msg.= "<input type='hidden' name='conta' value='".base64_encode($id)."'>";
							$msg.= "<input type='hidden' name='op' value='editar_conta_itens'>";
							$msg.= "<input type='hidden' name='op2' value='lancar'>";
							$msg.= "<input type='hidden' name='conta_itens' value='$id_conta_item'>";	
							$msg.= "<label for='valor' style='width:50%; display:inline-block;'>Valor recebido</label>";
							$msg.= "<label for='valor' style='width:50%; display:inline-block;'>Troco</label><br>";
							$msg.= "<input type='text' style='width:50%; display:inline;' class='preco totalValor' name='valor' ";
							$msg.= "value='".real($total)."' ".mascara("Valor2", null, null, "calcularTroco(this.value);", "calcularTroco(this.value);", "calcularTroco(this.value);");
							$msg.= " onclick='filtroValor();' onblur='filtroValor2();' autocomplete='off' id='valor'>";
							$msg.= "<input type='text' style='width:50%; display:inline;' class='inputValor totalValor preco' name='troco'>";
							$msg.= "<label for='tipo_pagamento'>Tipo de pagamento</label>";
							$msg.= "<input type='hidden' name='tipo_pagamento_sub' id='tipoPagamentoSub_1' value='".registro($id, "conta_itens", "tipo_pagamento_sub", "id_conta")."'>";
							$msg.= "<select name='tipoPagamento' id='tipoPagamento_1' onchange=\"showTipoPagamentoSub(this.value, this);\">";
							$msg.= opcaoSelect("pagamento_tipo", 1, "Ativo", registro($id, "conta_itens", "tipo_pagamento", "id_conta"));
							$msg.= "</select>";
							$sqlSubTipoPag = query("select * from pagamento_tipo_sub where id_pagamento_tipo='".registro($id, "conta_itens", "tipo_pagamento", "id_conta")."'");
							if(mysqli_num_rows($sqlSubTipoPag)){
								$display="";
							}else{
								$display="style='display:none;'";
							}
							$msg.= "<span id='spanTipoPagamentoSub_1' $display>";		
							$msg.= "<select name='tipoPagamentoSub' onchange='mudaTipoPagamentoSub(this.value);'>";
							$msg.= opcaoSelect("pagamento_tipo_sub", 2, "Ativo", registro($id, "conta_itens", "tipo_pagamento_sub", "id_conta"));
							$msg.= "</select>";
							$msg.= "</span>";
							$msg.= "<label for='caixa'>Caixa creditado</label>";
							$sqlCaixa = query("select id_caixa from caixa_movimento where id='$id_caixa_movimento'");
							if(mysqli_num_rows($sqlCaixa)){
								extract(mysqli_fetch_assoc($sqlCaixa));
							}else{
								$id_caixa = 1;
							}
							$msg.= "<select name='caixa'>";
					        $msg.=  opcaoSelect("caixa", 1, "Ativo", $id_caixa_movimento);
					        $msg.= "</select>";
					        //$msg.= "<input type='submit' class='submit' value='Enviar' ".pop("cupom.php?tipo=pdv&cupom=".$pdv, "310", "400").">";
					        $msg.= "<input type='submit' class='submit' value='Enviar'>";
							$msg.= "</div>";
							$msg.= "</form>";
							info($msg, "green", null, "none", "pagamento");
							$link = "<a href='#' ".info(null, null, null, null, null, "pagamento")." class='aSubmit btnEnviar'>Efetuar Pagamento</a>";
						}else{
							$link = "<a href='pesquisaConta2.php?op=visualizar&conta=".base64_encode($id)."' class='aSubmit btnEnviar'>Visualizar Conta</a>";
						}
						echo $link;
						echo "<a href='cadastrarPDV2.php?pdv=$pdv&op=cancelar' class='aSubmit btnDeletar'>Cancelar PDV</a>";
						
						echo "</p>";
							
					}
					
				echo "</td>";

			echo "</tr>";
			
			
		echo "</table>";	
	}else{
		info("PDV inexistente.");
		echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=cadastrarPDV.php'>";
	}
		
}



include "templates/downLogin.inc.php";


?>