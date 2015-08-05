<?php

class cliente_fornecedor{
	
	public $id;
	public $formato;
	public $tipo;
	public $nome;
	public $razao_social;
	public $cpf_cnpj;
	public $rg_ie;
	public $data_nascimento;
	public $email;
	public $fone1;
	public $fone2;
	public $endereco;
	public $numero;
	public $bairro;
	public $cidade;
	public $estado;
	public $cep;
	public $referencia;
	public $observacoes;
	public $latitude;
	public $longitude;
	public $status;
	
	
	
	function __construct($id = null, $formato = null, $tipo = null, $nome = null, $razao_social = null, $cpf_cnpj = null,
	$rg_ie = null, $data_nascimento = null, $email = null, $fone1 = null, $fone2 = null, $endereco = null, $numero = null, $bairro = null,
	$cidade = null, $estado = null, $cep = null, $referencia = null, $observacoes = null, $latitude = null, $longitude = null, $status = null){
		
		//caso tenha informado apenas o ID ele vai buscar o resto dos dados no db
		//caso ele tenha informado od ID e os outros atributos então ele irá construir o objeto
		if($id and !$formato){
			global $conexao;
			$sql = query("select * from cliente_fornecedor where id='$id'");
			if(mysqli_num_rows($sql)){
				extract(mysqli_fetch_assoc($sql));
			}
		}
		
		$this->id				= $id;
		$this->formato			= $formato;
		$this->tipo				= $tipo;
		$this->nome				= $nome;
		$this->razao_social		= $razao_social;
		$this->cpf_cnpj			= $cpf_cnpj;
		$this->rg_ie			= $rg_ie;
		$this->data_nascimento	= $data_nascimento;
		$this->email			= $email;
		$this->fone1			= $fone1;
		$this->fone2			= $fone2;
		$this->endereco			= $endereco;
		$this->numero			= $numero;
		$this->bairro			= $bairro;
		$this->cidade			= $cidade;
		$this->estado			= $estado;
		$this->cep				= $cep;
		$this->referencia		= $referencia;
		$this->observacoes		= $observacoes;
		$this->latitude			= $latitude;
		$this->longitude		= $longitude;
		$this->status			= $status;
		
		
	}
	
