<?php
	//para calcular o preco do produto inclue esse arquivos com as funcoes
	header('content-type: text/html; charset=ISO-8859-1');
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
			$inputId = $db->real_escape_string($_POST['id']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
				// Run the query: We use LIKE '$queryString%'
				// The percentage sign is a wild-card, in my example of countries it works like this...
				// $queryString = 'Uni';
				// Returned data = 'United States, United Kindom';
				
				// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
				// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$queryString%' LIMIT 10
				$instrucao = "SELECT id, nome, modelo, id_categoria, id_subcategoria, id_marca FROM produto ";
				$instrucao .= "WHERE nome LIKE '%$queryString%' or modelo LIKE '%$queryString%' ";
				$instrucao .= "or id_marca = ANY (select id from marca where nome like '%$queryString%') ";
				$instrucao .= "or id_categoria = ANY (select id from categoria where nome like '%$queryString%') ";
				$instrucao .= "or id_subcategoria = ANY (select id from sub_categoria where nome like '%$queryString%') ";
				$instrucao .= "LIMIT 10";
				$query = $db->query($instrucao);
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					$li = "li1";
					while ($result = $query ->fetch_object()) {
						// Format the results, im using <li> for the list, you can change it.
						// The onClick function fills the textbox with the result.
						
						//calcular o preco do produto
						$preco1 = precoProduto($result->id, true, false, false);
						$preco2 = precoProduto($result->id, false, true, false);
						
						// YOU MUST CHANGE: $result->value to $result->your_colum
						$nome = $result->nome . " " . $result->modelo . " " . registro($result->id_marca, "marca", "nome", "id");
						$nome .= " " . registro($result->id_categoria, "categoria", "nome", "id") . " " . registro($result->id_subcategoria, "sub_categoria", "nome", "id");
						$id = $result->id;
						
						//imagem
						$sql = query("select * from produto_imagem where id_produto=".$result->id);
						for($i=0, $tooltip=null; $i<mysqli_num_rows($sql); $i++){
							extract(mysqli_fetch_assoc($sql));
							if(file_exists("../".$miniatura)){
								$tooltip .= "<img ".pop("imagem.php?id=$id_produto&atributo=produto_imagem&referencia=id_produto", 600, 600)." src=\"$miniatura\">";	
							}
						}
						
						$js = "onClick=\"preencherP('$id', '$nome', '$inputId'); qtdAbled($inputId)\"";
						echo "<li class='$li'>";
	         			echo "<table class='listaItensOrcamento'><tr><td class='nomeLista'>$tooltip <span $js>$nome</span></td>";
	         			echo "<td class='precoLista' align='right'><span id='p1'>R$ ".real($preco1)."</span> <span id='p2'>R$ ".real($preco2)."  Desc. Max.</span></td></tr></table>";
	         			echo "</li>";
						
						if($li=="li1"){
							$li= "li2";
						}else{
							$li="li1";
						}
	         		}
				} else {
					echo 'ERROR: Existe um problema com a query.<br>'.$instrucao;
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