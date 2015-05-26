<?php
include "templates/upLogin.inc.php";

if(!isset($_POST["id"]) or !isset($_GET["id"])){
	$id = $tipo_item = $obrigatoriedade = $nome = $qtdItens =  $select_sub_item = $status = null;	
}

extract($_POST);
extract($_GET);

$osAtributo = new ordem_servico_atributo_padrao($id, $tipo_item, $obrigatoriedade, $nome, $qtdItens, $select_sub_item, $status);

if(isset($nome) and isset($id)){//op novo ou editar
	if(is_numeric($id)){//op editar
		$osAtributo->update();
	}else{
		$osAtributo->inserir();
		$osAtributo = new ordem_servico_atributo_padrao(null, null, null, null, null, null, null);
	}
}elseif(isset($id) && isset($op)){//op ativar ou inativar
	if($op=="ativo"){
		$osAtributo->ativar(base64_decode($id));
	}elseif($op=="inativo"){
		$osAtributo->inativar(base64_decode($id));
	}
}elseif(isset($id)){//apenas visualizar
	$sql = query("select * from ordem_servico_atributo_padrao where id='$id'");
	extract(mysqli_fetch_assoc($sql));
	$idAtributo = $id;
	$sql = query("select * from ordem_servico_atributo_padrao_sub where id_ordem_servico_atributo_padrao='$idAtributo'");
	$qtdItens = mysqli_num_rows($sql);
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		extract(mysqli_fetch_assoc($sql));
		$ordem_servico_padrao_sub[] = $valor;
	}
	$osAtributo = new ordem_servico_atributo_padrao($idAtributo, $tipo_item, $obrigatoriedade, $nome, $qtdItens, $select_sub_item, $status);
}
echo $osAtributo->formulario();


include "templates/downLogin.inc.php";
?>