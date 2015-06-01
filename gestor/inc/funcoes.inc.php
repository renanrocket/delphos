<?php
define('ALCUNHA', 'rocket');

//carregar automaticamente as classes
function __autoload($classe){
	$class_file = "class/{$classe}.class.php";
	$class_file_ado = "app.ado/{$classe}.class.php";
	$pasta = "";
	for($i=0; $i<4; $i++){
		if($i>0){
			$pasta .= "../";
		}
		if(file_exists($pasta.$class_file)){
			include_once $pasta.$class_file;
			break;
		}elseif(file_exists($pasta.$class_file_ado)){
			include_once $pasta.$class_file_ado;
			break;
		}
	}
	
}

//recebe o cookie do login e retorna o nome dele (renan)
function getNomeCookieLogin($cookie, $completo = true, $conn = ALCUNHA) {
	
	if ($cookie) {
		$conn = TConnection::open($conn);
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter("login", "=", $cookie));
		
		$sql = new TSqlSelect;
		$sql->setEntity("usuario");
		$sql->addColumn('nome');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
		}

		//deletar esse codigo apois migração do app.ado
		if(isset($conexao)){
			$sql = query("select nome from usuario where login='$cookie'");
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
		}
		
	} else {
		$nome = 0;
	}	
	if(!$completo){
		$nome = explode(" ", $nome);
		$cont = count($nome);
		$nome = $nome[0]." ".$nome[$cont-1];
	}

	return $nome;

}

//datas do tipo Y-m-d ou Y-m-d H:i:s
function subtrairDatas($data_inicio, $data_termino , $modo = "dias"){
	
	if(strripos($data_inicio, " ")!=false and strripos($data_termino, " ")!=false){
		$dataI = explode(" ", $data_inicio);
		$dataF = explode(" ", $data_termino);

		$dataIymd = explode("-", $dataI[0]);
		$dataFymd = explode("-", $dataF[0]);

		$dataIhis = explode(":", $dataI[1]);
		$dataFhis = explode(":", $dataF[1]);
		
		$data_inicial = mktime($dataIhis[0], $dataIhis[1], $dataIhis[2], $dataIymd[1], $dataIymd[2], $dataIymd[0]);
		$data_final = mktime($dataFhis[0], $dataFhis[1], $dataFhis[2], $dataFymd[1], $dataFymd[2], $dataFymd[0]);
	}else{
		$dataI = explode("-", $data_inicio);
		$dataF = explode("-", $data_termino);
		
		$data_inicial = mktime(0, 0, 0, $dataI[1], $dataI[2], $dataI[0]);
		$data_final = mktime(0, 0, 0, $dataF[1], $dataF[2], $dataF[0]);
	}
	
	if($modo == "min"){
		$denominador = 58.75;
	}elseif($modo == "horas"){
		$denominador = 3525;
	}elseif($modo=="dias"){
		$denominador = 84600;
	}elseif($modo=="anos"){
		$denominador = 30794400;
	}
	
	return  floor(($data_final - $data_inicial)/$denominador);
	//return  $data_final." - ".$data_inicial." / ".$denominador;
}


//funcao q seleciona o atributo da tabela a partir de um resultado do PK
//pk eh o numero do cont
//nomeTabela eh o nome da tabela a qual se refere
//atributo eh o numero do atributo na sequencia q segue a tabela
function registro($pk, $nomeTabela, $atributo_exibir, $atributo_de_busca = "id", $conn = ALCUNHA) {
	

	$conn = TConnection::open($conn);
		
	$criterio = new TCriteria;
	$criterio->add(new TFilter($atributo_de_busca, "=", $pk));
	
	$sql = new TSqlSelect;
	$sql->setEntity($nomeTabela);
	$sql->addColumn($atributo_exibir);
	$sql->setCriteria($criterio);
		
	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		return $$atributo_exibir;
	}else{
		return '';
	}

	//deletar esse codigo apois migração do app.ado
	if(isset($conexao)){
		$sql = query("select * from $nomeTabela where $atributo_de_busca='$pk'");
		$reg = mysqli_fetch_assoc($sql);
		return $reg[$atributo_exibir];
	}
	//fim
}


