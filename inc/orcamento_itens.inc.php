<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
	include "funcoes.inc.php";
	if(isset($_COOKIE["id_empresa"])){
		//includes do php para todas as paginas
		$conn = TConnection::open("gestor");

		$criterio = new TCriteria;
		$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

		$sql = new TSqlSelect;
		$sql->setEntity("cliente");
		$sql->addColumn('alcunha');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());
		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
			define("ALCUNHA", $alcunha);
		}

		//deletar esse codigo apois migração do app.ado
		file_exists("../conecta.php") ?
		include "../conecta.php" : $conexao = null;
		$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
		extract(mysqli_fetch_assoc($sql));
		include "../".$conectasrc;
		//fim
	}else{
		echo "Impossível se conectar com o banco de dados.";
		die;
	}
	
	extract($_POST);

	
		
	// Is there a posted query string?
	if(isset($_POST["queryString"])) {

		$queryString = $db->real_escape_string($_POST["queryString"]);
		$inputId = $db->real_escape_string($_POST["id"]);
		
		// Is the string length greater than 0?

		
		if(strlen($queryString) >0) {
			
			$li = "li1";
			
			
			$instrucao = "SELECT id as idServico from servico ";
			$instrucao .= "WHERE ";
			//servico
			$instrucao .= "(servico.nome LIKE '%$queryString%' ";
			$instrucao .= "or servico.id_categoria like (select id from categoria where nome like '%$queryString%' LIMIT 10) ";
			$instrucao .= "or servico.id_subcategoria like (select id from sub_categoria where nome like '%$queryString%' LIMIT 10) ";
			$instrucao .= "or servico.id='$queryString') ";
			$instrucao .= "and (status='Ativo' or status='1') ";
			$instrucao .= "LIMIT 10";

			$queryServico = $db->query($instrucao);
			
			if($queryServico){
					
				while($result = $queryServico -> fetch_object()){
					
					
					//calcular o preco do servico
					$sql = query("select * from servico_preco where id_servico=".$result->idServico);
					$qtdPreco = mysqli_num_rows($sql);
					extract(mysqli_fetch_assoc($sql));
					$preco1= real($valor);
					
					//imagem
					$idServico = $result->idServico;
					$sql = query("select * from servico_imagem where id_servico=".$result->idServico);
					for($i=0, $tooltip=null; $i<mysqli_num_rows($sql); $i++){
						extract(mysqli_fetch_assoc($sql));
						if(file_exists("../".$miniatura)){
							$tooltip .= "<img ".pop("imagem.php?id=$id_servico&atributo=servico_imagem&referencia=id_servico", 600, 600)." src=\"$miniatura\">";	
						}
					}
					
					$sql = query("select * from servico where id=".$result->idServico);
					extract(mysqli_fetch_array($sql));
					
												
					// YOU MUST CHANGE: $result->value to $result->your_colum
					$nomeCompleto = $nome ." ". registro($id_categoria, "categoria", "nome", "id") . " " . registro($id_subcategoria, "sub_categoria", "nome", "id");
					$nomeCompletoDisplay = "<b>".$nome ."</b><br>". registro($id_categoria, "categoria", "nome", "id") . " " . registro($id_subcategoria, "sub_categoria", "nome", "id");
					
					if($li=="li1"){
						$li= "li2";
					}else{
						$li="li1";
					}
					$js = "onClick=\"preencher('servico', '$idServico', '$nomeCompleto', '$preco1', '$inputId');\"";
         			echo "<li  class='$li'>";
         			echo "<table class='listaItensOrcamento'>";
         			echo "<tr>";
         			echo "<td rowspan='$qtdPreco' class='nomeLista'>$tooltip <span $js>$nomeCompletoDisplay</span></td>";
					
					
					//calcular o preco do servico
					$sql = query("select * from servico_preco where id_servico=".$result->idServico);
					for($i=0, $preco = null; $i<$qtdPreco; $i++){
						extract(mysqli_fetch_assoc($sql));
						if($i!=0){
							echo "<tr>";
						}
						$limite == 0 ? $limite = "<b style='font-size:18pt;'>&infin;</b>": false;
						echo "<td class='precoLista' align='right'><span id='p2'>até $limite und </span><span id='p1'>R$ ".real($valor)."</span></td>";
						echo "</tr>";
					}
         			
         			echo "</table>";
         			echo "</li>";
					
				}
				
			}
			
			$instrucao = "SELECT id as idProduto from produto ";
			$instrucao .= "WHERE ";
			//produto
			$instrucao .= "(produto.nome LIKE '%$queryString%' or produto.modelo LIKE '%$queryString%' or produto.cod_barra='$queryString' ";
			$instrucao .= "or produto.id_marca like (select id from marca where nome like '%$queryString%' LIMIT 10) ";
			$instrucao .= "or produto.id_categoria like (select id from categoria where nome like '%$queryString%' LIMIT 10) ";
			$instrucao .= "or produto.id_subcategoria like (select id from sub_categoria where nome like '%$queryString%' LIMIT 10) ";
			$instrucao .= "or produto.id='$queryString') ";
			$instrucao .= "and busca='1' and (status='Ativo' or status='1') limit 10";

			//echo $instrucao;
			$queryProduto = $db->query($instrucao);
			if($queryProduto) {
				
				while($result = $queryProduto->fetch_object()){	
			

					//calcular o preco do produto
					$preco1 = precoProduto($result->idProduto, true, false, false);
					$preco2 = precoProduto($result->idProduto, false, true, false);
					
					$sql = query("select * from produto where id=".$result->idProduto);
					extract(mysqli_fetch_assoc($sql));
					
					// YOU MUST CHANGE: $result->value to $result->your_colum
					$idProduto = $result->idProduto;
					$nomeCompleto = $nome . " " . $modelo . " " . registro($id_marca, "marca", "nome", "id");
					$nomeCompleto .= " " . registro($id_categoria, "categoria", "nome", "id") . " ";
					$nomeCompleto .= registro($id_subcategoria, "sub_categoria", "nome", "id");
					
					$nomeCompletoDisplay = "<b>".$nome . "</b><br>" . $modelo . " " . registro($id_marca, "marca", "nome", "id");
					$nomeCompletoDisplay .= " " . registro($id_categoria, "categoria", "nome", "id") . " ";
					$nomeCompletoDisplay .= registro($id_subcategoria, "sub_categoria", "nome", "id");
					
					//imagem
					$sql = query("select * from produto_imagem where id_produto=".$result->idProduto);
					for($i=0, $tooltip=null; $i<mysqli_num_rows($sql); $i++){
						extract(mysqli_fetch_assoc($sql));
						if(file_exists("../".$miniatura)){
							$tooltip .= "<img ".pop("imagem.php?id=$id_produto&atributo=produto_imagem&referencia=id_produto", 600, 600)." src=\"$miniatura\">";	
						}
					}
					

					if($li=="li1"){
						$li= "li2";
					}else{
						$li="li1";
					}
					$js = "onClick=\"preencher('produto','$idProduto', '$nomeCompleto', '$preco1', '$inputId');\"";
					echo "<li class='$li'>";
         			echo "<table class='listaItensOrcamento'>";
         			echo "<tr>";
         			echo "<td rowspan='3' class='nomeLista'>$tooltip <span $js>$nomeCompletoDisplay</span></td>";
         			echo "<td class='precoLista' align='right'><span id='p1'>R$ ".real($preco1)."</span></td>";
         			echo "</tr>";
         			echo "<tr>";
					echo "<td class='precoLista' align='right'><span id='p2'>por até R$ ".real($preco2)."</span></td>";
					echo "</tr>";
					
					if($contabilizar_estoque){
						echo "<tr>";
						echo "<td class='precoLista' align='right'><span id='p2'>Possuímos $qtd_estoque em estoque.</span></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
						echo "<td class='precoLista' align='right'><span id='p2'>Produto não contabilizado em estoque.</span></td>";
						echo "</tr>";
					}
         			
					
         			echo "</table>";
         			echo "</li>";
					
         		}
			}
		}
	}
	
?>