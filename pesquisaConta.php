<?php
include "templates/upLogin.inc.php";
//js especifico para pagina
?>
<script type="text/javascript">
	$(function(){
		checkFiltro();
		checkForma();
	});
	function checkFiltro(){
		var buscar = $('#buscarPor').val();
		if(buscar=='filtro'){
			$('#buscaNumero').hide();
			$('.buscaFiltro').show();
		}else if(buscar=='numero'){
			$('#buscaNumero').show();
			$('.buscaFiltro').hide();
		}
	}
	function checkForma(){
		var forma = $('#pesquisa').val();
		$('#spanEntidade').hide();
		$('#spanReferido').hide();
		$('#spanDoc').hide();
		$('#spanPlano').hide();
		if(forma=='entidade'){
			$('#spanEntidade').show();
		}else if(forma=='referido'){
			$('#spanReferido').show();
		}else if(forma=='doc'){
			$('#spanDoc').show();
		}else if(forma=='planoConta'){
			$('#spanPlano').show();
		}
	}
	

	function showNome(nome, op) {

		if (op == "completarEntidade" || op == "completarReferido" || op == "completarContaPlano") {
			
			if(op == "completarEntidade"){
				var idDocumento = "sugestoes1";
			}else if(op == "completarReferido"){
				var idDocumento = "sugestoes2";
			}else if(op == "completarContaPlano"){
				var idDocumento = "sugestoes3";
			}

			if (nome.length == 0) {
				document.getElementById(idDocumento).style.display = 'none';
			} else {

				document.getElementById(idDocumento).style.display = '';
				var cod = "<center><img width='30' src='img/loading.gif'></center>";

				if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp = new XMLHttpRequest();
				} else {// code for IE6, IE5
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}

				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 1) {
						document.getElementById(idDocumento + 'Lista').innerHTML = cod;
					} else if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById(idDocumento + 'Lista').innerHTML = xmlhttp.responseText;
					}
				}

				xmlhttp.open("GET", "inc/contaAutoCompletar.inc.php?nome=" + nome + "&op="+ op, true);
				xmlhttp.send();
			}
		} else if (op == "preencherEntidade") {

			document.getElementById('entidadeValue').value = nome;
			document.getElementById('sugestoes1').style.display = 'none';

		} else if (op == "preencherReferido") {

			document.getElementById('referidoValue').value = nome;
			document.getElementById('sugestoes2').style.display = 'none';

		} else if (op == "preencherContaPlano") {

			document.getElementById('contaPlanoValue').value = nome;
			document.getElementById('sugestoes3').style.display = 'none';

		}
	}
	function lookupOff(){
		$('.suggestionsBox').hide();
	}
	function filtro(){
		
		var valida = true;
		var alerta = "";

		if ($('#tipo').val() == 0){
			alerta += "Selecione o tipo de conta que voce deseja pesquisar.\n";
			valida = false;
			$('#tipo').attr("class", "avisoInput");
		}else{
			$('#tipo').attr("class", "");
		}
		
		if(valida){
			return true;
		}else{
			alert(alerta);
			return false;
		}
		
	}
</script>
<?php


