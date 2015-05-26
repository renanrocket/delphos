<?php
include "templates/upLogin.inc.php";


extract($_POST);
extract($_GET);

if(!isset($data1) and !isset($data2)){
    
    echo "<form class='form' name='formulario' method='post' action='relatorioMovimentoCaixa.php' enctype='multipart/form-data'>";
        echo "<div class='column'>";
            echo "<label for='data1'>Intervalo da pesquisa:</label>";
            echo inputData("formulario", "data1", null);
            echo " Hora: <select name='inicial_hora' style='display:inline-block; width:auto;'>";
            for($i=00; $i<=23; $i++){
                echo "<option value='$i'>$i</option>";
            }
            echo "</select>";
            echo " Min: <select name='inicial_min' style='display:inline-block; width:auto;'>";
            for($i=00; $i<=59; $i++){
                echo "<option value='$i'>$i</option>";
            }
            echo "</select>";
            echo "<label for='data2'>à</label>";
            echo inputData("formulario", "data2", null);
            echo "Hora: <select name='final_hora' style='display:inline-block; width:auto;'>";
            for($i=00; $i<=23; $i++){
                if($i==23){
                    echo "<option value='$i' selected='yes'>$i</option>";
                }else{
                    echo "<option value='$i'>$i</option>";
                }
            }
            echo "</select>";
            echo " Min: <select name='final_min' style='display:inline-block; width:auto;'>";
            for($i=00; $i<=59; $i++){
                if($i==59){
                    echo "<option value='$i' selected='yes'>$i</option>";
                }else{
                    echo "<option value='$i'>$i</option>";
                }
            }
            echo "</select>";
            echo "<br><input type='submit' class='submit' value='Enviar'>";
        echo "</div>";
    echo "</form>";
    
}else{
    

    $data1 = formataDataInv($_POST["data1"])." ".$_POST['inicial_hora'].":".$_POST['inicial_min'].":00";
    $data2 = formataDataInv($_POST["data2"])." ".$_POST['final_hora'].":".$_POST['final_min'].":00";
    
    if(strstr($data1, "0000-00-00") and strstr($data2, "0000-00-00")){
        $data1=date('0000-00-00 H:i:s');
        $data2=date('Y-m-d H:i:s');
    }

    //verificando qual das datas é maior e ajustando
    $dataMaior = strtotime($data1);
    $dataMenor = strtotime($data2);
    if ($dataMaior - $dataMenor > 0) {
        //troca
        $DATA = $data1;
        $data1 = $data2;
        $data2 = $DATA;
    }
    
    
    echo "<center>
    <table id='gradient-style' summary='Resultado da pesquisa'>
        <thead>
            <tr>
                <th scope='col' colspan='7'>Valores Atuais em cada Caixa</th>
            </tr>
        </thead>";
    
    $instrucao = "select * from caixa";
    $sql = query($instrucao);
    for($i = $Total = 0;$i<mysqli_num_rows($sql);$i++){
        extract(mysqli_fetch_assoc($sql));
        $sqlMovimento = query("select sum(valor) as credito from caixa_movimento where debito_credito='1' and id_caixa='$id'");
        extract(mysqli_fetch_assoc($sqlMovimento));
        $sqlMovimento = query("select sum(valor) as debito from caixa_movimento where debito_credito='0' and id_caixa='$id'");
        extract(mysqli_fetch_assoc($sqlMovimento));
        
        $total = round($credito,2) - round($debito,2);
        $Total += $total;
        
        echo "<tr>
                <td>$nome</td>
                <td colspan='5'>$descricao</td>
                <td>".inputValor(round($total,2))."</td>
            </tr>";
    }
        echo "<thead>
            <tr>
                <th scope='col' colspan='6' align='right'>Total</th>
                <th scope='col'>".inputValor($Total)."</th>
            </tr>
        </thead>";
        
         echo "<thead>
            <tr>
                <th scope='col' colspan='7' align='center'>Histórico de movimento de caixa no periodo ".formataData($data1)." à ".formataData($data2)."</th>
            </tr>
        </thead>";
        
        $instrucao = "select * from caixa_movimento where data>='$data1' and data<='$data2' order by data";
        $sql = query($instrucao);
        $linha = mysqli_num_rows($sql);
        
        for($i=$Total=0;$i<$linha;$i++){
            
            extract(mysqli_fetch_assoc($sql));
            
            if($debito_credito==0){
                $Total -= $valor;
                $cod = "<span style='color:red; font-weight: bold;'>R$ (-) ";
            }else{
                $Total += $valor;
                $cod = "R$ (+) ";
            }
            
            echo "<tr>";
            if(registro($id, "conta_itens", "id", "id_caixa_movimento")){
                echo "<td><a class='aSubmit' href='pesquisaConta2.php?conta=".base64_encode(registro($id, "conta_itens", "id_conta", "id_caixa_movimento"))."'>".registro($id, "conta_itens", "id_conta", "id_caixa_movimento")."</a></td>";
            }else{
                echo "<td></td>";
            }
                    
                echo "<td>".registro($id_caixa, "caixa", "nome")."</td>
                    <td>".registro(registro($id, "conta_itens", "tipo_pagamento", "id_caixa_movimento"), "pagamento_tipo", "tipo_pagamento")."</td>
                    <td>".registro(registro($id, "conta_itens", "tipo_pagamento_sub", "id_caixa_movimento"), "pagamento_tipo_sub", "sub_tipo_pagamento")."</td>
                    <td>$cod".real($valor)."</td>
                    <td>".formataData($data)."</td>
                    <td>".getNomeCookieLogin(registro($id_usuario, "usuario", "login"), false)."</td>
                </tr>";
            
        }
        echo "<thead>
            <tr>
                <th scope='col' colspan='2' align='right'>Total do Período</th>
                <th scope='col' colspan='3'>".inputValor($Total)."</th>
                <th scope='col' colspan='2'></th>
            </tr>
        </thead>";
        echo "<thead>
            <tr>
                <th scope='col' colspan='3'></th>
                <th scope='col' colspan='2' align='center'>Débitos</th>
                <th scope='col' colspan='2' align='center'>Créditos</th>
            </tr>
        </thead>";
        
        $sql = query("select * from pagamento_tipo where status='Ativo'");
        for($i=0; $i < mysqli_num_rows($sql) ;$i++){
            
            extract(mysqli_fetch_assoc($sql));
            
            echo "<tr>";
                    echo "<td colspan='3'>$tipo_pagamento</th>";
                    
                    
                    $sqlPagamento = query("select sum(valor) as totalDebito from conta_itens where data_pagamento>='$data1 00:00:00' and data_pagamento<='$data2 23:59:59' and id_conta = any(select id from conta where status='3') and tipo_pagamento='$id' and id_caixa_movimento is not null");
                    extract(mysqli_fetch_assoc($sqlPagamento));
                    echo "<td style='white-space:nowrap;'>R$ ".real(round($totalDebito,2))."</td>";
                    $sqlPagamentoSub = query("select id as id_pagamento_tipo_sub, sub_tipo_pagamento from pagamento_tipo_sub where id_pagamento_tipo='$id'");
                    echo "<td>";
                    echo "<table>";
                    for($j=0; $j<mysqli_num_rows($sqlPagamentoSub);$j++){
                        extract(mysqli_fetch_assoc($sqlPagamentoSub));
                        $instrucao = "select sum(valor) as totalDebitoSub from conta_itens where data_pagamento>='$data1 00:00:00' and data_pagamento<='$data2 23:59:59' and id_conta = any(select id from conta where status='3')  and tipo_pagamento_sub='$id_pagamento_tipo_sub' and id_caixa_movimento is not null";
                        $sqlSoma = query($instrucao);
                        extract(mysqli_fetch_assoc($sqlSoma));
                        echo "<tr>";
                            echo "<td>$sub_tipo_pagamento</td>";
                            echo "<td style='white-space:nowrap;'>R$ ".real(round($totalDebitoSub,2))."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</td>";
                    
                    $sqlPagamento = query("select sum(valor) as totalCredito from conta_itens where data_pagamento>='$data1 00:00:00' and data_pagamento<='$data2 23:59:59' and id_conta = any(select id from conta where status='2' or status='1') and tipo_pagamento='$id' and id_caixa_movimento is not null");
                    extract(mysqli_fetch_assoc($sqlPagamento));
                    echo "<td style='white-space:nowrap;'>R$ ".real(round($totalCredito,2))."</td>";
                    $sqlPagamentoSub = query("select id as id_pagamento_tipo_sub, sub_tipo_pagamento from pagamento_tipo_sub where id_pagamento_tipo='$id'");
                    echo "<td>";
                    echo "<table>";
                    for($j=0; $j<mysqli_num_rows($sqlPagamentoSub);$j++){
                        extract(mysqli_fetch_assoc($sqlPagamentoSub));
                        $instrucao = "select sum(valor) as totalCreditoSub from conta_itens where data_pagamento>='$data1 00:00:00' and data_pagamento<='$data2 23:59:59' and id_conta = any(select id from conta where status='2' or status='1')  and tipo_pagamento_sub='$id_pagamento_tipo_sub' and id_caixa_movimento is not null";
                        $sqlSoma = query($instrucao);
                        extract(mysqli_fetch_assoc($sqlSoma));
                        echo "<tr>";
                            echo "<td>$sub_tipo_pagamento</td>";
                            echo "<td style='white-space:nowrap;'>R$ ".real(round($totalCreditoSub,2))."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</td>";
                echo "</tr>";
                
            
        }
        
    echo "</table>";
    
    
}

include "templates/downLogin.inc.php";
?>

