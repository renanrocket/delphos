<?php
include 'templates/upLogin.inc.php';
?>

<script type="text/javascript">
	function showIndex(credencial) {

		var cred = new Array();
		cred[0] = "pesquisaOrcamento";
		cred[1] = "pesquisaOrdemServico";
		cred[2] = "pesquisaPDV";
		cred[3] = "pesquisaConta";
		cred[4] = "pesquisaProduto";
		cred[5] = "pesquisaMatricula";
		cred[6] = "pesquisaUsuario";

		for (var i = 0; i < cred.length; i++) {
			if (cred[i] == credencial) {
				if ($('#' + credencial).is(':visible')) {
					$('#' + credencial).hide(1000);
					$('#' + credencial + "_sep").hide();
				} else {
					$('#' + credencial).show(1000);
					$('#' + credencial + "_sep").show();
				}
			} else {
				$('#' + cred[i]).hide(1000);
				$('#' + cred[i] + "_sep").hide();
			}

		}

	}
</script>
<style type="text/css">
	@import url(plugins/FullWidthTabs/css/component.css); /*tab responsivo (existe a outra parte no javascript)*/
	body{
		background-color: white !important;
	}
</style>

<?php

	
	
	$sql = query("select * from historico order by id desc limit 10");
	if (mysqli_num_rows($sql) > 0) {
		echo "<div class='marquee'>";
	}
	for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
		extract(mysqli_fetch_assoc($sql));
		echo "<p>";
		$sqlNome = query("select nome as Nome from usuario where id='$id_usuario'");
		extract(mysqli_fetch_assoc($sqlNome));
		$Nome = explode(" ", $Nome);
		$cont = count($Nome);
		$Nome = $Nome[0] . " " . $Nome[$cont - 1];
		echo "Histórico - $id | às " . formataData($data) . " $Nome <b>$acao</b> ";
		switch ($tabela_afetada) {
			case 'produto' :
				echo "<a style='line-height: 30px; background:none;' href='cadastrarProduto.php?op=visualizar&id=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaProduto.png'></a>";
				break;
			case 'orcamento' :
				echo "<a style='line-height: 30px; background:none;' href='cadastrarOrcamento.php?op=visualizar&id=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaOrcamento.png'></a>";
				break;
			case 'ordem_servico' :
				echo "<a style='line-height: 30px; background:none;' href='cadastrarOrdemServico.php?op=visualizar&id=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaOrdemServico.png'></a>";
				break;
			case 'cliente_fornecedor' :
				echo "<a style='line-height: 30px; background:none;' href='cadastrarClienteFornecedor.php?op=visualizar&id_cliente_fornecedor=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaClienteFornecedor.png'></a>";
				break;
			case 'conta' :
				echo "<a style='line-height: 30px; background:none;' href='pesquisaConta2.php?conta=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaConta.png'></a>";
				break;
			case 'conta_itens' :
				$sqlContaItens = query("select id_conta as chave_principal from $tabela_afetada where id='$chave_principal'");
				if (mysqli_num_rows($sqlContaItens)) {
					extract(mysqli_fetch_assoc($sqlContaItens));
					echo "<a style='line-height: 30px; background:none;' href='pesquisaConta2.php?conta=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaConta.png'></a>";
				}
				break;
			case 'matricula' :
				echo "<a style='line-height: 30px; background:none;' href='cadastrarMatricula.php?op=visualizar&id=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaMatricula.png'></a>";
				break;
			case 'matricula_plano_assinatura' :
				$sqlMatricula = query("select id_matricula as chave_principal from $tabela_afetada where id='$chave_principal'");
				extract(mysqli_fetch_assoc($sqlMatricula));
				echo "<a style='line-height: 30px; background:none;' href='cadastrarMatricula.php?op=visualizar&id=" . base64_encode($chave_principal) . "'>Visualizar <img src='img/icones/pesquisaMatricula.png'></a>";
				break;
			default :
				break;
		}
		echo "</p>";
	}
	if (mysqli_num_rows($sql) > 0) {
		if (getCredencialUsuario("administrativoToken.php")) {
			echo "<span id='visualizarHistorico'><a style='line-height: 30px; background:none; padding-left:0px;' href='#' " . pop("historico.php", 600, 700) . ">";
			echo "<img style='margin-right:10px; margin-left:5px;' src='img/historico.png'>Visualizar histórico.</a></span>";
		}
		echo "</div>";
	}
	
	
	$data1 = date('Y-m') . "-01 00:00:00";
	$data2 = date('Y-m') . "-31 23:59:59";
	
	$tempo = 60;
	
	//echo "<meta HTTP-EQUIV='refresh' CONTENT='" . $tempo * 20 . ";URL=indexUsuario.php'>";
	
	echo "<div id='tabs' class='tabs'>";
		echo "<nav>";
			echo "<ul>";
			
				echo "<li>";
					echo "<a href='#oferta'><img src='img/icones/oferta.png'><span>Ofertas</span></a>";
				echo "</li>";
				
				
				function showTab($credencial, $texto = null){
					echo "<li><a href='#$credencial'><img src='img/icones/$credencial.png'><span>$texto</span></a></li>";
				}
				
				$credencial = "pesquisaOrcamento";
				if (getCredencialUsuario($credencial)) {
					$instrucao1 = "select * from orcamento where data_emissao>='" . date('Y-m-d H:i:s', strtotime("- 1 month")) . "' and data_emissao<='$data2' and status='1'";
					$sql1 = query($instrucao1);
					$instrucao2 = "select * from orcamento where data_emissao>='$data1' and data_emissao<='$data2' and (status='1' or status='2')";
					$sql2 = query($instrucao2);
					if (mysqli_num_rows($sql1) > 0 or mysqli_num_rows($sql2) > 0) {
						showTab($credencial, "Orçamentos");
					}
				
				}
				
				$credencial = "pesquisaPDV";
				if (getCredencialUsuario($credencial)) {
					$instrucao = "select id_conta as id from conta_itens where id_conta = any (select id from conta where tabela_referido='pdv' and status<>'4') and valor is null";
					//$instrucao = "select valor, id, referido from conta where tabela_referido='pdv' and status<>'4'";
					$sql = query($instrucao);
					for ($i = $cod = 0; $i < mysqli_num_rows($sql); $i++) {
						extract(mysqli_fetch_assoc($sql));
						$referido = registro($id, 'conta', 'referido');
						$valor = registro($id, 'conta', 'valor');
						$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
						$sqlParcial = query($instrucao);
						extract(mysqli_fetch_assoc($sqlParcial));
						$valorParcial = round($valorParcial, 2);
						if ($valorParcial < $valor) {
							$cod++;
						}
					}
					if ($cod > 0) {
						showTab($credencial, "PDV's");
					}
				}
				
				$credencial = "pesquisaOrdemServico";
				if (getCredencialUsuario($credencial)) {
				
					$instrucao1 = "select * from ordem_servico where data_venda>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
					$instrucao1 .= " and data_venda<='" . date('Y-m-d H:i:s') . "' and data_concluida is null";
					$sql1 = query($instrucao1);
					$instrucao2 = "select * from ordem_servico where data_concluida>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
					$instrucao2 .= " and data_concluida<='" . date('Y-m-d H:i:s') . "'";
					$sql2 = query($instrucao2);
					$instrucao3 = "select * from ordem_servico where data_previsao>='" . date('Y-m-d') . " 00:00:00'";
					$instrucao3 .= " and data_previsao<='" . date('Y-m-d') . " 23:59:59' and data_concluida is null";
					$sql3 = query($instrucao3);
					if (mysqli_num_rows($sql1) > 0 or mysqli_num_rows($sql2) > 0 or mysqli_num_rows($sql3) > 0) {
						showTab($credencial, "O.S's");
					}
				
				}
				
				$credencial = "pesquisaMatricula";
				if (getCredencialUsuario($credencial)) {
					$instrucao1 = "select * from matricula where status='1'";
					$sql1 = query($instrucao1);
					$instrucao2 = "select * from matricula where status='0' and id = any (select id_matricula from matricula_plano_assinatura where data_termino>='" . date('Y-m-d', strtotime("- 1 month")) . "')";
					$sql2 = query($instrucao2);
					if (mysqli_num_rows($sql1) > 0 or mysqli_num_rows($sql2) > 0) {
						showTab($credencial, "Matrículas");
					}
				}
				
				$credencial = "pesquisaConta";
				if (getCredencialUsuario($credencial)) {
				
					$mais15Dias = date('Y-m-d', strtotime("+ 15 days"));
					$instrucao1 = "select * from conta_itens where id_caixa_movimento is null and data_vencimento<='$mais15Dias' ";
					$instrucao1 .= "and id_conta = any (select id from conta where status='3')";
					$sql1 = query($instrucao1);
					$instrucao2 = "select * from conta_itens where id_caixa_movimento is null and data_vencimento<='$mais15Dias' ";
					$instrucao2 .= "and id_conta = any (select id from conta where status='2')";
					$sql2 = query($instrucao2);
					//$instrucao3 = "select * from conta where id = any(select id_conta from conta_itens where id_caixa_movimento is null) and (status='2' or status='3') and valor>0 order by status";
					//$sql3 = query($instrucao3);
					if (mysqli_num_rows($sql1) > 0 or mysqli_num_rows($sql2) > 0 /*or mysqli_num_rows($sql3) > 0*/) {
						showTab($credencial, "Finanças");
					}

				}
				
				$credencial = "pesquisaProduto";
				if (getCredencialUsuario($credencial)) {
				
					$instrucao = "select * from produto where qtd_estoque<=qtd_minima and qtd_minima<>'0'";
					$sql = query($instrucao);
					if (mysqli_num_rows($sql) > 0) {
						showTab($credencial, "Estoque");
					}
				}
				
				$credencial = "pesquisaUsuario";
				if (getCredencialUsuario("pesquisaClienteFornecedor") or getCredencialUsuario("pesquisaUsuario")) {
				
					$valida = false;
					$instrucao = "select data_nascimento from cliente_fornecedor where data_nascimento<>'0000-00-00'";
					$sql = query($instrucao);
					for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
						extract(mysqli_fetch_assoc($sql));
						$checkData = explode("-", $data_nascimento);
						if ($checkData[1] == date('m') and $checkData[2] >= date('d') and getCredencialUsuario("pesquisaClienteFornecedor")) {
							$valida = true;
						}
					}
					$instrucao = "select data_nascimento from usuario where data_nascimento<>'0000-00-00'";
					$sql = query($instrucao);
					for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
						extract(mysqli_fetch_assoc($sql));
						$checkData = explode("-", $data_nascimento);
						if ($checkData[1] == date('m') and $checkData[2] >= date('d') and getCredencialUsuario("pesquisaUsuario")) {
							$valida = true;
						}
					}
				
					if ($valida) {
						showTab($credencial, "Aniversariantes");
					}
				}
				
			echo "</ul>";
		echo "</nav>";
		echo "<div class='content' style='background-color:white;'>";
			
			function showSection($credencial, $html){
				if($html){
					echo "<section id='$credencial'>";
						echo "<div class='mediabox'>";
							echo "<img src='img/icones/$credencial.png'>";
							//echo "<h1>Desculpe pelo transtorno.</h1>";
							//echo "<p>Esta ferramenta está em reforma, em breve retornará ao normal.</p>";
							echo $html;
						echo "</div>";
					echo "</section>";
				}
			}
			function showHtml($ferramenta){
				global $html, $tempo, $credencial;

				$html .= "<script type='text/javascript'>";
					$html .= "$(document).ready(function() {";
						$html .= "$('#".$ferramenta."').html('<iframe src=\"iframe/" . $credencial . ".inc.php?op=".$ferramenta."&tempo=" . $tempo * 10 . "\" style=\"resize: none; overflow: hidden; height:540px;\" class=\"iframeIndex\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\"></iframe>')";
					$html .= "})";
				$html .= "</script>";
				$html .= "<div id='".$ferramenta."'></div>";

				return $html;


			}
			$endereco = "http://delphos.rocketsolution.com.br/plugins/QuotesRotator/index.php";
			//$endereco = "plugins/QuotesRotator/index.php";
			//$html = "<iframe src='//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacerocketsolution&amp;width=300&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=80&amp;appId=377867562315970' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:300px; height:80px;' allowTransparency='true'></iframe>";
			
			$html = rand(1,2);
			if($html==1){
				$html = '<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.3&appId=378139222396146";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));</script>';
				$html.= '<div class="fb-page" data-href="https://www.facebook.com/facerocketsolution" data-width="600" data-hide-cover="false" data-show-facepile="true" data-show-posts="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/facerocketsolution"><a href="https://www.facebook.com/facerocketsolution">Rocket Solution</a></blockquote></div></div>';
			}else{
				$html.= "<iframe width='500' height='458' src='http://iconosquare.com/widget.php?choice=myfeed&username=rocketsolution&show_infos=true&linking=instagram&width=500&height=458&mode=grid&layout_x=3&layout_y=2&padding=2&photo_border=true&background=FFFFFF&text=777777&widget_border=true&radius=5&border-color=DDDDDD&user_id=2059685499&time=1432862865557' allowTransparency='true' frameborder='0' scrolling='no' style='border:none; overflow:hidden; width:500px; height:458px;'></iframe>";
			}
			
			//$html.= "<br><br><iframe id='slide_prop' src='$endereco' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
			//$html.= "<br><br><iframe id='slide_note' src='http://g1.globo.com/?iframe=true&width=70%&height=95%' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
			//$html .= "<script src='http://g1.globo.com/Portal/G1V2/js/addNewsVertical.js' type='text/javascript'></script>";
			//$html .= "<script src='http://g1.globo.com/Portal/G1V2/js/addNewsHorizontal.js' type='text/javascript'></script>";
			
			showSection("oferta", $html);
			
			$credencial = "pesquisaOrcamento";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$instrucao1 = "select * from orcamento where data_emissao>='" . date('Y-m-d H:i:s', strtotime("- 1 month")) . "' and data_emissao<='$data2' and status='1'";
				$sql1 = query($instrucao1);
				$instrucao2 = "select * from orcamento where data_emissao>='$data1' and data_emissao<='$data2' and (status='1' or status='2')";
				$sql2 = query($instrucao2);
			
			
				if (mysqli_num_rows($sql1) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=orcamentoAnterior&tempo=" . $tempo * 5 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe><br>";
					$html .= showHtml('orcamentoAnterior');
				}
			
				if (mysqli_num_rows($sql2) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=orcamentoAtual&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('orcamentoAtual');
				}
				showSection($credencial, $html);
			}
			
			
			$credencial = "pesquisaPDV";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$instrucao = "select valor, id, referido from conta where tabela_referido='pdv' and status<>'4'";
				$sql = query($instrucao);
				for ($i = $cod = 0; $i < mysqli_num_rows($sql); $i++) {
					extract(mysqli_fetch_assoc($sql));
					$instrucao = "select sum(valor) as valorParcial from conta_itens where id_conta='$id'";
					$sqlParcial = query($instrucao);
					extract(mysqli_fetch_assoc($sqlParcial));
					$valorParcial = round($valorParcial, 2);
					if ($valorParcial < $valor) {
						$cod++;
					}
				}
				if ($cod > 0) {
					$html = "<iframe src='iframe/" . $credencial . ".inc.php?op=pdv&tempo=" . $tempo * 5 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html = showHtml('pdv');
				}
				
				showSection($credencial, $html);
			}

			$credencial = "pesquisaOrdemServico";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$instrucao1 = "select * from ordem_servico where data_venda>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
				$instrucao1 .= " and data_venda<='" . date('Y-m-d H:i:s') . "' and data_concluida is null";
				$sql1 = query($instrucao1);
				$instrucao2 = "select * from ordem_servico where data_concluida>='" . date('Y-m-d H:i:s', strtotime("- 1 day")) . "'";
				$instrucao2 .= " and data_concluida<='" . date('Y-m-d H:i:s') . "'";
				$sql2 = query($instrucao2);
				$instrucao3 = "select * from ordem_servico where data_previsao>='" . date('Y-m-d') . " 00:00:00'";
				$instrucao3 .= " and data_previsao<='" . date('Y-m-d') . " 23:59:59' and data_concluida is null";
				$sql3 = query($instrucao3);
			
			
				if (mysqli_num_rows($sql1) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=dia&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('dia');
				}
			
				if (mysqli_num_rows($sql2) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=concluida&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('concluida');
				}
			
				if (mysqli_num_rows($sql3) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=hoje&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('hoje');
				}
				
				showSection($credencial, $html);
				
			}
			
			$credencial = "pesquisaMatricula";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$instrucao1 = "select * from matricula where status='1'";
				$sql1 = query($instrucao1);
				$instrucao2 = "select * from matricula where status='0' and id = any (select id_matricula from matricula_plano_assinatura where data_termino>='" . date('Y-m-d', strtotime("- 1 month")) . "')";
				$sql2 = query($instrucao2);				
				
				if (mysqli_num_rows($sql1) > 0) {
					$html .= "<span><iframe src='iframe/" . $credencial . ".inc.php?op=ativos&tempo=" . $tempo * 5 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe></span>";
					
					for($i = 0; $i<mysqli_num_rows($sql1); $i++){
					
						extract(mysqli_fetch_assoc($sql1));
						
						$sqlAnamnese = query("select * from matricula_anamnese where id_matricula='$id'");
						$sqlAvaliacao = query("select * from matricula_avaliacao where id_matricula='$id'");
						$sqlExercicio = query("select * from matricula_exercicio where id_matricula='$id'");

						$linhaAnamnese = mysqli_num_rows($sqlAnamnese);
						$linhaAvaliacao = mysqli_num_rows($sqlAvaliacao);
						$linhaExercicio = mysqli_num_rows($sqlExercicio);
					}
				}else{
					$linhaAnamnese = $linhaAvaliacao = $linhaExercicio = 0;
				}
				if (mysqli_num_rows($sql2) > 0) {
					//$html .= "<span><iframe src='iframe/" . $credencial . ".inc.php?op=inativos&tempo=" . $tempo * 5 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe></span>";
					$html .= showHtml('inativos');
				}
				if ($linhaAnamnese==0 or $linhaAvaliacao==0 or $linhaExercicio==0) {
					//$html .= "<span><iframe src='iframe/" . $credencial . ".inc.php?op=fichas&tempo=" . $tempo * 5 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe></span>";
					$html .= showHtml('fichas');
				}
				
				showSection($credencial, $html);
				
			}
			
			$credencial = "pesquisaConta";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$mais15Dias = date('Y-m-d', strtotime("+ 15 days"));
				$instrucao1 = "select * from conta_itens where id_caixa_movimento is null and data_vencimento<='$mais15Dias' ";
				$instrucao1 .= "and id_conta = any (select id from conta where status='3')";
				$sql1 = query($instrucao1);
				$instrucao2 = "select * from conta_itens where id_caixa_movimento is null and data_vencimento<='$mais15Dias' ";
				$instrucao2 .= "and id_conta = any (select id from conta where status='2')";
				$sql2 = query($instrucao2);
				//$instrucao3 = "select * from conta where id = any(select id_conta from conta_itens where id_caixa_movimento is null) and (status='2' or status='3') and valor>0 order by status";
				//$sql3 = query($instrucao3);
				
				if (mysqli_num_rows($sql1) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=pagar&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('pagar');
				}
			
				if (mysqli_num_rows($sql2) > 0) {
					//$html  .= "<iframe src='iframe/" . $credencial . ".inc.php?op=receber&tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('receber');
				}
			
				//if (mysqli_num_rows($sql3) > 0) {
				if (mysqli_num_rows($sql1) > 0 or mysqli_num_rows($sql2) > 0) {
					//$html .= "<iframe src='iframe/" . $credencial . ".inc.php?op=grafico&tempo=" . $tempo . "' style='resize: none; overflow: hidden; height:540px;' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('grafico');	
				}
			
				showSection($credencial, $html);
			
			}
			
			$credencial = "pesquisaProduto";
			$html = null;
			if (getCredencialUsuario($credencial)) {
			
				$instrucao = "select * from produto where qtd_estoque<=qtd_minima and qtd_minima<>'0'";
				$sql = query($instrucao);
				if (mysqli_num_rows($sql) > 0) {
					//$html = "<iframe src='iframe/" . $credencial . ".inc.php?tempo=" . $tempo . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('produto');
				}
				showSection($credencial, $html);
			}
			
			$credencial = "pesquisaUsuario";
			$html = null;
			if (getCredencialUsuario("pesquisaClienteFornecedor") or getCredencialUsuario("pesquisaUsuario")) {
			
				$valida = false;
				$instrucao = "select nome, data_nascimento from cliente_fornecedor where data_nascimento<>'0000-00-00'";
				$sql = query($instrucao);
				for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
					extract(mysqli_fetch_assoc($sql));
					$checkData = explode("-", $data_nascimento);
					if ($checkData[1] == date('m') and $checkData[2] >= date('d') and getCredencialUsuario("pesquisaClienteFornecedor")) {
						$valida = true;
						
					}
				}
				$instrucao = "select data_nascimento from usuario where data_nascimento<>'0000-00-00'";
				$sql = query($instrucao);
				for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
					extract(mysqli_fetch_assoc($sql));
					$checkData = explode("-", $data_nascimento);
					if ($checkData[1] == date('m') and $checkData[2] >= date('d') and getCredencialUsuario("pesquisaUsuario")) {
						$valida = true;
						
					}
				}
				
				if ($valida) {
					//$html = "<iframe src='iframe/aniversariantes.inc.php?tempo=" . $tempo * 20 . "' class='iframeIndex' frameborder='0' marginheight='0' marginwidth='0'></iframe>";
					$html .= showHtml('aniversariantes');
				}
				
				showSection($credencial, $html);
			
			}
			
		echo "</div>";
	echo "</div>";

	echo "<script type='text/javascript' src='plugins/FullWidthTabs/js/cbpFWTabs.js'></script> <!-- tab responsivo (existe a outra parte no css)-->";
	echo "<script>new CBPFWTabs(document.getElementById('tabs'));</script>";
	?>


	


	<?php
include 'templates/downLogin.inc.php';
?>