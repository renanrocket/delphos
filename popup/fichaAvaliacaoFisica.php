<?php
include_once "../templates/upLoginImp.inc.php";
?>

<script type='text/javascript' src='../js/funcoes.js'></script>
<script type='text/javascript' src='../js/mascara.js'></script>
<script type='text/javascript' src='../js/jquery.js'></script>
<script type='text/javascript' src='../js/jquery.dimensions.js'></script>
<script type='text/javascript' src='../js/chili-1.7.pack.js'></script>

<script type='text/javascript'>
	function inverter(id){
		if($('#'+id).val()=="0"){
			$('#'+id).val(1);
		}else{
			$('#'+id).val(0);
		}
	}
	
	function showDobras(){
		var sexo = $("select[name='sexo']").val();
		var dobras = $("select[name='tipo_avaliacao']").val();
		if(sexo=="1"){
			$("#sexo").html("Masculino");
		}else{
			$("#sexo").html("Feminino");
		}
		
		if(dobras == "1"){
			$(".dobras_m_3").hide();
			$(".dobras_f_3").hide();
			$(".dobras_7").show();
		}else if(dobras=="0" && sexo=="1"){
			$(".dobras_m_3").show();
			$(".dobras_f_3").hide();
			$(".dobras_7").hide();
		}else if(dobras=="0" && sexo=="0"){
			$(".dobras_m_3").hide();
			$(".dobras_f_3").show();
			$(".dobras_7").hide();
		}
	}
	
	function calcularGordura(){
		
		var sexo = $("select[name='sexo']").val();
		var dobras = $("select[name='tipo_avaliacao']").val();
		var idade = parseInt($("input[name='idade']").val());
		var peso = $("input[name='peso']").val().length >0 ? parseFloat($("input[name='peso']").val().replace(",", ".")) : 0;
		
		var gordura;
		var pesoG;
		var pesoM;
		var pesoI;
		
		var torax_7 		= $("input[name='torax_7']").val().length >0 ? parseFloat($("input[name='torax_7']").val().replace(",", ".")) : 0;
		var axilar_7 		= $("input[name='axilar_7']").val().length >0 ? parseFloat($("input[name='axilar_7']").val().replace(",", ".")) : 0;
		var tricipital_7 	= $("input[name='tricipital_7']").val().length >0 ? parseFloat($("input[name='tricipital_7']").val().replace(",", ".")) : 0;
		var subescapular_7	= $("input[name='subescapular_7']").val().length >0 ? parseFloat($("input[name='subescapular_7']").val().replace(",", ".")) : 0;
		var abdominal_7		= $("input[name='abdominal_7']").val().length >0 ? parseFloat($("input[name='abdominal_7']").val().replace(",", ".")) : 0;
		var suprailiaca_7	= $("input[name='suprailiaca_7']").val().length >0 ? parseFloat($("input[name='suprailiaca_7']").val().replace(",", ".")) : 0;
		var coxa_7  		= $("input[name='coxa_7']").val().length >0 ? parseFloat($("input[name='coxa_7']").val().replace(",", ".")) : 0;
		
		var torax_m 		= $("input[name='torax_m']").val().length >0 ? parseFloat($("input[name='torax_m']").val().replace(",", ".")) : 0;
		var abdominal_m		= $("input[name='abdominal_m']").val().length >0 ? parseFloat($("input[name='abdominal_m']").val().replace(",", ".")) : 0;
		var coxa_m  		= $("input[name='coxa_m']").val().length >0 ? parseFloat($("input[name='coxa_m']").val().replace(",", ".")) : 0;

		var tricipital_f	= $("input[name='tricipital_f']").val().length >0 ? parseFloat($("input[name='tricipital_f']").val().replace(",", ".")) : 0;
		var suprailiaca_f	= $("input[name='suprailiaca_f']").val().length >0 ? parseFloat($("input[name='suprailiaca_f']").val().replace(",", ".")) : 0;
		var coxa_f  		= $("input[name='coxa_f']").val().length >0 ? parseFloat($("input[name='coxa_f']").val().replace(",", ".")) : 0;
		
		if(dobras=="1" && sexo=="1"){
			
			var x1 = torax_7 + axilar_7 + tricipital_7 + subescapular_7 + abdominal_7 + suprailiaca_7 + coxa_7;
			//Protocolo de PoLLock
			DC = 1.11200000 - ((0.00043499 * x1) + (0.00000055 * x1 * x1)) - (0.0002882 * idade);
			//Protocolo de Siri
			gordura = (((4.95 / DC) - 4.50) * 100).toFixed(2);
			pesoG = (gordura * peso / 100).toFixed(2).replace(".", ",");
			pesoM = (peso - gordura * peso / 100).toFixed(2);
			pesoI = parseFloat((pesoM/0.85)).toFixed(2).replace(".", ",");
			
		}else if(dobras=="1" && sexo=="0"){
			
			var x = torax_7 + axilar_7 + tricipital_7 + subescapular_7 + abdominal_7 + suprailiaca_7 + coxa_7;
			//Protocolo de PoLLock
			DC = 1.0970 - ((0.00046971 * x) + (0.00000056 * x * x)) - (0.00012828 * idade);
			//Protocolo de Siri
			gordura = (((4.95 / DC) - 4.50) * 100).toFixed(2);
			pesoG = (gordura * peso / 100).toFixed(2).replace(".", ",");
			pesoM = (peso - gordura * peso / 100).toFixed(2);
			pesoI = parseFloat((pesoM/0.75)).toFixed(2).replace(".", ",");
			
		}else if(dobras=="0" && sexo=="1"){
			
			var x = torax_m + abdominal_m + coxa_m;
			//Protocolo de PoLLock
			DC = 1.10938 - 0.0008267 * x + 0.0000016 * x * x - 0.0002574 * idade;
			//Protocolo de Siri
			gordura = (((4.95 / DC) - 4.50) * 100).toFixed(2);
			pesoG = (gordura * peso / 100).toFixed(2).replace(".", ",");
			pesoM = (peso - gordura * peso / 100).toFixed(2);
			pesoI = parseFloat((pesoM/0.85)).toFixed(2).replace(".", ",");
			
		}else if(dobras=="0" && sexo=="0"){
			
			var x = tricipital_f + suprailiaca_f + coxa_f;
			//Protocolo de PoLLock
			DC = 1.0994921 - 0.0009929 * x + 0.0000023 + x * x - 0.0001392 * idade;
			//Protocolo de Siri
			gordura = (((4.95 / DC) - 4.50) * 100).toFixed(2);
			pesoG = (gordura * peso / 100).toFixed(2).replace(".", ",");
			pesoM = (peso - gordura * peso / 100).toFixed(2);
			pesoI = parseFloat((pesoM/0.75)).toFixed(2).replace(".", ",");
			
		}
		
		
		$("input[name='gordura']").val(gordura.replace(".", ","));
		if(peso){
			$("input[name='peso_gordo']").val(pesoG);
			$("input[name='peso_magro']").val(pesoM.replace(".", ","));
			$("input[name='peso_ideal']").val(pesoI);
			$("input[name='peso']").attr("class", "");
		}else{
			$("input[name='peso']").attr("class", "avisoInput");
		}
	}
