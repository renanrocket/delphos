<?php
include "templates/upLogin.inc.php";
$conn = TConnection::open(ALCUNHA);
?>
<script type="text/javascript">
    function filtro(){
        var valida = true;
        var msg = '';
        if($('input[name="data1"]').val().length<10 && $('input[name="data1"]').val().length>0){
            valida = false;
            msg+= 'Data 1 inválida. Digite seguindo o exemplo: 00/00/0000<br>';
            $('input[name="data1"').addClass('avisoInput'); 
        }else{
            valida = true;
            $('input[name="data1"').removeClass('avisoInput'); 
        }
        if($('input[name="data2"]').val().length<10 && $('input[name="data2"]').val().length>0){
            valida = false;
            msg+= 'Data 2 inválida. Digite seguindo o exemplo: 00/00/0000<br>';
            $('input[name="data2"').addClass('avisoInput'); 
        }else{
            if($('input[name="data1"]').val().length<10 && $('input[name="data1"]').val().length>0){
                valida = false;
            }else{
                valida = true;
            }
            $('input[name="data2"').removeClass('avisoInput'); 
        }

        if(!valida){
            $('.avisoForm1').show();
            $('#span_avisoForm1').html(msg);
        }
        return valida;
    }
</script>
<?php
extract($_GET);
extract($_POST);


