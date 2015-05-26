<?php

include "templates/upLogin.inc.php";

?>

<script type="text/javascript">
	function qtdProduto(qtd){
		if(qtd<1){
			qtd = 1;
				$("input[name='qtd']").val("1");
		}
		if(qtd>30){
			qtd = 30;
			$("input[name='qtd']").val("30");
		}
		for(var i = 1; i<=30; i++){
			if(i<=qtd){
				$("#tr_produto_" + i).attr("style", "");
			}else{
				$("#tr_produto_" + i).attr("style", "display:none;");
			}
		}
	}

	function showProduto(input){

		id = input.id.split("_");

		if (input.value.length == 0) {
		// Hide the suggestion box.
			$('#suggestions_' + id[1]).hide();
		} else {
			$('#suggestions_' + id[1]).show();
			var cod = "<center><img width='30' src='img/loading.gif'></center>";
			$('#autoSuggestionsList_' + id[1]).html(cod);
			$.post("inc/etiqueta_itens.inc.php", {
				queryString : "" + input.value + "",
				inputId : "" + id[1] + ""
			}, function(data) {
				if (data.length > 0) {
					$('#autoSuggestionsList_' + id[1]).html(data);
				}
			});
		}

	}


	function lookupOff() {
		$('.suggestionsBox').hide();
		$('.suggestionsList').hide();
	}

	function preencher(id, nome, inputId) {
		$('#idItem_' + inputId).val(id);
		$('#item_' + inputId).val(nome);
		setTimeout("$('#suggestions_" + inputId + "').hide();", 200);
	}

	function menos(){
		var qtdEtiqueta = $("input[name='qtd']").val();
		if(qtdEtiqueta>1){
			qtdEtiqueta = parseInt(qtdEtiqueta) - 1;
			$("input[name='qtd']").val(qtdEtiqueta);
		}
		qtdProduto(qtdEtiqueta);
	}

	function mais(){
		var qtdEtiqueta = $("input[name='qtd']").val();
		if(qtdEtiqueta<31){
			qtdEtiqueta = parseInt(qtdEtiqueta) + 1;
			$("input[name='qtd']").val(qtdEtiqueta);
		}
		qtdProduto(qtdEtiqueta);
	}

</script>
<?php

//all
echo "<form method='get' action='popup/etiqueta.php' enctype='multipart/form-data'>";
echo "<table id='gradient-style'>";
    echo "<tr>";
        echo "<th>Emissão de etiquetas adesivas para anexar no produto";
        echo "<span style='float:right'>";
        	echo "<input type='hidden' name='qtd' value='1'></td>";
        	echo "<a href='#' title='Retirar uma etiqueta.' onclick=\"menos();\"><img src='img/menos.png'></a>";
        	echo "<a href='#' title='inserir uma etiqueta.' onclick=\"mais();\"><img src='img/mais.png'></a>";
        echo "</span>";
        echo "</th>";
    echo "</tr>";
	for($i=1, $style=""; $i<=30; $i++){
        if($i>1){
            $style="style='display:none;'";
        }
		echo "<tr id='tr_produto_$i' $style>";
            echo "<td>Produto $i:<br>";
			echo "<input type='hidden' name='idItem[]' id='idItem_$i'>";
			echo "<input type='text' name='item[]' onkeyup=\"showProduto(this);\" onblur='preencher();' id='item_$i' autocomplete='off'>";
			echo "<div class='suggestionsBox' id='suggestions_$i' style='display: none;'><span align='right'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
			echo "<div class='suggestionList' id='autoSuggestionsList_$i'></div></div>";
        echo "</tr>";
	}
    echo "<tr>";
        echo "<th><input type='submit' value='Enviar' onclick=\"this.form.target='_blank'; return true;\"></th>";
    echo "</tr>";
echo "</table>";
echo "</form>";
	
//end all
include "templates/downLogin.inc.php";

?>