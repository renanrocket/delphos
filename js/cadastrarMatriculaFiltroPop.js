function showPlano(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='../img/loading.gif'></center>";
		$('#tdPlano').html(cod);
		$.get("../inc/cadastrarMatricula.inc.php?op=plano", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				$('#tdPlano').html(data);
			}
		});
	}
}
function showDataTermino(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='../img/loading.gif'></center>";
		$('#tdDataTermino').html(cod);
		$.get("../inc/cadastrarMatricula.inc.php?op=dataTermino", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				
				$('#tdDataTermino').html(data);
			}
		});
	}
}

function showPlanoValor(plano){
	if(plano!=0){
		var cod = "<center><img width='30' src='../img/loading.gif'></center>";
		$("input[name='valorPlano']").attr("style", "width:70%;");
		$('#divPlanoPreco').show();
		$('#divPlanoPreco').html(cod);
		$.get("../inc/cadastrarMatricula.inc.php?op=valorPlano", {
			Plano : "" + plano + ""
		}, function(data) {
			if (data.length > 0) {
				$("input[name='valorPlano']").val(data);
				$("input[name='valorPlano']").attr("style", "width:99%;");
				$('#divPlanoPreco').hide();
				$('#visualizarHistorico').attr("style", "position:absolute; left:97%;"); //corrigindo o bug do navegador do renan
			}
		});
	}
}

