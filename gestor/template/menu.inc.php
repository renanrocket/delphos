<style type="text/css">
	@import url(css/menu.css);
</style>
<ul id="cbp-tm-menu" class="cbp-tm-menu">
	<li>
		<a href="indexGestor.php">Inicio</a>
	</li>
	<li class="">
	<a href="#">Chaves</a>
		<ul class="cbp-tm-submenu">
			<li>
				<a style="line-height: 20px; vertical-align: middle;" href="cad_chave.php?op=novo">
					<img src="img/icones/cadastrarOrcamento.png" class="imgFerramenta" style="height:1em; display:block;margin:0px;padding:0px;">Inserir Nova Chave
				</a>
			</li>
			<li>
				<a style="line-height: 20px; vertical-align: middle;" href="cad_chave.php?op=mostrarAtivo">
					<img src="img/icones/pesquisaOrcamento.png" class="imgFerramenta" style="height:1em; display:block;margin:0px;padding:0px;">Visualizar Chaves Ativas</a>
			</li>
			<li>
				<a style="line-height: 20px; vertical-align: middle;" href="cad_chave.php?op=mostrarInativo">
					<img src="img/icones/pesquisaOrcamento.png" class="imgFerramenta" style="height:1em; display:block;margin:0px;padding:0px;">Visualizar Chaves Inativas</a>
			</li>
			<li>
				<a style="line-height: 20px; vertical-align: middle;" href="pesquisaOrcamento.php">
					<img src="img/icones/pesquisaOrcamento.png" class="imgFerramenta" style="height:1em; display:block;margin:0px;padding:0px;">Buscar uma chave</a>
			</li>
		</ul>
	</li>
<ul>
<script type='text/javascript' src='../plugins/TooltipMenu/js/cbpTooltipMenu.min.js'></script> <!-- menu responsivo (existe a outra parte no css)-->
<script>var menu = new cbpTooltipMenu(document.getElementById('cbp-tm-menu'));</script>