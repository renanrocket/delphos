<?php
/*
 * formulario de criação e edição das chaves
 */
class chaveForm extends chave {

    public $Identidade;
	public $Cliente;
    public $Valor;
    public $Data;
    public $Id_usuario;

    function __construct($identidade = null, $cliente = null, $data = null) {
    	
		$this -> Cliente = $cliente;
        $this -> Identidade = $identidade;
        $this -> Data = $data;

    }

    function valida() {
        return $this -> Valor;
    }

    function formulario() {

        if (!is_null($this -> Identidade)) {
            $sql = query("select * from chaves_cliente where id='$id'", $conexao2);
            extract(mysql_fetch_assoc($sql));
            $this -> __construct($id, $datao);
            $op = "editar";
        } else {
            $op = "novo2";
        }
        $script = "<div id='login' style='top: 20%; left: 42%;'><center>";
        $script .= "<form name='formularioChave' method='post' action='cad_chave.php' enctype='multpart/form-data'>";
        $script .= "<input type='hidden' name='op' value='$op'>";
		$script .= "Cliente<br>";
		$script .= "<input type='text' name='cliente' value='$this->Cliente' style='width:auto;'><br>";

        $cod = $this -> chaveDecodificar();

        $script .= "Data de validade:<br>";
        $script .= inputData("formularioChave", "data", null, formataData($cod[0]));
        $script .= "inspirando em <input type='text' name='validade' value='" . $cod[1] . "' style='width:15px;' " . mascara("Integer") . "> dias<br>";
        $script .= "Acesso ao módulo:<br>";

        $script .= "<select name='modulo' style='width:auto;'>";
        if ($cod[2] == "basico") {
            $script .= "<option value='basico' selected>Básico</option>";
        } else {
            $script .= "<option value='basico'>Básico</option>";
        }
        if ($cod[2] == "ordemServico") {
            $script .= "<option value='ordemServico' selected>Ordem de Serviço</option>";
        } else {
            $script .= "<option value='ordemServico'>Ordem de Serviço</option>";
        }
        if ($cod[2] == "academia") {
            $script .= "<option value='academia' selected>Academia</option>";
        } else {
            $script .= "<option value='academia'>Academia</option>";
        }
        if ($cod[2] == "imoveis") {
            $script .= "<option value='imoveis' selected>Imóveis</option>";
        } else {
            $script .= "<option value='imoveis'>Imóveis</option>";
        }
    
        $script .= "<br>";

        $script .= "<input type='submit' value='Enviar'>";

        $script .= "</form>";

        $script .= "</center></div>";

        return $script;
    }

    function getValor() {
        return $this -> Valor;
        //return 'as';
    }

    function chaveDecodificar() {
        
        if($this->Identidade){
            $conn = TConnection::open('gestor');
            
            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $this->Identidade));
            $sql= new TSqlSelect;
            $sql->setEntity('chaves_cliente');
            $sql->addColumn('*');
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());
            $row = $result->fetch(PDO::FETCH_ASSOC);
            extract($row);
            //$sql = query("select valor from chaves_cliente where id='$this->Identidade'");
            //extract(mysql_fetch_assoc($sql));
            $this->Valor = $valor;
        }

        $cod = base64_decode($this -> Valor);
        $cod = explode(" ", $cod);

        return $cod;
    }

    function __set($propriedade, $valor) {
       if ($propriedade == "Valor") {
            $cod = base64_decode($valor);
            if (!strstr($cod, " ")) {
                info("Por favor digite a data de validade da chave.<br><a href=javascript:history.go(-1)>Voltar</a>", "#e2d000", "34 20");
            } elseif (strstr($cod, " ") and strstr($cod, "/")) {
                $cod2 = explode(" ", $cod);
                if (strlen($cod2[0]) < 10) {
                    info("Por favor digite a data de validade da chave.<br><a href=javascript:history.go(-1)>Voltar</a>", "#e2d000", "34 20");
                } else {
                    $this -> $propriedade = $valor;
                }
            } else {
                $this -> $propriedade = $valor;
            }
        } else {
            $this -> $propriedade = $valor;
        }
    }

}
?>