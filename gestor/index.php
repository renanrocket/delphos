<?php
	/*
	header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");        // Expired already 
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");      // good for HTTP/1.1 
	header("Pragma: no-cache");
	define("WP_MAX_MEMORY_LIMIT", "512M");
	*/
	
	

	$loginGestor = $senhaGestor = null;
	$end = "indexGestor.php";
	extract($_COOKIE);
	extract($_POST);
	extract($_GET);


	function formLogin($loginGestor, $end){
		$cod = "<form class='form' method='post' action='index.php' enctype='multipart/form-data' onsubmit='return filtro();'>";
		$cod.= "<input type='hidden' name='direcionar' value='$end'>";
		$cod.= "<div class='column'>";
			$cod.= "<label>Dados de Acesso";
			$cod.= "<input type='text' name='loginGestor' placeholder='Login'>";
			$cod.= "<input type='password' name='senhaGestor' placeholder='Senha'></label>";
			$cod.= "<label><input type='submit' value='Logar'></label>";
		$cod.= "</div>";
		return $cod;
	}


	if($loginGestor and $senhaGestor){
		
		include_once "template/indexUp.inc.php";

		$conn = TConnection::open('rocket');

		$criterio = new TCriteria;
		$criterio->add(new TFilter('login', '=', $loginGestor));
		$criterio->add(new TFilter('senha', '=', md5($senhaGestor)));

		$sql = new TSqlSelect;
		$sql->setEntity('usuario');
		$sql->addColumn('nome');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());
		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
			setcookie("login", $loginGestor);
			setcookie("senha", md5($senhaGestor));
			setcookie("id_empresa", 1);
			setcookie("chat", "1");

			$sql = new TSqlUpdate;
			$sql->setEntity('usuario');
			$sql->setRowData('online', 1);
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			
			echo "<div class='form'>";
				echo "<div class='column'>";
					echo "<label>Você está conectado a<br><b>GESTOR</b>, ";
					echo "como <b>$nome.</b></label>";
					echo "<label><img src='img/loading_bar.gif'></label>";
					echo "Iniciando sistema.";
				echo "</div>";
			echo "</div>";
			echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=$end'>";
			include_once "template/indexDown.inc.php";

		}else{
			include_once "template/indexUp.inc.php";
			echo formLogin($loginGestor, $end);
			include_once "template/indexDown.inc.php";
		}
	}else{
		include_once "template/indexUp.inc.php";
		echo formLogin($loginGestor, $end);
		include_once "template/indexDown.inc.php";
	}


	
?>