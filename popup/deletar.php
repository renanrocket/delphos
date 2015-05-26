<?php

include_once "../templates/upLoginImp.inc.php";


extract($_GET);

if (file_exists("../".$arq)){
	
	if(isset($confirma)){
		unlink("../".$arq);
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "window.opener.location.reload();";
		echo "window.close()";
		echo "</script>";
	}else{
		echo "<form method='get' action='deletar.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='arq' value='$arq'>";
		echo "<input type='hidden' name='confirma' value='true'>";
		echo "Você tem certeza que deseja deletar este arquivo?<br>";
		echo "$arq<br>";
		echo "<input type='submit' class='btnDeletar' value='Sim'>";
		echo "</form>";
	}
	
}else{
	echo "Arquivo não encontrado.";
}


include_once "../templates/downLoginImp.inc.php";
	
?>