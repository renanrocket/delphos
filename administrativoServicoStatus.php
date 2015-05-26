<?php
include "templates/upLogin.inc.php";
?>
<link rel="stylesheet" href="plugins/colorpick/css/colorpicker.css" type="text/css" />
<link rel="stylesheet" media="screen" type="text/css" href="plugins/colorpick/css/layout.css" />
<script type="text/javascript" src="plugins/colorpick/js/jquery.js"></script>
<script type="text/javascript" src="plugins/colorpick/js/colorpicker.js"></script>
<script type="text/javascript" src="plugins/colorpick/js/eye.js"></script>
<script type="text/javascript" src="plugins/colorpick/js/utils.js"></script>
<script type="text/javascript" src="plugins/colorpick/js/layout.js?ver=1.0.2"></script>
<?php


extract($_POST);
extract($_GET);

function formStatus($statusId = null){
	
	$sequenciaCor = array("Branco"=> "white", "Preto" => "black", "Vermelho" => "red", 
	"Amarelo"=>"yellow", "Verde"=>"green", "Azul"=>"blue", "Laranja"=>"#ffbf00", "Marrom"=>"#a35900",
	"Verde Claro"=>"#21ff00", "Azul Claro"=>"#00ffe9", "Rosa"=>"#ff00f2", "Cinza"=>"#c6c6c6");
	
	echo "<form method='post' action='administrativoServicoStatus.php' enctype='multipart/form-data'>";
	
	if($statusId){
		extract(mysqli_fetch_assoc(query("select * from ordem_servico_status where id='$statusId'")));
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='statusId' value='".base64_encode($statusId)."'>";
		$cor_font = str_replace("#", "", $cor_font);
		$cor_bg = str_replace("#", "", $cor_bg);
	}else{
		$id = $nome = $descricao = $status = null;
		$cor_font = "ffffff";
		$cor_bg = "000000";
		echo "<input type='hidden' name='op' value='novo'>";
	}
	echo "<tr>";
	echo "<th colspan='3'>Insira o novo status de ordem de serviço.</th>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Nome do status:<br><input type='text' name='nome' value='$nome'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Selecione a cor da fonte:<br>";
	
	echo "<input type='text' id='colorpickerField1' maxlength='6' name='cor_font' value='$cor_font'>";
	
	/*
	echo "<select name='cor_font'>";
	foreach ($sequenciaCor as $key => $value) {
		if($value==$cor_font){
			echo "<option value='$value' selected='yes' style='color:$value;'>$key</option>";
		}else{
			echo "<option value='$value' style='color:$value;'>$key</option>";
		}
	}
	echo "</select>";
	*/
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Selecione a cor do fundo:<br>";
	echo "<input type='text' id='colorpickerField2' maxlength='6' name='cor_bg' value='$cor_bg'>";
	/*
	echo "<select name='cor_bg'>";
	foreach ($sequenciaCor as $key => $value) {
		if($value==$cor_bg){
			echo "<option value='$value' selected='yes' style='color:$value;'>$key</option>";
		}else{
			echo "<option value='$value' style='color:$value;'>$key</option>";
		}
	}
	echo "</select>";
	*/
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Legenda:<br>";
	echo "<textarea name='descricao'>$descricao</textarea>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan='3'>Status:<br>";
	echo "<select name='status'>";
	if($status===0){
		echo "<option value='1'>Ativado</option>";
		echo "<option value='0' selected='yes'>Desativado</option>";
	}else{
		echo "<option value='1' selected='yes'>Ativado</option>";
		echo "<option value='0'>Desativado</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th colspan='3' align='right'><input type='submit' class='btnEnviar' value='Enviar'></th>";
	echo "</tr>";
	
	echo "</form>";
}

function formStatusCadastrados(){
	echo "<tr>";
	echo "<th colspan='3' align='center'>Status já cadastrados</th>";
	echo "</tr>";
	$sql = query("select * from ordem_servico_status order by status");
	for($i=0; $i<mysqli_num_rows($sql); $i++){
		extract(mysqli_fetch_array($sql));
		$style = "style='background:none; background-color:$cor_bg; color:$cor_font;'";
		echo "<tr>";
		echo "<td $style align='center'>";
		if($id<4){
			echo $id;
		}else{
			echo "<form method='get' action='administrativoServicoStatus.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='visualizar'>";
			echo "<input type='hidden' name='statusId' value='".base64_encode($id)."'>";
			echo "<input type='submit' value='$id'>";
		}
		echo "</form>";
		echo "</td>";
		echo "<td $style align='center'>";
		echo "$nome";
		echo "</td>";
		$status ? $status = "Ativado" : $status = "Desativado";
		echo "<td $style align='center'>$status</td>";
		echo "</tr>";
	}
	echo "<tr><th colspan='3' align='center'>Existem $i status de ordem de serviço</th></tr>";
}





if(!isset($op)){
	echo "<table id='gradient-style' style='width:40%'>";
	formStatus();
	formStatusCadastrados();
	echo "</table>";
}elseif(isset($op)){
	if(isset($statusId)){
		$statusId = base64_decode($statusId);
	}
	if($op=="editar" or $op=="novo"){
		$cor_font = "#".$cor_font;
		$cor_bg = "#".$cor_bg;
	}
	echo "<table id='gradient-style' style='width:40%'>";
	if($op=="visualizar"){
		formStatus($statusId);
	}elseif($op=="editar"){
		$sql = query("update ordem_servico_status set nome='$nome', cor_font='$cor_font', cor_bg='$cor_bg', descricao='$descricao', status='$status' where id='$statusId'");
		info("Status editado.");
		formStatus($statusId);
		formStatusCadastrados();
	}elseif($op=="novo"){
		$sql = query("insert into ordem_servico_status (nome, cor_font, cor_bg, descricao, status) values ('$nome', '$cor_font', '$cor_bg', '$descricao', '$status')");
		$statusId = mysqli_insert_id($conexao);
		info("Novo status de Ordem de serviço adicionado.");
		formStatus($statusId);
		formStatusCadastrados();
	}
	echo "</table>";
}



include "templates/downLogin.inc.php";
?>
