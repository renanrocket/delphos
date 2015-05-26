<?php

class empresa{
	
	public $op;
	public $id;
	public $imgsrc;
	public $razao_social;
	public $nome;
	public $cnpj;
	public $data_fundacao;
	public $email;
	public $fone1;
	public $fone2;
	public $fone3;
	public $endereco;
	public $numero;
	public $complemento;
	public $bairro;
	public $cidade;
	public $estado;
	public $cep;
	public $usarTimbrado;
	
	public $alcunha;
	public $conectasrc;
	public $status;
	
	
	function __construct($id= null, $conn = null, $tabela = null){
		
		if($id){
			$conn = TConnection::open($conn);
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', $id));
			$sql= new TSqlSelect;
			$sql->setEntity($tabela);
			$sql->addColumn('*');
			$sql->setCriteria($criteria);
			$result = $conn->query($sql->getInstruction());
			if($result->rowCount()){
				$row = $result->fetch(PDO::FETCH_ASSOC);
				extract($row);
			}
			
			$this->op				= 'editar';
			$this->id 				= $id;
			$this->imgsrc 			= $imgsrc;
			$this->razao_social 	= $razao_social;
			$this->nome 			= $nome;
			$this->cnpj				= $cnpj;
			$this->data_fundacao	= $data_fundacao;
			$this->email			= $email;
			$this->fone1			= $fone1;
			$this->fone2			= $fone2;
			$this->fone3			= $fone3;
			$this->endereco			= $endereco;
			$this->numero			= $numero;
			$this->complemento		= $complemento;
			$this->bairro			= $bairro;
			$this->cidade			= $cidade;
			$this->estado			= $estado;
			$this->cep				= $cep;
			$this->usarTimbrado		= $usarTimbrado;
			
			if($tabela=='cliente'){
				$this->alcunha 		= $alcunha;
				$this->conectasrc	= $conectasrc;
				$this->status		= $status;
			}
		}else{
			$this->op				= 'novo';
			$this->id 				= null;
			$this->imgsrc 			= null;
			$this->razao_social 	= null;
			$this->nome 			= null;
			$this->cnpj				= null;
			$this->data_fundacao	= null;
			$this->email			= null;
			$this->fone1			= null;
			$this->fone2			= null;
			$this->fone3			= null;
			$this->endereco			= null;
			$this->numero			= null;
			$this->complemento		= null;
			$this->bairro			= null;
			$this->cidade			= 1722;
			$this->estado			= 6;
			$this->cep				= '62000-000';
			$this->usarTimbrado		= null;
				
			if($tabela=='cliente'){
				$this->alcunha 		= null;
				$this->conectasrc	= null;
				$this->status		= 1;
			}
		}
		
	}
	
	function getForm(){
		
		
		//js para esta pagina em especifico
		echo "<script src='../js/cadastrarEmpresaFiltro.js' type='text/javascript'></script>";
		echo "<script src='../js/cadastrarEmpresaFormulario.js' type='text/javascript'></script>";
		
		echo "<div style='font-size:10px; position: absolute; top:40px; width:1000px;' id='msg'></div>";
		echo "<div id='visualizar'></div>";
		
		$titulo = explode("/", $_SERVER['PHP_SELF']);
		count($titulo) > 0 ? $linha = (count($titulo) - 1) : $linha = 0;
		echo "<form class='form' name='formCadastraEmpresa' id='formCadastraEmpresa' method='post' action='".$titulo[$linha]."' enctype='multipart/form-data' onSubmit='return filtro();'>";
		
		echo "<input type='hidden' name='op' value='$this->op'>";
		echo "<input type='hidden' name='id' value='$this->id'>";
		
		echo "<div class='column'>";
		echo "<label for='razao_social'>Razão Social</label><input type='text' name='razao_social' value='$this->razao_social'>";
		echo "<label for='nome'>Nome fantasia</label><input type='text' name='nome' value='$this->nome'>";
		echo "<label for='endereco'>Endereço</label><textarea name='endereco'>$this->endereco</textarea>";
		echo "<label for='bairro'>Bairro</label><input type='text' name='bairro' value='$this->bairro'>";
		echo "<label for='complemento'>Complemento</label><input type='text' name='complemento' value='$this->complemento'>";
		echo "<label for='numero'>Número</label><input type='text' name='numero' value='$this->numero' ".mascara("Integer").">";
		echo "</div>";
		
		echo "<div class='column'>";
		echo inputECC('estados', 'cidades', 'cep', $this->estado, $this->cidade, $this->cep, 'label');
		
		echo "<label for='fone1'>Telefone 1</label><input type='text' name='fone1' value='$this->fone1' " . mascara("Telefone", "14") . ">";
		echo "<label for='fone2'>Telefone 2</label><input type='text' name='fone2' value='$this->fone2' " . mascara("Telefone", "14") . ">";
		echo "<label for='fone3'>Telefone 3</label><input type='text' name='fone3' value='$this->fone3' " . mascara("Telefone", "14") . ">";
		echo "</div>";
		
		echo "<div class='column'>";
		echo "<label for='email'>E-mail da empresa</label><input type='text' name='email' value='$this->email' class='email'>";
		echo "<label for='data_fundacao'>Data de fundação</label>".inputData("formCadastraEmpresa", "data_fundacao", NULL, formataData($this->data_fundacao))."";
		echo "<label for='imgsrc' align='center' valign='middle' id='logo'>Logomarca</label>";
		if ($this->imgsrc) {
			echo "<img style='height:100px;' src='$this->imgsrc'><br>";
			echo "<a href='javascript:void(0)' onclick='logoInput();'>Trocar de imagem</a>";
		} else {
			echo "<input type='file' name='imgsrc' id='imgsrc' value='$this->imgsrc' accept='image/*'><br><span style='font-size:11px;'>Recomenda-se imagem do tamaho de até 182px X 100px</span>";
		}
		echo "<label for='cnpj'>CNPJ</label><input type='text' name='cnpj' value='$this->cnpj' ".mascara("Cnpj", "18").">";
		echo "<label for='usarTimbrado'>Usar esses dados como timbrado?</label><select name='usarTimbrado'>";
		if ($this->id == null){
			echo "<option value='0'>Não</option>";
			echo "<option value='1' selected='yes'>Sim</option>";
		} elseif($this->usarTimbrado == 0 or $this->usarTimbrado == "") {
			echo "<option value='0' selected='yes'>Não</option>";
			echo "<option value='1'>Sim</option>";
		}else{
			echo "<option value='0'>Não</option>";
			echo "<option value='1' selected='yes'>Sim</option>";
		}
		echo "</select>";
		echo "<div class='submit-wrap'><input type='submit' class='submit' value='Enviar'></div>";
		echo "</div>";
		
		

				
		echo "</form>";
	}
	
	
}

?>