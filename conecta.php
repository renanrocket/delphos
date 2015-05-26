<?php
if(mysqli_connect("localhost", "root", "")){
	$conexaoMaster = mysqli_connect("localhost", "root", "", "rocketso_delphosGestor");
}else{
	$conexaoMaster =null;
}
$db = new mysqli("localhost", "root", "", "rocketso_delphosGestor");
if($conexaoMaster <> null){
	mysqli_query($conexaoMaster,"SET NAMES 'utf8'");
	mysqli_query($conexaoMaster,'SET character_set_connection=utf8');
	mysqli_query($conexaoMaster,'SET character_set_client=utf8');
	mysqli_query($conexaoMaster,'SET character_set_results=utf8');
}

$dbmaster = true;
?>