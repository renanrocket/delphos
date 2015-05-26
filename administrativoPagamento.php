<?php
//php
include "templates/upLogin.inc.php";


//all
extract($_GET);
extract($_POST);

if(isset($pagamento)){
    
    function formPagamento($op, $id = null, $nome = null, $descricao = null){
        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
        echo "Insira a forma de pagamento:<br>";
        echo "<input type='hidden' name='pagamento' value='forma'>";
        echo "<input type='hidden' name='op' value='$op'>";
        if($id){
            echo "<input type='hidden' name='id' value='$id'>";
            $sql = query("select * from pagamento_forma where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            $nome = $forma_pagamento;
        }
        echo "Nome:<br><input type='text' name='nome' value='$nome' style='width:auto;'><br>";
        echo "Descrição:<br><textarea name='descricao' style='width:auto;'>$descricao</textarea><br>";
        if($id){
            echo "<select name='status' style='width:auto;'>";
                if($status=="Ativo"){
                    echo "<option value='Ativo' selected='yes'>Ativo</option>";
                    echo "<option value='Inativo'>Inativo</option>";
                }else{
                    echo "<option value='Ativo'>Ativo</option>";
                    echo "<option value='Inativo' selected='yes'>Inativo</option>";
                }
            echo "</select><br>"; 
        }
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
        echo "</form>";    
    }
    
    function tipoPagamento($op, $id = null, $nome = null){
        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
        echo "Insira o tipo de pagamento:<br>";
        echo "<input type='hidden' name='pagamento' value='tipo'>";
        echo "<input type='hidden' name='op' value='$op'>";
        if($id){
            echo "<input type='hidden' name='id' value='$id'>";
            $sql = query("select * from pagamento_tipo where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            $nome = $tipo_pagamento;
        }
        echo "Nome:<br><input type='text' name='nome' value='$nome' style='width:auto;'><br>";
        if($id){
            echo "<select name='status' style='width:auto;'>";
                if($status=="Ativo"){
                    echo "<option value='Ativo' selected='yes'>Ativo</option>";
                    echo "<option value='Inativo'>Inativo</option>";
                }else{
                    echo "<option value='Ativo'>Ativo</option>";
                    echo "<option value='Inativo' selected='yes'>Inativo</option>";
                }
            echo "</select><br>"; 
        }
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
        echo "</form>";    
    }
    
    function subTipoPagamento($op, $id = null, $nome = null, $idTipoPagamento = null){
        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
        echo "Insira o sub tipo de pagamento:<br>";
        echo "<input type='hidden' name='pagamento' value='subTipo'>";
        echo "<input type='hidden' name='op' value='$op'>";
        if($id){
            echo "<input type='hidden' name='id' value='$id'>";
            $sql = query("select * from pagamento_tipo_sub where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            $nome = $sub_tipo_pagamento;
            $idTipoPagamento = $id_pagamento_tipo;
        }
        if($op=="novo"){
            $statusOpcao = "Ativo";
        }else{
            $statusOpcao = null;
        }
        echo "Nome:<br><input type='text' name='nome' value='$nome' style='width:auto;'><br>";
        echo "Tipo de pagamento associado:<br><select name='idTipoPagamento' style='width:auto;'>";
            echo opcaoSelect("pagamento_tipo", 1, $statusOpcao, $idTipoPagamento);
        echo "</select><br>";
        if($id){
            echo "<select name='status' style='width:auto;'>";
                if($status=="Ativo"){
                    echo "<option value='Ativo' selected='yes'>Ativo</option>";
                    echo "<option value='Inativo'>Inativo</option>";
                }else{
                    echo "<option value='Ativo'>Ativo</option>";
                    echo "<option value='Inativo' selected='yes'>Inativo</option>";
                }
            echo "</select><br>"; 
        }
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
        echo "</form>";    
    }
    
    function parcelaPagamento(){
        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
            echo "Insira o valor do parcelamento máximo:<br>";
            $sql = query("select * from pagamento_parcela");
            extract(mysqli_fetch_assoc($sql));
            echo "<input type='hidden' name='pagamento' value='parcelas'>";
            echo "<input type='hidden' name='op' value='editar'>";
            echo "<input type='text' name='parcelaMax' value='$parcelaMax' style='width:auto;'>";
            echo "<input type='submit' class='btnEnviar' value='Enviar'>";
        echo "</form>";
    }
    
    function formPagseguro(){
        $sql = query("select * from pagseguro");
        if(mysqli_num_rows($sql)){
            extract(mysqli_fetch_assoc($sql));    
        }else{
            $email = $token = null;
        }
        echo "<form class='form' method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
            echo "<div class='column'>";
                echo "<input type='hidden' name='pagamento' value='pagseguro'>";
                echo "<input type='hidden' name='op' value='editar'>";
                echo "<label for='email'>Email</label>";
                echo "<input type='text' name='email_pagseguro' style='text-transform:none;' value='$email'>";
                echo "<label for='Token'>Token</label>";
                echo "<input type='text' name='token' style='text-transform:none;' value='$token'>";
                echo "<input type='submit' class='submit' value='Enviar'>";
                if(mysqli_num_rows($sql)){
                    echo "<input type='button' class='submit' onclick='$(\"#deletarPagSeguro\").submit()'value='Deletar'>";
                }
                echo "<label>Caso ainda não tenha um token<br> do Uol PagSeguro, crie um <br>";
                echo "<a href='https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml'>aqui!</a></label>";
            echo "</div>";
        echo "</form>";
        echo "<form id='deletarPagSeguro' method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='pagamento' value='pagseguro'>";
            echo "<input type='hidden' name='op' value='deletar'>";
        echo "</form>";

    }
    
#
#
#
#
# aqui começa a operação do forma de pagamento
#
#
#
#
    
    
    if($pagamento=="forma" and !isset($op)){
        
        formPagamento("novo");
        
        echo "<table>";
            echo "<tr>";
                echo "<td>Id</td>";
                echo "<td>Forma de pagamento</td>";
                echo "<td>Status</td>";
            echo "</tr>";
            $sql = query("select * from pagamento_forma");
            for($i=0;$i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                echo "<tr>";
                    echo "<td>";
                        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
                            echo "<input type='hidden' name='pagamento' value='forma'>";
                            echo "<input type='hidden' name='op' value='visualizar'>";
                            echo "<input type='hidden' name='id' value='$id'>";
                            echo "<input type='submit' value='$id'>";
                        echo "</form>";
                    echo "</td>";
                    echo "<td>$forma_pagamento</td>";
                    echo "<td>$status</td>";
                echo "</tr>";
            }
        echo "</table>";
    }elseif($pagamento=="forma" and isset($op)){
            
        if($op=="novo"){
            
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome da forma de pagamento.<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_forma where forma_pagamento='$nome'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe uma forma de pagamento com esse nome.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            
            if($valida){
                $instrucao = "insert into pagamento_forma (forma_pagamento, descricao, status) values ('$nome', '$descricao', 'Ativo');";
                $sql = query($instrucao);
                confirmacaoDB("Nova forma de pagamento inserida com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                formPagamento("novo", null, $nome, $descricao);
            }
            
        }elseif($op=="visualizar"){
            formPagamento("editar", $id);   
        }elseif($op=="editar"){
                
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome da forma de pagamento.<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_forma where forma_pagamento='$nome' and id<>'$id'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe uma forma de pagamento com esse nome.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            $sql = query("select forma_pagamento from pagamento_forma where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            if($id<="2" and ($status=="Inativo" or $nome<>$forma_pagamento)){
                $info .= "Infelizmente não podemos alterar essa forma de pagamento.<br>";
                $valida = false;
            }
            
            if($valida){
                $instrucao = "update pagamento_forma set forma_pagamento='$nome', descricao='$descricao', status='$status' where id='$id'";
                $sql = query($instrucao);
                confirmacaoDB("Forma de pagamento editada com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                formPagamento("visualizar", $id);
            }
        }


#
#
#
#
# aqui começa a operação do tipo de pagamento
#
#
#
#


    }elseif($pagamento=="tipo" and !isset($op)){
        
        tipoPagamento("novo");
        
        echo "<table>";
            echo "<tr>";
                echo "<td>Id</td>";
                echo "<td>Tipo de pagamento</td>";
                echo "<td>Status</td>";
            echo "</tr>";
            $sql = query("select * from pagamento_tipo");
            for($i=0;$i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                echo "<tr>";
                    echo "<td>";
                        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
                            echo "<input type='hidden' name='pagamento' value='tipo'>";
                            echo "<input type='hidden' name='op' value='visualizar'>";
                            echo "<input type='hidden' name='id' value='$id'>";
                            echo "<input type='submit' value='$id'>";
                        echo "</form>";
                    echo "</td>";
                    echo "<td>$tipo_pagamento</td>";
                    echo "<td>$status</td>";
                echo "</tr>";
            }
        echo "</table>";
    }elseif($pagamento=="tipo" and isset($op)){
            
        if($op=="novo"){
            
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome do tipo de pagamento.<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_tipo where tipo_pagamento='$nome'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe um tipo de pagamento com esse nome.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            
            if($valida){
                $instrucao = "insert into pagamento_tipo (tipo_pagamento, status) values ('$nome', 'Ativo');";
                $sql = query($instrucao);
                confirmacaoDB("Novo tipo de pagamento inserido com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                tipoPagamento("novo", null, $nome);
            }
            
        }elseif($op=="visualizar"){
            tipoPagamento("editar", $id);   
        }elseif($op=="editar"){
                
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome do tipo de pagamento.<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_tipo where tipo_pagamento='$nome' and id<>'$id'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe um tipo de pagamento com esse nome.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            $sql = query("select tipo_pagamento from pagamento_tipo where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            if($id=="1" and ($status=="Inativo" or $nome<>$tipo_pagamento)){
                $info .= "Infelizmente não podemos alterar esse tipo de pagamento.<br>";
                $valida = false;
            }
            
            if($valida){
                $instrucao = "update pagamento_tipo set tipo_pagamento='$nome', status='$status' where id='$id'";
                $sql = query($instrucao);
                confirmacaoDB("Tipo de pagamento editado com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                tipoPagamento("visualizar", $id);
            }
        }
    }


#
#
#
#
# aqui começa a operação do sub tipo de pagamento
#
#
#
#


    elseif($pagamento=="subTipo" and !isset($op)){
        
        subTipoPagamento("novo");
        
        echo "<table>";
            echo "<tr>";
                echo "<td>Id</td>";
                echo "<td>Sub Tipo de pagamento</td>";
                echo "<td>Tipo de pagamento Associado</td>";
                echo "<td>Status</td>";
            echo "</tr>";
            $sql = query("select * from pagamento_tipo_sub order by id_pagamento_tipo");
            for($i=0;$i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                echo "<tr>";
                    echo "<td>";
                        echo "<form method='post' action='administrativoPagamento.php' enctype='multipart/form-data'>";
                            echo "<input type='hidden' name='pagamento' value='subTipo'>";
                            echo "<input type='hidden' name='op' value='visualizar'>";
                            echo "<input type='hidden' name='id' value='$id'>";
                            echo "<input type='submit' value='$id'>";
                        echo "</form>";
                    echo "</td>";
                    echo "<td>$sub_tipo_pagamento</td>";
                    echo "<td>".registro($id_pagamento_tipo, "pagamento_tipo", "tipo_pagamento")."</td>";
                    echo "<td>$status</td>";
                echo "</tr>";
            }
        echo "</table>";
    }elseif($pagamento=="subTipo" and isset($op)){
            
        if($op=="novo"){
            
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome do sub tipo de pagamento.<br>";
                $valida = false;
            }
            if($idTipoPagamento=="0"){
                $info .= "Por favor marque o tipo de pagamento que deseja associar o sub tipo de pagamento ($nome).<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_tipo_sub where sub_tipo_pagamento='$nome' and id_pagamento_tipo='$idTipoPagamento'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe um sub tipo de pagamento com esse nome.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            
            if($valida){
                $instrucao = "insert into pagamento_tipo_sub (id_pagamento_tipo, sub_tipo_pagamento, status) values ('$idTipoPagamento', '$nome', 'Ativo');";
                $sql = query($instrucao);
                confirmacaoDB("Novo sub tipo de pagamento inserido com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                subTipoPagamento("novo", null, $nome, $idTipoPagamento);
            }
            
        }elseif($op=="visualizar"){
            subTipoPagamento("editar", $id);   
        }elseif($op=="editar"){
                
            $valida = true;
            $info = "";
            
            if(empty($nome)){
                $info .= "Por favor digite o nome do sub tipo de pagamento.<br>";
                $valida = false;
            }
            if($idTipoPagamento=="0"){
                $info .= "Por favor marque o tipo de pagamento que deseja associar o sub tipo de pagamento ($nome).<br>";
                $valida = false;
            }
            $sql = query("select * from pagamento_tipo_sub where sub_tipo_pagamento='$nome' and id<>'$id' and id_pagamento_tipo='$idTipoPagamento'");
            if(mysqli_num_rows($sql)>0){
                $info .= "Já existe um sub tipo de pagamento com esse nome associado a esse tipo de pagamento.<br>Por favor digite outro nome.<br>";
                $valida = false;
            }
            /*
            $sql = query("select tipo_pagamento from pagamento_tipo where id='$id'");
            extract(mysqli_fetch_assoc($sql));
            if($id=="1" and ($status=="Inativo" or $nome<>$tipo_pagamento)){
                $info .= "Infelizmente não podemos alterar esse tipo de pagamento.<br>";
                $valida = false;
            }
            */
            if($valida){
                $instrucao = "update pagamento_tipo_sub set id_pagamento_tipo='$idTipoPagamento', sub_tipo_pagamento='$nome', status='$status' where id='$id'";
                $sql = query($instrucao);
                confirmacaoDB("Sub tipo de pagamento editado com sucesso", "administrativoPagamento.php");
            }else{
                echo info($info, "red");
                subTipoPagamento("visualizar", $id);
            }
        }
    }
    
#
#
#
#
# aqui começa a operação das parcelas
#
#
#
#


    elseif($pagamento=="parcelas" and !isset($op)){
        
        parcelaPagamento();
        
    }elseif($pagamento=="parcelas" and isset($op)){
        
        $valida = true;
        $info = "";
        
        if(empty($parcelaMax)){
            $info .= "Você não pode deixar de preencher o valor da parcela.<br>";
            $valida = false;
        }
        if($parcelaMax<1){
            $info .= "Parcela deve ser no mínimo igual a 1 (um).<br>";
            $valida = false;
        }
        
        if($valida){
            $sql = query("update pagamento_parcela set parcelaMax='$parcelaMax'");
            confirmacaoDB("O valor do parcelamento máximo editado com sucesso.", "administrativoPagamento.php");
        }else{
            info($info);
            parcelaPagamento();
        }
    }

#
#
#
#
# aqui começa a operação das parcelas
#
#
#
#
    elseif($pagamento=="pagseguro" and !isset($op)){
        formPagseguro();
    }elseif($pagamento=="pagseguro" and isset($op)){
        
        if($op=="editar"){
            $sql = query("select * from pagseguro");
            if(mysqli_num_rows($sql)){
                $sql = query("UPDATE `pagseguro` SET `email`='$email_pagseguro',`token`='$token' WHERE 1");    
            }else{
                $sql = query("insert pagseguro (email, token) values ('$email_pagseguro', '$token')");
            }    
        }elseif($op=="deletar"){
            $sql = query("delete from pagseguro where 1");
        }
        

        formPagseguro();
    }
    
}else{
    
    echo "<form method='get' action='administrativoPagamento.php' enctype='multipart/forma-data'>";
        echo "Você deseja editar:<br>";
        echo "<select name='pagamento' style='width:auto;'>";
            echo "<option value='forma'>Forma de pagamento</option>";
            echo "<option value='tipo'>Tipo de pagamento</option>";
            echo "<option value='subTipo'>Sub Tipo de pagamento</option>";
            echo "<option value='parcelas'>Máximo de parcelas</option>";
            echo "<option value='pagseguro'>Login e Token do PagSeguro</option>";
        echo "</select><br>";
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
    echo "</form>";
    
}

//end all

include "templates/downLogin.inc.php";
?>