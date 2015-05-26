<?php
	//header('content-type: text/html; charset=ISO-8859-1');
	//para calcular o preco do produto inclue esse arquivos com as funcoes
	include "funcoes.inc.php";
	if(isset($_COOKIE["id_empresa"])){
		//includes do php para todas as paginas
		$conn = TConnection::open("gestor");

		$criterio = new TCriteria;
		$criterio->add(new TFilter("id", "=", $_COOKIE["id_empresa"]));

		$sql = new TSqlSelect;
		$sql->setEntity("cliente");
		$sql->addColumn('alcunha');
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());
		if($result->rowCount()){
			$row = $result->fetch(PDO::FETCH_ASSOC);
			extract($row);
			define("ALCUNHA", $alcunha);
		}

		//deletar esse codigo apois migração do app.ado
		file_exists("../conecta.php") ?
		include "../conecta.php" : $conexao = null;
		$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
		extract(mysqli_fetch_assoc($sql));
		include "../".$conectasrc;
		//fim
	}else{
		echo "Impossível se conectar com o banco de dados.";
		die;
	}
	
	
	if(!$db) {
		// Show error if we cannot connect.
		echo 'ERROR: Não pode se conectar com o banco de dados.<br>'. mysqli_connect_error();
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $db->real_escape_string($_POST['queryString']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
				
				$li = "li1";
				
				$instrucao = "SELECT id as idServico from servico ";
				$instrucao .= "WHERE ";
				//servico
				$instrucao .= "servico.nome LIKE '%$queryString%' ";
				$instrucao .= "or servico.id_categoria like (select id from categoria where nome like '%$queryString%' LIMIT 10) ";
				$instrucao .= "or servico.id_subcategoria like (select id from sub_categoria where nome like '%$queryString%' LIMIT 10) ";
				$instrucao .= "and status='Ativo' ";
				$instrucao .= "LIMIT 10";
				
				$queryServico = $db->query($instrucao);
				
				if($queryServico){
						
					while($result = $queryServico -> fetch_object()){
						
						
						//calcular o preco do produto
						$sql = query("select * from servico_preco where id_servico=".$result->idServico);
						$qtdPreco = mysqli_num_rows($sql);
						extract(mysqli_fetch_assoc($sql));
						$preco1= real($valor);
						
						//imagem
						$sql = query("select * from servico_imagem where id_servico=".$result->idServico);
						for($i=0, $tooltip=null; $i<mysqli_num_rows($sql); $i++){
							extract(mysqli_fetch_assoc($sql));
							if(file_exists("../".$miniatura)){
								$tooltip .= "<img ".pop("imagem.php?id=$id_servico&atributo=servico_imagem&referencia=id_servico", 600, 600)." src=\"$miniatura\">";	
							}
						}
						
						$sql = query("select * from servico where id=".$result->idServico);
						extract(mysqli_fetch_array($sql));
						
													
						// YOU MUST CHANGE: $result->value to $result->your_colum
						$nomeCompleto = $nome ." ". registro($id_categoria, "categoria", "nome", "id") . " " . registro($id_subcategoria, "sub_categoria", "nome", "id");
						
						if($li=="li1"){
							$li= "li2";
						}else{
							$li="li1";
						}
						$js = "onClick=\"preencher('$id', '$nomeCompleto', '$preco1');\"";
	         			echo "<li  class='$li'>";
	         			echo "<table class='listaItensOrcamento'>";
	         			echo "<tr>";
	         			echo "<td rowspan='$qtdPreco' class='nomeLista'>$tooltip <span $js>$nome</span></td>";
						
						
						//calcular o preco do produto
						$sql = query("select * from servico_preco where id_servico=".$result->idServico);
						for($i=0, $preco = null; $i<$qtdPreco; $i++){
							extract(mysqli_fetch_assoc($sql));
							if($i!=0){
								echo "<tr>";
							}
							$limite == 0 ? $limite = "<b style='font-size:18pt;'>&infin;</b>": false;
							echo "<td class='precoLista' align='right'><span id='p2'>até $limite und </span><span id='p1'>R$ ".real($valor)."</span></td>";
							echo "</tr>";
						}
	         			
	         			echo "</table>";
	         			echo "</li>";
						
					}
					
				}

			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
	mysqli_close($conexao);
?>