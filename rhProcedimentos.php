<?php
include "templates/upLogin.inc.php";


//op null (listar)
//op criar
//op novo
//op visualizar
//op editar

$op=null;
extract($_GET);
extract($_POST);

function formulario($id_formulario=null){
	
	echo "<form method='post' action='rhFormularios.php' enctype='multipart/form-data'>";
	
	if($id_formulario){
		$sql = query("select * from formularios where id='$id_formulario'");
		extract(mysqli_fetch_assoc($sql));
		$titulo = "Formulário $id $nome | <a href='rhFormularios.php?op=criar' class='aSubmit'>Novo Fomulário</a> | <a href='rhFormularios.php' class='aSubmit'>Lista de formulários</a>";
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='id' value='".base64_encode($id)."'>";
	}else{
		echo "<input type='hidden' name='op' value='novo'>";
		$id = $nome = $valor = null;
		$status = 1;
		$data = date("Y-m-d H:i:s");
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$titulo = "Novo Formulário | <a href='rhFormularios.php' class='aSubmit'>Listar Formulários</a> ";
	}
	
	$tabela = new tabela;
	$tabela->setTag(array($titulo));
	$tabela->resultados = false;
	
	$valores[] = "Nome<br><input type='text' name='nome' value='$nome'>";
	$valores[] = "Valor<br><textarea name='valor' class='ckeditor'>$valor</textarea>";
	
	$cod = "<select name='status'>";
	if($status){
		$cod .= "<option value='1' selected='yes'>Ativo</option>";
		$cod .= "<option value='0'>Inativo</option>";
	}else{
		$cod .= "<option value='1'>Ativo</option>";
		$cod .= "<option value='0' selected='yes'>Inativo</option>";
	}
	$cod .= "</select>";
	$valores[] = "Status<br>$cod";
	
	$valores[] = "Data de Criação<br>".formataData($data);;
	$valores[] = "Usuário que criou este formulário<br>".registro($id_usuario, "usuario", "nome");
	$valores[] = "<input type='submit' class='btnEnviar' value='Enviar'>";
	
	
	$tabela->setValores($valores);
	
	echo $tabela->showTabela();
	
	echo "</form>";
	
}

if($op){
	
	if($op=="criar"){
			
		formulario();
	
	}elseif($op=="novo"){
		
		$instrucao = "insert into formularios ";
		$instrucao .= "(nome, valor, status, data, id_usuario) values ";
		$instrucao .= "('$nome', '$valor', '$status', '".date('Y-m-d H:i:s')."', '".getIdCookieLogin($_COOKIE["login"])."')";
		$sql = query($instrucao);
		$id_formulario = mysqli_insert_id($conexao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Inseriu um novo formulário.";
		$tabela_afetada = "formulario";
		$chave_principal = $id_formulario;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		info("Formulário cadastrado com sucesso.");
		
		formulario($id_formulario);
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=rhFormularios.php?op=visualizar&id=".base64_encode($id_formulario)."'>";
	
	}elseif($op=="visualizar"){
		
		formulario(base64_decode($id));
		
	}elseif($op=="editar"){
			
		$id_formulario = base64_decode($id);
		
		$instrucao = "update formularios set ";
		$instrucao .= "nome='$nome', valor='$valor', status='$status' where id='$id_formulario'";
		$sql = query($instrucao);
		
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Editou um novo formulário.";
		$tabela_afetada = "formulario";
		$chave_principal = $id_formulario;
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		
		info("Formulário editado com sucesso.");
		
		formulario($id_formulario);
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=rhFormularios.php?op=visualizar&id=".base64_encode($id_formulario)."'>";
		
	}
	
}else{
	//op null (listar)
	$tabela = new tabela;
	
	$titulo = "<a href='rhFormularios.php?op=criar' class='aSubmit'>Novo Fomulário</a> | Lista de formulários";
	
	$tabela -> setTitulo($titulo);
	$tabela -> setTag(array("ID", "Nome do formulário", "Status"));
	
	$sql = query("select * from formularios");
	for ($i = 0, $valores = null; $i < mysqli_num_rows($sql); $i++) {
		extract(mysqli_fetch_assoc($sql));
	
		$cod = "<form method='get' action='rhFormularios.php' enctype='multipart/form-data'>";
		$cod .= "<input type='hidden' name='id' value='" . base64_encode($id) . "'>";
		$cod .= "<input type='hidden' name='op' value='visualizar'>";
		$cod .= "<input type='submit' value='$id'>";
		$cod .= "</form>";
	
		$valores[] = $cod;
		$valores[] = $nome;
		$status == 1 ? $valores[] = "Ativo" : $valores[] = "Inativo";
	}
	
	$tabela -> setValores($valores);
	echo $tabela -> showTabela();
}



include "templates/downLogin.inc.php";
?>

