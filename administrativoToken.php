<?php
//php
include "templates/upLogin.inc.php";


//all

if($_POST){
	
	extract($_POST);
	$valida = true;
	
	if(strlen($validade)<10){
		echo "Data inválida.<br>";
		$valida = false;
	}
	if(strlen($validade_hora)<5){
		echo "Hora inválida.<br>";
		$valida = false;
	}
	
	if($valida){
		
		$token = rand(100000, 999999);
		
		$instrucao = "select id from usuario where login='".$_COOKIE["login"]."'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		$data_criado = date('Y-m-d H:i:s');
		$data_validade = formataDataInv($validade)." ".$validade_hora.":00";
		
		$instrucao = "insert into tokens (token, id_usuario, data_criado, data_validade, vezes_permitido) values";
		$instrucao .= "('".md5($token)."', '$id', '$data_criado', '$data_validade', '$vezes')";
		
		$sql = query($instrucao);
		if(mysqli_affected_rows($conexao)>0){
			echo "Token criado com sucesso.<br>";
			echo "Valor: <b style='color:red;'>$token</b>";
		}
		
		
		
	}else{
		echo historico("-1");
	}
	
}else{
	
	$data = formataData(date('Y-m-d'));
	$hora = date("H:i", strtotime('+ 1 hour'));
	echo "<center>";
	echo "<form name='formulario' method='post' action='administrativoToken.php' enctype='multipart/form-data' style='width:300px'>";
	echo "Deseja criar token válido até quando?<br>";
	echo "Data ".inputData("formulario", "validade", null, $data)."
			Hora <input type='text' value='$hora' name='validade_hora' size='3' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'><br>";
	echo "Quantidade de vezes permitido usar esse token: <input type='text' name='vezes' value='1' size='2' onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'><br>";
	echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
	echo "</center>";
	
}

//end all

include "templates/downLogin.inc.php";
?>