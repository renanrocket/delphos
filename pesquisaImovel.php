<?php
include "templates/upLogin.inc.php";


//all
extract($_GET);
extract($_POST);

if(!isset($op)){
?>
	<script type="text/javascript">
		$(function(){
			$('#tipo_imovel').autocomplete({
				source: 'inc/ajaxPesquisaImovel.inc.php'
			});
		});
	</script>
	<form class='form' method='get' action='pesquisaImovel.php' enctype='multipar/form-data'>
		<div class='column'>
			<label>Procurar Imóvel por</label>
			<label for='tipo_imovel'>Tipo:</label>
			<input type='text' name='tipo_imovel' id='tipo_imovel' placeholder='casa'>
			<div class='submit-wrap'><input class='submit' type='submit' value='Entrar'></div>
		</div>
	</form>
<?php	
}else{
	
}

	
//end all

include "templates/downLogin.inc.php";
?>