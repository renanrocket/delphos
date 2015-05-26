<?php

class imovel{

	public $id;
	public $nome;
	public $tipo_imovel; //terreno casa edificil galpao
	public $valor_venda;
	public $valor_aluguel;
	public $endereco;
	public $numero;
	public $bairro;
	public $cidade;
	public $estado;
	public $cep;
	public $referencia;
	public $latitude;
	public $longitude;
	public $status;
	public $proprietario;

	function __construct($id = null, $nome = null, $tipo_imovel = null, $valor_venda =null, $valor_aluguel =null,
		$endereco = null, $numero = null, $bairro = null, $cidade = null, $estado = null,
		$cep = null, $referencia = null, $latitude = null, $longitude = null,
		$status = null, $proprietario = null){

		if($id){

			$conn = TConnection::open(ALCUNHA);

			$criterio = new TCriteria;
			$criterio->add(new TFilter('id', '=', $id));

			$sql= new TSqlSelect;
			$sql->setEntity('imovel');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			if($result->rowCount()){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
			}
		}

		$this->id 						= $id;
		$this->nome 					= $nome;
		$this->tipo_imovel 				= $tipo_imovel;
		$this->valor_venda 				= $valor_venda;
		$this->valor_aluguel 			= $valor_aluguel;
		$this->endereco 				= $endereco;
		$this->numero 					= $numero;
		$this->bairro 					= $bairro;
		$this->cidade 					= $cidade;
		$this->estado 					= $estado;
		$this->cep 						= $cep;
		$this->referencia 				= $referencia;
		$this->latitude 				= $latitude;
		$this->longitude 				= $longitude;
		$this->status 					= $status;
		$this->proprietario 			= $proprietario;
	}


