<?php
include "templates/upLogin.inc.php";

function movimento_caixa($id = null){
    
    echo "<form method='post' action='cadastrarCaixa.php' enctype='multpart/form-data'>";
        
        /*if($id<>null){
            echo "<input type='hidden' name='op' value='editar'>";
            echo "<input type='hidden' name='id' value='$id'>";
            $sql = query("select * from caixa_movimento where id='$id'");
            extract(mysqli_fetch_assoc($sql));
        }else{*/
            echo "<input type='hidden' name='op' value='lancar'>";
            $debito_credito = $valor = $id_caixa = null;
        /*}*/
    
        echo "Selecione a operação: ";
        if($debito_credito==0){
            echo "<input type='radio' name='operacao' value='0' checked>Débito <input type='radio' name='operacao' value='1'>Crédito<br>";   
        }else{
            echo "<input type='radio' name='operacao' value='0'>Débito <input type='radio' name='operacao' value='1' checked>Crédito<br>";
        }
        
        echo "Valor: ";
        echo "<input type='text' name='valor' value='$valor' ".mascara("Valor2")." class='preco totalValor' style='width:auto;'><br>";
        echo "Caixa referente: ";
        echo "<select name='id_caixa' style='width:auto;'>";
        echo opcaoSelect("caixa", 1, "Ativo", $id_caixa);
        echo "</select><br>";
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
        
    echo "</form>";
    
}

extract($_POST);
extract($_GET);

if(isset($op)){
    
    $valida = true;
    $info = "";
    
    if($op=="lancar" /*or $op=="editar"*/){
        $valor = str_replace(",", ".", $valor);
        if(empty($valor) or $valor==0){
            $info .= "Por favor insira algum valor no movimento do caixa.<br>";
            $valida = false;
        }
        if(empty($id_caixa) or $id_caixa==0){
            $info .= "Por favor marque algum caixa no movimento do caixa.<br>";
            $valida = false;
        }
    }
    
    
    
    if($op=="lancar" and $valida){
        
		$instrucao = "insert into caixa_movimento (debito_credito, valor, id_caixa, data, id_usuario) values ('$operacao', '$valor', '$id_caixa', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
        $sql = query($instrucao);
        
        $info .= "Movimento do caixa lançado.<br>";
            
        $id_usuario = getIdCookieLogin($_COOKIE["login"]);
        $data = date('Y-m-d H:i:s');
        $acao = "Lançou no caixa.";
        $tabela_afetada = "caixa_movimento";
        $chave_principal = mysqli_insert_id($conexao);
        
        insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
        
        movimento_caixa($chave_principal);
        
    /*}elseif($op=="editar" and $valida){
            
        $sql = query("update caixa_movimento set debito_credito='$operacao', valor='$valor', id_caixa='$id_caixa', data=''".date('Y-m-d H:i:s')."'', id_usuario='".getIdCookieLogin($_COOKIE["login"])."' where id='$id'");
        
        $info .= "Movimento do caixa editado.<br>";
            
        $id_usuario = getIdCookieLogin($_COOKIE["login"]);
        $data = date('Y-m-d H:i:s');
        $acao = "Editou o caixa.";
        $tabela_afetada = "caixa_movimento";
        $chave_principal = $id;
        
        insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
            
        movimento_caixa($id);
    }/*elseif($op=="visualizar"){
        movimento_caixa($id);*/
    }elseif($op=="deletar"){
        
        $id = base64_decode($id);
        
        $sql = query("delete from caixa_movimento where id='$id'");
        
        $info .= "Movimento do caixa deletado.<br>";
            
        $id_usuario = getIdCookieLogin($_COOKIE["login"]);
        $data = date('Y-m-d H:i:s');
        $acao = "Deletou o caixa.";
        $tabela_afetada = "caixa_movimento";
        $chave_principal = $id;
        
        insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
        
        confirmacaoDB("", "pesquisaCaixa.php");
        
    }
    
    if($valida){
        $cor = "green";
    }else{
        $cor = "red";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=cadastrarCaixa.php'>";
    }
    
    info($info, $cor);
    
}else{
    movimento_caixa();
}


include "templates/downLogin.inc.php";
?>

