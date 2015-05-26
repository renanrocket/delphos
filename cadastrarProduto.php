<?php
include "templates/upLogin.inc.php";


//all

extract($_POST);
extract($_GET);
//all
$info = "";
##se post for de op novo
//op nome modelo marca categoria subcategoria descricao valorCompra
//pis cofins icms ipi outros ml1por ml2por ml3por
##se post for de op editar
//op id
##se post for de op visualizar
//op id
if(!isset($op)){
	
	produto();
	
}else{
	
	if($op == "novo" or $op == "editar") {
		
		$qtd_minima = turnZero($qtd_minima);
		$qtd_minima = str_replace(",", ".", $qtd_minima);
		$qtd_estoque = str_replace(",", ".", $qtd_estoque);
		$valorCompra = str_replace(",", ".", $valorCompra);
		$mlpor = str_replace(",", ".", $mlpor);
		$descMaxpor = str_replace(",", ".", $descMaxpor);
		$ponto_valor = str_replace(",", ".", $ponto_valor);
		
		if (!isset($id)) {
			$id = "";
		}
		if (!isset($subcategoria)) {
			$subcategoria = "";
		}
	
		$subcategoria = turnZero($subcategoria);
		$volume = turnZero($volume);
		$qtd_minima = turnZero($qtd_minima);
		$qtd_estoque = turnZero($qtd_estoque);
		!isset($contabilizar)? $contabilizar = 0: $contabilizar = turnZero($contabilizar);
		!isset($busca)? $busca = 0: $busca = turnZero($busca);
		
	
		$valida = true;
	
		//filtro das variaveis
		if (mysqli_num_rows(query("select nome from produto where nome='$nome'")) and $op == "novo") {
			echo "Já existe um produto com esse nome: $nome.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where cod_barra='$cod_barra'")) and $op == "novo" and $cod_barra <> "") {
			echo "Já existe um produto com esse Código de Barras.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where nome='$nome' and id<>'$id'")) and $op == "editar") {
			echo "Já existe um produto com esse nome: $nome.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where cod_barra='$cod_barra' and id<>'$id'")) and $op == "editar" and $cod_barra <> "") {
			echo "Já existe um produto com esse Código de Barras.<br>";
			$valida = false;
		}
	}
	
	if ($op == "novo" and $valida) {
	
		//insercao no db do produto
		$instrucao = "insert into produto ";
		$instrucao .= "(nome, busca, modelo, cod_barra, id_categoria, id_subcategoria, id_marca, ";
		$instrucao .= "descricao, valor_compra, mlpor, descMaxpor, qtd_minima, qtd_estoque, id_volume, ";
		$instrucao .= "contabilizar_estoque, ponto_valor, ponto_troca) values ";
		$instrucao .= "('$nome', '$busca', '$modelo', '$cod_barra', '$categoria', '$subcategoria', ";
		$instrucao .= "'$marca', '$descricao', '$valorCompra', '$mlpor', '$descMaxpor', '$qtd_minima', '$qtd_estoque', '$volume', ";
		$instrucao .= "'$contabilizar', '$ponto_valor', '$ponto_troca')";
		$sql = query($instrucao);
		$chave_principal = mysqli_insert_id($conexao);
	
		//inserindo na tributação
		$instrucao = "insert into produto_tributacao ";
		$instrucao .= "(id_produto, nome, tipo_valor, valor) values";
		for ($i = 0; $i < $qtdTributacao; $i++) {
			$i != 0 ? $instrucao .= ", " : false;
			$tributacaoValor[$i] = str_replace(",", ".", $tributacaoValor[$i]);
			$instrucao .= "('$chave_principal', '$tributacao[$i]', '$tipo_valor[$i]', '$tributacaoValor[$i]')";
		}
		$sql = query($instrucao);
	
		if (mysqli_affected_rows($conexao) > 0) {
	
			$info = "Cadastro do produto efetuado com sucesso.<br>";
	
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
			$acao = "Cadastrou um produto.";
			$tabela_afetada = "produto";
	
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
	
			confirmacaoDB($info, "?op=visualizar&id=".base64_encode($chave_principal));
	
			produto($chave_principal);
			
			
	
		} else {
			include "inc/msgErro.inc";
		}
	} elseif ($op == "editar" and $valida) {
	
		//insercao no db do produto
		$instrucao = "update produto set ";
		$instrucao .= "nome='$nome', busca='$busca', modelo='$modelo', id_categoria='$categoria', id_subcategoria='$subcategoria', ";
		$instrucao .= "id_marca='$marca', descricao='$descricao', valor_compra='$valorCompra', mlpor='$mlpor', descMaxpor='$descMaxpor', ";
		$instrucao .= "contabilizar_estoque='$contabilizar', cod_barra='$cod_barra', qtd_minima='$qtd_minima', qtd_estoque='$qtd_estoque', ";
		$instrucao .= "id_volume='$volume', ponto_valor='$ponto_valor', ponto_troca='$ponto_troca' ";
		$instrucao .= "where id='$id'";
	
		$sql = query($instrucao);
	
		//inserindo na tributação
		$sql = query("delete from produto_tributacao where id_produto='$id'");
		$instrucao = "insert into produto_tributacao ";
		$instrucao .= "(id_produto, nome, tipo_valor, valor) values";
		for ($i = 0; $i < $qtdTributacao; $i++) {
			$i != 0 ? $instrucao .= ", " : false;
			$tributacaoValor[$i] = str_replace(",", ".", $tributacaoValor[$i]);
			$instrucao .= "('$id', '$tributacao[$i]', '$tipo_valor[$i]', '$tributacaoValor[$i]')";
		}
		$sql = query($instrucao);
	
		if (mysqli_affected_rows($conexao) > 0) {
	
			$info = "Alteração do produto efetuado com sucesso.<br>";
	
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
			$acao = "Editou um produto.";
			$tabela_afetada = "produto";
			$chave_principal = $id;
	
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
	
			confirmacaoDB($info, "?op=visualizar&id=".base64_encode($id));
	
			produto($id);
	
		} else {
			include "inc/msgErro.inc";
		}
	
	} elseif ($op == "visualizar") {
	
		$id = base64_decode($id);
	
		produto($id);
	
	} elseif ($op == "deletar") {

		$id = base64_decode($id);

		$sql = query("update produto set status='0' where id='$id'");
		//$sql = query("update produto set busca='0' where id='$id'");

		produto($id);

	} elseif ($op == "inserir") {

		$id = base64_decode($id);

		$sql = query("update produto set status='1' where id='$id'");
		//$sql = query("update produto set busca='1' where id='$id'");
		
		produto($id);

	}
}
	
//end all

include "templates/downLogin.inc.php";
?>