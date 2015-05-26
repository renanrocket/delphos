<?php
include "templates/upLogin.inc.php";

extract($_GET);


if(isset($busca)){
    	
    $data1 = isset($_GET["data1"]) ? formataDataInv($_GET["data1"]) : "0000-00-00";
	$data2 = isset($_GET["data2"]) ? formataDataInv($_GET["data2"]) : "0000-00-00";
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
    
    $instrucao = "select * from orcamento where ";
    
    switch ($busca) {
        case 'id':
            $instrucao .= "id='$numero'";
            break;
        
        case 'cliente':
            $instrucao .= "cliente like '%$nome_cliente%' ";
			$instrucao.= "and $intervalo>='$data1 00:00:00' and $intervalo<='$data2 23:59:59'";
            break;
        
        case 'produto':
            $instrucao .= "id = any(select id_orcamento from orcamento_itens where id_item like '%$nome_produto%' or ";
            $instrucao .= "id_item = any (select id from produto where nome like '%$nome_produto%' or modelo like '%$nome_produto%')) ";
            $instrucao .= "and $intervalo>='$data1 00:00:00' and $intervalo<='$data2 23:59:59'";
            break;
        
        case 'status':
            $instrucao .= "status='$status'";
            $instrucao .= "and $intervalo>='$data1 00:00:00' and $intervalo<='$data2 23:59:59'";
            break;
        
        case 'usuario':
            $instrucao .= "id_usuario='$id_usuario'";
            $instrucao .= "and $intervalo>='$data1 00:00:00' and $intervalo<='$data2 23:59:59'";
            break;
        
        case 'data':
            $instrucao .= "$intervalo>='$data1 00:00:00' and $intervalo<='$data2 23:59:59'";
            break;
         
    }

    $sql = query($instrucao);
    if(mysqli_num_rows($sql)>0){
        
        echo "<table id='gradient-style' summary='Resultado da pesquisa'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th scope='col' colspan='5' align='center'>Orçamentos encontrados na pesquisa</th>";
                echo "</tr>";
                echo "<tr>";
                    echo "<th scope='col'>Orçamento</th>";
                    echo "<th scope='col'>Contato</th>";
                    echo "<th scope='col'>Valor</th>";
                    echo "<th scope='col'>Data Emissão</th>";
                    echo "<th scope='col'>Status</th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tfoot>";
                echo "<tr>";
                   echo "<td colspan='5' align='center'>Encontrado(s) ".mysqli_num_rows($sql)." orçamento(s)</td>";
                echo "</tr>";
            echo "</tfoot>";
            echo "<tbody>";
            for($i=$total=0; $i<mysqli_num_rows($sql); $i++){
                extract(mysqli_fetch_assoc($sql));
                
                echo "<tr align='center' valign='middle'>";
                    echo "<form method='post' action='cadastrarOrcamento.php' enctype='multipart/form-data'>";
                        echo "<td>";
                            echo "<input type='hidden' name='op' value='visualizar'>";
                            echo "<input type='hidden' name='id' value='".base64_encode($id)."'>";
                            echo "<input type='submit' value='$id'>";
                        echo "</td>";
                        echo "<td style='text-transform: capitalize;'>$cliente</td>";
                        $Sql = query("select quantidade, valor_produto from orcamento_itens where id_orcamento='$id'");
                        $valor = 0;
                        for($j= 0; $j<mysqli_num_rows($Sql); $j++){
                            extract(mysqli_fetch_assoc($Sql));
                            $valor += $quantidade * $valor_produto;
                        }
						$total += $valor;
                        echo "<td style='white-space:nowrap;'><b>R$ ".real($valor)."<b></td>";
                        echo "<td>".formataData($data_emissao)."</td>";
                        if($status==1){
                            echo "<td><img style='width:40px;' title='Aguardando venda' src='img/aguardando_venda.png'></td>";
                        }elseif($status==2){
                            echo "<td><img style='width:40px;' title='Vendido' src='img/vendido.png'></td>";
                        }else{
                        	echo "<td><img style='width:40px;' title='Deletado' src='img/deletar.png'></td>";
                        }
                    echo "</form>";
                echo "</tr>";
                
            }
            echo "<tr>";
            	echo "<td colspan='2' align='right'>Total</td>";
				echo "<td style='white-space:nowrap; font-size:20px;'>R$ ".real($total)."</td>";
				echo "<td colspan='2'></td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table>";
        
        
        
    }else{
        echo "<div class='msg'>";
            echo "Nenhum Orçamento encontrado com esse filtro.";
        echo "</div>";
        echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=pesquisaOrcamento.php'>";
    }
    
}else{
    
    echo "<script type='text/javascript' src='js/pesquisaOrcamentoFormulario.js'></script>";
    
    echo "<form name='formulario' method='get' action='pesquisaOrcamento.php' enctype='multipart/form-data' style='width:40%;' onsubmit='return filtro();'>";
    
        echo "Buscar orçamento por:<br>";
        echo "<select name='busca' onchange=\"showPesquisa(this.value)\">";
            echo "<option value='id'>Número</option>";
            echo "<option value='cliente'>Contato / Cliente</option>"; // incluir data
            echo "<option value='produto'>Produto</option>"; //incluir data
            echo "<option value='status'>Por Status</option>"; //incluir data
            echo "<option value='usuario'>Usuário</option>"; //incluir data
            echo "<option value='data'>Intervalo entre datas</option>";
        echo "</select>";
        
        echo "<br><br>";
        
        echo "<div id='id'>";
            echo "Insira o numero do Orçamento:";
            echo "<input type='text' name='numero' ".mascara("Integer").">";
        echo "</div>";
        
        echo "<div id='cliente' style='display:none;'>";
            echo "Insira o nome do cliente ou contato do orçamento:";
            echo "<input type='text' name='nome_cliente'>";
        echo "</div>";
        
        echo "<div id='produto' style='display:none;'>";
            echo "Insira o nome do produto:";
            echo "<input type='text' name='nome_produto'>";
        echo "</div>";
        
        echo "<div id='status' style='display:none;'>";
            echo "Selecione o status do orçamento: ";
            $sql = query("select status from orcamento group by status");
            echo "<select name='status'>";
                for($i=0; $i<mysqli_num_rows($sql); $i++){
                    extract(mysqli_fetch_assoc($sql));
                    $opcao = registro($status, "conta_status", "nome");
                    if($opcao=="A receber")
                        $opcao = "Vendido";
                    echo "<option value='$status'>$opcao</option>";
                }
            echo "</select>";
        echo "</div>";
        
        echo "<div id='usuario' style='display:none;'>";
            echo "Selecione o usuário <a href='#' title='Somente irão estar disponíveis na busca<br>os usuários que já emitiram algum orçamento.'><img src='img/info.png' class='imgHelp'></a>:";
            $sql = query("select id_usuario from orcamento group by id_usuario");
            echo "<select name='id_usuario'>";
                for($i=0; $i<mysqli_num_rows($sql); $i++){
                    extract(mysqli_fetch_assoc($sql));
                    echo "<option value='$id_usuario'>".registro($id_usuario, "usuario", "nome")."</option>";
                }
            echo "</select>";
        echo "</div>";
        
        echo "<br>";
        
        echo "<div id='data' style='display:none;'>";
            echo "Selecione o intervalo de datas:<br>";
            echo "<input type='radio' name='intervalo' value='data_emissao' checked> Emissão ou ";
            echo "<input type='radio' name='intervalo' value='data_venda'> Venda";
            echo inputData("formulario", "data1", "50%");
            echo "à<br>";
            echo inputData("formulario", "data2", "50%");
        echo "</div>";
        
        echo "<br>";
        
        echo "<input type='submit' class='btnEnviar' value='Enviar'>";
    echo "</form>";
    
}


include "templates/downLogin.inc.php";
?>

