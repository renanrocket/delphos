<?php


class chave {
	    
    var $validade;
    var $dias;
    var $modulo;
    var $valor;
    
    
    //funcao para codificar chave
    function codificar(){
        $cod = "$this->validade $this->dias $this->modulo";
        return base64_encode($cod);
    }
    
    //funcao para decodificar o valor da chave
    function decodificar(){
        return base64_decode($this->valor);
    }
    
    //decodifica o valor e atribui o restante dos atributos da classe
    function decodificar_atribuir(){
        $Valor = base64_decode($this->valor);
        $Valor = explode(" ", $Valor);
        $this->validade = $Valor[0];
        $this->dias = $Valor[1];
        $this->modulo = $Valor[2];
    }
    
    //funcao para verificar se o valor codificado é realmente valido ou não
    function verificar_valor_valido(){
        $verifica = base64_decode($this->valor);
        
        $verifica1 = explode(" ", $verifica);
        $verifica2 = explode("/", $verifica1[0]);
        
        if(count($verifica1)<>3){
            return false;
        }elseif(count($verifica2)<>3){
            return false;
        }elseif(!is_numeric($verifica1[1])){
            return false;
        }elseif($verifica1[2]<>"basico" 
            and $verifica1[2]<>"ordemServico" 
            and $verifica1[2]<>"academia"
            and $verifica1[2]<>"imoveis"){
            return false;
        }else{
            return true;
        }

    }
    
    //funcao para verificar o prazo devalidade da chave
    function contar_dias(){
        
        //licensa full
        if($this->validade=="00/00/0000"){
            $data = explode("/", date('d/m/Y', strtotime("+ 30 days")));
            $time = mktime(0,0,0, $data[1], $data[0], $data[2]) - time();
            return intval($time/86400);
        }else{
            $data = explode("/", $this->validade);
            $time = mktime(0,0,0, $data[1], $data[0], $data[2]) - time();
            return intval($time/86400);
        }
        
    }
    
    ///funcao para verificar se a chave esta dentro do prazo de validade ou não
    function verificar_validade(){
        
        if($this->contar_dias() + $this->dias >= 0){
            return true;
        }else{
            return false;
        }
        
    }
    
    //funcao para verificar se vai ser mostrado na pagina inicial do sistema um aviso
    //para o usuario renovar a chave
    function verificar_validade_aviso(){
        
        if($this->contar_dias()<7 and $this->contar_dias()>=0){
            return "aviso1";
        }elseif($this->contar_dias() + $this->dias >= 0 and $this->contar_dias() < 0){
            return "aviso2";
        }else{
            return false;
        }
        
    }
    
}


?>