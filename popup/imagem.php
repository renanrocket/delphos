<?php

include_once "../templates/upLoginImp.inc.php";

//all
extract($_GET);
if(isset($_GET["img"])){
	echo "<img src='../$img'>";	
}elseif(isset($_GET["id"]) and isset($_GET["atributo"]) and isset($_GET["referencia"])){
	$sql = query("select * from $atributo where $referencia='$id'");
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		extract(mysqli_fetch_assoc($sql));
		echo "<img src='../$imagem'>";
	}
}


//end all

include_once "../templates/downLoginImp.inc.php";
?>