function formularioPesquisa(){
    
    global $conn;

    $info = new info;
    $info->msg = null;
    $info->cor = 'red';
    $info->display = 'none';
    $info->class = 'avisoForm1';
    echo $info->getInfo();

    echo "<form class='form' name='formulario' method='post' action='relatorioHistoricoVendas.php' enctype='multipart/form-data' onsubmit='return filtro();'>";
        echo "<div class='column'>";
        echo "<label for='data1'>Data Inicial</label>";
        echo inputData("formulario", "data1", null);
        echo "Hora: <select name='inicial_hora' style='display:inline-block; width:auto;'>";
        for($i=00; $i<=23; $i++){
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";
        echo " Min: <select name='inicial_min' style='display:inline-block; width:auto;'>";
        for($i=00; $i<=59; $i++){
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";
        echo "<label for='data2'>Data Final</label>";
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
        echo "</div>";
        echo "<div class='column'>";
        echo "<label for='usuario'>Vendas efetuadas:</label>";
        echo "<select name='usuario' id='usuario'>";
            echo "<option value='Todas as vendas'>Todas as vendas</option>";
            echo "<option value='Excerto o PDV'>Excerto o PDV</option>";
            echo "<option value='Somente o PDV'>Somente o PDV</option>";
            echo opcaoSelect("usuario", 3, "ativo",null,null,null,0,false);
        echo "</select>";

        $sql = new TSqlSelect;
        $sql->setEntity('registro');
        $sql->addColumn('max(id)');

        $criterio = new TCriteria;
        $criterio->add(new TFilter('id', '=', '('.$sql->getInstruction().')'));

        $sql = new TSqlSelect;
        $sql->setEntity('registro');
        $sql->addColumn('valor');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());
        
        if($result->rowCount()>0){
            extract($result->fetch(PDO::FETCH_ASSOC));
            $chave = new chave;
            $chave -> valor = $valor;
            $chave -> decodificar_atribuir();
            if($chave->modulo!='imoveis' or $chave->modulo!='academia'){
                echo "<label for='produtos'>Produtos:</label>";
                echo "<select name='produtos' id='produtos'>";
                    echo "<option value='Todos os produtos'>Todos os Produtos</option>";
                    echo "<option value='Discriminar por produtos'>Discriminar por produtos</option>";
                echo "</select>";        
            }else{
                echo "<input type='hidden' name='produtos' value='Todos os produtos'>";
            }
        }else{
            echo "<input type='hidden' name='produtos' value='Todos os produtos'>";
        }
        $info = new info;
        $info->msg = 'Carregando relatório<br><br><img src="img/loading_bar.gif">';
        $info->cor = 'blue';
        $info->display = 'none';
        $info->class = 'info_loading';
        echo $info->getInfo();
        echo "<div class='submit-wrap'><input type='submit' ".$info->getJs()." class='submit' value='Enviar'></div>";
        
        echo "</div>";
    echo "</form>";
}

if(isset($data1) and isset($data2)){
    
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
    //calculando diferença entre as datas
    $dataA = explode("-", $data1);
    $dataB = explode("-", $data2);
    $time = mktime(0,0,0, $dataB[1], $dataB[2], $dataB[0]) - mktime(0,0,0, $dataA[1], $dataA[2], $dataA[0]);
    $intervalo = intval($time/86400);
    if(!isset($inicial_hora)){
        $inicial_hora = $inicial_min = '00';
        $final_hora = '23';
        $final_min = '59';
    }
    /*
    *
    *
    *
    *   Esse script de correção ficará aqui até descobrir o que está causando o erro e corrigi-lo
    *
    *
    *
    */
    $sql = query("select * from conta where status='2' and data>='$data1 $inicial_hora:$inicial_min:00' and data<='$data2 $final_hora:$final_min:59'");
    for($i=0; $i<mysqli_num_rows($sql); $i++){
        extract(mysqli_fetch_assoc($sql));
        if($tabela_referido=="pdv" or $tabela_referido=="orcamento"){

            if($tabela_referido=="pdv"){
                $instrucao = "select data as data_venda from pdv_itens where id_pdv='$referido' group by id_pdv";
            }elseif($tabela_referido=="orcamento"){
                $instrucao = "select data_venda from orcamento where id='$referido' and status='2'";
            }
            $sql2= query($instrucao);
            if(mysqli_num_rows($sql2)){
                extract(mysqli_fetch_assoc($sql2));

                $data_venda = explode(" ", $data_venda);
                $data = explode(" ", $data);
                if($data[0]!=$data_venda[0]){
                    if(!isset($data_venda[1])){
                        $data_venda[1]="".$inicial_hora.":".$inicial_min.":00";
                    }
                    $sql3= query("update conta set data='".$data_venda[0]." ".$data_venda[1]."' where id='$id'");
                    //echo "Conta $id alterada de ".$data[0]." ".$data[1]." para ".$data_venda[0]." ".$data_venda[1]."<br>";
                }
            }
        }
    }
    //fim do script de correção

	
    
    if($produtos=='Todos os produtos'){


        //declarando todas as variaveis importantes
        $titulo = $arrayTag = $matrizGraficoV = $matrizGraficoR = $array = $ultimaLinha = $instrucao = null;
        $labels = $datasets = null;
         /*
         * Para que seja realmente uma venda a tabela conta o status tem q ser = 2 e o referido ser numerico
         * para que seja uma venda proveniente de orçamento a entidade deve ser diferente de PDV
         * para que seja uma venda proveniente de um PDV a entidade deve ser igual a PDV
         */
        $instrucao = null;
        if($usuario=="Todas as vendas"){
            $instrucao[0] = " from conta where data>='";
            $instrucao[1] = " ".$inicial_hora.":".$inicial_min.":00' and data<='";
            $instrucao[2] = " ".$final_hora.":".$final_min.":59' and status='2' and tabela_referido is not null"; // para excluir contas a receber que não são provenientes de nenhuma venda
        }elseif($usuario == "Excerto o PDV"){
            $instrucao[0] = " from conta where data>='";
            $instrucao[1] = " ".$inicial_hora.":".$inicial_min.":00' and data<='";
            $instrucao[2] = " ".$final_hora.":".$final_min.":59' and status='2' and entidade<>'PDV' and tabela_referido is not NULL"; // para excluir contas a receber que não são provenientes de nenhuma venda
        }elseif($usuario=="Somente o PDV"){
            $instrucao[0] = " from conta where data>='";
            $instrucao[1] = " ".$inicial_hora.":".$inicial_min.":00' and data<='";
            $instrucao[2] = " ".$final_hora.":".$final_min.":59' and status='2' and entidade='PDV'";
        }elseif(is_numeric($usuario) and $usuario<>0){
            $instrucao[0] = " from conta where data>='";
            $instrucao[1] = " ".$inicial_hora.":".$inicial_min.":00' and data<='";
            $instrucao[2] = " ".$final_hora.":".$final_min.":59' and status='2' and id_usuario='$usuario'";
        }elseif($usuario==0){
            info("Você deve selecionar uma opção diferente de \"--\"");
            formularioPesquisa();
            die;
        }

        /*    
        if($produtos!='Todos os produtos'){
            $instrucao[3] = " and \ncase conta.tabela_referido ";
            $instrucao[3].= "\nwhen 'pdv' then conta.referido= any(select id from pdv where id= any(select id_pdv from pdv_itens where id_produto='$produtos')) ";
            $instrucao[3].= "\nwhen 'orcamento' then conta.referido= any(select id from orcamento where id= any(select id_orcamento from orcamento_itens where id_item='$produtos' and tabela_item='produto')) end ";
            //$instrucao[3].= "\nwhen 'plano_assinatura' then conta.referido= any(select id from matricula where id= any(select id_matricula from matricula_plano_assinatura where id_plano_assinatura='$produtos')) ";
            //$instrucao[3].= "\nwhen 'ordem_servico' then conta.referido= any(select id from ordem_servico where id_servico='$produtos') end ";
        }else{
            $instrucao[3] = null;
        }
        */
        
        //$instrucao[3] = " and \ncase conta.tabela_referido ";
        //$instrucao[3].= "\nwhen 'pdv' then conta.referido= any(select id from pdv where id= any(select id_pdv from pdv_itens where id_produto='$produtos')) ";
        //$instrucao[3].= "\nwhen 'orcamento' then conta.referido= any(select id from orcamento where id= any(select id_orcamento from orcamento_itens where id_item='$produtos' and tabela_item='produto')) end ";
        



        $titulo = "Balanço de vendas de ".formataData($data1)." a ".formataData($data2)."<br>Forma de pesquisa: ";
       //$INSTRUCAO = "select conta.* ". $instrucao[0] . $data1 . $instrucao[1] . $data2 . $instrucao[2] ." "./*$instrucao[3]*/." order by data";
        $INSTRUCAO = "select conta.* ". $instrucao[0] . $data1 . $instrucao[1] . $data2 . $instrucao[2] ." order by data";
        $sql = query($INSTRUCAO);
        //relatório especifico
        //echo $INSTRUCAO;
        if($intervalo < 31){
            
            if(is_numeric($usuario)){
                $titulo .= registro($usuario, "usuario", "nome");
            }else{
                $titulo .= $usuario;
            }
            if(is_numeric($produtos)){
                $arrayTag = array("ID", "Vendedor", "Cliente", "Data", "Valor Vendido", "Pespectiva de lucro", "Empresa");
            }else{
                $arrayTag = array("ID", "Vendedor", "Cliente", "Data", "Valor Vendido", "Pespectiva de lucro", "Valor Recebido", "Forma de Pagamento", "Parcelas", "Empresa");
            }
            
            for($i=$TotalRecebido=$TotalLucro=$total=0; $i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                
                $form = "<form method='get' action='pesquisaConta2.php' enctype='multipart/form-data'>";
                    $form .= "<input type='hidden' name='conta' value='".base64_encode($id)."'>";
                    $form .= "<input type='submit' value='$id'>";
                $form .= "</form>";
                
                $array[] = $form;
                $array[] = primeiroUltimo(registro($id_usuario, "usuario", "nome"), true);
                if(is_numeric($entidade)){
                    $array[] = primeiroUltimo(registro($entidade, $tabela_entidade, "nome"), true);
                }else{
                    $array[] = $entidade;
                }
                $array[] = formataData($data, false, true);

                if($tabela_referido=="orcamento"){
                    $instrucaoLucro = "select tabela_item, id_item as item, quantidade, valor_produto as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido' ";
                }elseif($tabela_referido=="pdv"){
                    $instrucaoLucro = "select id_produto as item, quantidade, preco as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido' ";
                    $tabela_item="produto";
                }elseif($tabela_referido=="ordem_servico" and !is_numerico($produtos)){
                    $instrucaoLucro = "select id_servico as item, quantidade, valor from $tabela_referido where id='$referido'";
                    $tabela_item="servico";
                }elseif($tabela_referido=="plano_assinatura" and !is_numerico($produtos)){
                    $instrucaoLucro = "select valor from plano_assinatura where id=(select id_plano_assinatura from matricula_plano_assinatura where id='$referido')";
                }

                $sqlLucroItens= query($instrucaoLucro);
                
                for($l=$valorLucro=$valorVendido=0; $l<mysqli_num_rows($sqlLucroItens); $l++){
                    extract(mysqli_fetch_assoc($sqlLucroItens));
                    
                    if($tabela_referido=="orcamento"){
                        if($tabela_item=="produto"){
                            $valorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                            $valorVendido += $valor * $quantidade;
                        }elseif($tabela_item=="servico"){
                            $sqlServicoProduto = query("select id_produto, qtd from servico_produto where id_servico='$item'");
                            for($h=0; $h<mysqli_num_rows($sqlServicoProduto); $h++){
                                extract(mysqli_fetch_assoc($sqlServicoProduto));
                                $valorParcial = precoProduto($id_produto, false, false, true) * $qtd;
                            }
                            if($valor*$quantidade>$valorParcial){
                                $valorLucro += $valor * $quantidade - $valorParcial;    
                            }else{
                                $valorLucro += $valorParcial - $valor * $quantidade;
                            }
                            $valorVendido += $valor * $quantidade;
                        }elseif($tabela_item=="item"){
                            $valorLucro += $valor;
                        }

                    }elseif($tabela_referido=="pdv"){
                        $valorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                        $valorVendido += $valor * $quantidade;
                        
                    }elseif($tabela_referido=="ordem_servico"){
                        $sqlServicoProduto = query("select id_produto, qtd from servico_produto where id_servico='$item'");
                        for($h=0; $h<mysqli_num_rows($sqlServicoProduto); $h++){
                            extract(mysqli_fetch_assoc($sqlServicoProduto));
                            $valorParcial = precoProduto($item, false, false, true) * $qtd;
                        }
                        $valorLucro += $valor * $quantidade - $valorParcial;
                        $valorVendido += $valor * $quantidade;
                    }elseif($tabela_referido=="plano_assinatura"){
                        $valorLucro += $valor;
                    }
                }
                
                $array[] = "R$ ".real(round($valorVendido,2));
                $total+= $valorVendido;     
                $array[] = "R$ ".real(round($valorLucro,2));
                if(!is_numeric($produtos)){
                    extract(mysqli_fetch_assoc(query("select sum(valor) as totalRecebido from conta_itens where id_conta=$id")));
                    $array[] = "R$ ".real(round($totalRecebido,2));
                    $array[] = registro($forma_pagamento, "pagamento_forma", "forma_pagamento");
                    $array[] = $parcelas;
                    $TotalRecebido += $totalRecebido;
                }
                $array[] = registro($empresa, "empresa", "nome");
                $TotalLucro+= $valorLucro;
            }
            
            //$sql = query("select sum(valor) as total".$instrucao[0].$data1.$instrucao[1].$data2.$instrucao[2].$instrucao[3]);
            //extract(mysqli_fetch_assoc($sql));
            $ultimaLinha = "<tr><td colspan='4' align='right'>Total</td>";
            $ultimaLinha.= "<td class='totalValor' align='center'>R$ ".real(round($total,2))."</td>";
            $ultimaLinha.= "<td class='totalValor' align='center'>R$ ".real(round($TotalLucro,2))."</td>";
            if(!is_numeric($produtos)){
                $ultimaLinha.= "<td class='totalValor' align='right'>R$ ".real(round($TotalRecebido,2))."</td>";
                $ultimaLinha.= "<td colspan='3'></td>";
            }
            $ultimaLinha.= "<tr>";
            
        }elseif($intervalo >= 31){
            
            if(is_numeric($produtos)){
                info("A pesquisa de venda de produto num periodo maior que 30 dias não está habilitada.");
                include "templates/downLogin.inc.php";
                die;
            }
            
            if(is_numeric($usuario)){
                $titulo .= registro($usuario, "usuario", "nome");
            }else{
                $titulo .= $usuario;
            }
            if(!is_numeric($produtos)){
                $arrayTag = array("Mês", "Valor de vendas", "Pespectiva de lucro", "Valor Recebido" , "Quem mais Vendeu", "Quem mais recebeu");
            }else{
                $arrayTag = array("Mês", "Valor de vendas", "Pespectiva de lucro", "Qtd Vendido");
            }
            
            for($i=$TotalRecebido=$TotalVenda=$TotalLucro=$valorVenda=$valorLucro=$valorRecebido=$TotalQtd=0, $checkMes[] = null; $i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                
                $dataPesquisa = explode("-", $data);
                $dataPesquisaMes = $dataPesquisa[0]."-".$dataPesquisa[1];
               
                if(!in_array($dataPesquisaMes, $checkMes)){//se caso o mes ja não tiver sido feito a busca fazer a busca, caso contrario pular e continuar o loop
                    
                    $diaInicial = $dataPesquisa[1]==$dataA[1] ? $dataA[2] : 01;
                    $diaFinal = $dataPesquisa[1]==$dataB[1] ? $dataB[2] : date("t", mktime(0,0,0, $dataPesquisa[1], '01', $dataPesquisa[0]));
                    
                    $instrucaoNovo = $instrucao[0].$dataPesquisa[0]."-".$dataPesquisa[1]."-".$diaInicial.$instrucao[1].$dataPesquisa[0]."-".$dataPesquisa[1]."-".$diaFinal.$instrucao[2];

                    $formulario = "<form method='post' action='relatorioHistoricoVendas.php' enctype='multipart/form-data'>";
                    $formulario .= "<input type='hidden' name='data1' value='".$diaInicial."/".$dataPesquisa[1]."/".$dataPesquisa[0]."'>";
                    $formulario .= "<input type='hidden' name='data2' value='".$diaFinal."/".$dataPesquisa[1]."/".$dataPesquisa[0]."'>";
                    $formulario .= "<input type='hidden' name='usuario' value='".$_POST["usuario"]."'>";
                    $formulario .= "<input type='hidden' name='produtos' value='".$_POST["produtos"]."'>";
                    $formulario .= "<input type='submit' style='min-width:130px;' value='".mesNominal($data)." ". $dataPesquisa[0]."'>";
                    $formulario .= "</form>";
                    
                    $array[] = $formulario;
                    if(!is_numeric($produtos)){

                        $instrucaoVendido = "select sum(valor) as valorVenda".$instrucaoNovo;
                        
                        extract(mysqli_fetch_assoc(query($instrucaoVendido)));
                        $array[] = "R$ ".real(round($valorVenda,2));
                        $TotalVenda += $valorVenda;
                        
                        $sqlLucro = query("select referido, tabela_referido".$instrucaoNovo);
                        for($j=$valorLucro=0; $j<mysqli_num_rows($sqlLucro); $j++){
                            extract(mysqli_fetch_assoc($sqlLucro));
                            if($tabela_referido=="orcamento"){
                                $instrucaoLucro = "select tabela_item, id_item as item, quantidade, valor_produto as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido'";
                            }elseif($tabela_referido=="pdv"){
                                $instrucaoLucro = "select id_produto as item, quantidade, preco as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido'";
                                $tabela_item="produto";
                            }elseif($tabela_referido=="ordem_servico"){
                                $instrucaoLucro = "select id_servico as item, quantidade, valor from $tabela_referido where id='$referido'";
                                $tabela_item="servico";
                            }elseif($tabela_referido=="plano_assinatura"){
                                $instrucaoLucro = "select sum(valor) as valor from conta_itens where valor is not null and id_conta = any(select id".$instrucaoNovo.")";
                                $instrucaoLucro = str_replace("status='2'", "status='3'", $instrucaoLucro);
                            }
                            $sqlLucroItens= query($instrucaoLucro);
                            for($l=0; $l<mysqli_num_rows($sqlLucroItens); $l++){
                                extract(mysqli_fetch_assoc($sqlLucroItens));
                                
                                if($tabela_referido=="orcamento"){
                                    if($tabela_item=="produto"){
                                        $valorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                                    }elseif($tabela_item=="servico"){
                                        $sqlServicoProduto = query("select id_produto, qtd from servico_produto where id_servico='$item'");
                                        for($h=0; $h<mysqli_num_rows($sqlServicoProduto); $h++){
                                            extract(mysqli_fetch_assoc($sqlServicoProduto));
                                            $valorParcial = precoProduto($id_produto, false, false, true) * $qtd;
                                        }
                                        if($valor*$quantidade>$valorParcial){
                                            $valorLucro += $valor * $quantidade - $valorParcial;    
                                        }else{
                                            $valorLucro += $valorParcial - $valor * $quantidade;
                                        }

                                    }elseif($tabela_item=="item"){
                                        $valorLucro += $valor;
                                    }
                                }elseif($tabela_referido=="pdv"){
                                    $valorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                                }elseif($tabela_referido=="ordem_servico"){
                                    $sqlServicoProduto = query("select id_produto, qtd from servico_produto where id_servico='$item'");
                                    for($h=0; $h<mysqli_num_rows($sqlServicoProduto); $h++){
                                        extract(mysqli_fetch_assoc($sqlServicoProduto));
                                        $valorParcial = precoProduto($item, false, false, true) * $qtd;
                                    }
                                    $valorLucro += $valor * $quantidade - $valorParcial;
                                }elseif($tabela_referido=="plano_assinatura"){
                                    $valorLucro += $valor;
                                }

                            }
                        }
                        $array[] = "R$ ".real(round($valorLucro,2));
                        $TotalLucro += $valorLucro;

                        $instrucaoRecebido = "select sum(valor) as valorRecebido from conta_itens where valor is not null and id_conta = any(select id".$instrucaoNovo.")";
                        extract(mysqli_fetch_assoc(query($instrucaoRecebido)));
                        $array[] = "R$ ".real(round($valorRecebido,2));
                        $TotalRecebido += $valorRecebido;

                        $sqlVendedorMes = query("select count(id_usuario) as usuario, id_usuario as vendedorMes".$instrucaoNovo." group by id_usuario order by usuario desc limit 1");
                        if(mysqli_num_rows($sqlVendedorMes)){
                            extract(mysqli_fetch_assoc($sqlVendedorMes));
                        }else{
                            $usuario = $vendedorMes = null;
                        }
                        //extract(mysqli_fetch_assoc(query("select count(id_usuario) as usuario, id_usuario as vendedorMes".$instrucaoNovo." group by id_usuario order by usuario desc limit 1")));
                        $array[] = getNomeCookieLogin(registro($vendedorMes, "usuario", "login"), false);
                        $sqlRecebeu = query("select count(id_usuario) as usuario, sum(valor) as valorArrecadado, id_usuario as arrecadadorMes from conta_itens where valor is not null and id_conta = any(select id ".$instrucaoNovo.") group by id_usuario order by usuario desc limit 1");
                        if(mysqli_num_rows($sqlRecebeu)){
                            extract(mysqli_fetch_assoc($sqlRecebeu));
                            $array[] = getNomeCookieLogin(registro($arrecadadorMes, "usuario", "login"), false);
                        }else{
                            $array[] = "--";
                        }
                    }else{
                        /*
                        $sqlMeses = query($INSTRUCAO);
                        for($l=$TotalVendido=$TotalLucrado=$TotalQuantificado=0; $l<mysqli_num_rows($sqlMeses); $l++){
                            extract(mysqli_fetch_assoc($sqlMeses));
                            
                            if($tabela_referido=="orcamento"){
                                $instrucaoLucro = "select tabela_item, id_item as item, quantidade, valor_produto as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido' ";
                                if($produtos){
                                    $instrucaoLucro .= "and id_item='$produtos'";
                                }
                            }elseif($tabela_referido=="pdv"){
                                $instrucaoLucro = "select id_produto as item, quantidade, preco as valor from ".$tabela_referido."_itens where id_".$tabela_referido."='$referido' ";
                                $tabela_item="produto";
                                if($produtos){
                                    $instrucaoLucro .= "and id_produto='$produtos'";
                                }
                            }

                            $sqlLucroItens= query($instrucaoLucro);
                            for($j=$ValorLucro=0; $j<mysqli_num_rows($sqlLucroItens); $j++){
                                extract(mysqli_fetch_assoc($sqlLucroItens));
                                
                                if($tabela_referido=="orcamento"){
                                    if($tabela_item=="produto"){
                                        $ValorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                                        $valor = $valor * $quantidade;
                                    }elseif($tabela_item=="servico"){
                                        $sqlServicoProduto = query("select id_produto, qtd from servico_produto where id_servico='$item'");
                                        for($h=0; $h<mysqli_num_rows($sqlServicoProduto); $h++){
                                            extract(mysqli_fetch_assoc($sqlServicoProduto));
                                            $valorParcial = precoProduto($id_produto, false, false, true) * $qtd;
                                        }
                                        if($valor*$quantidade>$valorParcial){
                                            $ValorLucro += $valor * $quantidade - $valorParcial;    
                                        }else{
                                            $valorLucro += $valorParcial - $valor * $quantidade;
                                        }
                                        $valor = $valor * $quantidade;
                                    }elseif($tabela_item=="item"){
                                        $ValorLucro += $valor;
                                        $quantidade = 1;
                                    }
                                }elseif($tabela_referido=="pdv"){
                                    $ValorLucro += ($valor * $quantidade) - (turnZero(precoProduto($item, false, false, true)) * $quantidade);
                                    $valor = $valor * $quantidade;
                                }

                            }
                            
                            $TotalVendido+= $valor;
                            $TotalLucrado+= $ValorLucro;
                            $TotalQuantificado+= $quantidade;



                        }
                        
                        $array[] = "R$ ".real($TotalVendido);
                        $array[] = "R$ ".real($TotalLucrado);
                        $array[] = real($TotalQuantificado);

                        $valorVenda += $TotalVendido;
                        $valorLucro += $TotalLucrado;
                        $valorRecebido += $TotalQuantificado;
                        */
                    }
                    
                    $checkMes[] = $dataPesquisaMes;
                    
                    //preenchendo matriz do grafico
                    $labels[] = mesNominal($data);
                    $venda[] = turnZero($valorVenda);
                    $lucro[] = turnZero($valorLucro);
                    $recebido[] = turnZero($valorRecebido);

                }
                
            }
            
            if(!is_numeric($produtos)){
                $sql = query("select sum(valor) as total".$instrucao[0].$data1.$instrucao[1].$data2.$instrucao[2]);
                extract(mysqli_fetch_assoc($sql));
                $ultimaLinha = "<tr><td align='right'>Total</td><td class='totalValor' align='center'>R$ ".real(round($TotalVenda,2))."</td>";
                $ultimaLinha .= "<td class='totalValor' align='center'>R$ ".real(round($TotalLucro,2))."</td>";
                $ultimaLinha .= "<td class='totalValor' align='center'>R$ ".real(round($TotalRecebido,2))."</td>";
                $ultimaLinha .= "<td align='center'>Falta receber</td><td class='totalValor'>R$ ".real(round($TotalVenda,2) - round($TotalRecebido,2))."</td></tr>";
            }else{
                $ultimaLinha = "<tr><td align='right'>Total</td><td class='totalValor' align='center'>R$ ".real(round($TotalVenda,2))."</td>";
                $ultimaLinha .= "<td class='totalValor' align='center'>R$ ".real(round($TotalLucro,2))."</td>";
                $ultimaLinha .= "<td class='totalValor' align='center'>".real(round($TotalQtd,2))."</td>";
                $ultimaLinha .= "</tr>";
            }
            
        }
        
        
        $tabela = new tabela;
        $tabela->setTitulo($titulo);
        $tabela->setTag($arrayTag);
        $tabela->setValores($array);
        $tabela->ultimaColuna = $ultimaLinha;
        echo $tabela->showTabela();

        
        
        
        if($labels){

            $datasets = array_merge($venda,$lucro,$recebido);
            echo grafico("Line", $labels, $datasets, array("Vendas (R$)", "Pespectiva lucro (R$)", "Recebido (R$)"), array(850, 250));
        }


    }elseif($produtos=='Discriminar por produtos'){
			
        $criterio = new TCriteria;
        $criterio->add(new TFilter('status', '=', '1'));
        
		$sql = new TSqlSelect;
		$sql->setEntity('pdv');
		$sql->addColumn('id');
		$sql->setCriteria($criterio);
		
		
        $criterio = new TCriteria;
        $criterio->add(new TFilter('data', '>=', "$data1 $inicial_hora:$inicial_min:00"));
        $criterio->add(new TFilter('data', '<=', "$data2 $final_hora:$final_min:59"));
        $criterio->add(new TFilter('id_pdv', '= any', '('.$sql->getInstruction().')'));

        $sql = new TSqlSelect;
        $sql->setEntity('pdv_itens');
        $sql->addColumn('id_produto');
        $sql->addColumn('quantidade');
        $sql->addColumn('preco');
        $sql->setCriteria($criterio);
		
		
        $result = $conn->query($sql->getInstruction());
        
        $produto['id'] = array();
        for($i=0; $i<$result->rowCount(); $i++){
            extract($result->fetch(PDO::FETCH_ASSOC));
            
            if($id_produto=='0'){
                $nome = '10%';
            }else{
                $nome = registro($id_produto, 'produto', 'nome');
            }

            if(in_array($id_produto, $produto['id'])){
                $pos = array_search($id_produto, $produto['id']);
                $produto['quantidade'][$pos] += $quantidade;
                $produto['sub_total'][$pos] += $preco;
                $produto['total'][$pos] += round($preco * $quantidade,2);

            }else{
                $produto['id'][] = $id_produto;
                $produto['nome'][] = $nome;
                $produto['quantidade'][] = $quantidade;
                $produto['sub_total'][] = $preco;
                $produto['total'][] = round($preco * $quantidade,2);

                $produto2['quantidade'][] = 0;
                $produto2['sub_total'][] = 0;
                $produto2['total'][] = 0;
            }
        
        }
        if($i>0){
        	array_multisort($produto['quantidade'], SORT_DESC, $produto['total'], $produto['id'], $produto['nome']);
        }else{
        	$array[] = null;
        	$array[] = null;
        	$array[] = null;
        	$array[] = null;
        }
        
        for($i=$quantidade=$total=0; $i<count($produto['id']); $i++){

            if($produto['id'][$i]){
                $array[] = "<a class='aSubmit' href='cadastrarProduto.php?op=visualizar&id=".base64_encode($produto['id'][$i])."'>".$produto['id'][$i]."</a>";
            }else{
                $array[] = " ";
            }
            $array[] = $produto['nome'][$i];
            $array[] = $produto['quantidade'][$i];
            $array[] = "R$ ".real($produto['total'][$i]);

            $quantidade +=$produto['quantidade'][$i];
            $total +=$produto['total'][$i];
            


        }
        
        $ultimaLinha = "<tr><td></td><td align='right'>Total</td><td class='totalValor' align='center'>".$quantidade."</td>";
        $ultimaLinha .= "<td class='totalValor' align='center'>R$ ".real(round($total,2))."</td>";
        $ultimaLinha .= "</tr>";


        $tabela = new tabela;
        $DATAInicial1 = formataData("$data1 $inicial_hora:$inicial_min:00");
        $DATAFinal1 = formataData("$data2 $final_hora:$final_min:59");
        $tag = array("ID", "Produto" , "quantidade", "Total Vendido");
        $titulo = "Produtos X quantidade X Total Vendido<br>Período de $DATAInicial1 a $DATAFinal1";
        
        $tabela->setTitulo($titulo);
        $tabela->setTag($tag);
        $tabela->setValores($array);
        $tabela->ultimaColuna = $ultimaLinha;
        echo $tabela->showTabela();

        
        //tipo = tipo do grafico Radar | Bar | Doughnut | Line | Pie | PolarArea
        /*
        if("$data1 $inicial_hora:$inicial_min:00"!= "0000-00-00 0:0:00"){

            
            
            $DATAInicial1 = formataData("$data1 $inicial_hora:$inicial_min:00");
            $DATAFinal1 = formataData("$data2 $final_hora:$final_min:59");
            
            !$intervalo? $intervalo=1:false;

            $DATAInicial2 = date('Y-m-d H:i:s', strtotime("$data1 $inicial_hora:$inicial_min:00 - $intervalo days"));
            $DATAFinal2 = "$data1 $final_hora:$final_min:00";


            $criterio = new TCriteria;
            $criterio->add(new TFilter('data', '>=', $DATAInicial2));
            $criterio->add(new TFilter('data', '<=', $DATAFinal2));

            $sql = new TSqlSelect;
            $sql->setEntity('pdv_itens');
            $sql->addColumn('id_produto');
            $sql->addColumn('quantidade');
            $sql->addColumn('preco');
            $sql->setCriteria($criterio);

            $result = $conn->query($sql->getInstruction());
            
            $produto2['id'] = array();
            for($i=0; $i<$result->rowCount(); $i++){
                extract($result->fetch(PDO::FETCH_ASSOC));
                
                if($id_produto=='0'){
                    $nome = '10%';
                }else{
                    $nome = registro($id_produto, 'produto', 'nome');
                }

                if(in_array($id_produto, $produto['id'])){
                    $pos = array_search($id_produto, $produto['id']);
                    $produto2['quantidade'][$pos] += $quantidade;
                    $produto2['sub_total'][$pos] += $preco;
                    $produto2['total'][$pos] += round($preco * $quantidade,2);

                }else{
                    $produto2['id'][] = $id_produto;
                    $produto2['nome'][] = $nome;
                    $produto2['quantidade'][] = $quantidade;
                    $produto2['sub_total'][] = $preco;
                    $produto2['total'][] = round($preco * $quantidade,2);
                    
                    
                }
            
            }
            if($result->rowCount()==0){
                $produto2['id'] = $produto['id'];
                $produto2['nome'] = $produto['nome'];
                
            }



            
            echo "<h1>Gráfico Total Quantidade</h1>";
            $legenda = array("Quantidade $DATAInicial1 a $DATAFinal1",
                "Quantidade ".formataData($DATAInicial2)." a ".formataData($DATAFinal2));
            $datasets = array_merge($produto['quantidade'], $produto2['quantidade']);
            echo grafico("Line", $produto['id'], $datasets, $legenda, array(850, 250));
        

        }
        */
        $legenda = array("Valor R$");
        
        if($i>0){
        	if(count($produto['total'])<100){
        		echo "<h2>Total em vendas</h2>";
        		echo grafico("Line", $produto['nome'], $produto['total'], $legenda, array(900, 400));
        		echo "<h2>Quantidade vendida</h2>";
        		echo grafico("Line", $produto['nome'], $produto['quantidade'], $legenda, array(900, 400));
        	}	
        }
        

        
        //echo "<h1>Gráfico Total vendido</h1>";
        //echo grafico("Radar", $produto['nome'], $produto['total'], null, array(850, 850));
        

    }
    
    
    
	
}else{
    
    formularioPesquisa();
    
}

include "templates/downLogin.inc.php";
?>

