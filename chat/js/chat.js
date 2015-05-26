$(function(){
	$('body').delegate('.mensagem', 'keydown', function(e){
		var campo = $(this);
		var mensagem = campo.val();
		var to = $(this).attr('id');
		
		if(e.keyCode == 13){
			if(mensagem != ''){
				$.post('chat/ajax/chat.php',{
					acao: 'inserir',
					mensagem: mensagem,
					para: to
				}, function(retorno){
					$('#jan_'+to+' ul.listar').append(retorno);
					var height = $('#jan_'+to+' ul.listar')[0].scrollHeight;
					$('#jan_'+to+' ul.listar').scrollTop(height);
					campo.val('');

				});
				
			}
		}
	})
});