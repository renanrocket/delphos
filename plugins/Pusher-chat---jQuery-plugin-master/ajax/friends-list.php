<?php

include_once "../../../inc/funcoes.inc.php";
include "../../../individual/rocket.php";
echo "{";

$sql = query("select id, login from usuario where id>1 and status='Ativo'");
for($i=0; $i<mysqli_num_rows($sql); $i++){
	extract(mysqli_fetch_assoc($sql));

	if($_COOKIE["id_empresa"]!=1 or getIdCookieLogin($_COOKIE["login"])!=$id){//evitar de aparecer o seu proprio perfil na friend list
		echo "\"1_$id\": [\"$login Rocket Solution\", ";
		$sql_imagem = query("select imagem, miniatura from usuario_imagem where id_usuario='$id'");
		if(mysqli_num_rows($sql_imagem)){
			extract(mysqli_fetch_assoc($sql_imagem));
			echo "\"$miniatura\", ";
			//$link = "http://delphos.rocketsolution.com.br/$imagem";
			$link = "http:///www.rocketsolution.com.br";
		}else{
			echo "\"img/rocket.png\", ";
			$link = "http://www.rocketsolution.com.br";
		}

		if($_COOKIE["id_empresa"]==1){//se estiver logado no sistema da rocket
			echo "\"http://delphos.rocketsolution.com.br/cadastrarUsuario.php?op=visualizar&ID=".base64_encode($id)."\"], \n";
		}else{
			echo "\"$link\"], \n";
		}
	}
}

if(isset($_COOKIE["id_empresa"])){
	//includes do php para todas as paginas
	file_exists("../../../conecta.php") ? include "../../../conecta.php" : $conexaoMaster = null;
	
	$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'", $conexaoMaster);
	extract(mysqli_fetch_assoc($sql));
	include "../../../".$conectasrc;
}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}
if($_COOKIE["id_empresa"]==1){//se estiver logado no sistema da rocket

	$sql_conecta = query("select id as idEmpresa, nome, conectasrc from cliente where id>1 and status='1'", $conexaoMaster);
	for($j=0; $j<mysqli_num_rows($sql_conecta); $j++){
		extract(mysqli_fetch_assoc($sql_conecta));
		include "../../../".$conectasrc;
		$sql = query("select id, login from usuario where id>1 and status='Ativo'");
		for($i=0; $i<mysqli_num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			if($i>0 or $j>0){
				echo ", \n";
			}
			echo "\"".$idEmpresa."_$id\": [\"$login $nome\", ";
			$sql_imagem = query("select miniatura from usuario_imagem where id_usuario='$id'");
			if(mysqli_num_rows($sql_imagem)){
				extract(mysqli_fetch_assoc($sql_imagem));
				echo "\"$miniatura\", ";
			}else{
				echo "\"img/rocket.png\", ";    		
			}
			echo "\"http://delphos.rocketsolution.com.br/cadastrarUsuario.php?empresa=".base64_encode($idEmpresa)."&op=visualizar&ID=".base64_encode($id)."\"]";
			//echo "\"http://delphos.rocketsolution.com.br\"], \n";
		}	
	}

}else{
	$sql = query("select id, login from usuario where id>1 and status='Ativo'");
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		extract(mysqli_fetch_assoc($sql));
		if($i>0){
			echo ", \n";
		}
		echo "\"".$_COOKIE["id_empresa"]."_$id\": [\"$login\", ";
		$sql_imagem = query("select miniatura from usuario_imagem where id_usuario='$id'");
		if(mysqli_num_rows($sql_imagem)){
			extract(mysqli_fetch_assoc($sql_imagem));
			echo "\"$miniatura\", ";
		}else{
			echo "\"img/rocket.png\", ";    		
		}
		echo "\"http://delphos.rocketsolution.com.br/cadastrarUsuario.php?op=visualizar&ID=".base64_encode($id)."\"]";
		//echo "\"http://delphos.rocketsolution.com.br\"], \n";
	}
}

echo "\n}";







?>