</script>

<?php


//all
//retirar virgula de todas as variaveis para implementação no banco de dados
$cont = count($_POST);
$array = array_keys($_POST);
for ($i=0; $i<$cont; $i++){
	$_POST[$array[$i]] = str_replace(",", ".", $_POST[$array[$i]]);
	if($i>11){
		$_POST[$array[$i]] = real($_POST[$array[$i]], true);
	}
}
$caractere = "O";
extract($_GET);
extract($_POST);
$id_matricula = base64_decode($id_matricula);

function fichaAvaliacaoFisica($ID = null){
	
	global $id_matricula;
	global $caractere;
	$id_cliente = registro($id_matricula, "matricula", "id_cliente");
	$telefone = registro($id_matricula, "matricula", "telefone");
	
	$instrucao = "select * from matricula_avaliacao where id='$ID'";
	$sql = query($instrucao);
	if(mysqli_num_rows($sql)){
		
		$reg = mysqli_fetch_assoc($sql);
		$cont = count($reg);
		$array = array_keys($reg);
		for ($i=0; $i<$cont; $i++){
			$reg[$array[$i]] = str_replace(".", ",", $reg[$array[$i]]);
		}
		extract($reg);
		
	}else{
		
		$sexo = $tipo_avaliacao = $objetivo = $restricoes = $pa_repouso = $peso = $estatura = $torax_inspirado = $torax_relaxado = 
		$cintura = $abdome = $quadril = $antebraco_d = $antebraco_e = $braco_d = $braco_e = $coxa_d = $coxa_e = $panturrilha_d = 
		$panturrilha_e = $pescoco = $ombro = $torax = $coxa = $abdominal = $tricipital = $suprailiaca = $axilar = $subescapular = 
		$gordura = $peso_gordo = $peso_magro = $peso_ideal = $flexoes = $flexoes_classificacao = $abdominais = $abdominais_classificacao = 
		$escoliose_toracica = $escoliose_lombar = $hipercifose = $hiperlordose = $joelho_genu_varo = $joelho_genu_valgo = 
		$joelho_genu_flexo = $joelho_recurvato = $wells_cm = $wells_classificacao = $id_usuario = $data = null;
		
		$sexo=1;
	}
	
	echo "<form name='formulario' method='post' action='fichaAvaliacaoFisica.php?id_matricula=".base64_encode($id_matricula)."' enctype='multipart/form-data'>";
	if($ID){
		echo "<input type='hidden' name='id_matricula_avaliacao' value='".base64_encode($ID)."'>";
		echo "<input type='hidden' name='op' value='editar'>";
	}else{
		echo "<input type='hidden' name='op' value='novo'>";
	}
	
	echo "<table>";
		echo "<tr>";
			echo "<td colspan='14'>";
				cabecalho();
			echo "</td>";
		echo "</tr>";
		
		echo "</tr>";
			echo "<td colspan='2'><span>ID Matrícula:</span>$id_matricula</td>";
			echo "<td colspan='2'><span>ID Ficha:</span>$ID</td>";
			echo "<td colspan='4'><span>Nome:</span>";
			echo is_numeric($id_cliente) ? registro($id_cliente, "cliente_fornecedor", "nome") : $id_cliente;
			echo "</td>";
			echo "<td colspan='1'><span>Telefone:</span>$telefone</td>";
			echo "<td colspan='4'><span>Avaliador:</span>";
				echo "<select name='avaliador'>";
					echo opcaoSelect("usuario", "nome", "Ativo", $id_usuario, null, "and id<>1", 0, false);
				echo "</select>";
			echo "</td>";
			echo "<td colspan='1'><span>Data da avaliação:</span>".inputData("formulario", "data", null, formataData($data))."</td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th colspan='14'>Ficha de Avaliação Física</th>";
		echo "</tr>";
		
		$idade = subtrairDatas(registro($id_cliente, "cliente_fornecedor", "data_nascimento"), date('Y-m-d'), "anos");
		echo "<input type='hidden' name='idade' value='$idade'>";
		
		//primeira linha
		echo "<tr>";
			echo "<td style='border:none;' colspan='2'><span>Sexo</span>";
				echo "<select name='sexo' onchange='showDobras();'>";
					if($sexo=="0"){
						echo "<option value='1'>Masculino</option>";
						echo "<option value='0' selected='yes'>Feminino</option>";
					}else{
						echo "<option value='1' selected='yes'>Masculino</option>";
						echo "<option value='0'>Feminino</option>";
					}
				echo "</select>";
			echo "</td>";
			echo "<td style='border:none;' colspan='2'><span>Tipo de avaliação</span>";
				echo "<select name='tipo_avaliacao' onchange='showDobras();'>";
					if($tipo_avaliacao=="1"){
						echo "<option value='0'>Pollock 3 dobras</option>";
						echo "<option value='1' selected='yes'>Pollock 7 dobras</option>";
					}else{
						echo "<option value='0' selected='yes'>Pollock 3 dobras</option>";
						echo "<option value='1'>Pollock 7 dobras</option>";
					}
				echo "</select>";
			echo "</td>";
			echo "<td style='border:none;' colspan='2'><span>Objetivo</span><input type='text' name='objetivo' value='$objetivo' style='width:auto;'></td>";
			echo "<td style='border:none;' colspan='2'><span>Restrições</span><input type='text' name='restricoes' value='$restricoes' style='width:auto;'></td>";
			echo "<td style='border:none;' colspan='2'><span>PA Repouso</span><input type='text' name='pa_repouso' value='$pa_repouso' ".mascara("Data", 5)." style='width:auto;'></td>";
			echo "<td style='border:none;' colspan='2'><span>Peso (kg)</span><input type='text' name='peso' value='$peso' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:auto;'></td>";
			echo "<td style='border:none;' colspan='2'><span>Estatura (cm)</span><input type='text' name='estatura' value='$estatura' ".mascara("Valor2")." style='width:auto;'></td>";
		echo "</tr>";
		
		//segunda linha
		echo "<tr>";
			echo "<td colspan='4' style='border:none;'>Perimetria (cm)</td>";
			echo "<td style='border:none;'>D</td>";
			echo "<td style='border:none;'>E</td>";
			echo "<td style='border:none;'></td>";
			echo "<td colspan='7' style='border:none;'>Dobras Cutâneas ";
			echo "<div id='sexo' style='display:inline-block;'>";
				if($sexo=="0"){
					echo "Feminino";
				}else{
					echo "Masculino";
				}
			echo "</div></td>";
		echo "</tr>";
		
		$width = "80";
		
		//terceira linha
		echo "<tr>";
			echo "<td>Pescoço</td>";
			echo "<td colspan='2'><input type='text' name='pescoco' value='$pescoco' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td>Antebraço</td>";
			echo "<td><input type='text' name='antebraco_d' value='$antebraco_d' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td><input type='text' name='antebraco_e' value='$antebraco_e' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td style='border:none;'></td>";
			
			if($tipo_avaliacao==1){
				$d_dobras_m_3 = "display:none;";
				$d_dobras_f_3 = "display:none;";
				$d_dobras_7 = "";
			}elseif($tipo_avaliacao==0 && $sexo==1){
				$d_dobras_m_3 = "";
				$d_dobras_f_3 = "display:none;";
				$d_dobras_7 = "display:none;";
			}elseif($tipo_avaliacao==0 && $sexo==0){
				$d_dobras_m_3 = "display:none;";
				$d_dobras_f_3 = "";
				$d_dobras_7 = "display:none;";
			}else{
				$d_dobras_m_3 = "";
				$d_dobras_f_3 = "display:none;";
				$d_dobras_7 = "display:none;";
			}
			
			//dobras_m_3
			echo "<td class='dobras_m_3' colspan='2' style='$d_dobras_m_3'>Tórax</td>";
			echo "<td class='dobras_m_3' colspan='3' style='$d_dobras_m_3'><input type='text' name='torax_m' value='$torax' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_f_3
			echo "<td class='dobras_f_3' colspan='2' style='$d_dobras_f_3'>Tríceps</td>";
			echo "<td class='dobras_f_3' colspan='3' style='$d_dobras_f_3'><input type='text' name='tricipital_f' value='$tricipital' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_7
			echo "<td class='dobras_7' style='$d_dobras_7'>Tórax</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='torax_7' value='$torax' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			echo "<td class='dobras_7' colspan='2' style='$d_dobras_7'>Axilar Média</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='axilar_7' value='$axilar' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			
			echo "<td colspan='1' style='border:none; white-space:nowrap;'>Gordura atual (%)</td>";
			echo "<td style='border-left:none; border-bottom:none;'><input type='text' name='gordura' value='$gordura' class='inputValor' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
		echo "</tr>";
		
		//quarta linha
		echo "<tr>";
			echo "<td>Ombro</td>";
			echo "<td colspan='2'><input type='text' name='ombro' value='$ombro' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td>Braço</td>";
			echo "<td><input type='text' name='braco_d' value='$braco_d' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td><input type='text' name='braco_e' value='$braco_e' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td style='border:none;'></td>";
			
			//dobras_m_3
			echo "<td class='dobras_m_3' colspan='2' style='$d_dobras_m_3'>Coxa</td>";
			echo "<td class='dobras_m_3' colspan='3' style='$d_dobras_m_3'><input type='text' name='coxa_m' value='$coxa' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_f_3
			echo "<td class='dobras_f_3' colspan='2' style='$d_dobras_f_3'>Suprailíaca</td>";
			echo "<td class='dobras_f_3' colspan='3' style='$d_dobras_f_3'><input type='text' name='suprailiaca_f' value='$suprailiaca' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_7
			echo "<td class='dobras_7' style='$d_dobras_7'>Triciptal</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='tricipital_7' value='$tricipital' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			echo "<td class='dobras_7' colspan='2' style='$d_dobras_7'>Subescapular</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='subescapular_7' value='$subescapular' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			
			echo "<td colspan='1' style='border:none; white-space:nowrap;'>Peso Gordo (kg)</td>";
			echo "<td style='border-top:none; border-left:none; border-bottom:none;;'><input type='text' name='peso_gordo' value='$peso_gordo' class='inputValor' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
		echo "</tr>";
		
		//quinta linha
		echo "<tr>";
			echo "<td>Tórax</td>";
			echo "<td style='white-space:nowrap;'>I:<input type='text' name='torax_inspirado' value='$torax_inspirado' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
			echo "<td style='white-space;nowrap;'>R:<input type='text' name='torax_relaxado' value='$torax_relaxado' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
			echo "<td>Coxa</td>";
			echo "<td><input type='text' name='coxa_d' value='$coxa_d' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td><input type='text' name='coxa_e' value='$coxa_e' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td style='border:none;'></td>";
			
			//dobras_m_3
			echo "<td class='dobras_m_3' colspan='2' style='$d_dobras_m_3'>Abdominal</td>";
			echo "<td class='dobras_m_3' colspan='3' style='$d_dobras_m_3'><input type='text' name='abdominal_m' value='$abdominal' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_f_3
			echo "<td class='dobras_f_3' colspan='2' style='$d_dobras_f_3'>Coxa</td>";
			echo "<td class='dobras_f_3' colspan='3' style='$d_dobras_f_3'><input type='text' name='coxa_f' value='$coxa' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			//dobras_7
			echo "<td class='dobras_7' style='$d_dobras_7'>Abdominal</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='abdominal_7' value='$abdominal' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			echo "<td class='dobras_7' colspan='2' style='$d_dobras_7'>Suprailíaca</td>";
			echo "<td class='dobras_7' style='$d_dobras_7'><input type='text' name='suprailiaca_7' value='$suprailiaca' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			
			echo "<td colspan='1' style='border:none; white-space:nowrap;'>Peso Magro (kg)</td>";
			echo "<td style='border-top:none; border-left:none; border-bottom:none;'><input type='text' name='peso_magro' value='$peso_magro' class='inputValor' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
		echo "</tr>";
		
		//sexta linha
		echo "<tr>";
			echo "<td>Cintura</td>";
			echo "<td colspan='2'><input type='text' name='cintura' value='$cintura' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td>Panturrilha</td>";
			echo "<td><input type='text' name='panturrilha_d' value='$panturrilha_d' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td><input type='text' name='panturrilha_e' value='$panturrilha_e' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td style='border:none;'></td>";
			
			//dobras_m_3
			echo "<td class='dobras_m_3' colspan='5' style='border:none; $d_dobras_m_3'></td>";
			//dobras_f_3
			echo "<td class='dobras_f_3' colspan='5' style='border:none; $d_dobras_f_3'></td>";
			//dobras_7
			echo "<td class='dobras_7' style='$d_dobras_7'>Coxa</td>";
			echo "<td class='dobras_7' colspan='4' style='$d_dobras_7'><input type='text' name='coxa_7' value='$coxa' ".mascara("Valor2", null, null, "calcularGordura()", "calcularGordura()", "calcularGordura()")." style='width:".$width."px;'></td>";
			
			echo "<td colspan='1' style='border-top:none; border-right:none; white-space:nowrap;'>Peso Ideal (kg)</td>";
			echo "<td style='border-top:none; border-left:none;'><input type='text' name='peso_ideal' value='$peso_ideal' class='inputValor' ".mascara("Valor2")." style='width:".($width/2)."px;'></td>";
		echo "</tr>";
		
		//setima linha
		echo "<tr>";
			echo "<td>Abdome</td>";
			echo "<td colspan='2'><input type='text' name='abdome' value='$abdome' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td colspan='3' style='border:none;'></td>";
			echo "<td style='border:none;'></td>";
			
			echo "<td style='border:none; border-left:none;' colspan='7'></td>";
		echo "</tr>";
		
		//oitava linha
		echo "<tr>";
			echo "<td>Quadril</td>";
			echo "<td colspan='2'><input type='text' name='quadril' value='$quadril' ".mascara("Valor2")." style='width:".$width."px;'></td>";
			echo "<td colspan='3' style='border:none; border-bottom:solid 1px white;'></td>";
			echo "<td style='border:none;'></td>";
			
			echo "<td style='border:none; border-left:none;' colspan='7'></td>";
		echo "</tr>";
		
		//setima linha
		echo "<tr>";
			echo "<td colspan='6' style='border:none;'>Neuromotores</td>";
			echo "<td style='border:none;'></td>";
			echo "<td colspan='7' style='border:none;'>Avaliação Postural</td>";
		echo "</tr>";
		
		//oitava linha
		echo "<tr>";
			echo "<td>Flexões</td>";
			echo "<td colspan='2'><input type='text' name='flexoes' value='$flexoes' ".mascara("Integer")." style='width:".$width."px;'></td>";
			echo "<td>Classificação</td>";
			echo "<td colspan='2'><select name='flexoes_classificacao'>";
				for($i=1; $i<6; $i++){
					if($i==$flexoes_classificacao){
						echo "<option value='$i' selected='yes'>".str_repeat($caractere, $i)."</option>";
					}else{
						echo "<option value='$i'>".str_repeat($caractere, $i)."</option>";
					}
				}
			echo "</select></td>";
			echo "<td style='border:none;'></td>";
			echo "<td>Escoliose Torácica ";
			if($escoliose_toracica){
				echo "<input type='checkbox' name='escoliose_toracica_checkbox' checked='yes' onclick=\"inverter('escoliose_toracica');\">";
				echo "<input type='hidden' name='escoliose_toracica' id='escoliose_toracica' value='1'>";
			}else{
				echo "<input type='checkbox' name='escoliose_toracica_checkbox' onclick=\"inverter('escoliose_toracica');\">";
				echo "<input type='hidden' name='escoliose_toracica' id='escoliose_toracica' value='0'>";
			}
			echo "</td>";
			echo "<td>Escoliose Lombar ";
			if($escoliose_lombar){
				echo "<input type='checkbox' name='escoliose_lombar_checkbox' checked='yes' onclick=\"inverter('escoliose_lombar');\">";
				echo "<input type='hidden' name='escoliose_lombar' id='escoliose_lombar' value='1'>";
			}else{
				echo "<input type='checkbox' name='escoliose_lombar_checkbox' onclick=\"inverter('escoliose_lombar');\">";
				echo "<input type='hidden' name='escoliose_lombar' id='escoliose_lombar' value='0'>";
			}
			echo "</td>";
			echo "<td colspan='2'>Hipercifose ";
			if($hipercifose){
				echo "<input type='checkbox' name='hipercifose_checkbox' checked='yes' onclick=\"inverter('hipercifose');\">";
				echo "<input type='hidden' name='hipercifose' id='hipercifose' value='1'>";
			}else{
				echo "<input type='checkbox' name='hipercifose_checkbox' onclick=\"inverter('hipercifose');\">";
				echo "<input type='hidden' name='hipercifose' id='hipercifose' value='0'>";
			}
			echo "</td>";
			echo "<td>Hiperlordose ";
			if($hiperlordose){
				echo "<input type='checkbox' name='hiperlordose_checkbox' checked='yes' onclick=\"inverter('hiperlordose');\">";
				echo "<input type='hidden' name='hiperlordose' id='hiperlordose' value='1'>";
			}else{
				echo "<input type='checkbox' name='hiperlordose_checkbox' onclick=\"inverter('hiperlordose');\">";
				echo "<input type='hidden' name='hiperlordose' id='hiperlordose' value='0'>";
			}
			echo "</td>";
			echo "<td colspan='2'>Joelho Genu Varo ";
			if($joelho_genu_varo){
				echo "<input type='checkbox' name='joelho_genu_varo_checkbox' checked='yes' onclick=\"inverter('joelho_genu_varo');\">";
				echo "<input type='hidden' name='joelho_genu_varo' id='joelho_genu_varo' value='1'>";
			}else{
				echo "<input type='checkbox' name='joelho_genu_varo_checkbox' onclick=\"inverter('joelho_genu_varo');\">";
				echo "<input type='hidden' name='joelho_genu_varo' id='joelho_genu_varo' value='0'>";
			}
			echo "</td>";
		echo "</tr>";
		
		//nona linha
		echo "<tr>";
			echo "<td>Abdominais</td>";
			echo "<td colspan='2'><input type='text' name='abdominais' value='$abdominais' ".mascara("Integer")." style='width:".$width."px;'></td>";
			echo "<td>Classificação</td>";
			echo "<td colspan='2'><select name='abdominais_classificacao'>";
				for($i=1; $i<6; $i++){
					if($i==$abdominais_classificacao){
						echo "<option value='$i' selected='yes'>".str_repeat($caractere, $i)."</option>";
					}else{
						echo "<option value='$i'>".str_repeat($caractere, $i)."</option>";
					}
				}
			echo "</select></td>";
			echo "<td style='border:none;'></td>";
			echo "<td>Joelho Genu Valgo ";
			if($joelho_genu_valgo){
				echo "<input type='checkbox' name='joelho_genu_valgo_checkbox' checked='yes' onclick=\"inverter('joelho_genu_valgo');\">";
				echo "<input type='hidden' name='joelho_genu_valgo' id='joelho_genu_valgo' value='1'>";
			}else{
				echo "<input type='checkbox' name='joelho_genu_valgo_checkbox' onclick=\"inverter('joelho_genu_valgo');\">";
				echo "<input type='hidden' name='joelho_genu_valgo' id='joelho_genu_valgo' value='0'>";
			}
			echo "</td>";
			echo "<td>Joelho Genu Flexo ";
			if($joelho_genu_flexo){
				echo "<input type='checkbox' name='joelho_genu_flexo_checkbox' checked='yes' onclick=\"inverter('joelho_genu_flexo');\">";
				echo "<input type='hidden' name='joelho_genu_flexo' id='joelho_genu_flexo' value='1'>";
			}else{
				echo "<input type='checkbox' name='joelho_genu_flexo_checkbox' onclick=\"inverter('joelho_genu_flexo');\">";
				echo "<input type='hidden' name='joelho_genu_flexo' id='joelho_genu_flexo' value='0'>";
			}
			echo "</td>";
			echo "<td colspan='2'>Joelho Recurvato ";
			if($joelho_recurvato){
				echo "<input type='checkbox' name='joelho_recurvato_checkbox' checked='yes' onclick=\"inverter('joelho_recurvato');\">";
				echo "<input type='hidden' name='joelho_recurvato' id='joelho_recurvato' value='1'>";
			}else{
				echo "<input type='checkbox' name='joelho_recurvato_checkbox' onclick=\"inverter('joelho_recurvato');\">";
				echo "<input type='hidden' name='joelho_recurvato' id='joelho_recurvato' value='0'>";
			}
			echo "</td>";
			echo "<td colspan='3' style='border:none;'></td>";
		echo "</tr>";
		
		//decima linha
		echo "<tr>";
			echo "<td colspan='6' style='border:none;'>Teste de Flexibilidade de banco de Wells</td>";
			echo "<td colspan='8' style='border:none;'></td>";
		echo "</tr>";
		
		//decima primeira linha
		echo "<tr>";
			echo "<td>Centimetros</td>";
			echo "<td colspan='2'><input type='text' name='wells_cm' value='$wells_cm' ".mascara("Integer")." style='width:".$width."px;'></td>";
			echo "<td>Classificação</td>";
			echo "<td colspan='2'><select name='wells_classificacao'>";
				for($i=1; $i<6; $i++){
					if($i==$wells_classificacao){
						echo "<option value='$i' selected='yes'>".str_repeat($caractere, $i)."</option>";
					}else{
						echo "<option value='$i'>".str_repeat($caractere, $i)."</option>";
					}
				}
			echo "</select></td>";
			echo "<td colspan='8' style='border:none;'></td>";
		echo "</tr>";
		
	echo "</table>";
	
	if(getCredencialUsuario("cadastrarMatricula.php")){
		echo "<input type='submit' class='btnEnviar' value='Enviar'>";
	}
	
	echo "</form>";
	
	
	
	
}


