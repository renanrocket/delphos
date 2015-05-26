<?php
include_once "../templates/upLoginImp.inc.php";
?>
<script type='text/javascript'>
	function pop(pagina){
		window.opener.location.href = pagina; //faz o refresh na página pai
   		window.close(); //fecha o pop-up
	}	
</script>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<?php
	echo "
		<script type='text/javascript' src='../js/funcoes.js'></script>
		<script type='text/javascript' src='../js/mascara.js'></script>
        <script type='text/javascript' src='../js/jquery.js'></script>
		";

	
	extract($_GET);
	if(!isset($pagina)){
		$pagina = 1;
	}else{
		$pagina = base64_decode($pagina);
	}
	
	$limite = 20;
	
	$inicio = $pagina * $limite - $limite;
	
	$sql = query("select * from historico order by id desc limit $inicio, $limite");
	echo "<table>";
	echo "<tr>";
		echo "<td>Histórico</td>";
		echo "<td>Data</td>";
		echo "<td>Usuário</td>";
		echo "<td>Ação</td>";
		echo "<td>Visualizar</td>";
	echo "</tr>";
		for($i=0; $i<mysqli_num_rows($sql); $i++){
			echo "<tr>";
			extract(mysqli_fetch_assoc($sql));
			$sqlNome = query("select nome from usuario where id='$id_usuario'");
			if(mysqli_num_rows($sqlNome)>0){
				extract(mysqli_fetch_assoc($sqlNome));
				$nome = explode(" ", $nome);
				$cont = count($nome);
				$nome = $nome[0]." ".$nome[$cont-1];
			}else{
				$nome = "";
			}
			echo "<td>$id</td>";
			echo "<td>".formataData($data)."</td>";
			echo "<td>$nome</td>";
			echo "<td><b>$acao</b></td>";
			echo "<td>";
			switch ($tabela_afetada) {
				case 'produto':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarProduto.php?op=visualizar&id=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaProduto.png'></a>";
					break;
				case 'orcamento':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarOrcamento.php?op=visualizar&id=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaOrcamento.png'></a>";
					break;
				case 'ordem_servico':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarOrdemServico.php?op=visualizar&id=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaOrdemServico.png'></a>";
					break;
				case 'cliente_fornecedor':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarClienteFornecedor.php?op=visualizar&id_cliente_fornecedor=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaClienteFornecedor.png'></a>";
					break;
				case 'conta':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../pesquisaConta2.php?conta=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaConta.png'></a>";
					break;
				case 'conta_itens':
					$sqlContaItens= query("select id_conta as chave_principal from $tabela_afetada where id='$chave_principal'");
					if(mysqli_num_rows($sqlContaItens)){
						extract(mysqli_fetch_assoc($sqlContaItens));
						echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../pesquisaConta2.php?conta=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaConta.png'></a>";
					}
					break;
				case 'matricula':
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarMatricula.php?op=visualizar&id=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaMatricula.png'></a>";
					break;
				case 'matricula_plano_assinatura':
					$sqlMatricula= query("select id_matricula as chave_principal from $tabela_afetada where id='$chave_principal'");
					extract(mysqli_fetch_assoc($sqlMatricula));
					echo "<a style='line-height: 30px; background:none;' href='#' onclick='pop(\"../cadastrarMatricula.php?op=visualizar&id=".base64_encode($chave_principal)."\");'><img height='30' src='../img/icones/pesquisaMatricula.png'></a>";
					break;
			}			
			echo "</td>";
			echo "</tr>";
		}
	echo "</table>";
	
	echo "<br><br><br>";
	
	$sql_select_all = "SELECT * FROM historico";
	$sql_query_all = query($sql_select_all);
	$total_registros = mysqli_num_rows($sql_query_all);
	$pags = ceil($total_registros/$limite);
	
	$max_links = 3;
	echo "<a class='aSubmit' href='historico.php?pagina=".base64_encode(1)." target='_self'>Primeira pagina</a> ";
	for($i = $pagina-$max_links; $i <= $pagina-1; $i++) {
		if($i >0) {
			 echo "<a class='aSubmit' href='historico.php?pagina=".base64_encode($i)."'>".$i."</a> "; 
		} 
	}
	if($pagina!=1){
		echo $pagina." ";
	}else{
		echo "1 ";
	}
	for($i = $pagina+1; $i <= $pagina+$max_links; $i++) {
		if($i <= $pags) {
			echo "<a class='aSubmit' href='historico.php?pagina=".base64_encode($i)."'>".$i."</a> ";
		} 
	}
	echo "<a class='aSubmit' href='historico.php?pagina=".base64_encode($pags)."'>Última pagina</a>";
	
	
	include_once "../templates/downLoginImp.inc.php";
?>	
