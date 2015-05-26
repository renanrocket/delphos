<?php
include "templates/upLogin.inc.php";

extract($_GET);

if(!isset($_GET["busca"])){
	
	echo "<form method='get' action='pesquisaMatricula.php' enctype='multipart/form-data'>";
		echo "Digite o nome do cliente:<br>";
		echo "<input type='text' name='busca' style='width:auto;'><br>";
		echo "Status:<br>";
		echo "<select name='status' style='width:auto;'>";
			echo "<option value='3'>Todos os Status</option>";
			echo "<option value='1'>Ativo</option>";
			echo "<option value='0'>Inativo</option>";
			echo "<option value='2'>Em Pausa</option>";
		echo "</select><br>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
	
}else{

	$conn = TConnection::open(ALCUNHA);

	$criterio = new TCriteria;
	$criterio->add(new TFilter('nome', 'like', "%$busca%"));
	$criterio->setProperty('order', 'nome');

	$sql = new TSqlSelect;
	$sql->setEntity('cliente_fornecedor');
	$sql->addColumn('id');
	$sql->setCriteria($criterio);

	$criterio1 = new TCriteria;
	$criterio1->add(new TFilter('id_cliente', '=', $busca), TExpression::OR_OPERATOR);
	$criterio1->add(new TFilter('id', '=', $busca), TExpression::OR_OPERATOR);
	$criterio2 = new TCriteria;
	$criterio2->add(new TFilter('id_cliente', '= any', '('.$sql->getInstruction().')'));

	$criterio4 = new TCriteria;
	$criterio4->add($criterio1, TExpression::OR_OPERATOR);
	$criterio4->add($criterio2, TExpression::OR_OPERATOR);
	$criterio = new TCriteria;
	$criterio->add($criterio4);
	
	if($status!=3){
		$criterio3 = new TCriteria;
		$criterio3->add(new TFilter('status', '=', $status ));
		$criterio->add($criterio3);
	}
	



	$sql = new TSqlSelect;
	$sql->setEntity('matricula');
	$sql->addColumn('*');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	
	$array = null;
	for($i = $total = 0; $i<$result->rowCount(); $i++){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		$array[] = formVisualizar("cadastrarMatricula.php", $id);
		$array[] = showImagemClienteFornecedor($id_cliente, 1 , 1);
		$array[] = is_numeric($id_cliente) ? formVisualizar("cadastrarClienteFornecedor.php", $id_cliente, "id_cliente_fornecedor")." ".registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
		$array[] = $telefone;
		switch ($status) {
			case '0':
				$array[] = "Inativo";
				break;
			case '1':
				$array[] = "Ativo";
				break;
			
			default:
				$array[] = "Em pausa";
				break;
		}
	}
	$tabela = new tabela;
	$tag = array("ID", "Foto", "Contato", "Telefone" , "Status");
	$titulo = "Buscar por $busca";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	echo $tabela->showTabela();
/*
	if($status==3){
		$instrucao = "select * from matricula where (id_cliente like '%$busca%' ";
		$instrucao .= "or id_cliente = any (select id from cliente_fornecedor where nome like '%$busca%' order by nome))";	
	}else{
		$instrucao = "select * from matricula where (id_cliente like '%$busca%' ";
		$instrucao .= "or id_cliente = any (select id from cliente_fornecedor where nome like '%$busca%' order by nome)) and status='$status'";
	}
	
	$sql = query($instrucao);
	
	$array = null;
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$array[] = formVisualizar("cadastrarMatricula.php", $id);
		$array[] = showImagemClienteFornecedor($id_cliente, 1 , 1);
		$array[] = is_numeric($id_cliente) ? formVisualizar("cadastrarClienteFornecedor.php", $id_cliente, "id_cliente_fornecedor")." ".registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
		$array[] = $telefone;
		switch ($status) {
			case '0':
				$array[] = "Inativo";
				break;
			case '1':
				$array[] = "Ativo";
				break;
			
			default:
				$array[] = "Em pausa";
				break;
		}
		
	}

	$tabela = new tabela;
	$tag = array("ID", "Foto", "Contato", "Telefone" , "Status");
	$titulo = "Buscar por $busca";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	echo $tabela->showTabela();
	*/
	
}

include "templates/downLogin.inc.php";
?>

