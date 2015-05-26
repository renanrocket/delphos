<?php
if(mysqli_connect("localhost", "rocketso_root", "ROCKETadmin99")){
	$conexao = mysqli_connect("localhost", "rocketso_root", "ROCKETadmin99", "rocketso_rocket");
}else{
	$conexao =null;
}

$db = new mysqli("localhost", "rocketso_root", "ROCKETadmin99", "rocketso_rocket");

if($conexao <> null){
	mysqli_query($conexao,"SET NAMES 'utf8'");
	mysqli_query($conexao,'SET character_set_connection=utf8');
	mysqli_query($conexao,'SET character_set_client=utf8');
	mysqli_query($conexao,'SET character_set_results=utf8');
}

unset($dbmaster);

?>