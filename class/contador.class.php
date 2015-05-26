<?php

class contador{
	public $class;
	public $cont;
	public $tipo; //prog ou regr

	function __construct($class, $cont, $tipo = 'prog'){
		$this->class = $class;
		$this->cont = $cont;
		$this->tipo = $tipo;
	}

	function getHtml(){
		$cod = "<script type=\"text/javascript\">";	
		$cod.= "var count_".$this->class." = ".$this->cont.";\n";
		$cod.= "var hora_".$this->class." = '00';\n";
		$cod.= "var min_".$this->class." = '00';\n";
		$cod.= "var html_".$this->class." = null;\n";
		$cod.= "var ".$this->class." = $.timer(\n";
		$cod.= "function() {\n";
		$cod.= "count_".$this->class."++;\n";
		$cod.= "while(count_".$this->class.">60){\n";
			$cod.= "min_".$this->class." ++;\n";
			$cod.= "count_".$this->class." = count_".$this->class."-60;\n";
		$cod.= "}\n";
		
		$cod.= "while(min_".$this->class.">60){\n";
			$cod.= "hora_".$this->class." ++;\n";
			$cod.= "min_".$this->class." = min_".$this->class."-60;\n";
		$cod.= "}\n";
		$cod.= "if(count_".$this->class.".toString().length<2){\n";
			$cod.= "count_".$this->class." = '0'+count_".$this->class.";\n";
		$cod.= "}\n";
		$cod.= "if(min_".$this->class.".toString().length<2){\n";
			$cod.= "min_".$this->class." = '0'+min_".$this->class.";\n";
		$cod.= "}\n";
		$cod.= "if(hora_".$this->class.".toString().length<2){\n";
			$cod.= "hora_".$this->class." = '0'+hora_".$this->class.";\n";
		$cod.= "}\n";
		$cod.= "html_".$this->class." = hora_".$this->class."+' : '+min_".$this->class."+' : '+count_".$this->class.";\n";
		$cod.= "$('.".$this->class."').html(html_".$this->class.");\n";
		$cod.= "},\n";
		$cod.= "1000,\n";
		$cod.= "true\n";
		$cod.= ");\n";	
		$cod.= "</script>\n";

		$cod.= "<span class='".$this->class."'></span>";

		return $cod;
	}
}







?>