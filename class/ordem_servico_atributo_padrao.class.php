<?php

class ordem_servico_atributo_padrao{
	
	public $id;
	public $tipo_item;
	public $obrigatoriedade;
	public $nome;
	public $qtdItens;
	public $ordem_servico_atributo_padrao_sub;
	public $status;
	
	
	function __construct($id, $tipo_item, $obrigatoriedade, $nome, $qtdItens, $valor, $status){
		$this->id									= $id;
		$this->tipo_item							= $tipo_item;
		$this->obrigatoriedade						= $obrigatoriedade;
		$this->nome									= $nome;
		$this->status							 	= $status;
		$this->qtdItens								= $qtdItens;
		for($i=0; $i<$qtdItens; $i++){
			$this->ordem_servico_atributo_padrao_sub[$i]	= $valor[$i];	
		}
		
	}
	
	function formulario(){
		
		global $conexao;
		
		$script = "<script type='text/javascript' src='js/administrativoOrdemServicoAtributoPadrao.js'></script>";
		$script .= "<table id='gradient-style'>";
		$script .= "<form name='formulario' method='post' action='administrativoOrdemServicoAtributoPadrao.php' enctype='multipart/form-data'>";
		$script .= "<input type='hidden' name='id' value='$this->id'>";
		$script .= "<tr>";
		$script .= "<th colspan='4'>Cadastrar um novo atributo para as Ordens de Serviço";
		if($this->status=="1"){
			$script .= "<a href='administrativoOrdemServicoAtributoPadrao.php?id=".base64_encode($this->id)."&op=inativo' ";
			$script .= "title='Deletar este atributo'><img src='img/deletar.png'></a>";
		}elseif($this->status=="0"){
			$script .= "<a href='administrativoOrdemServicoAtributoPadrao.php?id=".base64_encode($this->id)."&op=ativo' ";
			$script .= "title='Reativar este atributo'><img src='img/inserir.png'></a>";
		}
		$script	.= "</th>";
		$script .= "</tr>";
		$script .= "<tr>";
		$script .= "<td>Pergunta ou nome do atributo<br><input type='text' name='nome' value='$this->nome'></td>";
		$script .= "<td>";
		$script .= "Obrigatório preencher este campo?<br>";
		$script .= "<select name='obrigatoriedade'>";
		if($this->obrigatoriedade==1){
			$script .= "<option value='1' selected='yes'>Sim</option>";
			$script .= "<option value='0'>Não</option>";
		}else{
			$script .= "<option value='1'>Sim</option>";
			$script .= "<option value='0' selected='yes'>Não</option>";
		}
		$script .= "</select>";
		$script .= "</td>";
		$script .= "<td>Tipo de atributo<br>";
		$script .= "<select name='tipo_item' onchange='showSubItens(this.value)'>";
		$opcoes = array("Texto curto", "Texto com parágrafo", "Arquivo", "Seleção de itens", "Imagem");
		$cont = count($opcoes);
		for($i=0; $i<$cont;$i++){
			if($this->tipo_item==$opcoes[$i]){
				$script .= "<option value='$opcoes[$i]' selected='yes'>$opcoes[$i]</option>";
			}else{
				$script .= "<option value='$opcoes[$i]'>$opcoes[$i]</option>";
			}
		}
		$script .= "</select>";
		$script .= "</td>";
		$script .= "<td>";
		
		$this->qtdItens == null ? $style="style='display:none;'" : $style="";
		
		$script .= "<table id='subItens' $style>";
		$script .= "<tr>";
		$js = "mudaQtdItens(this.value);";
		$script .= "<th>Quantidade de opções <input type='text' name='qtdItens' id='qtdItem' maxlength='2' onblur='$js' style='width:20px;' value='$this->qtdItens' ".mascara("Integer")."></th>";
		$script .= "</tr>";
		for($i=1, $j=0; $i<=99; $i++, $j++){
			if(empty($this->ordem_servico_atributo_padrao_sub[$j])){
				$valor = "Opção $i";
				if($i>2){
					$style="style='display:none;'";	
				}else{
					$style="";
				}
			}else{
				$valor = $this->ordem_servico_atributo_padrao_sub[$j];
				$style = "";
			}
			$script .= "<tr class='selecao' id='selecao_$i' $style>";
			$script .= "<td><input type='text' name='select_sub_item[]' id='valor_$i' value='$valor'></td>";
			$script .= "</tr>";	
		}
		
		$script .= "</table>";
		
		$script .= "</td>";
		$script .= "</tr>";
		$script .= "<tr>";
		$script .= "<th colspan='4' align='right'><input type='submit' class='btnEnviar' value='Enviar'></th>";
		$script .= "</tr>";
		
		$script .= "</form>";
		
		//caso não seja uma edição irá apresentar a tabela dos outros atributos já cadastrados
		if(!$this->id){
			
			$sql = query("select * from ordem_servico_atributo_padrao where status='1'");
			for($i=0; $i<mysqli_num_rows($sql); $i++){
				if($i==0){
					$script .= "<tr>";
					$script .= "<th colspan='4'>Outros atributos ";
					$script .= "<select onchange='mudaAtributos(this.value)' style='width:auto;'>";
					$script .= "<option value='Ativos' selected='yes'>Ativos</option>";
					$script .= "<option value='Inativos'>Inativos</option>";
					$script .= "</select>";
					$script .= "</th>";
					$script .= "</tr>";
					
					$script .= "<tr>";
					$script .= "<th>Pergunta ou nome do atributo</th>";
					$script .= "<th>Obrigatório preencher este campo?</th>";
					$script .= "<th>Tipo de atributo</th>";
					$script .= "<th>Valores da seleção</th>";
					$script .= "</tr>";
				}
				extract(mysqli_fetch_array($sql));
				$script .= "<tr class='ativo'>";
				$script .= "<td>";
				$script .= "<form method='post' action='administrativoOrdemServicoAtributoPadrao.php' enctype='multipart/form-data'>";
				$script .= "<input type='hidden' name='id' value='$id'>";
				$script .= "<input type='submit' value='$id'>";
				$script .= " $nome";
				$script .= "</form>";
				$script .= "</td>";
				$obrigatoriedade == 1? $obrigatoriedade = "Sim" : $obrigatoriedade = "Não";
				$script .= "<td>$obrigatoriedade</td>";
				$script .= "<td>$tipo_item</td>";
				$sqlSub = query("select * from ordem_servico_atributo_padrao_sub where id_ordem_servico_atributo_padrao='$id'");
				$script .= "<td><table>";
				for($l=0; $l<mysqli_num_rows($sqlSub); $l++){
					extract(mysqli_fetch_assoc($sqlSub));
					$script .= "<tr>";
					$script .= "<td>$valor</td>";
					$script .= "</tr>";	
				}
				$script .= "</table></td>";
				$script .= "</tr>";
			}
			$sql = query("select * from ordem_servico_atributo_padrao where status='0'");
			for($j=0; $j<mysqli_num_rows($sql); $j++){
				if($j==0 && $i==0){
					$script .= "<tr>";
					$script .= "<th colspan='4'>Outros atributos ";
					$script .= "<select onchange='mudaAtributos(this.value)' style='width:auto;'>";
					$script .= "<option value='Ativos'>Ativos</option>";
					$script .= "<option value='Inativos' selected='yes'>Inativos</option>";
					$script .= "</select>";
					$script .= "</th>";
					$script .= "</tr>";
					
					$script .= "<tr>";
					$script .= "<th>Pergunta ou nome do atributo</th>";
					$script .= "<th>Obrigatório preencher este campo?</th>";
					$script .= "<th>Tipo de atributo</th>";
					$script .= "<th>Valores da seleção</th>";
					$script .= "</tr>";
				}
				extract(mysqli_fetch_array($sql));
				$script .= "<tr class='inativo' style='display:none;'>";
				$script .= "<td>";
				$script .= "<form method='post' action='administrativoOrdemServicoAtributoPadrao.php' enctype='multipart/form-data'>";
				$script .= "<input type='hidden' name='id' value='$id'>";
				$script .= "<input type='submit' value='$id'>";
				$script .= " $nome";
				$script .= "</form>";
				$script .= "</td>";
				
				$obrigatoriedade == 1? $obrigatoriedade = "Sim" : $obrigatoriedade = "Não";
				$script .= "<td>$obrigatoriedade</td>";
				$script .= "<td>$tipo_item</td>";
				$sqlSub = query("select * from ordem_servico_atributo_padrao_sub where id_ordem_servico_atributo_padrao='$id'");
				$script .= "<td><table>";
				for($l=0; $l<mysqli_num_rows($sqlSub); $l++){
					extract(mysqli_fetch_assoc($sqlSub));
					$script .= "<tr>";
					$script .= "<td>$valor</td>";
					$script .= "</tr>";	
				}
				$script .= "</table></td>";
				$script .= "</tr>";
			}
		}
		$script .= "</table>";
		
		return $script;
	}
	
