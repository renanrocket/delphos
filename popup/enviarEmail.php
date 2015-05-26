<?php
include "../templates/upLoginImp.inc.php";

require_once "../plugins/uolpagseguro_2.2.2/PagSeguroLibrary/PagSeguroLibrary.php";

extract($_POST);
$corpo = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
$corpo .= "<html>";
$corpo .= "<head>";
$corpo .= "<meta http-equiv=\"content-Type\" content=\"text/html; charset=UTF-8\" />";
$corpo .= "<title>Delphos</title>";
$corpo .= "</head>";
$corpo .= "<body>";


$sql = query("select email as Email, token as Token from pagseguro");

if(mysqli_num_rows($sql)>0){
	extract(mysqli_fetch_assoc($sql));
	
	
	if(isset($id_conta) and isset($valor)){
		$numero_item = $id_conta;
		$Valor = $valor;
		$valorTotal = null;

		$credentials = new PagSeguroAccountCredentials($Email, $Token);

		$pag = new PagSeguroPaymentRequest();
		$pag->setCurrency("BRL");
		
		
		$sql= query("select * from conta where id='$id_conta'");
		$reg= mysqli_fetch_assoc($sql);
		if (!is_numeric($reg["referido"])) {

			$referido.= $reg["referido"];

			$pag->addItem($numero_item, $reg["referido"], 1, real($valor,true));

			$valorTotal += $valor;

		} elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]) {//caso seja associado
			$referido = null;
			if($reg["tabela_referido"] == "orcamento"){
				$sqlReferido = query("select * from orcamento_itens where id_orcamento='".$reg["referido"]."'");
				for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
					extract(mysqli_fetch_assoc($sqlReferido));
					if(is_numeric($id_item)){
						$referido.= $quantidade." X R$ ".real($valor_produto)." ".registro($id_item, $tabela_item, "nome");

						$pag->addItem($id_item, registro($id_item, $tabela_item, "nome"), $quantidade, real($valor_produto ,true));

						$valorTotal += real($valor_produto ,true);
					
					}else{
						$referido.= $quantidade." X R$ ".real($valor_produto)." ".$id_item;
						
						$pag->addItem($id, $id_item, $quantidade, real($valor_produto ,true));

						$valorTotal += real($valor_produto ,true);

					}			
				}
			}elseif($reg["tabela_referido"] == "pdv"){
				$sqlReferido = query("select * from pdv_itens where id_pdv='".$reg["referido"]."'");
				for($j=0; $j<mysqli_num_rows($sqlReferido); $j++){
					extract(mysqli_fetch_assoc($sqlReferido));
					$referido.= $quantidade." X R$ ".real($preco)." ".registro($id_produto, "produto", "nome");

					$pag->addItem($id_produto, registro($id_produto, "produto", "nome"), $quantidade, real($preco, true));

					$valorTotal += real($preco ,true);

				}
			}elseif($reg["tabela_referido"]=="ordem_servico"){

				$sqlReferido = query("select * from ordem_servico where id_ordem_servico='".$reg["referido"]."'");
				extract(mysqli_fetch_assoc($sqlReferido));
				if(is_numeric($id_servico)){

					$referido.= $quantidade." X R$ ".real($valor)." ".registro($id_servico, "servico", "nome");

					$pag->addItem($id_servico, registro($id_servico, "servico", "nome"), $quantidade, real($valor, true));

					$valorTotal += real($valor ,true);
				
				}

			}elseif($reg["tabela_referido"]=="plano_assinatura"){
				$sqlReferido = query("select * from matricula_plano_assinatura where id_matricula='".$reg["referido"]."' and status='1'");
				extract(mysqli_fetch_assoc($sqlReferido));
				$referido.= "R$ ".real($valor)." ".registro($id_plano_assinatura, "plano_assinatura", "nome");

				$pag->addItem($id_plano_assinatura, registro($id_plano_assinatura, "plano_assinatura", "nome"), 1, real($valor, true));

				$valorTotal += real($valor ,true);

			}else{
				$referido.= $reg["tabela_referido"];

				$pag->addItem($id_conta, $reg["tabela_referido"], 1, real($valor, true));

				$valorTotal += real($valor ,true);
			}
		}
		//se o item for grande demais, resumi-lo
		if($Valor!=$valorTotal){
			$referido = "Conta $id_conta";
			$pag = new PagSeguroPaymentRequest();
			$pag->setCurrency("BRL");
			$pag->addItem($id_conta, $referido, 1, real($Valor, true));
		}
		

		$pag->setShippingType(3);
		$pag->setRedirectUrl("http://www.rocketsolution.com.br");
        $cod = $pag->register($credentials);
 
		$email = str_replace("#cod", $cod, $email);
		
	}
	
}

$corpo .= $email;

$corpo .= "<br><br><center><span style='font-size:9px;'>Sistema de e-mail desenvolvido por Rocket Solution</span><br>";
$corpo .= "<a href='http://www.rocketsolution.com.br'>";
$corpo .= "<img src='http://" . $_SERVER["HTTP_HOST"] . "/img/logo.png' style='width:100px;'></a></center><br>";

$corpo .= "</body>";
$corpo .= "</html>";

$headers = "MIME-Version: 1.0\n";
$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= 'From: '.$remetente . "\r\n";
$headers .= 'Reply-To: '.$remetente . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();

if (mail($destinatario, $assunto, $corpo, $headers)) {
	echo "E-mail enviado com sucesso.";
	echo "<script type='text/javascript'>
		$(function(){
			ww = window.open(window.location, \"_self\");
			ww.close();
		}); 
		</script>";
} else {
	echo "Falha ao enviar e-mail.";
	
}

include "../templates/downLoginImp.inc.php";
?>