if(!isset($op)){
	
	fichaAvaliacaoFisica();	
	
}else{
	
	if($op=="novo"){
		$instrucao = "insert into matricula_avaliacao ";
		$instrucao .= "(id_matricula, sexo , tipo_avaliacao, objetivo, restricoes, pa_repouso, peso, estatura, pescoco, ombro, ";
		$instrucao .= "torax_inspirado, torax_relaxado, cintura, abdome, quadril, antebraco_d, antebraco_e, braco_d, ";
		$instrucao .= "braco_e, coxa_d, coxa_e, panturrilha_d, panturrilha_e, ";
		if($sexo=="1" and $tipo_avaliacao=="0"){//masculino 3 dobras
			$instrucao .= "torax, coxa, abdominal, ";
		}elseif($sexo=="0" and $tipo_avaliacao=="0"){//feminino 3 dobras
			$instrucao .= "tricipital, suprailiaca, coxa, ";
		}elseif(($sexo=="1" or $sexo=="0") and $tipo_avaliacao=="1"){//masculino 7 dobras
			$instrucao .= "torax, axilar, tricipital, subescapular, abdominal, suprailiaca, coxa, ";
		}
		$instrucao .= "gordura, peso_gordo, peso_magro, peso_ideal, flexoes, flexoes_classificacao, abdominais, abdominais_classificacao, ";
		$instrucao .= "escoliose_toracica, escoliose_lombar, hipercifose, hiperlordose, joelho_genu_valgo, joelho_genu_flexo, joelho_recurvato, ";
		$instrucao .= "wells_cm, wells_classificacao, id_usuario, data) ";
		$instrucao .= "values ";
		$instrucao .= "('$id_matricula', '$sexo', '$tipo_avaliacao', '$objetivo', '$restricoes', '$pa_repouso', '$peso', '$estatura', '$pescoco', '$ombro', ";
		$instrucao .= "'$torax_inspirado', '$torax_relaxado', '$cintura', '$abdome', '$quadril', '$antebraco_d', '$antebraco_e', '$braco_d', ";
		$instrucao .= "'$braco_e', '$coxa_d', '$coxa_e', '$panturrilha_d', '$panturrilha_e', ";
		if($sexo=="1" and $tipo_avaliacao=="0"){//masculino 3 dobras
			$instrucao .= "'$torax_m', '$coxa_m', '$abdominal_m', ";
		}elseif($sexo=="0" and $tipo_avaliacao=="0"){//feminino 3 dobras
			$instrucao .= "'$tricipital_f', '$suprailiaca_f', '$coxa_f', ";
		}elseif(($sexo=="1" or $sexo=="0") and $tipo_avaliacao=="1"){//masculino 7 dobras
			$instrucao .= "'$torax_7', '$axilar_7', '$tricipital_7', '$subescapular_7', '$abdominal_7', '$suprailiaca_7', '$coxa_7', ";
		}
		$instrucao .= "'$gordura', '$peso_gordo', '$peso_magro', '$peso_ideal', '$flexoes', '$flexoes_classificacao', '$abdominais', '$abdominais_classificacao', ";
		$instrucao .= "'$escoliose_toracica', '$escoliose_lombar', '$hipercifose', '$hiperlordose', '$joelho_genu_valgo', '$joelho_genu_flexo', '$joelho_recurvato', ";
		$instrucao .= "'$wells_cm', '$wells_classificacao', '$avaliador', '".formataDataInv($data)."')";
		$sql = query($instrucao);
		$id_matricula_avaliacao = mysqli_insert_id($conexao);
		$id_matricula = $id_matricula;
	}elseif($op=="editar"){
		
		$instrucao = "delete from matricula_avaliacao where id='".base64_decode($id_matricula_avaliacao)."'";
		$sql = query($instrucao);
		if(mysqli_affected_rows($conexao)>0){
			$instrucao = "insert into matricula_avaliacao ";
			$instrucao .= "(id_matricula, sexo , tipo_avaliacao, objetivo, restricoes, pa_repouso, peso, estatura, pescoco, ombro, ";
			$instrucao .= "torax_inspirado, torax_relaxado, cintura, abdome, quadril, antebraco_d, antebraco_e, braco_d, ";
			$instrucao .= "braco_e, coxa_d, coxa_e, panturrilha_d, panturrilha_e, ";
			if($sexo=="1" and $tipo_avaliacao=="0"){//masculino 3 dobras
				$instrucao .= "torax, coxa, abdominal, ";
			}elseif($sexo=="0" and $tipo_avaliacao=="0"){//feminino 3 dobras
				$instrucao .= "tricipital, suprailiaca, coxa, ";
			}elseif(($sexo=="1" or $sexo=="0") and $tipo_avaliacao=="1"){//masculino 7 dobras
				$instrucao .= "torax, axilar, tricipital, subescapular, abdominal, suprailiaca, coxa, ";
			}
			$instrucao .= "gordura, peso_gordo, peso_magro, peso_ideal, flexoes, flexoes_classificacao, abdominais, abdominais_classificacao, ";
			$instrucao .= "escoliose_toracica, escoliose_lombar, hipercifose, hiperlordose, joelho_genu_valgo, joelho_genu_flexo, joelho_recurvato, ";
			$instrucao .= "wells_cm, wells_classificacao, id_usuario, data) ";
			$instrucao .= "values ";
			$instrucao .= "('$id_matricula', '$sexo', '$tipo_avaliacao', '$objetivo', '$restricoes', '$pa_repouso', '$peso', '$estatura', '$pescoco', '$ombro', ";
			$instrucao .= "'$torax_inspirado', '$torax_relaxado', '$cintura', '$abdome', '$quadril', '$antebraco_d', '$antebraco_e', '$braco_d', ";
			$instrucao .= "'$braco_e', '$coxa_d', '$coxa_e', '$panturrilha_d', '$panturrilha_e', ";
			if($sexo=="1" and $tipo_avaliacao=="0"){//masculino 3 dobras
				$instrucao .= "'$torax_m', '$coxa_m', '$abdominal_m', ";
			}elseif($sexo=="0" and $tipo_avaliacao=="0"){//feminino 3 dobras
				$instrucao .= "'$tricipital_f', '$suprailiaca_f', '$coxa_f', ";
			}elseif(($sexo=="1" or $sexo=="0") and $tipo_avaliacao=="1"){//masculino 7 dobras
				$instrucao .= "'$torax_7', '$axilar_7', '$tricipital_7', '$subescapular_7', '$abdominal_7', '$suprailiaca_7', '$coxa_7', ";
			}
			$instrucao .= "'$gordura', '$peso_gordo', '$peso_magro', '$peso_ideal', '$flexoes', '$flexoes_classificacao', '$abdominais', '$abdominais_classificacao', ";
			$instrucao .= "'$escoliose_toracica', '$escoliose_lombar', '$hipercifose', '$hiperlordose', '$joelho_genu_valgo', '$joelho_genu_flexo', '$joelho_recurvato', ";
			$instrucao .= "'$wells_cm', '$wells_classificacao', '$avaliador', '".formataDataInv($data)."')";
			$sql = query($instrucao);
			$id_matricula_avaliacao = mysqli_insert_id($conexao);
			$id_matricula = $id_matricula;
		}
	}elseif($op=="deletar"){
		
		if(!isset($confirma)){
			
			echo "<form method='post' action='fichaAvaliacaoFisica.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='id_matricula_avaliacao' value='$id_matricula_avaliacao'>";
				echo "<input type='hidden' name='id_matricula' value='$id_matricula'>";
				echo "<input type='hidden' name='confirma' value='true'>";
				echo "<input type='hidden' name='op' value='deletar'>";
				echo "<h1>Deseja realmente deletar está avaliação?</h1>";
				echo "<input class='aSubmit' type='submit' value='Sim'><br><br>";
				echo "<a href='#' class='aSubmit' onclick='window.close();'>Não</a>";
			echo "</form>";
			
		}else{
			$instrucao = "delete from matricula_avaliacao where id='".base64_decode($id_matricula_avaliacao)."'";
			$sql = query($instrucao);
			
			echo "<script language=\"JavaScript\" type=\"text/javascript\">";
				echo "window.opener.location.reload();";
				echo "window.close()"; 
			echo "</script>";
			
		}
			
		
	}
	if($op!="deletar"){
		if(!is_numeric($id_matricula_avaliacao)){
			$id_matricula_avaliacao = base64_decode($id_matricula_avaliacao);
		}
		fichaAvaliacaoFisica($id_matricula_avaliacao);
	}
	
}

//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script>