<script type="text/javascript">

	var tempo =<?php echo $_GET["tempo"] ?>000;
	function loading() {
		$("#gradient-style").html("<tr><td align='center'>Carregando...</td></tr>")
	}

	function resetTimeout() {
		loading();
		clearTimeout(timeout);
		timeout = setTimeout("location.reload(true);", 1);
	}
	
	var timeout = setTimeout("location.reload(true);", tempo);
	var timerefresh = setTimeout("loading();", tempo - 1000);

	var intervaloAlerta = setInterval(function(){
		if($(".alerta").attr("id")=="alerta_red" || $(".alerta").attr("id")==undefined){
			$(".alerta").attr("id", "alerta_red1");
		}

		if($(".alerta").attr("id")=="alerta_white"){
			$(".alerta").attr("id", "alerta_red");
		}
		if($(".alerta").attr("id")=="alerta_red1"){
			$(".alerta").attr("id", "alerta_white");
		}
		
		
	}, 400);
	
</script>
<?php
include "../templates/upIframe.inc.php";


//all

extract($_GET);
$botaoAtualizar =  "<span style='float:right; display:inline-block;'><a href='javascript:void(0);' onclick='resetTimeout();'><img width='30' src='../img/refresh.png'></a></span>";
$data1 = date('Y-m') . "-01 00:00:00";
$data2 = date('Y-m') . "-31 23:59:59";