//all
$conn= TConnection::open(ALCUNHA);
extract($_POST);
extract($_GET);
if(!isset($buscarPor)){
	echo "<form class='form' name='formulario' method='post' action='pesquisaConta.php' enctype='multipart/form-data' onsubmit=\"return filtro();\">";
	echo "<div class='column'>";
		echo "<label for='buscarPor'>Buscar por:</label>";
		echo "<select name='buscarPor' id='buscarPor' onchange='checkFiltro();'>";
			echo "<option value='filtro'>Usar Filtro</option>";
			echo "<option value='numero'>Número de conta</option>";
		echo "</select>";
	echo "</div>";
	
	echo "<div class='column buscaFiltro'>";
	//tipo de pesquisa
	echo "<label for='tipo'>Tipo</label>";
	echo "<select name='tipo' id='tipo'>";
	echo opcaoSelect("conta_status", 1, "Ativo", null, null, null, 0, false);
	echo "</select>";
	$sql = new TSqlSelect;
	$sql->setEntity('empresa');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$result= $conn->query($sql->getInstruction());
	$linha = $result->rowCount();
	echo "<label for='empresa'>Empresa</label>";
	echo "<select name='empresa' id='empresa'>";
	echo "<option value='0'>Todas as Empresas</option>";
	for ($i = 0; $i < $result->rowCount(); $i++) {
		$reg = $result->fetch(PDO::FETCH_ASSOC);
		echo "<option value='$reg[id]'>$reg[nome]</option>";
	}
	echo "</select>";

	//vencimento
	echo "<label for='data1'>Vencimento";
	echo inputData("formulario", "data1", null)."<br>à<br>".inputData("formulario", "data2", null)."</label>";
	echo "</div>";



	echo "<div class='column'>";
	echo "<span class='buscaFiltro'>";
	//quitação
	echo "<label for='quitacao'>Buscar se</label>";
	echo "<select name='quitacao' id='quitacao'>";
	echo "<option value='todos'>Todos</option>";
	echo "<option value='quitados'>Quitados</option>";
	echo "<option value='nquitados'>Não quitados</option>";
	echo "</select>";
	//forma de pesquisa
	echo "<label for='pesquisa'>Forma de pesquisa</label>";
	echo "<select id='pesquisa' name='pesquisa' onchange=\"checkForma();\">";
	echo "<option value='0'>--</option>";
	echo "<option value='entidade'>Cliente ou Fornecedor</option>";
	echo "<option value='referido'>Referido</option>";
	echo "<option value='doc'>Numero do Documento</option>";
	echo "<option value='planoConta'>Plano de contas</option>";
	echo "</select>";
	//entidade
	echo "<span id='spanEntidade'>";
	echo "<label for='entidadeValue'>Cliente ou Fornecedor</label>";
	echo "<input type='text' name='entidade' id='entidadeValue' onkeyup='showNome(this.value, \"completarEntidade\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes1' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes1Lista'></div></div>";
	//echo "<br><div id='sugestoes2' class='suggestionList' style='display:none;'></div></td>";
	echo "</span>";
	//referido
	echo "<span id='spanReferido'>";
	echo "<label for='referidoValue'>Referido</label>";
	echo "<input type='text' name='referido' id='referidoValue' onkeyup='showNome(this.value, \"completarReferido\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes2' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes2Lista'></div></div>";
	echo "<div id='sugestoes1' class='suggestionList' style='display:none;'></div>";
	echo "</span>";
	//Numero do documento
	echo "<span id='spanDoc'>";
	echo "<label for='doc'>Numero do Documento</label>";
	echo "<input type='text' id='doc' name='doc'>";
	echo "</span>";
	//plano de contas
	echo "<span id='spanPlano'>";
	echo "<label for='contaPlanoValue'>Plano de contas</label>";
	echo "<input type='text' name='planoConta' id='contaPlanoValue' onkeyup='showNome(this.value, \"completarContaPlano\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes3' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes3Lista'></div></div>";
	echo "<div id='sugestoes1' class='suggestionList' style='display:none;'></div>";
	echo "</span>";
	echo "</span>";
	//buscar por numero de conta
	echo "<span id='buscaNumero'>";
		echo "<label for='numeroConta'>Número da conta</label>";
		$sql = new TSqlSelect;
		$sql->setEntity('conta');
		$sql->addColumn('id');
		$result = $conn->query($sql->getInstruction());
		$contas = $result->rowCount();
		if($contas>1){
			$contas = "De 1 à $contas";
		}
		echo "<input type='text' name='numeroConta' id='numeroConta' placeholder='$contas' ".mascara('Integer').">";
	echo "</span>";

	$info = new info;
	$info->msg = 'Carregando relatório<br><br><img src="img/loading_bar.gif">';
	$info->cor = 'blue';
	$info->display = 'none';
	$info->class = 'info_loading';
	echo $info->getInfo();
	echo "<div class='submit-wrap'><input type='submit' ".$info->getJs()." class='submit' value='Enviar'></div>";

	echo "</div>";
	echo "</form>";
	/*
	echo "<form name='formulario' method='post' action='pesquisaConta.php' enctype='multipart/form-data' onsubmit=\"return filtro();\">";
	echo "<table>";
	echo "<tr>";
	echo "<td colspan='2'>";
	echo "Relatório de contas";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td >Tipo</td>";
	echo "<td><select name='tipo' id='tipo'>";
	echo opcaoSelect("conta_status", 1, "Ativo");
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td >Empresa</td>";
	echo "<td><select name='empresa'>";
	echo "<option value='0'>Todas as Empresas</option>";
	$sql = query("select id, nome from empresa");
	$linha = mysqli_num_rows($sql);
	for ($i = 0; $i < $linha; $i++) {
		$reg = mysqli_fetch_row($sql);
		echo "<option value='$reg[0]'>$reg[1]</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>Forma de pesquisa</td>";
	echo "<td><select id='pesquisa' name='pesquisa' onchange=\"escolha(this.value);\">";
	echo "<option value='0'>--</option>";
	echo "<option value='entidade'>Entidade referente</option>";
	echo "<option value='referido'>Referido</option>";
	echo "<option value='doc'>Numero do Documento</option>";
	echo "<option value='planoConta'>Plano de contas</option>";
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	//quitação
	echo "<tr>";
	echo "<td>Buscar se</td>";
	echo "<td><select name='quitacao'>";
	echo "<option value='todos'>Todos</option>";
	echo "<option value='quitados'>Quitados</option>";
	echo "<option value='nquitados'>Não quitados</option>";
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	//vencimento
	echo "<tr>";
	echo "<td>Vencimento</td>";
	echo "<td>";
	echo inputData("formulario", "data1", null)."<br>à<br>".inputData("formulario", "data2", null);
	echo "</td>";
	echo "</tr>";
	//entidade
	echo "<tr id='entidade' style='display:none;'>";
	echo "<td>Entidade referente</td>";
	echo "<td><input type='text' name='entidade' id='entidadeValue' onkeyup='showNome(this.value, \"completarEntidade\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes1' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes1Lista'></div></div>";
	//echo "<br><div id='sugestoes2' class='suggestionList' style='display:none;'></div></td>";
	echo "</tr>";
	//referido
	echo "<tr id='referido' style='display:none;'>";
	echo "<td>Referido</td>";
	echo "<td><input type='text' name='referido' id='referidoValue' onkeyup='showNome(this.value, \"completarReferido\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes2' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes2Lista'></div></div>";
	echo "<br><div id='sugestoes1' class='suggestionList' style='display:none;'></div></td>";
	echo "</tr>";
	//Numero do documento
	echo "<tr id='doc' style='display:none;'>";
	echo "<td>Numero do Documento</td>";
	echo "<td><input type='text' name='doc'>";
	echo "</td>";
	echo "</tr>";
	//plano de contas
	echo "<tr id='planoConta' style='display:none;'>";
	echo "<td>Plano de contas</td>";
	echo "<td><input type='text' name='planoConta' id='contaPlanoValue' onkeyup='showNome(this.value, \"completarContaPlano\");' autocomplete='off'>";
	echo "<div class='suggestionsBox' id='sugestoes3' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
	echo "<div class='suggestionList' id='sugestoes3Lista'></div></div>";
	echo "<br><div id='sugestoes1' class='suggestionList' style='display:none;'></div></td>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td></td>";
	echo "<td><input type='submit' class='btnEnviar' value='Enviar'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";*/
}else{
	#
	#
	#
	#
	#
	# --------------------- inicia a busca -------------------------
	#
	#
	#
	#
	#
	
	$DATA1 = $data1;
	$DATA2 = $data2;
	
	if($buscarPor=='filtro'){
		$data1 = formataDataInv($data1);
		$data2 = formataDataInv($data2);
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
		
		if ($empresa > 0) {
			$cod = "conta.empresa='$empresa' and";
		}else{
			$cod = "";
		}
		switch ($pesquisa) {
			case 'entidade' :
				$cod2 = "and (conta.entidade like '%$entidade%' or conta.entidade in (select id from cliente_fornecedor where nome like '%$entidade%') ";
				$cod2 .= "or conta.entidade in (select id from usuario where nome like '%$entidade%'))";
				break;
			
			case 'referente' :
				$cod2 = "and conta.referido like '%$referente%'";
				break;
				
			case 'doc' :
				$cod2 = "and conta.documento='$doc'";
				break;
				
			case 'planoConta' :
				$cod2 = "and conta.conta_plano=(select id from conta_plano where nome='$planoConta')";
				break;
			default:
				$cod2 = "";
		}
		
		

		$instrucao = "select conta.id, tabela_entidade, entidade, tabela_referido,referido, conta.valor, data_vencimento from ";
		$instrucao .= "conta join conta_itens on conta.id=conta_itens.id_conta where ";
		//$instrucao .= "$cod status='$tipo' and id = any (select id_conta from conta_itens where data_vencimento>='$data1' and data_vencimento<='$data2') ";
		$instrucao .= "$cod conta.status='$tipo' and conta_itens.data_vencimento>='$data1 00:00:00' and conta_itens.data_vencimento<='$data2 23:59:59' ";
		$instrucao .= "$cod2";

		

	}elseif($buscarPor=='numero'){
		$instrucao = "select id, tabela_entidade, entidade, tabela_referido,referido, valor from conta where ";
		$instrucao .= "id='$numeroConta'";
	}


	if(isset($_GET['pag'])){
		$pag = $_GET['pag'];
	}else{
		$pag = 0;
	}
	/*
?>
<script type="text/javascript">
	$(function(){
		$.post('inc/ajaxPesquisaContaPag.inc.php', {
			pag : <?php echo $pag ?>,
			instrucao : "<?php echo $instrucao ?>",
			buscarPor : "<?php echo $buscarPor ?>",
			tipo : "<?php echo $tipo ?>",
			empresa : "<?php echo $empresa ?>",
			data1 : "<?php echo $DATA1 ?>",
			data2 : "<?php echo $DATA2 ?>",
			quitacao : "<?php echo $quitacao ?>",
			pesquisa : "<?php echo $pesquisa ?>",
			entidade : "<?php echo $entidade ?>",
			referido : "<?php echo $referido ?>",
			doc : "<?php echo $doc ?>",
			planoConta : "<?php echo $planoConta ?>",
			numeroConta : "<?php echo $numeroConta ?>"
		}
		,function (data){
			$('#paginacao').html(data);
		});
	});
</script>

<?php
	$instrucao2 = " limit ".($pag*10).",10";


	$sql = query($instrucao.$instrucao2);*/
	$sql = query($instrucao);
	$linha = mysqli_num_rows($sql);

	echo "<table id='gradient-style' summary='Resultado da pesquisa'>";
	
	echo "<thead>";
    	echo "<tr>";
        	echo "<th scope='col'>ID</th>";
            echo "<th scope='col' style='white-space:nowrap;'>Cliente ou Fornecedor</th>";
            echo "<th scope='col' style='white-space:nowrap;'>Referente</th>";
			echo "<th scope='col' style='white-space:nowrap;'>Valor Total</th>";
			echo "<th scope='col' style='white-space:nowrap;'>Valor Quitado</th>";
			echo "<th scope='col' style='white-space:nowrap;'>Falta Quitar</th>";
        echo "</tr>";
    echo "</thead>";
    
	echo "<tfoot>";
    	echo "<tr>";
        	echo "<th colspan='6'>Contas ".registro($tipo, "conta_status", "nome")."</td>";
        echo "</tr>";
    echo "</tfoot>";
	
	echo "<tbody>";

	//iniciando as variaveis que representarao os valores totais no final da tabela
	$VT = 0;
	$VQ = 0;
	$FQ = 0;
	
	function restoDaTabela(){
		
		global $reg, $VT, $VQ, $FQ, $total, $data1, $data2;
		
		echo "<tr>";
		echo "<td><form method='get' action='pesquisaConta2.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='conta' value='".base64_encode($reg["id"])."'>";
		echo "<input type='submit' value='".$reg["id"]."'>";
		echo "</form></td>";

		if (is_numeric($reg["entidade"]) and $reg["tabela_entidade"] and $reg["tabela_referido"]!='pdv') {//se o caso de for cliente cadastrado corrigir o erro
			echo "<td style='white-space:nowrap;'>";
			if($reg["tabela_entidade"]== "cliente_fornecedor" and getCredencialUsuario("pesquisaClienteFornecedor.php")){
				echo "<form method='get' action='cadastrarClienteFornecedor.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='op' value='visualizar'>";
				echo "<input type='hidden' name='id_cliente_fornecedor' value='".base64_encode($reg["entidade"])."'>";
				echo "<input type='submit' value='".registro($reg["entidade"], $reg["tabela_entidade"], "nome")."'>";
				echo "</form>";
			}elseif($reg["tabela_entidade"]== "usuario" and getCredencialUsuario("pesquisaUsuario.php")){
				echo "<form method='get' action='cadastrarUsuario.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='op' value='visualizar'>";
				echo "<input type='hidden' name='id' value='".$reg["entidade"]."'>";
				echo "<input type='submit' value='".registro($reg["entidade"], $reg["tabela_entidade"], "nome")."'>";
				echo "</form>";
			}else{
				$msg = "Entidade referente cadastrado, mas você não possui credências para visualizar o cadastro.<br>";
				echo $reg["entidade"]." ".ajudaTool($msg);
			}
			echo "</td>";
		} else {
			echo "<td style='white-space:nowrap;'>".$reg["entidade"]."</td>";
		}
		if(is_numeric($reg["referido"]) and $reg["tabela_referido"]=="orcamento"){
			echo "<td>";
			echo "<form method='post' action='cadastrarOrcamento.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='hidden' name='id' value='".base64_encode($reg["referido"])."'>";
			echo "<input type='submit' value='Orçamento ".$reg["referido"]."'>";
			echo "</form>";
			echo "</td>";
		}elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]=="pdv"){
			echo "<td>";
			echo "<form method='get' action='cadastrarPDV.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='hidden' name='pdv' value='".base64_encode($reg["referido"])."'>";
			echo "<input type='submit' value='".registro($reg["referido"], "pdv", "nome")."'>";
			echo "</form>";
			echo "</td>";
		}elseif(is_numeric($reg["referido"]) and $reg["tabela_referido"]=="ordem_servico"){
			echo "<td>";
			echo "<form method='get' action='cadastrarOrdemServico.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='hidden' name='id' value='".base64_encode($reg["referido"])."'>";
			echo "<input type='submit' value='Ordem de Serviço ".$reg["referido"]."'>";
			echo "</form>";
			echo "</td>";
		}else{
			echo "<td>".$reg["referido"]."</td>";
		}
		echo "<td style='white-space:nowrap;'>R$ " . real($reg["valor"]) . "</td>";
		echo "<td style='white-space:nowrap;'>R$ " . real($total) . "</td>";
		if ($reg["valor"] - $total > 0) {
			echo "<td style='white-space:nowrap; font-weight:bold; color:black;'>R$ " . real($reg["valor"] - $total) . "</td>";
		} else {
			echo "<td style='white-space:nowrap;'>R$ " . real($reg["valor"] - $total) . "</td>";
		}
		echo "</tr>";
		$VT += $reg["valor"];
		$VQ += $total;
		$FQ += $reg["valor"] - $total;
	}
	$array = array();
	for ($i = 0; $i < $linha; $i++) {
		
		$reg = mysqli_fetch_assoc($sql);
		if(!in_array($reg['id'], $array)){
			$array[] = $reg['id'];
			//calculando o quanto que foi pago
			$instrucao = "select sum(valor) as total from conta_itens where id_conta='".$reg["id"]."'";
			$Sql = query($instrucao);
			extract(mysqli_fetch_assoc($Sql));
			$total = round($total, 2);
			
			if($quitacao=="todos"){
				echo restoDaTabela();
			}elseif($quitacao=="quitados" and $reg["valor"]<=$total){
				echo restoDaTabela();
			}elseif($quitacao=="nquitados" and $reg["valor"]>$total){
				echo restoDaTabela();
			}
		}
		
	}

	echo "</tr>";
	echo "<td colspan='3'></td>";
	echo "<td>Valor Total</td>";
	echo "<td>Valor Quitado</td>";
	echo "<td>Falta Quitar</td>";
	echo "</tr>";
	echo "<tr><td colspan='6' align='center' id='paginacao'></td></tr>";
	echo "<tr>";
	echo "<td colspan='3' style='text-align:right;'>TOTAL</td>";
	echo "<td>R$ " . real($VT) . "</td>";
	echo "<td>R$ " . real($VQ) . "</td>";
	echo "<td style='white-space:nowrap; font-weight:bold; color:black;'>R$ " . real($FQ) . "</td>";
	echo "</tr>";
	echo "</tbody>";
	echo "</table>";
	
}


//end all

include "templates/downLogin.inc.php";
?>