	function inserir(){
		$instrucao = "insert into ordem_servico_atributo_padrao ";
		$instrucao .= "(tipo_item, obrigatoriedade, nome) ";
		$instrucao .= "values ";
		$instrucao .= "('$this->tipo_item', '$this->obrigatoriedade', '$this->nome')";
		$sql = query($instrucao);
		
		$id = ultimaId("ordem_servico_atributo_padrao");
		
		$chave_principal = $id;
				
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Acrescentou um atributo padrão da ordem de serviço";
		$tabela_afetada = "ordem_servico_atributo_padrao";
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
		if($this->qtdItens>1){
			$instrucao = "insert into ordem_servico_atributo_padrao_sub ";
			$instrucao .= "(id_ordem_servico_atributo_padrao, valor)";
			$instrucao .= "value ";
			for($i=0; $i<$this->qtdItens; $i++){
				if($i<>0){
					$instrucao .= ", ";
				}
				$instrucao .= "($id, '".$this->ordem_servico_atributo_padrao_sub[$i]."')";
			}
			$sql = query($instrucao);
		}
		
	}
	
	function update(){
		if($this->id){
			$instrucao = "update ordem_servico_atributo_padrao set ";
			$instrucao .= "tipo_item='$this->tipo_item', obrigatoriedade='$this->obrigatoriedade', nome='$this->nome' ";
			$instrucao .= "where id='$this->id'";
			$sql = query($instrucao);
			
			$chave_principal = $this->id;
					
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$dataAtual = date('Y-m-d H:i:s');
			$acao = "Alterou um atributo padrão da ordem de serviço";
			$tabela_afetada = "ordem_servico_atributo_padrao";
			
			insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
			
			$sql = query("delete from ordem_servico_atributo_padrao_sub where id_ordem_servico_atributo_padrao='$this->id'");
			if($this->qtdItens>1){
				$instrucao = "insert into ordem_servico_atributo_padrao_sub ";
				$instrucao .= "(id_ordem_servico_atributo_padrao, valor)";
				$instrucao .= "value ";
				for($i=0; $i<$this->qtdItens; $i++){
					if($i<>0){
						$instrucao .= ", ";
					}
					$instrucao .= "($this->id, '".$this->ordem_servico_atributo_padrao_sub[$i]."')";	
				}
				$sql = query($instrucao);
			}
		}
	}

	function ativar($id){
		$sql = query("update ordem_servico_atributo_padrao set status='1' where id='$id'");
		
		$chave_principal = $id;
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Alterou um atributo padrão da ordem de serviço";
		$tabela_afetada = "ordem_servico_atributo_padrao";
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	}
	
	function inativar($id){
		$sql = query("update ordem_servico_atributo_padrao set status='0' where id='$id'");
		
		$chave_principal = $id;
		$id_usuario = getIdCookieLogin($_COOKIE["login"]);
		$dataAtual = date('Y-m-d H:i:s');
		$acao = "Alterou um atributo padrão da ordem de serviço";
		$tabela_afetada = "ordem_servico_atributo_padrao";
		
		insertHistorico($id_usuario, $dataAtual, $acao, $tabela_afetada, $chave_principal);
	}
}


?>