if ($op == "ativos") {
	
	matriculaStatus(0);
	
	$instrucao = "select id_matricula from matricula_plano_assinatura where id_matricula = any (select id from matricula where status='1') and status='1' and data_termino='0000-00-00' order by data_previsao asc";
	$sqlMatricula = query($instrucao);
	
	for($i = $total = 0, $array = null; $i<mysqli_num_rows($sqlMatricula); $i++){
		
		extract(mysqli_fetch_assoc($sqlMatricula));
		
		$instrucao = "select * from matricula where id='$id_matricula'";
		extract(mysqli_fetch_assoc(query($instrucao)));
		
		$cod = "<form method='get' action='../cadastrarMatricula.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
		$instrucao = "select data_inicio from matricula_plano_assinatura where id_matricula='$id' and ";
		$instrucao .= "id=(select min(id) from matricula_plano_assinatura where id_matricula='$id')";
		$sqlMatriculaPlano = query($instrucao);
		extract(mysqli_fetch_assoc($sqlMatriculaPlano));
		$array[] = formataData($data_inicio);
		$instrucao = "select data_previsao from matricula_plano_assinatura where id_matricula='$id' and ";
		$instrucao .= "id=(select max(id) from matricula_plano_assinatura where id_matricula='$id')";
		$sqlMatriculaPlano = query($instrucao);
		extract(mysqli_fetch_assoc($sqlMatriculaPlano));
		
		if(subtrairDatas(date('Y-m-d'), $data_previsao)>1){
			$texto = "Faltam ".subtrairDatas(date('Y-m-d'), $data_previsao)." dias";
		}elseif(subtrairDatas(date('Y-m-d'), $data_previsao)>0){
			$texto = "Falta ".subtrairDatas(date('Y-m-d'), $data_previsao)." dia";
		}elseif(subtrairDatas(date('Y-m-d'), $data_previsao)==0){
			$texto = "<b class='alerta'>Hoje</b>";
		}elseif(subtrairDatas(date('Y-m-d'), $data_previsao)>-1){
			$texto = "<b class='alerta'>Já se passou ".(subtrairDatas(date('Y-m-d'), $data_previsao) * -1)." dia</b>";
		}else{
			$texto = "<b class='alerta'>Já se passaram ".(subtrairDatas(date('Y-m-d'), $data_previsao) * -1)." dias</b>";
		}
		
		$array[] = formataData($data_previsao)."<br>$texto";
		
	}
	
	$tabela = new tabela;
	$tag = array("ID", "Contato" , "Desde de", "Previsto para<br>finalizar");
	if($i>1){
		$text1 = "Existem";
		$text2 = "matrículas ativas.";
	}else{
		$text1 = "Existe";
		$text2 = "matrícula ativa.";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaMatricula.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();

} elseif ($op == "inativos") {
	
	
	$instrucao = "select * from matricula where status='0' and id = any (select id_matricula from matricula_plano_assinatura where data_termino>='" . date('Y-m-d', strtotime("- 1 month")) . "')";
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<form method='get' action='../cadastrarMatricula.php' target='_main' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
		
		$array[] = $cod;
		$array[] = is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
		$sqlMatriculaPlano = query("select min(id), data_inicio from matricula_plano_assinatura where id_matricula='$id'");
		extract(mysqli_fetch_assoc($sqlMatriculaPlano));
		$array[] = formataData($data_inicio);
		$sqlMatriculaPlano = query("select max(id), data_termino from matricula_plano_assinatura where id_matricula='$id'");
		extract(mysqli_fetch_assoc($sqlMatriculaPlano));
		$array[] = formataData($data_termino);
		
	}
	
	$tabela = new tabela;
	$tag = array("ID", "Contato" , "Desde de", "Finalizado");
	if($i>1){
		$text1 = "Existem";
		$text2 = "matrículas inativas";
	}else{
		$text1 = "Existe";
		$text2 = "matrícula inativa";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaMatricula.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2 nos últimos 30 dias</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();
	
	
} elseif ($op == "fichas") {
	
	
	$instrucao = "select * from matricula where status='1'";
	$sql = query($instrucao);
	
	for($i = $total = 0; $i<mysqli_num_rows($sql); $i++){
		
		extract(mysqli_fetch_assoc($sql));
		$idMatricula = $id;
		
		$sqlAnamnese = query("select * from matricula_anamnese where id_matricula='$id'");
		$sqlAvaliacao = query("select * from matricula_avaliacao where id_matricula='$id'");
		$sqlExercicio = query("select * from matricula_exercicio where id_matricula='$id'");

		$linhaAnamnese = mysqli_num_rows($sqlAnamnese);
		$linhaAvaliacao = mysqli_num_rows($sqlAvaliacao);
		$linhaExercicio = mysqli_num_rows($sqlExercicio);

		if($linhaAnamnese==0 or $linhaAvaliacao==0 or $linhaExercicio==0){
			$cod = "<form method='get' action='../cadastrarMatricula.php' target='_main' enctype='multipart/form-data'>";
			$cod .= "<input type='hidden' name='op' value='visualizar'>";
			$cod .= "<input type='hidden' name='id' value='".base64_encode($id)."'>";
			$cod .= "<input type='submit' value='$id'>";
			$cod .= "</form>";
			
			$array[] = $cod;
			$array[] = is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
			
			$linhaAnamnese ? $array[] = "<img class='check' src='../img/check-ok.png'>" : $array[] = "<img class='check' src='../img/check-no.png'>";
			
			$linhaAvaliacao ? $array[] = "<img class='check' src='../img/check-ok.png'>" : $array[] = "<img class='check' src='../img/check-no.png'>";

			$linhaExercicio ? $array[] = "<img class='check' src='../img/check-ok.png'>" : $array[] = "<img class='check' src='../img/check-no.png'>";
			
			$total++;
		}

		
	}
	
	$tabela = new tabela;
	$tag = array("ID", "Contato" , "Anamnese", "Avaliação", "Exercicio");
	if($total>1){
		$text1 = "Existem";
		$text2 = "matrículas com fichas incompletas";
	}else{
		$text1 = "Existe";
		$text2 = "matrícula com ficha incompleta";
	}
	$titulo = "<img class='iframeImg' src='../img/icones/pesquisaMatricula.png'> $botaoAtualizar";
	$titulo .= "<span>$text1 $i $text2</span>";
	$tabela->setTitulo($titulo);
	$tabela->setTag($tag);
	$tabela->setValores($array);
	$tabela->style = "width:100%; padding:0px; margin:0px;";
	echo $tabela->showTabela();
	
	
} 

include "../templates/downIframe.inc.php";
?>