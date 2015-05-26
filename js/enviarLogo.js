//<!--
$(document).ready(function() {
	/* #imagem é o id do input, ao alterar o conteudo do input execurará a função baixo */
	$('#imgsrc').change(function() {
		$('#visualizar').html('<img src="img/loading.gif" alt="Enviando..." style="width:30px;">Enviando...');
		/* Efetua o Upload sem dar refresh na pagina */
		$('#formCadastraEmpresa').ajaxForm({
			target : '#visualizar' // o callback será no elemento com o id #visualizar
		});
	});
})
//-->