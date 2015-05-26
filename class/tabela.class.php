<?php

/**
 * objeto que gera uma tabela
 * 
 *  @param $titulo recebe o titulo da table body
 *  @param $rodape recebe o tfoot da table (string)
 *  @param $tags são os nomes das colunas, a variavel eh um array e sua quantidade determinará a quantidade de coluna
 *  @param $valores eh uma matriz que seu expoente x o tamanho do array da tags e o expoente y sera o numero de resultados;
 */
 
 
 
class tabela {
	
	public $style = null;
    private $titulo;
    private $rodape;
    private $tags;
    private $valores;
	public $resultados = true;
    public $ultimaColuna;
    private $erro = false;
    
    function setTitulo($titulo){
        $this->titulo = $titulo;
    }
    
    function setRodape($rodape){
        $this->rodape = $rodape;
    }
    
    function setTag($tagArray){
        $this->tags = $tagArray;
    }
    
    function setValores($valoresArray){
        if(isset($this->tags)){
            $radical = count($valoresArray) / count($this->tags);
            if(is_integer($radical)){
                for($i=0; $i<$radical;$i++){
                    for($j=0; $j<count($this->tags); $j++){
                        $array[] = $valoresArray[$i * count($this->tags) + $j];//determinando o radical do array
                    }
                    $this->valores[] = $array;
                    unset($array);
                }
            }else{
                $this->erro = "Quantidade de tags diferente de quantidade de valores.";
            }
            
        }else{
            return false;
        }
    }
    
    function showTabela(){
        
        $script  = "<table id='gradient-style' style='$this->style' summary='Resultado da pesquisa'>";
        $script .= "<thead>";
        $colspan = count($this->tags);
		if($this->titulo){
			$script .= "<tr>";
			$script .= "<th scope='col' colspan='".$colspan."' align='center'>".$this->titulo."</th>";	
			$script .= "</tr>";
		}
        $script .= "<tr>";
        foreach ($this->tags as $key => $value) {
            $script .= "<th scope='col' align='center'>$value</th>";
        }
        $script .= "</tr>";
        $script .= "</thead>";
        
		$linha = count($this->valores);
		
		if($this->resultados){
			$script .= "<tfoot>";
	        $script .= "<tr>";
	        
	        if($linha>1){
	            $result = "Foram encontrados $linha resultados.";
	        }elseif($linha==1){
	            $result = "Foi encontrado $linha resultado.";
	        }else{
	            $result = "Não foi encontrado nenhum resultado.";
	        }
			$script .= "<td colspan='".$colspan."' align='center'>$result";
	        if(isset($this->rodape)){
	            $script .= "<br>".$this->rodape;
	        }
	        $script .= "</td>";
	        $script .= "</tr>";
	        $script .= "</tfoot>";	
		}
        
        $script .= "<tbody>";
        if($this->erro){
            $script .= "<td align='center' colspan='$colspan' style='white-space:nowrap; color:red; font-weight: bold;'>".$this->erro."</td>";
        }else{
            for($i=0; $i<$linha; $i++){
                $script .= "<tr align='center'>";
                    $coluna = count($this->valores[$i]);
                    for($j=0; $j<$coluna; $j++){
                    	if(strstr($this->style, "width") and !strstr($this->valores[$i][$j], "R$")){
                    		$script .= "<td align='center'>".$this->valores[$i][$j]."</td>";
                    	}else{
                    		$script .= "<td align='center' style='white-space:nowrap;'>".$this->valores[$i][$j]."</td>";	
                    	}
                        
                    }
                $script .= "</tr>";
            }  
            if(isset($this->ultimaColuna)){
                $script .= $this->ultimaColuna;
            }  
        }
        
        
        
        
        $script .= "</tbody>";
        $script .= "</table>";
        
        
        return $script;
        
    }
	
}








?>