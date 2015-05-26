<?php
include "templates/upLogin.inc.php";
//js para esta pagina em especifico
echo "<script src='js/orcamentoNovoFiltro.js' type='text/javascript'></script>";

extract($_POST);
extract($_GET);

function formToken($pdv){
	echo "<form method='post' action='cadastrarPDV2.php?pdv=$pdv' enctype='multipart/form-data' style='display:inline-block;'>";
		echo "Para deletar esse PDV e seus respectivos pagamentos, digite um token:<br>";
		echo "<input type='hidden' name='op' value='cancelar2'>";
		echo "<input type='password' name='token'>";
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	echo "</form>";
}


if($op=="cancelar"){
	
	/*
	* 
	* operação deletar apenas para usuários com credênciais ou para aqueles que inserirem o token
	* 
	*/

	//$instrucao = "select * from credenciais where id_usuario='".getIdCookieLogin($_COOKIE["login"])."' and ferramenta='administrativoToken.php'";
	//if(mysqli_num_rows(query($instrucao))>1){
	if(verificaCredencialToken()){	
		echo "<form method='post' action='cadastrarPDV2.php?pdv=$pdv' enctype='multipart/form-data' style='display:inline-block;'>";
			echo "Deseja realmente deletar esse PDV e seus respectivos pagamentos?<br><br>";
			echo "<input type='hidden' name='op' value='cancelar2'>";
			echo "<input type='submit' value='Sim'>";
		echo "</form><br>";
		echo "<form method='get' action='cadastrarPDV.php' enctype='multipart/form-data' style='display:inline-block;'>";
			echo "<input type='hidden' name='pdv' value='$pdv'>";
			echo "<input type='submit' value='Não'>";
		echo "</form>";
	}else{
		formToken($pdv);
	}

}else{
	
	$idPdv = base64_decode($pdv);
	
	if(isset($token)){
		
		$validaToken = false;
		$hoje =  date('Y-m-d H:i:s');
		$instrucao = "select * from tokens where token='".md5($token)."' and data_validade>='$hoje' and vezes_permitido > any (select vezes_usado from tokens where token='".md5($token)."' and data_validade>='$hoje')";
		$sqlToken = query($instrucao);
		$linhaToken = mysqli_num_rows($sqlToken);
		if($linhaToken>0){
			
			
			$instrucao = "select id as idConta from conta where tabela_referido='pdv' and referido='$idPdv'";
			$sql = query($instrucao);
			extract(mysqli_fetch_assoc($sql));
			
			//dando alta no estoque
			$sqlProduto = query("select id_produto as idProduto, quantidade from pdv_itens where id_pdv='$idPdv'");
			for($i=0;$i<mysqli_num_rows($sql); $i++){
				extract(mysqli_fetch_assoc($sqlProduto));
				$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
				extract(mysqli_fetch_assoc($sqlProduto));
				if($contabilizar_estoque){
					$qtd_estoque += $quantidade;
					$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");					
				}
			}
			
			
			$sql = query("update pdv set status='0' where id='$idPdv'");
			//$sql = query("delete from pdv_itens where id_pdv='$idPdv'");
			$sql = query("update conta set status='4' where id='$idConta'");//4 cancelar
	        $sql = query("select id_caixa_movimento from conta_itens where id_conta='$idConta'");
	        for ($i=0; $i< mysqli_num_rows($sql); $i++) {
	            extract(mysqli_fetch_assoc($sql));
	            $sqlMovimento = query("delete from caixa_movimento where id='$id_caixa_movimento'");
	        }
			$sql = query("update conta_itens set valor=NULL where id_conta='$idConta'");
			
			info("PDV deletado com sucesso.");
			
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarPDV.php'>";

						$regToken = mysqli_fetch_assoc($sqlToken);
			$regToken["vezes_usado"]++;
			$sqlToken = query("update tokens set vezes_usado='".$regToken["vezes_usado"]."' where token='".md5($token)."'");
			$validaToken = true;
			
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Usou um token.";
			$tabela_afetada = "tokens";
			$chave_principal = $regToken["id"];
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
		}else{
			
			formToken($pdv);
			
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Tentou usar um token.";
			$tabela_afetada = NULL;
			$chave_principal = NULL;
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		}
	}else{
		
		$instrucao = "select id as idConta from conta where tabela_referido='pdv' and referido='$idPdv'";
		$sql = query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		//dando alta no estoque
		$instrucao = "select id_produto as idProduto, quantidade from pdv_itens where id_pdv='$idPdv'";
		$sql = query($instrucao);
		for($i=0;$i<mysqli_num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			$sqlProduto = query("select nome, qtd_estoque, contabilizar_estoque from produto where id='$idProduto'");
			if(mysqli_num_rows($sqlProduto)>0){
				extract(mysqli_fetch_assoc($sqlProduto));
				if($contabilizar_estoque){
					$qtd_estoque += $quantidade;
					$sqlProduto = query("update produto set qtd_estoque='$qtd_estoque' where id='$idProduto'");	
				}
			}
		}
		$sql = query("update pdv set status='0' where id='$idPdv'");
		//$sql = query("delete from pdv_itens where id_pdv='$idPdv'");
		$sql = query("update conta set status='4' where id='$idConta'");//4 cancelar
        $sql = query("select id_caixa_movimento from conta_itens where id_conta='$idConta'");
        for ($i=0; $i< mysqli_num_rows($sql); $i++) {
            extract(mysqli_fetch_assoc($sql));
            $sqlMovimento = query("delete from caixa_movimento where id='$id_caixa_movimento'");
        }
		$sql = query("update conta_itens set valor=NULL where id_conta='$idConta'");
		
		info("PDV deletado com sucesso.");
			
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarPDV.php'>";
		
	}
	
		
}

include "templates/downLogin.inc.php";


?>