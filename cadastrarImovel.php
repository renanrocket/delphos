<?php
include "templates/upLogin.inc.php";


//all
extract($_GET);
extract($_POST);

if(!isset($op)){
	$imovel = new imovel;
	echo $imovel->form();
}else{
	$conn = TConnection::open(ALCUNHA);
	if($op=="novo"){
			
		$cont = count($_POST);
		$array = array_keys($_POST);

		$_POST['valor_venda'] = real2($_POST['valor_venda']);
		$_POST['valor_aluguel'] = real2($_POST['valor_aluguel']);

		$sql = new TSqlInsert;
		$sql->setEntity('imovel');

		for($i=$check=0; $i<$cont; $i++){
			if($array[$i]=='op'){
				$i++;
				$check = true;
			}
			if($check){
				$sql->setRowData($array[$i], $_POST[$array[$i]]);	
			}
		}
		
		
		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($conn->lastInsertId());

		}else{

			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel(null, $_POST['nome']);
		}
	}elseif($op=='novaAvaliacao'){

		$cont = count($_POST);
		$array = array_keys($_POST);

		$_POST['valor'] = real2($_POST['valor']);
		if($_POST['data']){
			$_POST['data'] = formataDataInv($_POST['data']);
		}else{
			$_POST['data'] = date('Y-m-d');
		}

		$sql = new TSqlInsert;
		$sql->setEntity('imovel_avaliacao');

		for($i=$check=0; $i<$cont;$i++){
			if($array[$i]=='op'){
				$i++;
				$check=true;
			}
			if($check){
				$sql->setRowData($array[$i], $_POST[$array[$i]]);
			}
		}
		
		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);
			

		}else{

			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
			
		}
		
	}elseif($op=='deletarAvaliacao'){

		$avaliacao = base64_decode($avaliacao);
		$id_imovel = base64_decode($id_imovel);
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id','=',$avaliacao));

		$sql = new TSqlDelete;
		$sql->setEntity('imovel_avaliacao');
		$sql->setCriteria($criterio);
		
		
		
		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);

		}else{


			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
		}


	}elseif($op=='novoInvestimento'){
		
		$cont = count($_POST);
		$array = array_keys($_POST);

		$_POST['valor'] = real2($_POST['valor']);
		if($_POST['data']){
			$_POST['data'] = formataDataInv($_POST['data']);
		}else{
			$_POST['data'] = date('Y-m-d');
		}

		$sql = new TSqlInsert;
		$sql->setEntity('imovel_capital_investido');

		for($i=$check=0; $i<$cont;$i++){
			if($array[$i]=='op'){
				$i++;
				$check=true;
			}
			if($check){
				$sql->setRowData($array[$i], $_POST[$array[$i]]);
			}
		}

		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);
			

		}else{

			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
			
		}

	}elseif($op=='deletarCapitalInvestido'){

		$capital = base64_decode($capital);
		$id_imovel = base64_decode($id_imovel);
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id','=',$capital));

		$sql = new TSqlDelete;
		$sql->setEntity('imovel_capital_investido');
		$sql->setCriteria($criterio);
		
		
		
		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);

		}else{


			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
		}


	}elseif($op=='novoTag'){
		
		$cont = count($_POST);
		$array = array_keys($_POST);

		$sql = new TSqlInsert;
		$sql->setEntity('imovel_caracteristicas');

		for($i=$check=0; $i<$cont;$i++){
			if($array[$i]=='op'){
				$i++;
				$check=true;
			}
			if($check){
				$sql->setRowData($array[$i], $_POST[$array[$i]]);
			}
		}

		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);
			

		}else{

			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
			
		}

	}elseif($op=='deletarTag'){

		$tag = base64_decode($tag);
		$id_imovel = base64_decode($id_imovel);
		$criterio = new TCriteria;
		$criterio->add(new TFilter('id','=',$tag));

		$sql = new TSqlDelete;
		$sql->setEntity('imovel_caracteristicas');
		$sql->setCriteria($criterio);
		
		
		
		if($result = $conn->query($sql->getInstruction())){
			
			$imovel = new imovel($id_imovel);

		}else{


			$info = new info;
			$info->msg = OPERACAO_ERRO;
			$info->cor = 'red';
			$info->display = null;
			$info->class = 'operacao_erro';
			echo $info->getInfo();

			$imovel = new imovel($id_imovel);
		}


	}

	echo $imovel->form();
}

	
//end all

include "templates/downLogin.inc.php";
?>