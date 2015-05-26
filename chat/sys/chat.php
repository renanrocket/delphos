<?php
	session_start();
	include_once "../config.php";
	require_once('../classes/BD.class.php');
	BD::conn();
	
	$acao = $_POST['acao'];
	
	switch($acao){
		case 'inserir':
			$para = $_POST['para'];
			$mensagem = strip_tags($_POST['mensagem']);
			
			$pegar_nome = BD::conn()->prepare("SELECT nome FROM `usuarios` WHERE id = ?");
			$pegar_nome->execute(array($_SESSION['id_user']));
			$ft = $pegar_nome->fetchObject();
			
			$inserir = BD::conn()->prepare("INSERT INTO `mensagens` (id_de, id_para, data, mensagem) VALUES(?,?,NOW(),?)");
			if($inserir->execute(array($_SESSION['id_user'], $para, $mensagem))){
				echo '<li><span>'.$ft->nome.' disse:</span><p>'.$mensagem.'</p></li>';
			}
			
		break;
		
		case 'verificar':
			$ids = $_POST['ids'];
			$retorno = array();
			
			if($ids == ''){
				$retorno['mensagens'] = '';
			}else{
				foreach($ids as $indice => $id){
					$selecionar = BD::conn()->prepare("SELECT * FROM `mensagens` WHERE id_de = ? AND id_para = ? OR id_de = ? AND id_para = ?");
					$selecionar->execute(array($_SESSION['id_user'], $id, $id, $_SESSION['id_user']));
					
					$mensagem = '';
					while($ft = $selecionar->fetchObject()){
						$nome = BD::conn()->prepare("SELECT nome FROM `usuarios` WHERE id = ?");
						$nome->execute(array($ft->id_de));
						$name = $nome->fetchObject();
						
						$mensagem .= '<li><span>'.$name->nome.' disse:</span><p>'.$ft->mensagem.'</p></li>';
					}
					$retorno['mensagens'][$id] = $mensagem;
				}
			}
		
			$verificar = BD::conn()->prepare("SELECT id_de FROM `mensagens` WHERE id_para = ? AND lido = ? GROUP BY id_de");
			$verificar->execute(array($_SESSION['id_user'], 0));
			
			if($verificar->rowCount() == 0){
				$retorno['nao_lidos'] = '';
			}else{
				while($user = $verificar->fetchObject()){
					$retorno['nao_lidos'][] = $user->id_de;
				}
			}
			$retorno = json_encode($retorno);
			echo $retorno;
		break;
		
		case 'mudar_status':
			$user = $_POST['user'];
			$mudar_st = BD::conn()->prepare("UPDATE `mensagens` SET lido = '1' WHERE id_de = ? AND id_para = ?");
			$mudar_st->execute(array($user, $_SESSION['id_user']));
		break;
	}
?>