//recebe o cookie do login e retorna o id dele (renan)
function getIdCookieLogin($cookie, $conn = ALCUNHA) {

	if ($cookie) {
		$conn = TConnection::open($conn);
		
		$criterio = new TCriteria;
		$criterio->add(new TFilter("login", "=", $cookie));
		
		$sql = new TSqlSelect;
		$sql->setEntity("usuario");
		$sql->addColumn('id');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
		}else{
			$id = 0;
		}

		//deletar esse codigo apois migração do app.ado
		if(isset($conexao)){
			$sql = query("select id from usuario where login='$cookie'");
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
		}
	} else {
		$id = 0;
	}

	return $id;

}

//funcao para criar uma input data com um botao de calendario do lado
#----------------------------- nescessario nomear o formulario e por ele na variavel NOMEFORMULARIO ---------------------------#
function inputData($nomeFormulario, $nomeInput, $posicao, $valorInput = null, $classInput = null, $id = null) {
	
	$cod = "<script type='text/javascript'>\n";
	$cod .= "$(function() {\n";
    			$cod .= "$(\"input[name='".$nomeInput."']\").datepicker();\n";
			$cod .= "});\n";
	$cod .= "</script>";
	$cod .= "<input type='text' name='$nomeInput' value='$valorInput' placeholder='DD/MM/AAAA' class='inputData $classInput' ".mascara("Data", "10").">";
	
	return $cod;
}

//funcao formataDataInv, formata a data para forma do banco de dados. Recebe uma variavel assim DD/MM/YYYY HH:II:SS
//e a transforma assim: YYYY-MM-DD HH:II:SS
function formataDataInv($data) {
	if ($data) {
		$data = explode("/", $data);
		if (strstr($data[2], " ", true)) {
			$Data = explode(" " . $data[2]);
			$data = $Data[0] . "-" . $data[1] . "-" . $data[0] . " " . $Data[1];
		} else {
			$data = $data[2] . "-" . $data[1] . "-" . $data[0];
		}
	} else {
		$data = "0000-00-00";
	}
	return $data;
}

//funcao data, formata a data. Recebe uma variavel assim: YYYY-MM-DD HH:II:SS
//e a transforma assim: DD/MM/YYYY HH:II:SS
function formataData($DATA, $somenteHora = false, $somenteData = false) {
	if ($DATA and $DATA <> "0000-00-00") {
		$data = explode("-", $DATA);
		if (strstr($data[2], " ") == True) {
			$Data = explode(" ", $data[2]);
			if($somenteHora){
			    return $Data[1];
			}elseif($somenteData){
				return $Data[0] . "/" . $data[1] . "/" . $data[0];
			}else{
                return $Data[0] . "/" . $data[1] . "/" . $data[0] . " " . $Data[1];    
			}
			
		} else {
			return $data[2] . "/" . $data[1] . "/" . $data[0];
		}
	} else {
		return "";
	}
}

//funcao para selecionar a mascara nas input text
##  Tipos de mascara ##
/*
 * Integer Telefone TelefoneCall Cpf Cep Cnpj Romanos Data Hora Valor Valor2 Valor3 Area Placa
 */
function mascara($mascara, $tamanho = null, $codExtra = null , $onKeyDown = null, $onKeyPress = null, $onKeyUp = null) {
	$cod = "onKeyDown='Mascara(this,$mascara); $onKeyDown' onKeyPress='Mascara(this,$mascara); $onKeyPress' onKeyUp='Mascara(this,$mascara); $onKeyUp'";
	switch ($mascara) {
		case 'Telefone':
			$cod .= "placeholder='(00) 00000.0000'";
			break;
		case 'Cpf':
			$cod .= "placeholder='000.000.000-00'";
			break;
		case 'Cep':
			$cod .= "placeholder='00000-000'";
			break;
		
		default:
			# code...
			break;
	}
	
	if ($tamanho) {
		$cod .= " maxlength='$tamanho'";
	}
	if ($codExtra) {
		$cod .= " " . $codExtra;
	}

	return $cod;
}



?>