	/*
	 * $associado é a variavel que vai dizer se o formulário de cadastro de cliente_fornecedor estará associado
	 * a algum outro formulário, adaptando assim o formulário.
	 * 	id_cliente_fornecedor
		tipo
		associado //cadastrarCliente
		naoassociado //tipo2 #referente a cliente fornecedor
		naoassociado //op (inativo ativo)
		naoassociado //token (existir ou nao)
		associado //contato
		associado //telefoneC
		nome
		razao_social (existir ou nao)
		doc1
		verificaDoc1 #verifica se o cpf ou cnpj é valido
		email
		doc2
		telefone1
		telefone2
		data
		endereco
		bairro
		numero
		referencia
		estado
		cidade
		cep
		observacoes
	 */
	function formulario($associado = false){
		$script = "<script type='text/javascript' src='js/cadastrarClienteFiltro.js'></script>";
		if(!$associado){
			$script .= "<form name='formulario' method='post' action='cadastrarClienteFornecedor.php' enctype='multipart/form-data' onsubmit='return filtroClienteFornecedor();'>";
			$script .= "<table id='gradient-style'>";
		}
		$script .= "<input type='hidden' name='id_cliente_fornecedor' value='$this->id'>";
		$script .= "<tr>";
		if($this->tipo=="j"){
			$Tipo = "Juridica";
			$checkF = "";
			$checkJ = "checked='yes'";
		}else{
			$Tipo = "Fisica";
			$checkF = "checked='yes'";
			$checkJ = "";
		}
		
		if($associado){
			if($this->id){
				$itensForm1Style= "class='itensForm1' style='display:none;'";
				$itensForm2Style= "class='itensForm2' style=''";
				$itensForm3Style= "class='itensForm3' style='display:none;'";
				$script .= "<input type='hidden' value='$this->tipo' name='tipo'>";
			}else{
				$itensForm1Style= "class='itensForm1' style=''";
				$itensForm2Style= "class='itensForm2' style='display:none;'";
				$itensForm3Style= "class='itensForm3' style='display:none;'";
				
				$script .= "<th scope='col' colspan='6'>";
				
				$script .= "<span $itensForm2Style id='tipo_pessoa'>";
				$script .= "<input type='radio' value='f' name='tipo' $checkF onclick=\"tipoPessoaDoc(this.value);\">Pessoa Física ";
				$script .= "<input type='radio' value='j' name='tipo' $checkJ onclick=\"tipoPessoaDoc(this.value);\">Pessoa Jurídica";
				$script .= "</span>";
				$script .= "<span $itensForm1Style>";
				$script .= "<input type='radio' value='false' name='cadastrarCliente' onclick='mudaForm();'> Deseja cadastrar um cliente?";
				$script .= "</span>";
				
				$script .= "</th>";
			}
		}else{
			if($this->formato == "fornecedor"){
				$checkC = "";
				$checkFo = "checked='yes'";
			}else{
				$checkC = "checked='yes'";
				$checkFo = "";
			}
			$itensForm1Style= "class='itensForm1' style='display:none;'";
			$itensForm2Style= "class='itensForm2' style=''";
			$itensForm3Style= "class='itensForm3' style=''";
			
			$script .= "<th scope='col' colspan='6'>";
			
			$script .= "<span $itensForm2Style>";
			$script .= "<input type='radio' value='f' name='tipo' $checkF onclick=\"tipoPessoaDoc(this.value);\">Pessoa Física ";
			$script .= "<input type='radio' value='j' name='tipo' $checkJ onclick=\"tipoPessoaDoc(this.value);\">Pessoa Jurídica";
			$script .= "</span>";
			$script .= "<span style='padding:30px;'></span>";
			$script .= "<span $itensForm2Style>";
			$script .= "<input type='radio' value='cliente' name='tipo2' $checkC>Cliente ";
			$script .= "<input type='radio' value='fornecedor' name='tipo2' $checkFo>Fornecedor";
			$script .= "</span>";
			$script .= "<span style='padding:30px;'></span>";
			$script .= "<span $itensForm2Style>";
			if($this->status=="1" and $this->id){
				$script .= "<a href='cadastrarClienteFornecedor.php?id_cliente_fornecedor=".base64_encode($this->id)."&op=inativo&token=false' title='Desativar este $this->formato'>";
				$script .= "<img src='img/usuarioDesativar.png' style='width:30px;'></a>";
			}elseif($this->status=="0" and $this->id){
				$script .= "<a href='cadastrarClienteFornecedor.php?id_cliente_fornecedor=".base64_encode($this->id)."&op=ativo' title='Ativar este $this->formato'>";
				$script .= "<img src='img/usuarioAtivar.png' style='width:30px;'></a>";
			}
			$script .= "</span>";
			$script .= "</th>";
			
		}

		
		
		$script .= "</tr>";
		
		if($associado){
			$script .= "<tr $itensForm1Style>";
			$script .= "<td colspan='5'>";
			$script .= "Contato<br><input type='text' name='contato' value='$this->nome' autocomplete='off' onkeyup='clienteShow(this, \"contato\");' onBlur='cloneInput(this.value, \"nome\");'>";
			$script .= "<div class='suggestionsBox' id='suggestions' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
			$script .= "<div class='suggestionList' id='autoSuggestionsList'></div></div>";
			$script .= "</td>";
            //$script .= "<td colspan='1'><span style='white-space:nowrap;'>Telefone do Contato</span><br><input type='text' name='telefoneC' autocomplete='off' value='$this->fone1' ".mascara("Telefone", "15", null, null, null, 'clienteShow(this, "fone"); cloneInput(this.value, \"telefone1\");')."></td>";
			$script .= "<td colspan='1'><span style='white-space:nowrap;'>Telefone do Contato</span><br><input type='text' name='telefoneC' autocomplete='off' value='$this->fone1' ".mascara("Telefone", "15", "onBlur='cloneInput(this.value, \"telefone1\");'", null, null, 'clienteShow(this, "fone");')."></td>";
			$script .= "</tr>";
		}else{
			$script .= "<input type='hidden' value='true' name='cadastrarCliente'>";
			$script .= "<input type='hidden' name='contato' value='$this->nome'>";
			$script .= "<input type='hidden' name='telefoneC' value='$this->fone1'>";
		}
		$script .= "<tr $itensForm2Style>";
		
		$script .= "<td rowspan='2' align='center'>";
		$script .= showImagemClienteFornecedor($this->id, 8, 0);
		$script .= "</td>";
		
		$script .= "<td colspan='4' id='cliente'>";
		if($this->tipo=="j"){
			$js = "onBlur='verificaDoc(\"j\");'";
			$script .= "<div>Nome da Empresa<br><input type='text' name='nome' value='$this->nome' onBlur='cloneInput(this.value, \"contato\");' autocomplete='off'></div><div>Razão Social<br><input type='text' name='razao_social' value='$this->razao_social' autocomplete='off'></div>";
			$script .= "</td>";
			$script .= "<td colspan='1' id='doc1'>CNPJ<br><input type='text' name='doc1' value='$this->cpf_cnpj' ".mascara("Cnpj", "18", $js).">";
			$doc2 = "Inscrição Estadual";
		}else{
			$js = "onBlur='verificaDoc(\"f\");'";
			$script .= "Cliente<br><input type='text' name='nome' onBlur='cloneInput(this.value, \"contato\");' value='$this->nome' autocomplete='off'>";
			$script .= "</td>";
			$script .= "<td colspan='1' id='doc1'>CPF<br><input type='text' name='doc1' value='$this->cpf_cnpj' ".mascara("Cpf", "14", $js).">";
			$doc2 = "RG";
		}
		$script .= "<span id='checkDoc1' style='display:none;'></span>";
		$script .= "<input type='hidden' name='verificaDoc1' value='true'>";
		$script .= "</td>";
		
		$script .= "</tr>";
		$script .= "<tr $itensForm2Style>";
		$script .= "<td colspan='4'>E-mail ".ajudaTool("É importante distinguir letras maiúsculas de minúsculas no e-mail.")."<br><input type='text' name='email' style='text-transform:none;' value='$this->email' class='email'></td>";
		$script .= "<td colspan='1' id='doc2'>$doc2<br><input type='text' name='doc2' value='$this->rg_ie' ".mascara("Integer")."></td>";
		$script .= "</tr>";
		
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='4'>Telefone 1<br><input type='text' name='telefone1' onBlur='cloneInput(this.value, \"telefoneC\");' value='$this->fone1' ".mascara("Telefone", "15")."></td>";
		$script .= "<td colspan='1'>Telefone 2<br><input type='text' name='telefone2' value='$this->fone2' ".mascara("Telefone", "15")."></td>";
		!strstr($this->data_nascimento, "/") ? $this->data_nascimento = formataData($this->data_nascimento) : false;
		if($this->tipo=="j"){
			$script .= "<td colspan='1'><span id='data'>Data deFuncação</span><br>".inputData("formulario", "data", false, $this->data_nascimento)."</td>";			
		}else{
			$script .= "<td colspan='1'><span id='data'>Data de Nascimento</span><br>".inputData("formulario", "data", false, $this->data_nascimento)."</td>";	
		}
		$script .= "</tr>";
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='4' id='endereco'>Endereço<br><input type='text' name='endereco' value='$this->endereco'></td>";
		$script .= "<td colspan='1'>Numero<br><input type='text' name='numero' value='$this->numero' ".mascara("Integer")."></td>";
		$script .= "<td colspan='1'>Bairro<br><input type='text' name='bairro' value='$this->bairro'></td>";
		$script .= "</tr>";
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='4'>Estado<br><select name='estado' id='estados' onchange=\"ajax(this.value, 'cidades')\">";
		$script .= opcaoSelect("estados", "2", false, $this->estado);
		$script .= "<select></td>";
		$script .= "<td colspan='1' id='tdCidades'>Cidade<br><select name='cidade' id='cidades'>";
		$sql_cidades = query("select * from cidades where estados_cod_estados='$this->estado'");
		for($i=0;$i<mysqli_num_rows($sql_cidades);$i++){
			$reg = mysqli_fetch_assoc($sql_cidades);
			if($this->cidade == $reg["cod_cidades"]){
				$script .= "<option value='".$reg["cod_cidades"]."' selected='yes'>".$reg["nome"]."</option>";
			}else{
				$script .= "<option value='".$reg["cod_cidades"]."'>".$reg["nome"]."</option>";
			}
			
		}
		$script .= "</select></td>";
		$script .= "<td colspan='1' id='tdCep'>CEP<br><input type='text' value='$this->cep' name='cep' " . mascara("Cep", "9") . "></td>";
		$script .= "</tr>";
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='4'>Ponto de Referência<br><input type='text' name='referencia' value='$this->referencia'></td>";
		$script .= "<td colspan='1'>Latitude ".ajudaTool("Para descobrir sua Latitude e Longitude<br><img src=\"img/info_latlgn.jpg\">")."<br><input type='text' name='latitude' value='$this->latitude'></td>";
		$script .= "<td colspan='1'>Longitude".ajudaTool("Para descobrir sua Latitude e Longitude<br><img src=\"img/info_latlgn.jpg\">")."<br><input type='text' name='longitude' value='$this->longitude'></td>";
		$script .= "</tr>";
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='6'>";
		if(!empty($this->latitude) and !empty($this->longitude)){
			
			$script .= "<iframe style='width:100%; height:360px; resize: both;' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' ";
			$script .= "src='https://maps.google.com.br/maps?f=d&amp;source=s_d&amp;saddr=".$this->latitude.",".$this->longitude;
			$script .= "&amp;daddr=&amp;geocode=&amp;aq=&amp;sll=".$this->latitude.",".$this->longitude."&amp;sspn=0.001981,0.00284";
			$script .= "&amp;t=h&amp;hl=pt-BR&amp;mra=mift&amp;mrsp=0&amp;sz=19&amp;ie=UTF8&amp;ll=".$this->latitude.",".$this->longitude;
			$script .= "&amp;spn=0.001981,0.00284&amp;output=embed'></iframe>";
			
		}else{
			$enderecoMap = strtolower($this->endereco).", ";
			//if($this->numero){
			//	$enderecoMap .= $this->numero.", ";
			//}
			$enderecoMap .= strtolower(registro($this->cidade, "cidades", "nome", "cod_cidades"))." - ";
			$enderecoMap .= strtolower(registro($this->estado, "estados", "nome", "cod_estados"));
			
			$script .= "<iframe style='width:100%; height:360px; resize: both;' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' ";
			$script .= "src='https://maps.google.com.br/maps?f=q&amp;source=s_q&amp;hl=pt-BR&amp;geocode=&amp;q=";
			$script .= $enderecoMap."&amp;sll=amp;sspn=&amp;t=h&amp;ie=UTF8&amp;hq=";
			$script .= $enderecoMap."&amp;ll=&amp;spn=0.015734,0.011467&amp;output=embed'></iframe>";
			
		}
		$script .= "</td>";
		$script .= "</tr>";
		
		$sql = query("select sum(quantidade * valor_ponto) as pontos from ponto_log where id_cliente='".$this->id."'");
		extract(mysqli_fetch_assoc($sql));
		if($pontos>0 && !$associado){
			$script .= "<tr $itensForm3Style>";
			$script .= "<th colspan='3'>Pontos acumulados</th>";
			$msg = "Clique aqui para visualizar as compras desse Cliente";
			$script .= "<th colspan='3'>Data da última compra ".ajudaTool($msg, "pesquisaOrcamento.php?busca=cliente&nome_cliente=".$this->nome."&intervalo=data_emissao")."</th>";
			$script .= "</tr>";
			//extract(mysqli_fetch_assoc($sql));
			$script .= "<tr $itensForm3Style>";
			$script .= "<td colspan='3'><input type='text' class='ponto inputValor totalValor' name='pontos' value='".real(round($pontos,2))."'></td>";
			$sql = query("select data as pontos_data from conta where entidade='".$this->id."' and tabela_entidade='cliente_fornecedor' and status='2' order by id desc");
			if(mysqli_num_rows($sql)){
				extract(mysqli_fetch_assoc($sql));
			}else{
				$pontos_data = null;
			}
			$script .= "<td colspan='3'><input type='text' class='inputValor' name='pontos_data' value='".formataData($pontos_data)."'></td>";
			$script .= "</tr>";
		}else{
			$script .= "<input type='hidden' name='pontos' value='null'>";
			$script .= "<input type='hidden' name='pontos_data' value='null'>";
		}
		
		
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='6'>";
		$script .= arqVisualizar("obs".$this->formato, "Visualizar / Esconder observações deste $this->formato.", 1); 
		$script .= " Observações do cadastro $this->formato</td>";
		$script .= "</tr>";
		$script .= "<tr $itensForm3Style>";
		$script .= "<td colspan='6' class='obs".$this->formato."' style='display:none;'><textarea class='ckeditor' id='obsCadastro' name='observacoes'>$this->observacoes</textarea></td>";
		$script .= "</tr>";
		
		if(!$associado){
			
			$sql = query("select * from cliente_fornecedor_imagem where id_cliente_fornecedor='$this->id'");
			$imagensQtd = mysqli_num_rows($sql);
			
			if($this->id){
				$script .= "<tr>";
					$script .= "<th colspan='6'><a href='#' ".pop("cliente_fornecedor_imagem_upload.php?id=$this->id", 600, 700)." title='Adicionar imagens para $this->nome'><img class='imgHelp' src='img/mais.png'></a> Imagens deste ";
					if($this->formato=="cliente"){
						$script .= "Cliente";
					}else{
						$script .= "Fornecedor";
					}
					$script .= "</th>";
				$script .= "<tr>";
			}
			if($imagensQtd>0 and $this->id){
				$script .= "<tr>";
				$script .= "<td colspan='6' align='center'>";
				
				$script .= showImagemClienteFornecedor($this->id, 8, 0, true);
				
				$script .= "</td>";
				$script .= "</tr>";
			}
			
			
			$script .= "<tr>";
			$script .= "<th colspan='5' align='right'><input type='submit' class='btnEnviar' value='Enviar'></th>";
			$script .= "<th><input type='reset' value='Cancelar'></th>";
			$script .= "</tr>";
			$script .= "</table>";
		}
		
		return $script;
	}

