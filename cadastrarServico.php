<?php
include "templates/upLogin.inc.php";

extract($_GET);
extract($_POST);

if($_POST or $_GET){
	if(isset($_GET["id"])){
		$idServico = base64_decode($id);
	}
	
	//caso o usuario recarrege a operação novo e evitar de cadastrar o mesmo produto 2 vezes
	if(!isset($nome)){
		$nome = null;
	}
	$sql = query("select * from servico where nome='$nome'");
	if(mysqli_num_rows($sql)>0 and $op=="novo"){
		extract(mysqli_fetch_assoc($sql));
		$op="visualizar";
	}
	
	
	if($op == "novo"){//operação novo
		if(!isset($SUBCATEGORIA)){
			$SUBCATEGORIA = 0;
		}
		$subcategoria = turnZero($SUBCATEGORIA);
		//inserindo no servico
		$instrucao  = "insert into servico (nome, id_categoria, id_subcategoria, descricao) ";
		$instrucao .= "values ";
		$instrucao .= "('$nome', '$categoria', '$subcategoria', '$descricao')";
		$sql = query($instrucao);
		
		if(mysqli_affected_rows($conexao)==1){
			$idServico = mysqli_insert_id($conexao);
			
			//inserindo no servico_produto
			$instrucao  = "insert into servico_produto ";
			$instrucao .= "(id_servico, id_produto, produto, qtd, id_produto_volume, subTotal) ";
			$instrucao .= "values ";
			
			for($i = 0; $i<$produtoQtd; $i++){
				$quantidade[$i] = str_replace(",", ".", $quantidade[$i]);
				$produtoSubTotal[$i] = str_replace(",", ".", $produtoSubTotal[$i]);
				$i>0 ? $instrucao .= ", " : false;
				$instrucao .= "('$idServico', '".$id_produto[$i]."', '".$produto[$i]."', '".$quantidade[$i]."', '".$volume[$i]."', '".$produtoSubTotal[$i]."')";
			}
			$sql = query($instrucao);
			
			
			//inserindo na comissao
			$instrucao  = "insert into servico_comissao ";
			$instrucao .= "(id_servico, usuario_tabela, usuario_valor, preco_tipo, preco_valor, preco_por, preco_max, penalidade) ";
			$instrucao .= "values ";
			for($i=0; $i<$comissaoQtd; $i++){
				$i>0 ? $instrucao .= ", " : false;
				
				$precoValorReal[$i] = real($precoValorReal[$i], true);
				$precoValorPor[$i] = real($precoValorPor[$i], true);
				$precoValorMax[$i] = real($precoValorMax[$i], true);
				$penalidade[$i] = real($penalidade[$i], true);
				
				$instrucao .= "('$idServico', '".$usuarioTabela[$i]."', '".$usuarioValor[$i]."', '".$precoTipo[$i]."', ";
				$instrucao .= "'".$precoValorReal[$i]."', '".$precoValorPor[$i]."', '".$precoValorMax[$i]."', '".$penalidade[$i]."')";
			}
			$sql = query($instrucao);
			
			//inserindo nos preços
			$instrucao  = "insert into servico_preco ";
			$instrucao .= "(id_servico, limite, valor) values ";
			for($i=0; $i<$precoQtd; $i++){
				$preco[$i] = str_replace(",", ".", $preco[$i]);
				$i>0 ? $instrucao .= ", " : false;
				$instrucao .= "('$idServico', '".$quantidadePreco[$i]."', '".$preco[$i]."')";
			}
			$sql = query($instrucao);
		}

		
		confirmacaoDB("Serviço cadastrado com sucesso.", "cadastrarServico.php?id=".base64_encode($idServico)."&op=visualizar");
		
	}elseif($op=="editar"){
		$idServico = base64_decode($_GET["id"]);
		
		//inserindo no servico
		!isset($subcategoria)? $subcategoria = null: false;
		$instrucao  = "update servico set nome='$nome', id_categoria='$categoria', id_subcategoria='$subcategoria', descricao='$descricao' ";
		$instrucao .= "where id='$idServico'";
		$sql = query($instrucao);
		
		$sql = query("delete from servico_produto where id_servico='$idServico'");
		
		//inserindo no servico_produto
		$instrucao  = "insert into servico_produto ";
		$instrucao .= "(id_servico, id_produto, produto, qtd, id_produto_volume, subTotal) ";
		$instrucao .= "values ";
		
		for($i = 0; $i<$produtoQtd; $i++){
			$quantidade[$i] = str_replace(",", ".", $quantidade[$i]);
			$produtoSubTotal[$i] = str_replace(",", ".", $produtoSubTotal[$i]);
			$i>0 ? $instrucao .= ", " : false;
			$instrucao .= "('$idServico', '".$id_produto[$i]."', '".$produto[$i]."', '".$quantidade[$i]."', '".$volume[$i]."', '".$produtoSubTotal[$i]."')";
		}
		$sql = query($instrucao);
		
		$sql = query("delete from servico_comissao where id_servico='$idServico'");
		
		//inserindo na comissao
		$instrucao  = "insert into servico_comissao ";
		$instrucao .= "(id_servico, usuario_tabela, usuario_valor, preco_tipo, preco_valor, preco_por, preco_max, penalidade) ";
		$instrucao .= "values ";
		for($i=0; $i<$comissaoQtd; $i++){
			$i>0 ? $instrucao .= ", " : false;
			
			$precoValorReal[$i] = real($precoValorReal[$i], true);
			$precoValorPor[$i] = real($precoValorPor[$i], true);
			$precoValorMax[$i] = real($precoValorMax[$i], true);
			$penalidade[$i] = real($penalidade[$i], true);
			
			$instrucao .= "('$idServico', '".$usuarioTabela[$i]."', '".$usuarioValor[$i]."', '".$precoTipo[$i]."', ";
			$instrucao .= "'".$precoValorReal[$i]."', '".$precoValorPor[$i]."', '".$precoValorMax[$i]."', '".$penalidade[$i]."')";
		}
		$sql = query($instrucao);
		
		$sql = query("delete from servico_preco where id_servico='$idServico'");
		
		//inserindo nos preços
		$instrucao  = "insert into servico_preco ";
		$instrucao .= "(id_servico, limite, valor) values ";
		for($i=0; $i<$precoQtd; $i++){
			$preco[$i] = str_replace(",", ".", $preco[$i]);
			$i>0 ? $instrucao .= ", " : false;
			$instrucao .= "('$idServico', '".$quantidadePreco[$i]."', '".$preco[$i]."')";
		}
		$sql = query($instrucao);
		
		info("Serviço editado com sucesso!");
		
	}

	servicoForm($idServico, "editar");
	
	
}else{
	servicoForm();
}


include "templates/downLogin.inc.php";
?>

