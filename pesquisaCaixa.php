<?php
include "templates/upLogin.inc.php";

extract($_POST);

if(!isset($data1) and !isset($data2)){
    
    echo "<form name='formulario' method='post' action='pesquisaCaixa.php' enctype='multipart/form-data'>";
        echo "Intervalo da pesquisa:<br>";
        echo inputData("formulario", "data1", null)."<br>à<br>".inputData("formulario", "data2", null);
        echo "<br><input type='submit' class='btnEnviar' value='Enviar'>";
    echo "</form>";
    
}else{
    
    $data1 = formataDataInv($_POST["data1"]);
    $data2 = formataDataInv($_POST["data2"]);
    if($data1=="0000-00-00" and $data2=="0000-00-00"){
        $data2=date('Y-m-d');
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
    
    
    $instrucao = "select * from caixa_movimento where data>='$data1 00:00:00' and data<='$data2 23:59:59' order by data";
    $sql = query($instrucao);
    $linha = mysqli_num_rows($sql);
    
    echo "<center>
    <table id='gradient-style' summary='Resultado da pesquisa'>
        <thead>
            <tr>
                <th scope='col'>Operação</th>
                <th scope='col'>Valor</th>
                <th scope='col'>Caixa</th>
                <th scope='col'>Data e hora</th>
                <th scope='col'>Usuário</th>
                <th scope='col'><a href='#' title='O movimento do caixa somente pode ser <br>deletado caso não seja associado a <br>nenhuma conta'><img src='img/info.png' class='imgHelp'></a></th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan='7' align='center'>$linha Movimentações</td>
            </tr>
        </tfoot>

        <tbody>";
    
    for($i=$total=0; $i<$linha; $i++){
            
        extract(mysqli_fetch_assoc($sql));
        
        echo "<tr>";
        if($debito_credito==0){
            $debito_credito = "Débito";
            $cor = "red";
            $total-=$valor;
        }else{
            $debito_credito = "Crédito";
            $cor = "green";
            $total+=$valor;
        }
        $valor = real($valor);
        echo "<td>$debito_credito</td>
        <td style='white-space:nowrap; color:$cor;'><b>R$ $valor</b></td>
        <td style='white-space:nowrap;'>".registro($id_caixa, "caixa", "nome")."</td>
        <td style='white-space:nowrap;'>".formataData($data)."</td>
        <td style='white-space:nowrap;'>".registro($id_usuario, "usuario", "nome")."</td>";
        $instrucao = "select id as idConta from conta where id=(select id_conta from conta_itens where id_caixa_movimento='$id')";
        $sqlConta = query($instrucao);
        if(mysqli_num_rows($sqlConta)==1){
            extract(mysqli_fetch_assoc($sqlConta));
            echo "<td style='white-space:nowrap;'>Conta <a class='aSubmit' href='pesquisaConta2.php?conta=".base64_encode($idConta)."' title='Visualizar Conta'>$idConta</td>";
        }else{
            echo "<td style='white-space:nowrap;'><a href='cadastrarCaixa.php?id=".base64_encode($id)."&op=deletar' title='Deletar movimento de conta'><img style='width:30px;' src='img/deletar.png'></a></td>";
        }
        echo "<tr>";
        
        
    }
    if($total<0){
        $cor = "red";
    }else{
        $cor = "green";
    }
    echo "<tr>";
        echo "<td>Total</td>";
        echo "<td tyle='white-space:nowrap;'><b style='font-size:18px; color:$cor;'>R$ ".real($total)."</b></td>";
        echo "<td colspan='4'></td>";
    echo "</tr>";
    echo "</tbody>
        </table>";
}



include "templates/downLogin.inc.php";
?>