	function form(){

		$nome = $this->id+1;

		$conn = TConnection::open(ALCUNHA);
	
	?>

		<script>
			$(function() {
				$( "#tabs" ).tabs();
			});
		</script>
		<div id="tabs" style="width:70%">
			<ul>
				<li><a href="#tabs-1">Imóvel</a></li>
				<li><a href="#tabs-2">Avaliação</a></li>
				<li><a href="#tabs-3">Investimento</a></li>
				<li><a href="#tabs-4">Características</a></li>
			</ul>
			<form name="formulario" method="post" action="cadastrarImovel.php" enctype="multipart/form-data">
			<?php
				if($this->id){
					echo "<input type='hidden' name='op' value='editar'>";
					echo "<input type='hidden' name='id' value='".base64_decode($this->id)."'>";
				}else{
					echo "<input type='hidden' name='op' value='novo'>";
				}
			?>
				<div id="tabs-1" class="tabs">
					<table id='gradient-style' style='width:100%;margin:0px; padding:0px;'>
						<tr>
							<th colspan="4">Cadastro do imóvel</th>
						</tr>
						<tr>
							<td colspan='4'>
								Nome do Imóvel<br>
								<input name="nome" type="text" placeholder="Casa <?php echo $nome?>" value="<?php echo $this->nome; ?>">
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								Tipo de imóvel<br>
								<input type='text' name='tipo_imovel' onkeyup="buscar_tipo_imovel(this.value)" value='<?php echo $this->tipo_imovel ?>' placeholder='Terreno, casa, AP...'>
								<script type="text/javascript">
									function buscar_tipo_imovel(tipo){
										$.post("inc/ajaxTipoImovel.inc.php", {
											tipo : tipo
										}, function(data) {
											if (data.length > 0) {
												$('#suggestions').show();
												$('#autoSuggestionsList').html(data);
											}else{
												lookupOff();
											}
										});
									}
									function lookupOff(){
										$('#suggestions').hide();
									}
									function preencher(valor){
										$('input[name="tipo_imovel"]').val(valor);
										lookupOff();
									}
								</script>
								<div class='suggestionsBox' id='suggestions' style='display:none;'>
									<span style='float:right;'><input type='button' id='deletar' value='X' onclick="lookupOff();"></span>
									<div class='suggestionList' id='autoSuggestionsList'>
										<ul>
											<li>
												<a href='#'>asd</a>
											</li>
											<li>
												<a href='#'>asd 2</a>
											</li>
										</ul>
									</div>
								</div>
							</td>
							<td>
								Valor de Venda<br>
								<input name="valor_venda" type="text" <?php echo mascara('Valor3'); ?> placeholder="Preço sugerido para venda" value="<?php echo $this->valor_venda; ?>">
							</td>
							<td>
								Valor para alugar<br>
								<input name="valor_aluguel" type="text" <?php echo mascara('Valor3'); ?> placeholder="Preço sugerido para alugar" value="<?php echo $this->valor_aluguel; ?>">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								Endereço<br>
								<input name="endereco" type="text" placeholder="Rua, Av., Travessa..." value="<?php echo $this->endereco; ?>">
							</td>
							<td>
								Número<br>
								<input name="numero" type="text" <?php echo mascara('Integer'); ?> placeholder="03" value="<?php echo $this->numero; ?>">
							</td>
							<td>
								Bairro<br>
								<input name="bairro" type="text" placeholder="Centro" value="<?php echo $this->bairro; ?>">
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<?php
									echo inputECC("estado", "cidade", "cep", $this->estado, $this->cidade, $this->cep);
								?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								Proprietário<br>
								<select name="proprietario">
									<?php
										$criterio = new TCriteria;
										$criterio->add( new TFilter('usarTimbrado', '=', '1'));
										$sql = new TSqlSelect;
										$sql->setEntity('empresa');
										$sql->addColumn('nome');
										$sql->setCriteria($criterio);
										$result = $conn->query($sql->getInstruction());
										for($i=0; $i<$result->rowCount(); $i++){
											$row = $result->fetch(PDO::FETCH_ASSOC);
											extract($row);
											if($this->proprietario==0){
												echo "<option value='0' selected='yes'>$nome</option>";
											}else{
												echo "<option value='0'>$nome</option>";
											}
										}

										$criterio = new TCriteria;
										$criterio->add( new TFilter('status', '=', '1'));
										$criterio->setProperty('order', 'nome');
										$sql = new TSqlSelect;
										$sql->setEntity('cliente_fornecedor');
										$sql->addColumn('nome');
										$sql->addColumn('fone1');
										$sql->addColumn('id');
										$sql->setCriteria($criterio);
										
										$result = $conn->query($sql->getInstruction());
										for($i=0; $i<$result->rowCount(); $i++){
											$row = $result->fetch(PDO::FETCH_ASSOC);
											extract($row);
											if($this->proprietario==$id){
												echo "<option value='$id' selected='yes'>$nome $fone1</option>";
											}else{
												echo "<option value='$id'>$nome $fone1</option>";
											}
										}
									?>
								</select>
							</td>
							<td colspan="2">
								Status<br>
								<select name="status">
									<?php

										$sql = new TSqlSelect;
										$sql->setEntity('imovel_status');
										$sql->addColumn('*');
										$result = $conn->query($sql->getInstruction());
										for($i=0; $i<$result->rowCount(); $i++){
											$row = $result->fetch(PDO::FETCH_ASSOC);
											extract($row);
											if($this->status==$id){
												echo "<option value='$id' selected='yes'>$nome</option>";
											}else{
												echo "<option value='$id'>$nome</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th colspan="2">
							</th>
							<th>
								<input class='btnEnviar' type='submit' value='Enviar'>
							</th>
							<th>
								<input type='reset' value='Cancelar'>
							</th>
						</tr>
					</table>			
				</div>
			</form>
			<form name="formulario_tabs_2" method="post" action="cadastrarImovel.php#tabs-2" enctype="multipart/form-data">

				<div id="tabs-2" class="tabs">
					<table id="gradient-style" class="tabs" style="margin:0px">
						<tr>
							<th colspan="4">Histórico de avaliação do imóvel</th>
						</tr>
						<tr>
							<th></th>
							<th>O imóvel está avaliado em</th>
							<th>Data</th>
							<th>Avaliador</th>
						</tr>
						<?php
							$criterio = new TCriteria;
							$criterio->add(new TFilter('id_imovel', '=', $this->id));
							$sql = new TSqlSelect;
							$sql->setEntity('imovel_avaliacao');
							$sql->addColumn('*');
							$sql->setCriteria($criterio);
							$result = $conn->query($sql->getInstruction());
							if($result->rowCount()){
								for($i=0; $i<$result->rowCount(); $i++){
									$row = $result->fetch(PDO::FETCH_ASSOC);
									extract($row);
									
									$info = new info;
									$info->msg = "Deseja mesmo deletar esta avaliação?<br><br>";
									$info->msg.= "<a class='aSubmit' href='cadastrarImovel.php?op=deletarAvaliacao&avaliacao=".base64_encode($id)."&id_imovel=".base64_encode($this->id)."#tabs-2'>Sim</a>";
									$info->msg.= "<a class='aSubmit' href='#' onclick='".$info->getJsApagar()."' >Não</a>";
									$info->display = 'none';
									$info->class = 'info_avaliacao_'.$i;
									echo $info->getInfo();
									echo "<tr>";
										echo "<td><a href='#' ".$info->getJs()."><img src='img/menos.png'></a></td>";
										echo "<td class='totalValor preco'>".real($valor)."</td>";
										echo "<td>".formataData($data)."</td>";
										echo "<td>".getNomeCookieLogin(registro($id_usuario, "usuario", "login"), false)."</td>";
									echo "</tr>";
									$labels[] = formataData($data);
									$Datasets[] = $valor;
									
									if($i+1==$result->rowCount()){
										$valorAvaliado = $valor;
									}
									
								}
							}else{
								?>
								<tr>
									<td colspan='4' align="center">Imóvel ainda não foi avaliado.</td>
								</tr>
								<?php
							}

							if($this->id){

						?>
						<input type='hidden' name='op' value='novaAvaliacao'>
						<input type='hidden' name='id_imovel' value='<?php echo $this->id; ?>'>
						<tr>
							<td></td>
							<td><input class="preco totalValor" type='text' name='valor' placeholder='Valor estimado' <?php echo mascara('Valor3') ?>></td>
							<td><?php echo inputData('formulario_tabs_2', 'data', null) ?></td>
							<td>
								<select name='id_usuario'>
								<?php
									
									echo opcaoSelect('usuario', 'nome', 'Ativo', getIdCookieLogin($_COOKIE["login"]), null, 'and id>1', 0, false);
								?>
								</select>
								</td>
						</tr>
						<tr>
							<td colspan='4' align='center'>
								<?php
									if(isset($labels)){
										echo grafico('Line', $labels, $Datasets, null, array(900, 200));
									}
								?>
							</td>
						</tr>
						<tr>
							<th colspan="2">
							</th>
							<th>
								<input class='btnEnviar' type='submit' value='Enviar'>
							</th>
							<th>
								<input type='reset' value='Cancelar'>
							</th>
						</tr>
						<?php } ?>
					</table>
				</div>
			</form>
			<form name="formulario_tabs_3" method="post" action="cadastrarImovel.php#tabs-3" enctype="multipart/form-data">
				<div id="tabs-3" class="tabs">
					<table id="gradient-style" class="tabs" style="margin:0px">
						<tr>
							<th colspan="5">Histórico de investimentos do imóvel</th>
						</tr>
						<tr>
							<th colspan="2">Valor</th>
							<th>Data</th>
							<th>Referente a</th>
							<th>Usuário</th>
						</tr>
						<?php
							$criterio = new TCriteria;
							$criterio->add(new TFilter('id_imovel', '=', $this->id));
							$sql = new TSqlSelect;
							$sql->setEntity('imovel_capital_investido');
							$sql->addColumn('*');
							$sql->setCriteria($criterio);
							$result = $conn->query($sql->getInstruction());
							if($result->rowCount()){	
								for($i=$valorInvestido=0;$i<$result->rowCount();$i++){
									$row = $result->fetch(PDO::FETCH_ASSOC);
									extract($row);

									$info = new info;
									$info->msg = "Deseja mesmo deletar este histórico de investimento?<br><br>";
									$info->msg.= "<a class='aSubmit' href='cadastrarImovel.php?op=deletarCapitalInvestido&capital=".base64_encode($id)."&id_imovel=".base64_encode($this->id)."#tabs-3'>Sim</a>";
									$info->msg.= "<a class='aSubmit' href='#' onclick='".$info->getJsApagar()."' >Não</a>";
									$info->display = 'none';
									$info->class = 'info_captal_investido_'.$i;
									echo $info->getInfo();
									echo "<tr>";
										echo "<td><a href='#' ".$info->getJs()."><img src='img/menos.png'></a></td>";
										echo "<td class='totalValor preco'>".real($valor)."</td>";
										echo "<td>".formataData($data)."</td>";
										echo "<td>".$referente."</td>";
										echo "<td>".getNomeCookieLogin(registro($id_usuario, "usuario", "login"), false)."</td>";
									echo "</tr>";
									$valorInvestido += $valor;

								}
							}else{
								echo "<tr>";
									echo "<td colspan='5' align='center'>Nenhum investimento registrado.</td>";
								echo "</tr>";
							}

							if($this->id){
						?>
						<input type='hidden' name='op' value='novoInvestimento'>
						<input type='hidden' name='id_imovel' value='<?php echo $this->id; ?>'>
						<tr>
							<td></td>
							<td><input class="preco totalValor" type='text' name='valor' placeholder='Valor investido' <?php echo mascara('Valor3') ?>></td>
							<td><?php echo inputData('formulario_tabs_2', 'data', null) ?></td>
							<td><input type='text' name='referente' placeholder='Ex: Pintura, reforma.'></td>
							<td>
								<select name='id_usuario'>
								<?php
									
									echo opcaoSelect('usuario', 'nome', 'Ativo', getIdCookieLogin($_COOKIE["login"]), null, 'and id>1', 0, false);
								?>
								</select>
								</td>
						</tr>
						<tr>
							<td colspan='5' align='center'>
								<?php
									if(isset($valorInvestido) & isset($valorAvaliado)){
										
										echo grafico("Radar", array("Valor de venda", "Valor avaliado", "Valor investido"), array($this->valor_venda, $valorAvaliado, $valorInvestido), null, array(900,500));

									}
								?>
							</td>
						</tr>
						<tr>
							<th colspan="3">
							</th>
							<th>
								<input class='btnEnviar' type='submit' value='Enviar'>
							</th>
							<th>
								<input type='reset' value='Cancelar'>
							</th>
						</tr>
						<?php } ?>
					</table>
				</div>
			</form>
			<form name="formulario_tabs_3" method="post" action="cadastrarImovel.php#tabs-4" enctype="multipart/form-data">
				<div id="tabs-4" class="tabs">
					<table id="gradient-style" class="tabs" style="margin:0px">
						<tr>
							<th colspan='2'>Atributo</th>
							<th>valor</th>
						</tr>
						<?php

							$criterio = new TCriteria;
							$criterio->add(new TFilter('id_imovel', '=', $this->id));
							$sql = new TSqlSelect;
							$sql->setEntity('imovel_caracteristicas');
							$sql->addColumn('*');
							$sql->setCriteria($criterio);
							$result = $conn->query($sql->getInstruction());
							if($result->rowCount()){	
								for($i=0;$i<$result->rowCount();$i++){
									$row = $result->fetch(PDO::FETCH_ASSOC);
									extract($row);
									//$tag = $tag_valor = $i;
									$info = new info;
									$info->msg = "Deseja mesmo deletar esta característica?<br><br>";
									$info->msg.= "<a class='aSubmit' href='cadastrarImovel.php?op=deletarTag&tag=".base64_encode($id)."&id_imovel=".base64_encode($this->id)."#tabs-4'>Sim</a>";
									$info->msg.= "<a class='aSubmit' href='#' onclick='".$info->getJsApagar()."' >Não</a>";
									$info->display = 'none';
									$info->class = 'info_tag_'.$i;
									echo $info->getInfo();
									echo "<tr>";
										echo "<td><a href='#' ".$info->getJs()."><img src='img/menos.png'></a></td>";
										echo "<td>".$tag."</td>";
										echo "<td>".$tag_valor."</td>";
									echo "</tr>";

								}
							}else{
								echo "<tr>";
									echo "<td colspan='3' align='center'>Nenhuma característica registrado.</td>";
								echo "</tr>";
							}

							if($this->id){
								$Tag[] = "Quartos";
								$Tag[] = "Banheiros";
								$Tag[] = "Sala de estar";
								$Tag[] = "Cozinha";
								$Tag[] = "Garagem para quantos carros";
								$Tag[] = "Varanda";
								$Tag[] = "Piscina";
								$Tag[] = "Pisos";
								$Tag[] = "Área construida";
								$Tag[] = "Área do terreno";

								$Tag_valor[] = "4";
								$Tag_valor[] = "2";
								$Tag_valor[] = "1";
								$Tag_valor[] = "1";
								$Tag_valor[] = "2";
								$Tag_valor[] = "1";
								$Tag_valor[] = "0";
								$Tag_valor[] = "2 (terreo e o primeiro piso)";
								$Tag_valor[] = "70m²";
								$Tag_valor[] = "130m²";

								$cont = (count($Tag)-1);
								$cont = rand(0, $cont);
						?>
						<input type='hidden' name='op' value='novoTag'>
						<input type='hidden' name='id_imovel' value='<?php echo $this->id; ?>'>
						<tr>
							<td></td>
							 <script>
								$(function() {
									$("#tags").autocomplete({
										source: 'inc/ajaxImovelTag.inc.php'
									});
								});
								
							</script>
							<td><input type='text' id='tags' name='tag' placeholder='<?php echo $Tag[$cont] ?>'></td>
							<td><input type='text' name='tag_valor' placeholder='<?php echo $Tag_valor[$cont] ?>'></td>
						</tr>
						<tr>
							<th></th>
							<th>
								<input class='btnEnviar' type='submit' value='Enviar'>
							</th>
							<th>
								<input type='reset' value='Cancelar'>
							</th>
						</tr>


						<?php
							}
						?>
					</table>
				</div>
			</form>
		</div>

		<?php
	}
}

?>