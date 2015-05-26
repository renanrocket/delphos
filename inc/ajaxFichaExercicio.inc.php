<?php 
include "funcoes.inc.php";
if(isset($_COOKIE["id_empresa"])){
	//includes do php para todas as paginas
	$conn = TConnection::open("gestor");

	$criterio = new TCriteria;
	$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

	$sql = new TSqlSelect;
	$sql->setEntity("cliente");
	$sql->addColumn('alcunha');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		extract($row);
		define("ALCUNHA", $alcunha);
	}

	//deletar esse codigo apois migração do app.ado
	file_exists("../conecta.php") ?
	include "../conecta.php" : $conexao = null;
	$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
	extract(mysqli_fetch_assoc($sql));
	include "../".$conectasrc;
	//fim
}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}

extract($_POST);

if(isset($idExercicio)){
	if($idExercicio){
		$sql = query("select * from exercicio where id='$idExercicio'");
		extract(mysqli_fetch_assoc($sql));
		
		$cod = "<table><tr>";
		$cod .= "<td><span>Imagem</span>".showImagemExercicio($idExercicio, 2)."</td>";
		$cod .= "<td><span>Benefícios</span><center style='font-size:30px;'>".$beneficios."</center></td>";
		$cod .= "<td><span>Musculos afetados</span><center style='font-size:30px;'>";
		$sql = query("select * from exercicio_musculo where id_exercicio='$idExercicio'");
		for($i=0; $i<mysqli_num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			$cod.= registro($id_musculo, "musculos", "nome")."<br>";
		}
		$cod .="</center></td>";
		echo $cod;
	}
}elseif(isset($idMatriculaExercicioItem)){
	$sql = query("select * from matricula_exercicio_item where id='$idMatriculaExercicioItem'");
	extract(mysqli_fetch_assoc($sql));
	if($checked==0){
		$sql = query("update matricula_exercicio_item set checked='1' where id='$idMatriculaExercicioItem'");
		$checked=1;
	}else{
		$sql = query("update matricula_exercicio_item set checked='0' where id='$idMatriculaExercicioItem'");
		$checked=0;
	}
	echo $checked;
	//quando ele chegar no final de todos os treiono ele deve resetar todo os treinos
	$instrucao = "select * from matricula_exercicio where status = '1' and id_matricula = '$idMatricula' and id = any ";
	$instrucao .= "(select id_matricula_exercicio from matricula_exercicio_item where checked = '0')";
	$sql1 = query($instrucao);
	if(mysqli_num_rows($sql1)==0){
		$instrucao = "select id from matricula_exercicio where status = '1' and id_matricula = '$idMatricula' and id = any ";
		$instrucao .= "(select id_matricula_exercicio from matricula_exercicio_item where checked = '1')";
		$sql = query($instrucao);
		for($i=0; $i<mysqli_num_rows($sql); $i++){
			extract(mysqli_fetch_assoc($sql));
			$sqlUpdate = query("update matricula_exercicio_item set checked='0' where id_matricula_exercicio='$id'");
		}
		
	}
	
}




mysqli_close($conexao);


?>