<?php

include "template/indexUp.inc.php";
include "template/menu.inc.php";
extract($_POST);
extract($_GET);

function sqlChave($operacao, $objeto = null){

    

    if ($operacao == "inserir") {

        $conn = TConnection::open('gestor');

        $sql = new TSqlInsert;
        $sql->setEntity('chaves_cliente');
        $sql->setRowData('cliente', $objeto->Cliente);
        $sql->setRowData('valor', $objeto->getValor());
        $sql->setRowData('data', $objeto->Data);
        echo $sql->getInstruction();
        
        if($result = $conn->query($sql->getInstruction())){
            return true;
        }else{
            return false;
        }

        //$sql = query("insert into chaves_cliente (cliente, valor, data) values ('".$objeto -> Cliente."', '" . $objeto -> getValor() . "', '" . $objeto -> Data . "')", $conexao2);
        
        //return confirmacaoDB("<h1>Chave inserida</h1><br><input type='text' style='width:90%; text-transform:none;' name='chave' value='" . $objeto -> getValor() . "'>", null, true);
    
    }elseif ($operacao == "selecionar") {
        
        $conn = TConnection::open('gestor');

        $sql = new TSqlSelect;
        $sql->setEntity('chaves_cliente');
        $sql->addColumn('*');
        return $result = $conn->query($sql->getInstruction());
        
    }

}

if(isset($op)){
    
    
    
    if($op=="novo"){
        
        $formulario = new chaveForm();
        $formulario->Id_cliente = null;
        $formulario->Valor = base64_encode(date('Y-m-d', strtotime('+ 30 days'))." 2 basico");
        echo $formulario->formulario();
        
    }elseif($op=="mostrarAtivo" or $op=="mostrarInativo"){
        
        echo "<div id='login'>";
        $sql = sqlChave("selecionar"); 
        echo "<table  id='gradient-style'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th scope='col' >ID</th>";
				echo "<th scope='col' >Cliente</th>";
                echo "<th scope='col' >Valor</th>";
                echo "<th scope='col' >Módulo</th>";
                echo "<th scope='col' >Inspira em</th>";
                echo "<th scope='col' >Carencia de</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
		$resultado = 0;
        while ($reg = $sql->fetch(PDO::FETCH_ASSOC)) {
            extract($reg);
			$chave = new chaveForm($id);
            $chaveArray = $chave->chaveDecodificar();

            $data = explode("/", $chaveArray[0]);
            $time = mktime(0,0,0, $data[1], $data[0], $data[2]) - time();
            
            $dias = intval($time/86400);
            if(($dias>0 and $op=='mostrarAtivo') or $chaveArray[0]=="00/00/0000"){
            	$resultado++;
            	echo "<tr>";
                    echo "<th scope='col' >$id</th>";
                    echo "<th scope='col' >$cliente</th>";
                    echo "<th scope='col' ><input type='text' value='".$chave->getValor()."' style='width:90%; text-transform:none;'></th>";
                    echo "<th scope='col' >$chaveArray[2]</th>";
                    echo "<th scope='col' >$chaveArray[0]<br>";
					if($chaveArray[0]!="00/00/0000"){
						if($dias>1){
                            echo "Faltam ";
                        }elseif($dias==1){
                            echo "Falta ";
                        }elseif($dias<1 and $dias>-2){
                            echo "Já se passou ";
                        }elseif($dias<-1){
                            echo "Já se passaram ";
                        }
                        echo "$dias ";
                        if($dias>1){
                            echo "dias";
                        }elseif($dias<-1){
                            echo "dias";
                        }else{
                            echo "dia";
                        }
					}
                    echo "</th>";
                    echo "<th scope='col' >$chaveArray[1] dias</th>";
                echo "</tr>";
            }elseif($dias<0 and $op=='mostrarInativo' and $chaveArray[0]!="00/00/0000"){
            	$resultado++;
            	echo "<tr>";
                    echo "<th scope='col' >$id</th>";
                    echo "<th scope='col' >$cliente</th>";
                    echo "<th scope='col' ><input type='text' value='".$chave->getValor()."' style='width:90%; text-transform:none;'></th>";
                    echo "<th scope='col' >$chaveArray[2]</th>";
                    echo "<th scope='col' >$chaveArray[0]<br>";
					if($chaveArray[0]!="00/00/0000"){
						if($dias>1){
                            echo "Faltam ";
                        }elseif($dias==1){
                            echo "Falta ";
                        }elseif($dias<1 and $dias>-2){
                            echo "Já se passou ";
                        }elseif($dias<-1){
                            echo "Já se passaram ";
                        }
                        echo "$dias ";
                        if($dias>1){
                            echo "dias";
                        }elseif($dias<-1){
                            echo "dias";
                        }else{
                            echo "dia";
                        }
					}
                    echo "</th>";
                    echo "<th scope='col' >$chaveArray[1] dias</th>";
                echo "</tr>";
            }
           
        }
        echo "</tbody>";
		
		echo "<tfoot>";
            echo "<tr>";
                echo "<th scope='col'  colspan='6'>Existem $resultado chaves registradas</th>";
            echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";
        
        echo "</div>";
        
        
    }elseif($op == "novo2"){

        
        
        $chave = new chave;
        $chave->validade = $data;
        $chave->dias = $validade;
        $chave->modulo = $modulo;
        $dados = new chaveForm(null, $cliente,date('Y-m-d'));
        $dados->Valor = $chave->codificar();
        
        
        if($dados->valida()){
            echo "<div id='login'>";
            echo sqlChave("inserir", $dados);
            echo "</div>";
        }
        
    }

    
}else{
    
    echo "<div id='login'>";
        echo "<form method='post' action='administrativoChave.php' enctype='multipart/form-data'>";
        echo "Selecione a operação:";
        echo "<select name='op'>";
            echo "<option value='novo1'>Inserir Nova Chave</option>";
            echo "<option value='mostrarAtivo'>Mostrar Chaves Ativas</option>";
			echo "<option value='mostrarInativo'>Mostrar Chaves Inativas</option>";
        echo "</select>";
        echo "<input type='submit' value='Enviar'>";
        echo "</form>";
        
    echo "</div>";
    
}

include "../chat/chat.php";
include "template/indexDown.inc.php";

?>
