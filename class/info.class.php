<?php


class info {


	public $msg;
	public $cor = '#e2d000';
	public $posicaoTop;
	public $posicaoLeft;
	public $display = 'none';
	public $class;


	function getInfo(){

		if($this->posicaoTop){
	        $this->posicaoTop = "top: ".$this->posicaoTop."%;";
	    }
	    if($this->posicaoLeft){
	        $this->posicaoLeft = "left: ".$this->posicaoLeft."%;";
	    }
	    $style = $this->posicaoLeft." ".$this->posicaoTop;
		$cod = "<div class='infoBack ".$this->class."' style='display: ".$this->display.";'></div>";
		$cod .= "<div id='info' class='info ".$this->class."' style='$style; display: ".$this->display.";' align='center' valign='middle'>";
		$cod .= "<div style='color: #F6F4F7; font-weight: bolder; background-color: ".$this->cor."; position:absolute; top:-1px; left:-2px; border-top-left-radius: 10px; width:100%; line-height:20px; display:block;'>Mensagem do sistema!</div>";
		$cod .= "<a href='#' id='deletar' class='infoX' onclick=\"infoApagar$this->class();\">X</a>";
		$cod .= "<br><span id='span_".$this->class."'>".$this->msg."<span></div>";
		$cod .= "<script type='text/javascript'>";
			$cod .= "function infoApagar$this->class() {";
				$cod .= "$(\".info\").hide();";
				$cod .= "$(\".infoBack\").hide();";
			$cod .= "}";
		$cod .= "</script>";

		return $cod;
	}

	function getJs(){
		$cod = "onclick=\"$('.".$this->class."').show();\"";
		return $cod;
	}

	function getJsApagar(){
		$cod = "infoApagar$this->class();";
		return $cod;
	}
}





?>