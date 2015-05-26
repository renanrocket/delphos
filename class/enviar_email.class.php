<?php

class enviar_email{

	private $cabeca;
	private $corpo;
	private $corpoCabeca;
	private $corpoFinal;
	
	public $remetente;
	public $destinatario;
	public $assunto;
	public $corpoMeio;

	public function __construct($remetente, $destinatario, $assunto, $corpoMeio){
		
		$this->remetente 	= $remetente;
		$this->destinatario = $destinatario;
		$this->assunto 		= $assunto;
		$this->corpoMeio 	= $corpoMeio;

		$this->cabeca 		= "MIME-Version: 1.0\n";
		$this->cabeca 	   .= "Content-type: text/html; charset=UTF-8\n";
		$this->cabeca      .= 'From: '.$this->remetente . "\r\n";
		$this->cabeca 	   .= 'Reply-To: '.$this->remetente . "\r\n";
		$this->cabeca 	   .= 'X-Mailer: PHP/' . phpversion();

		$this->corpoCabeca  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
		$this->corpoCabeca .= "<html>";
		$this->corpoCabeca .= "<head>";
		$this->corpoCabeca .= "<meta http-equiv=\"content-Type\" content=\"text/html; charset=UTF-8\" />";
		$this->corpoCabeca .= "<title>Delphos</title>";
		$this->corpoCabeca .= "</head>";
		$this->corpoCabeca .= "<body>";

		$this->corpoFinal   = "<br><br><center><span style='font-size:9px;'>Sistema de e-mail desenvolvido por Rocket Solution</span><br>";
		$this->corpoFinal  .= "<a href='http://www.rocketsolution.com.br'>";
		$this->corpoFinal  .= "<img src='http://" . $_SERVER["HTTP_HOST"] . "/img/logo.png' style='width:100px;'></a></center><br>";
		$this->corpoFinal  .= "</body>";
		$this->corpoFinal  .= "</html>";

		$this->corpo 		= $this->corpoCabeca.$this->corpoMeio.$this->corpoFinal;

	}

	public function setEmail($idEmail, $arrayVariaveis){

		extract($arrayVariaveis);
		$conn = TConnection::open(ALCUNHA);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('id', '=', $idEmail));

		$sql = new TSqlSelect;
		$sql->setEntity('email');
		$sql->addColumn('*');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());
		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);

			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_email', '=', $id));

			$sql = new TSqlSelect;
			$sql->setEntity('email_variaveis');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$resultVariaveis = $conn->query($sql->getInstruction());
			for($i=0; $i<$resultVariaveis->rowCount(); $i++){
				$row = $resultVariaveis->fetch(PDO::FETCH_ASSOC);
				extract($row);
				$assunto = str_replace($buscar, $$trocar, $assunto);
				$email = str_replace($buscar, $$trocar, $email);
			}
		}
		$this->assunto 	= $assunto;
		$this->corpo 	= $this->corpoCabeca.$email.$this->corpoFinal;
	}

	public function getRemetente(){
		return $this->remetente;
	}

	public function getDestinatario(){
		return $this->destinatario;
	}

	public function getAssunto(){
		return $this->assunto;
	}

	public function getCorpo(){
		return $this->corpo;
	}

	public function enviarEmail(){
		if($this->destinatario and $this->remetente){
			if (mail($this->destinatario, $this->assunto, $this->corpo, $this->cabeca)) {
				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}

}

?>