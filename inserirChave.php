<?php
if (isset($_POST["chave"]) or isset($_GET["op"])) {
	include "templates/upChave.inc.php";
} else {
	
	echo "<script type='text/javascript' src='js/jquery.js'></script>
        <script type='text/javascript' src='js/jquery.dimensions.js'></script>
        <script type='text/javascript' src='js/chili-1.7.pack.js'></script>";
}
?>

<script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
<body id='bodyLogin' style='padding-top:1em; text-align: center;'>
	<?php
	
	$sql = query("select nome as nomeEmpresa from empresa where usarTimbrado='1'");
	if(mysqli_num_rows($sql)>0){
		extract(mysqli_fetch_assoc($sql));
	}



	
	require_once "plugins/uolpagseguro_2.2.2/PagSeguroLibrary/PagSeguroLibrary.php";
	
	
	function formularioChave($msg) {

		global $nomeEmpresa;
		$credentials = new PagSeguroAccountCredentials(EMAIL, TOKEN);


		//basico R$ 40,00
		$pag = new PagSeguroPaymentRequest();
		$pag->setCurrency("BRL");
		$pag->addItem('01', "Delphos - Modulo Básico ($nomeEmpresa)", 1, 40.00);
		$pag->setShippingType(3);
		$pag->setRedirectUrl("http://www.rocketsolution.com.br");
        $code1 = $pag->register($credentials, true);
        //ordemServico R$ 80,00
        $pag = new PagSeguroPaymentRequest();
		$pag->setCurrency("BRL");
		$pag->addItem('01', "Delphos - Modulo Ordem de Serviço ($nomeEmpresa)", 1, 80.00);
		$pag->setShippingType(3);
		$pag->setRedirectUrl("http://www.rocketsolution.com.br");
		$code2 = $pag->register($credentials, true);
		//academia R$ 90,00
		$pag = new PagSeguroPaymentRequest();
		$pag->setCurrency("BRL");
		$pag->addItem('01', "Delphos - Modulo Academia ($nomeEmpresa)", 1, 90.00);
		$pag->setShippingType(3);
		$pag->setRedirectUrl("http://www.rocketsolution.com.br");
		$code3 = $pag->register($credentials, true);
		//imoveis R$ 50,00
		$pag = new PagSeguroPaymentRequest();
		$pag->setCurrency("BRL");
		$pag->addItem('01', "Delphos - Modulo Imóveis ($nomeEmpresa)", 1, 50.00);
		$pag->setShippingType(3);
		$pag->setRedirectUrl("http://www.rocketsolution.com.br");
		$code4 = $pag->register($credentials, true);
        echo "<script type='text/javascript'>
        	function lightbox(){

				var opcao = $(\"select[name='tipoModulo']\").val();
				
				if(opcao==\"basico30\"){
					var code = \"$code1\";
				}
				if(opcao==\"ordemServico30\"){
					var code = \"$code2\";
				}
				if(opcao==\"academia30\"){
					var code = \"$code3\";
				}
				if(opcao==\"imoveis30\"){
					var code = \"$code4\";
				}
				
				PagSeguroLightbox(code);
			}</script>";

		$script = '
		<form method="post" action="inserirChave.php" enctype="multipart/form-data" class="form" style="display: inline-flex;">
			<div class="column" style="width:100%">
			<img src="img/rocket_software.png" width="200">
			<h1>Desculpe pelo transtorno!</h1>
            '.$msg.'<br>
            Para continuar utilizando o <b style="font-size:17px;">Sistema Delphos</b>, por favor insira uma nova chave:
            	<input type="text" name="chave" style="text-transform: none;" placeholder="Chave do produto">
	            <div class="submit-wrap"><input type="submit" class="submit" value="Inserir Chave"></div>
			<br><br>Caso não possua chave, adquira uma:<br>';
		$script .= '<select name="tipoModulo" onchange="mudaForm(this.value)">';
		$sql = query("select valor from registro where id=(select max(id) from registro)");
		if (mysqli_num_rows($sql) > 0) {
			extract(mysqli_fetch_assoc($sql));
			$chave = new chave;
			$chave -> valor = $valor;
			$chave -> decodificar_atribuir();
			if ($chave -> modulo == "basico") {
				$script .= "<option value='basico30' selected='yes'>Básico 30 dias</option>";
				$script .= "<option value='ordemServico30'>Ordem de Serviço 30 dias</option>";
				$script .= "<option value='academia30'>Academia 30 dias</option>";
				$script .= "<option value='imoveis30'>Imoveis 30 dias</option>";
			} elseif ($chave -> modulo == "ordemServico") {
				$script .= "<option value='basico30'>Básico 30 dias</option>";
				$script .= "<option value='ordemServico30' selected='yes'>Ordem de Serviço 30 dias</option>";
				$script .= "<option value='academia30'>Academia 30 dias</option>";
				$script .= "<option value='imoveis30'>Imoveis 30 dias</option>";
			} elseif ($chave -> modulo == "academia"){
				$script .= "<option value='basico30'>Básico 30 dias</option>";
				$script .= "<option value='ordemServico30'>Ordem de Serviço 30 dias</option>";
				$script .= "<option value='academia30' selected='yes'>Academia 30 dias</option>";
				$script .= "<option value='imoveis30'>Imoveis 30 dias</option>";
			} else {
				$script .= "<option value='basico30'>Básico 30 dias</option>";
				$script .= "<option value='ordemServico30'>Ordem de Serviço 30 dias</option>";
				$script .= "<option value='academia30'>Academia 30 dias</option>";
				$script .= "<option value='imoveis30' selected='yes'>Imoveis 30 dias</option>";
			}
		} else {
			$script .= "<option value='basico30'>Básico 30 dias</option>";
			$script .= "<option value='ordemServico30'>Ordem de Serviço 30 dias</option>";
			$script .= "<option value='academia30'>Academia 30 dias</option>";
			$script .= "<option value='imoveis30'>Imoveis 30 dias</option>";
		}
		$script .= "</select><br>";

		$script .= "<a class='submit' href='#' onclick='lightbox();'>Obter uma chave!</a>";
		
		$script .= "</div></form>";		
		
		return $script;
	}

	

	extract($_POST);
	extract($_GET);
	if(isset($_POST["chave"])){
		$CHAVE = $_POST['chave'];
	}
	if(isset($_GET["chave"])){
		$CHAVE = $_GET['chave'];
	}
	
	if (!isset($CHAVE)) {
		
		if (!isset($_GET["op"])) {
			$msg = "Mas identificamos que a licença do seu software<br>está fora do prazo de validade.";
		} else {
			$msg = "";
		}

		echo formularioChave($msg);

		
	} else {
		$licenca = new chave;
		$licenca -> valor = $chave;
		
		if ($licenca -> verificar_valor_valido()) {

			$licenca -> decodificar_atribuir();
			$sql = query("insert into registro (valor) value ('" . $licenca -> valor . "')");

			/*
			 *
			 *
			 * ################	 DELETANDO DO BANCO DE DADOS TODAS AS FERRAMENTAS PARA DEPOIS INSERI-LAS #############
			 *
			 *
			 */

			$sql = query("delete from ferramentas");

			/*
			 *
			 *
			 * ################ REGULARIZANDO A TABELA FERRAMENTA (MENU) #################
			 *
			 *
			 *
			 * /

			 //deletando as ferramentas anteriores
			 $sql = query("delete from ferramentas");

			 
			 */
			$contFerr = 1;
			$cod = "INSERT INTO `ferramentas` (`id`, `ferramenta`, `nome`) VALUES";
			if($licenca -> modulo != "imoveis"){
				
				$cod.= "(".($contFerr++).", 'cadastrarPDV.php', 'PDV'),
				(".($contFerr++).", 'cadastrarOrcamento.php', 'Novo Orçamento'),
				(".($contFerr++).", 'cadastrarProduto.php', 'Produto'),
				(".($contFerr++).", 'cadastrarTrocaPonto.php', 'Trocar produtos por pontos'),
				(".($contFerr++).", 'cadastrarMProduto.php', 'Movimentar Produto'),
				(".($contFerr++).", 'pesquisaOrcamento.php', 'Buscar Orçamento'),
				(".($contFerr++).", 'pesquisaPDV.php', 'Comandas'),
				(".($contFerr++).", 'pesquisaTrocaPonto.php', 'Buscar Trocas de pontos'),
				(".($contFerr++).", 'pesquisaProduto.php', 'Pesquisar produtos'),
				(".($contFerr++).", 'pesquisaMProduto.php', 'Movimentação de Produto'),
				(".($contFerr++).", 'relatorioHistoricoVendas.php', 'Histórico de vendas'),
				(".($contFerr++).", 'relatorioEstoque.php', 'Estoque'),
				(".($contFerr++).", 'administrativoEtiqueta.php', 'Gerar etiqueta produto'),";
			}
			$cod.= "(".($contFerr++).", 'cadastrarEmpresa.php', 'Empresa'),
			(".($contFerr++).", 'cadastrarClienteFornecedor.php', 'Cliente / Fornecedor'),
			(".($contFerr++).", 'cadastrarUsuario.php', 'Usuário'),
			(".($contFerr++).", 'cadastrarConta.php', 'Nova conta (a pagar / a receber)'),
			(".($contFerr++).", 'cadastrarCaixa.php', 'Entrada / Saída do Caixa'),
			(".($contFerr++).", 'pesquisaEmpresa.php', 'Visualizar empresas'),
			(".($contFerr++).", 'pesquisaClienteFornecedor.php', 'Pesquisar Cliente / Fornecedor'),
			(".($contFerr++).", 'pesquisaUsuario.php', 'Pesquisar usuários'),
			(".($contFerr++).", 'pesquisaConta.php', 'Pesquisar conta'),
			(".($contFerr++).", 'pesquisaCaixa.php', 'Registro de movimentação do caixa'),
			(".($contFerr++).", 'relatorioMovimentoCaixa.php', 'Movimento do caixa'),
			(".($contFerr++).", 'relatorioBalancoConta.php', 'Balanço de Contas'),
			(".($contFerr++).", 'administrativoToken.php', 'Gerar novo Token'),
			(".($contFerr++).", 'administrativoPagamento.php', 'Condições de pagamento'),
			(".($contFerr++).", 'administrativoSuporte.php', 'Solicitar Suporte')";
			/*,
			(28, 'rhFolhaPagamento.php', 'Folha de Pagamento'),
			(29, 'rhFrequenciaUsuario.php', 'Frequência do Colaborador'),
			(30, 'rhProcedimentos.php', 'Procedimentos'),
			(31, 'rhFormularios.php', 'Formulários')";
			*/
			

			if ($licenca -> modulo == "ordemServico") {

				$cod .= ", (".($contFerr++).", 'cadastrarServico.php', 'Serviço'),
				(".($contFerr++).", 'cadastrarOrdemServico.php', 'Nova Ordem de Serviço'),
				(".($contFerr++).", 'cadastrarOrdemCompra.php', 'Ordem de Compra'),
				(".($contFerr++).", 'pesquisaServico.php', 'Pesquisar serviço'),
				(".($contFerr++).", 'pesquisaOrdemServico.php', 'Pesquisar Ordem de Serviço'),
				(".($contFerr++).", 'pesquisaOrdemCompra.php', 'Mostrar Ordem de Compra'),
				(".($contFerr++).", 'relatorioMapaProducao.php', 'Mapa de produção'),
				(".($contFerr++).", 'relatorioProducaoUsuario.php', 'Produção por usuário'),
				(".($contFerr++).", 'relatorioProducaoEmpresa.php', 'Produção por empresa'),
				(".($contFerr++).", 'administrativoServicoStatus.php', 'Status do Serviço'),
				(".($contFerr++).", 'administrativoOrdemServicoAtributoPadrao.php', 'Padronizar Ordem de Serviço');";

			}
			
			if($licenca -> modulo == "academia"){
				$cod .= ", (".($contFerr++).", 'cadastrarMatricula.php', 'Nova Matrícula'),
				(".($contFerr++).", 'cadastrarPlanoAssinatura.php', 'Plano / Assinatura'),
				(".($contFerr++).", 'cadastrarExercicio.php', 'Exercício'),
				(".($contFerr++).", 'pesquisaMatricula.php', 'Buscar Matrículas'),
				(".($contFerr++).", 'pesquisaPlanoAssinatura.php', 'Pesquisar Plano / Assinatura'),
				(".($contFerr++).", 'pesquisaExercicio.php', 'Pesquisar exercicios'),
				(".($contFerr++).", 'relatorioFrequenciaCliente.php', 'Frequencia do aluno / cliente'),
				(".($contFerr++).", 'administrativoAnamneseAtributoPadrao.php', 'Perguntas da anamnese');";
			}

			if($licenca -> modulo == "imoveis"){
				$cod .= ", (".($contFerr++).", 'cadastrarImovel.php', 'Cadastrar imóvel'),
				(".($contFerr++).", 'cadastrarVendaAluga.php', 'Vender ou alugar imóvel'),
				(".($contFerr++).", 'pesquisaImovel.php', 'Pesquisar imóvel'),
				(".($contFerr++).", 'pesquisaVendaAluga.php', 'Pesquisar vendas e alugueis');";
			}

			

			$sql = query($cod);

			/*
			 *
			 *
			 *
			 * ####################### REGULARIZANDO A TABELA CREDENCIAIS (ACESSO DOS USUÁRIOS) #################
			 *
			 *
			 *
			 */

			//verificando se é a primeira vez que está acessando o sistema
			//ou se ja utiliza o sistema mas está inseirindo mais um prazo da chave
			//30 é o numero de ferramentas que o plano basico oferece
			for ($i = 1; $i < 3; $i++) {
				$sql = query("select * from usuario where id='$i'");
				if (mysqli_num_rows($sql) > 0) {
					$sql = query("select * from credenciais where id_usuario='$i'");
					if (mysqli_num_rows($sql) < 30) {
						$cod = "insert into credenciais (id_usuario, ferramenta) values";
						if($licenca->modulo!='imoveis'){
							$cod.= "
							($i, 'cadastrarPDV.php'),
							($i, 'cadastrarOrcamento.php'),
							($i, 'cadastrarProduto.php'),
							($i, 'cadastrarMProduto.php'),
							($i, 'pesquisaPDV.php'),
							($i, 'pesquisaOrcamento.php'),
							($i, 'pesquisaProduto.php'),
							($i, 'pesquisaMProduto.php'),
							($i, 'relatorioHistoricoVendas.php'),
							($i, 'relatorioEstoque.php'),
							";
						}
						$cod.= "
						($i, 'cadastrarEmpresa.php'),
						($i, 'cadastrarClienteFornecedor.php'),
						($i, 'cadastrarUsuario.php'),
						($i, 'cadastrarConta.php'),
						($i, 'cadastrarCaixa.php'),
						($i, 'pesquisaEmpresa.php'),
						($i, 'pesquisaClienteFornecedor.php'),
						($i, 'pesquisaUsuario.php'),
						($i, 'pesquisaConta.php'),
						($i, 'pesquisaCaixa.php'),
						($i, 'relatorioMovimentoCaixa.php'),
						($i, 'relatorioBalancoConta.php'),
						($i, 'administrativoToken.php'),
						($i, 'administrativoPagamento.php'), 
						($i, 'administrativoSuporte.php')";
						$sql = query($cod);
					}
				}
			}

			//regularizando as credenciais caso existe um downgrade de modulo
			if ($licenca -> modulo <> "ordemServico") {
				$cod = "delete from credenciais where ";
				$cod .= "ferramenta = 'cadastrarServico.php' or ";
				$cod .= "ferramenta = 'cadastrarOrdemServico.php' or ";
				$cod .= "ferramenta = 'cadastrarOrdemCompra.php' or ";
				$cod .= "ferramenta = 'pesquisaServico.php' or ";
				$cod .= "ferramenta = 'pesquisaOrdemServico.php' or ";
				$cod .= "ferramenta = 'pesquisaOrdemCompra.php' or ";
				$cod .= "ferramenta = 'relatorioMapaProducao.php' or ";
				$cod .= "ferramenta = 'relatorioProducaoUsuario.php' or ";
				$cod .= "ferramenta = 'relatorioProducaoEmpresa.php' or ";
				$cod .= "ferramenta = 'administrativoServicoStatus.php' or ";
				$cod .= "ferramenta = 'administrativoOrdemServicoAtributoPadrao.php'";
				$sql = query($cod);
			}
			if ($licenca -> modulo <> "academia") {
				$cod = "delete from credenciais where ";
				$cod .= "ferramenta = 'cadastrarMatricula.php' or ";
				$cod .= "ferramenta = 'cadastrarPlanoAssinatura.php' or ";
				$cod .= "ferramenta = 'cadastrarExercicio.php' or ";
				$cod .= "ferramenta = 'pesquisaMatricula.php' or ";
				$cod .= "ferramenta = 'pesquisaPlanoAssinatura.php' or ";
				$cod .= "ferramenta = 'pesquisaExercicio.php' or ";
				$cod .= "ferramenta = 'relatorioFrequenciaCliente.php' or ";
				$cod .= "ferramenta = 'administrativoAnamneseAtributoPadrao.php'";
				$sql = query($cod);
			}

			if ($licenca -> modulo <> "academia") {
				$cod = "delete from credenciais where ";
				$cod .= "ferramenta = 'cadastrarImovel.php' or ";
				$cod .= "ferramenta = 'cadastrarVendaAluga.php' or ";
				$cod .= "ferramenta = 'pesquisaImovel.php' or ";
				$cod .= "ferramenta = 'pesquisaVendaAluga.php'";
				$sql = query($cod);
			}


			//regularizando as credenciais caso existe um upgrade de modulo
			if ($licenca -> modulo <> "basico") {
				//verificando se é a primeira vez que está acessando o sistema
				//ou se ja utiliza o sistema mas está inseirindo mais um prazo da chave
				//30 é o numero de ferramentas que o plano basico oferece
				for ($i = 1; $i < 3; $i++) {//menor que 3, pois o Super Usuario e o administrador do sistema devem ser graciados com as novas ferramentas
					$sql = query("select * from usuario where id='$i'");
					if (mysqli_num_rows($sql) > 0) {
						if ($licenca -> modulo == "ordemServico") {
							$cod = "insert into credenciais (id_usuario, ferramenta) values";
							$cod .= "($i, 'cadastrarServico.php'), ";
							$cod .= "($i, 'cadastrarOrdemServico.php'), ";
							$cod .= "($i, 'cadastrarOrdemCompra.php'), ";
							$cod .= "($i, 'pesquisaServico.php'), ";
							$cod .= "($i, 'pesquisaOrdemServico.php'), ";
							$cod .= "($i, 'pesquisaOrdemCompra.php'), ";
							$cod .= "($i, 'relatorioMapaProducao.php'), ";
							$cod .= "($i, 'relatorioProducaoUsuario.php'), ";
							$cod .= "($i, 'relatorioProducaoEmpresa.php'), ";
							$cod .= "($i, 'administrativoServicoStatus.php'),";
							$cod .= "($i, 'administrativoOrdemServicoAtributoPadrao.php')";
						}elseif($licenca -> modulo == "academia"){
							$cod = "insert into credenciais (id_usuario, ferramenta) values";
							$cod .= "($i, 'cadastrarMatricula.php'), ";
							$cod .= "($i, 'cadastrarPlanoAssinatura.php'), ";
							$cod .= "($i, 'cadastrarExercicio.php'), ";
							$cod .= "($i, 'pesquisaMatricula.php'), ";
							$cod .= "($i, 'pesquisaPlanoAssinatura.php'), ";
							$cod .= "($i, 'pesquisaExercicio.php'), ";
							$cod .= "($i, 'relatorioFrequenciaCliente.php'), ";
							$cod .= "($i, 'administrativoAnamneseAtributoPadrao.php')";
						}elseif($licenca -> modulo == "imoveis"){
							$cod = "insert into credenciais (id_usuario, ferramenta) values";
							$cod .= "($i, 'cadastrarImovel.php'), ";
							$cod .= "($i, 'cadastrarVendaAluga.php'), ";
							$cod .= "($i, 'pesquisaImovel.php'), ";
							$cod .= "($i, 'pesquisaVendaAluga.php')";
						}
						$sql = query($cod);
					}
				}
			}
			echo "<div class='column' style='position:absolute; top: 150px; width:100%; align-content:center;'>
                <table style='display: inline-flex;'>
                    <tr>
                        <td align='center'><img src='img/rocket_software.png' width='200'></td>
                        <td align='center'>";
			echo "Sua nova chave foi inserida com sucesso!";
			echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=indexUsuario.php'>";
			echo "
                            </td>
                        </tr>
                    </table>
                </div>";
			
			if(isset($_COOKIE["login"])){
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Inseriu uma nova chave para mais ".$licenca->contar_dias()." dias.";
				$tabela_afetada = "registro";
				$chave_principal = ultimaId("registro");
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);	
			}
			

		} else {

			echo formularioChave("Mas a chave que inseriu não é válida.");
		}

	}
	?>
</body>
</html>