	function inserir(){
		
		global $conexao;
		
		if(!is_numeric($this->id)){
			//inserindo na tabela cliente_fornecedor
			$instrucao = "insert into cliente_fornecedor (formato, tipo, nome, razao_social, cpf_cnpj, rg_ie, data_nascimento, ";
			$instrucao .= "email, fone1, fone2, endereco, bairro, numero, cidade, estado, cep, referencia, observacoes, latitude, longitude, status) values ";
			$instrucao .= "('$this->formato', '$this->tipo', '$this->nome', '$this->razao_social', '$this->cpf_cnpj', '$this->rg_ie', '" . formataDataInv($this->data_nascimento) . "', ";
			$instrucao .= "'$this->email', '$this->fone1', '$this->fone2', '$this->endereco', '$this->bairro', '$this->numero', '$this->cidade', '$this->estado', ";
			$instrucao .= "'$this->cep', '$this->referencia', '$this->observacoes', '$this->latitude', '$this->longitude', '1')";
			$sql = query($instrucao);
			
			if(mysqli_affected_rows($conexao)){
				
				$chave_principal = mysqli_insert_id($conexao);
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Cadastrou o $this->formato";
				$tabela_afetada = "cliente_fornecedor";
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
	
	function update(){
			
		global $conexao;
			
		if($this->id){
			if($this->status==""){
				$this->status=1;
			}
			$instrucao = "update cliente_fornecedor set formato='$this->formato', ";
			$instrucao .= "tipo='$this->tipo', nome='$this->nome', razao_social='$this->razao_social', cpf_cnpj='$this->cpf_cnpj', rg_ie='$this->rg_ie', ";
			$instrucao .= "data_nascimento='".formataDataInv($this->data_nascimento)."', email='$this->email', fone1='$this->fone1', fone2='$this->fone2', ";
			$instrucao .= "endereco='$this->endereco', bairro='$this->bairro', numero='$this->numero', cidade='$this->cidade', estado='$this->estado', ";
			$instrucao .= "cep='$this->cep', referencia='$this->referencia', observacoes='$this->observacoes', latitude='$this->latitude', ";
			$instrucao .= "longitude='$this->longitude', status='$this->status' ";
			$instrucao .= "where id='$this->id'";
			
			
			$sql = query($instrucao);
			
			if(mysqli_affected_rows($conexao)){
				
				$chave_principal = mysqli_insert_id($conexao);
				
				$id_usuario = getIdCookieLogin($_COOKIE["login"]);
				$dataAtual = date('Y-m-d H:i:s');
				$acao = "Editou o $this->formato";
				$tabela_afetada = "cliente_fornecedor";
				
				insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
				
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}	
		
	}
	
	
}


?>