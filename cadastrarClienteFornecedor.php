<?php
include "templates/upLogin.inc.php";
echo "<script type='text/javascript' src='js/cadastrarClienteFiltro.js'></script>";



//all
$id_cliente_fornecedor = $tipo = $tipo2 = $nome = $razao_social = $doc1 = $doc2 =
$data = $email = $telefone1 = $telefone2 = $endereco = $numero = $bairro = $cidade = 
$estado = $cep = $referencia = $observacoes = $latitude = $longitude = $status = null;

extract($_POST);
extract($_GET);
if(isset($_GET["id_cliente_fornecedor"])){
	$id_cliente_fornecedor = base64_decode($_GET["id_cliente_fornecedor"]); 
}

//tipo2 = formato
//tipo = tipoPessoa

if(is_numeric($id_cliente_fornecedor)){
	$cliente_fornecedor = new cliente_fornecedor($id_cliente_fornecedor, $tipo2, $tipo, $nome, $razao_social, $doc1, $doc2,
													$data, $email, $telefone1, $telefone2, $endereco, $numero, $bairro, $cidade,
													$estado, $cep, $referencia, $observacoes, $latitude, $longitude, $status);
}


if(isset($nome) or isset($id_cliente_fornecedor)){
	if(isset($op)){ //operacao desativar ativar visualizar
		if($op=="inativo" and $token == "false"){
			$sql = query("update cliente_fornecedor set status='0' where id='".base64_decode($id_cliente_fornecedor)."'");
			$id_cliente_fornecedor = base64_decode($id_cliente_fornecedor);
		}elseif($op=="inativo" and $token != "false" ){
			$sql = query("update cliente_fornecedor set status='0' where id='".base64_decode($id_cliente_fornecedor)."'");
			$id_cliente_fornecedor = base64_decode($id_cliente_fornecedor);
		}elseif($op=="ativo"){
			$sql = query("update cliente_fornecedor set status='1' where id='".base64_decode($id_cliente_fornecedor)."'");
			$id_cliente_fornecedor = base64_decode($id_cliente_fornecedor);
		}
		if($op!="visualizar"){
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarClienteFornecedor.php?op=visualizar&id_cliente_fornecedor=".base64_encode($id_cliente_fornecedor)."'>";	
		}
	}else{
		if($cliente_fornecedor->id){
			$cliente_fornecedor->update();
			info($cliente_fornecedor->nome." editado com sucesso.");
		}else{
			$cliente_fornecedor->inserir();
			$id_cliente_fornecedor = ultimaId("cliente_fornecedor");
			info($cliente_fornecedor->nome." cadastrado com sucesso.");
		}
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarClienteFornecedor.php?op=visualizar&id_cliente_fornecedor=".base64_encode($id_cliente_fornecedor)."'>";
	}
	
	$cliente_fornecedor->__construct($id_cliente_fornecedor);
	echo $cliente_fornecedor->formulario();
	
} else {
	echo $cliente_fornecedor->formulario();	
}


include "templates/downLogin.inc.php";
?>