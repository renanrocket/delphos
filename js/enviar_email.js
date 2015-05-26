function ajaxEnviarEmail(remetente, destinatario, assunto, corpo, idDiv){
	remetente = $(remetente).val();
	destinatario = $(destinatario).val();
	assunto = $(assunto).val();
	corpo = $(corpo).val();
	
	$("#"+idDiv).html("<label>Enviando e-mail</label><br><img src='img/loading_bar.gif'>");
	$.post("inc/ajaxEnviarEmail.inc.php",{
		remetente : "" + remetente + "",
		destinatario : "" + destinatario + "",
		assunto : "" + assunto + "",
		corpo : "" + corpo + ""
	}, function(data){
		$("#"+idDiv).html(data);
		var intervalo = window.setInterval(infoApagar, 3000);
		window.setTimeout(function() {
			clearInterval(intervalo);
		}, 3500);
	});
}