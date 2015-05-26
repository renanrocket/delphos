<?php
	if(!file_exists("chat/css/style.css")){
		$pasta1 = '../';
		$pasta2 = '2';
	}else{
		$pasta1 = $pasta2 = null;
	}
	echo '<link href="'.$pasta1.'chat/css/style.css" rel="stylesheet" type="text/css" />';
	echo '<script type="text/javascript" src="'.$pasta1.'chat/js/functions'.$pasta2.'.js"></script>';
	echo '<script type="text/javascript" src="'.$pasta1.'chat/js/chat'.$pasta2.'.js"></script>';
	

	if(isset($_COOKIE["chat"])){
		if($_COOKIE["chat"]==1){
			echo "<div id='contatos'>";
			echo "<script type='text/javascript'>
				$(function (){
					$('.close').show();
			    	$('.open').hide();
			    	$('#contatos-ul').show(100);
			    	$('#contatos').removeClass(\"fixar\");
			    	$('body').animate({
			    		paddingRight: \"206px\"
			    	}, 100);
				});
			</script>";
		}else{
			echo "<div id='contatos' class='fixar'>";
			echo "<script type='text/javascript'>
				$(function (){
					$('.close').hide();
			    	$('.open').show();
			    	$('#contatos-ul').hide(100);
			    	$('#contatos').addClass(\"fixar\");
			    	$('body').animate({
			    		paddingRight: \"0px\"
			    	}, 100);
				});
			</script>";
		}
	}else{
		echo "<div id='contatos' class='fixar'>";
		echo "<script type='text/javascript'>
				$(function (){
					$('.close').hide();
			    	$('.open').show();
			    	$('#contatos-ul').hide(100);
			    	$('#contatos').addClass(\"fixar\");
			    	$('body').animate({
			    		paddingRight: \"0px\"
			    	}, 100);
				});
			</script>";
	}


?>

<div class="contatos-topo"><span class="close">&#x25BC;</span><span class="open">&#x25B2;</span><div>Contatos</div></div>
	<ul id='contatos-ul'>
	<?php
		
		/*include_once "../inc/funcoes.inc.php";
		
		if(isset($_COOKIE["id_empresa"])){
			//includes do php para todas as paginas
			$conn = TConnection::open("gestor");
		
			$criterio = new TCriteria;
			$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

			$sql = new TSqlSelect;
			$sql->setEntity('cliente');
			$sql->addColumn('alcunha');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			
			if($result->rowCount()){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				define("ALCUNHA", $alcunha);
				
			}

		}else{
			echo "Impossível se conectar com o banco de dados.";
			die;
		}*/

		function getDataOffline($data){
			$data = subtrairDatas(date('Y-m-d H:i:s'), $data, 'min');
			$data = $data*-1;
			$retorno[0] = 'm';
			if($data>=60){
				$data = $data/60;
				$retorno[0] = 'h';
			}
			if($data>=24 and $retorno[0]=='h'){
				$data = $data/24;
				$retorno[0] = 'd';
			}
			if($data>=30 and $retorno[0]=='d'){
				$data = $data/30;
				$retorno[0] = 'me';
			}
			$retorno[1] = (int)($data);
			return $retorno;
		}

		
		if($_COOKIE['id_empresa']!=1){//se não for logado na rocket

			$conn = TConnection::open('rocket');
			
			$criterio = new TCriteria;
			$criterio->add(new TFilter('id', '>', 1));
			$criterio->add(new TFilter('status', '=', 'Ativo'));
			$criterio->setProperty('order', 'online');
			$criterio->setProperty('desc_asc', 'desc');

			$sql = new TSqlSelect;
			$sql->setEntity('usuario');
			$sql->addColumn('id');
			$sql->addColumn('login');
			$sql->addColumn('online');
			$sql->addColumn('online_data');
			$sql->setCriteria($criterio);
			
			$result = $conn->query($sql->getInstruction());
			$linha = $result->rowCount();
			
			for($i=0; $i<$linha; $i++){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				if($_COOKIE['id_empresa']!=1 or ($_COOKIE['login']!=$login and getIdCookieLogin($_COOKIE['login'], "rocket")!=$id)){
					
					$nome = ucwords(strtolower(getNomeCookieLogin($login, false, "rocket")))."<span class=\"contato_empresa\">Rocket Solution</span>";
					if($online){
						$class = "online";
						$Online_data[0] = "<span class='online_span'></span>";
						$Online_data[1] = '';
					}else{
						$class = "offline";
						$Online_data = getDataOffline($online_data);
						if($Online_data[1]=='182'){
							$Online_data[0] = '';
							$Online_data[1] = '';
						}
					}
					$imagem = registro($id, "usuario_imagem", "miniatura", "id_usuario", "rocket");
					if($imagem){
						$imagemsrc = "<img src='$imagem'>";
					}else{
						$imagemsrc = "<img src='img/rocket.png'>";
						$imagem = 'img/rocket.png';
					}
					echo "<li id='li_1_$id' class='$class'><span class='data'>".$Online_data[1]." ".$Online_data[0]."</span><a href='javascript:void(0);' imagem='$imagem' nome='$nome' id='1_$id' class='comecar'>$imagemsrc $nome</a></li>";

				}//if
			}//for
			
			$conn = TConnection::open(ALCUNHA);
			
			$criterio = new TCriteria;
			$criterio->add(new TFilter('id', '>', 1));
			$criterio->add(new TFilter('status', '=', 'Ativo'));
			$criterio->setProperty('order', 'online');
			$criterio->setProperty('desc_asc', 'desc');

			$sql = new TSqlSelect;
			$sql->setEntity('usuario');
			$sql->addColumn('id');
			$sql->addColumn('login');
			$sql->addColumn('online');
			$sql->addColumn('online_data');
			$sql->setCriteria($criterio);
			
			$result = $conn->query($sql->getInstruction());
			$linha = $result->rowCount();
			
			for($i=0; $i<$linha; $i++){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				if($_COOKIE['login']!=$login and getIdCookieLogin($_COOKIE['login'], ALCUNHA)!=$id){
					
					$nome = ucwords(strtolower(getNomeCookieLogin($login, false)."<span class=\"contato_empresa\"> ".registro($_COOKIE["id_empresa"], "cliente", "nome", "id", "gestor"))."</span>");

					if($online){
						$class = "online";
						$Online_data[0] = '<span class="online_span"></span>';
						$Online_data[1] = '';
					}else{
						$class = "offline";
						$Online_data = getDataOffline($online_data);
						if($Online_data[1]=='182'){
							$Online_data[0] = '';
							$Online_data[1] = '';
						}
					}
					$imagem = registro($id, "usuario_imagem", "miniatura", "id_usuario", ALCUNHA);
					if($imagem){
						$imagemsrc = "<img src='$imagem'>";
					}else{
						$imagemsrc = "<img src='img/rocket.png'>";
						$imagem = 'img/rocket.png';
					}
					echo "<li id='li_".$_COOKIE["id_empresa"]."_".$id."' class='$class'><span class='data'>".$Online_data[1]." ".$Online_data[0]."</span><a href='javascript:void(0);' imagem='$imagem' nome='$nome' id='".$_COOKIE["id_empresa"]."_".$id."' class='comecar'>$imagemsrc $nome</a></li>";

				}//if
			}//for

			
		}else{



			$conn = TConnection::open('gestor');
			
			$criterio = new TCriteria;
			$criterio->add(new TFilter('status', '=', 1));


			$sql = new TSqlSelect;
			$sql->setEntity('cliente');
			$sql->addColumn('alcunha');
			$sql->addColumn('nome as nomeEmpresa');
			$sql->addColumn('id as idEmpresa');
			$sql->setCriteria($criterio);
			
			$result = $conn->query($sql->getInstruction());
			$contatos_on = $contatos_off = null;	
			for($i=0; $i<$result->rowCount(); $i++){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
				

				$conn_empresa = TConnection::open($alcunha);
				
				$criterio = new TCriteria;
				$criterio->add(new TFilter('id', '>', 1));
				$criterio->add(new TFilter('status', '=', 'Ativo'));
				$criterio->setProperty('order', 'online');
				$criterio->setProperty('desc_asc', 'desc');

				$sql = new TSqlSelect;
				$sql->setEntity('usuario');
				$sql->addColumn('id');
				$sql->addColumn('login');
				$sql->addColumn('online');
				$sql->addColumn('online_data');
				$sql->setCriteria($criterio);
				
				
				$result_usuario = $conn_empresa->query($sql->getInstruction());
				$linha = $result_usuario->rowCount();
				
				for($j=0; $j<$linha; $j++){
					$row = $result_usuario->fetch(PDO::FETCH_ASSOC);
					extract($row);
					if($_COOKIE['login']!=$login || getIdCookieLogin($_COOKIE['login'], ALCUNHA)!=$id){
						
						$nome = ucwords(strtolower(getNomeCookieLogin($login, false, $alcunha)."<span class=\"contato_empresa\"> ".$nomeEmpresa."</span>"));
						if($online){
							$class = "online";
							$Online_data[0] = '<span class="online_span"></span>';
							$Online_data[1] = '';
						}else{
							$class = "offline";
							$Online_data = getDataOffline($online_data);
							if($Online_data[1]=='182'){
								$Online_data[0] = '';
								$Online_data[1] = '';
							}
						}
						$imagem = registro($id, "usuario_imagem", "miniatura", "id_usuario", $alcunha);
						if($imagem){
							$imagemsrc = "<img src='".$pasta1."$imagem'>";
						}else{
							$imagemsrc = "<img src='".$pasta1."img/rocket.png'>";
							$imagem = $pasta1.'img/rocket.png';
						}
						
						if($class=="online"){
							$contatos_on .= "<li id='li_".$idEmpresa."_".$id."' class='$class'><span class='data'>".$Online_data[1]." ".$Online_data[0]."</span>";
							$contatos_on .=  "<a href='javascript:void(0);' imagem='$imagem' nome='$nome' id='".$idEmpresa."_".$id."' class='comecar'>";
							$contatos_on .=  "$imagemsrc $nome</a></li>";
						}else{
							$contatos_off .= "<li id='li_".$idEmpresa."_".$id."' class='$class'><span class='data'>".$Online_data[1]." ".$Online_data[0]."</span>";
							$contatos_off .=  "<a href='javascript:void(0);' imagem='$imagem' nome='$nome' id='".$idEmpresa."_".$id."' class='comecar'>";
							$contatos_off .=  "$imagemsrc $nome</a></li>";
						}
						
					}//if
				}//for
			}//for
			echo $contatos_on.$contatos_off;
			


		}
	?>
	</ul>
</div>
<div style="position:absolute; top:0; right:0;" id="retorno"><div>
<div id="janelas"></div>