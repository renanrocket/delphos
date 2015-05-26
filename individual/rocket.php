<?php
if(mysqli_connect("localhost", "root", "")){
	$conexao = mysqli_connect("localhost", "root", "", "rocketso_padrao");
}else{
	$conexao =null;
}
$db = new mysqli("localhost", "root", "", "rocketso_padrao");
if($conexao <> null){
	mysqli_query($conexao,"SET NAMES 'utf8'");
	mysqli_query($conexao,'SET character_set_connection=utf8');
	mysqli_query($conexao,'SET character_set_client=utf8');
	mysqli_query($conexao,'SET character_set_results=utf8');
}


?>