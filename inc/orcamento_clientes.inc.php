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
		if(isset($_POST['search'])) {
			$search = $db->real_escape_string($_POST['search']);
			
			// Is the string length greater than 0?
			
			if(strlen($search) >0) {
				// Run the query: We use LIKE '$search%'
				// The percentage sign is a wild-card, in my example of countries it works like this...
				// $search = 'Uni';
				// Returned data = 'United States, United Kindom';
				
				// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
				// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$search%' LIMIT 10
					//op = a true procura produto por id
					$query = $db->query("SELECT * FROM cliente_fornecedor WHERE nome LIKE '%$search%' or razao_social like '%$search%'");
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					$li = "li1";
					while ($result = $query ->fetch_object()) {
						// Format the results, im using <li> for the list, you can change it.
						// The onClick function fills the textbox with the result.
						
						//calcular o preco do produto
						
						// YOU MUST CHANGE: $result->value to $result->your_colum
						$id = $result->id;
						$tipo = $result->tipo;
						$nome = $result->nome;
						$razao_social = $result->razao_social;
						$cpf_cnpj = $result->cpf_cnpj;
						$rg_ie = $result->rg_ie;
						$data_nascimento = formataData($result->data_nascimento);
						$email = $result->email;
						$fone1 = $result->fone1;
						$fone2 = $result->fone2;
						$endereco = $result->endereco;
						$numero = $result->numero;
						$bairro = $result->bairro;
						$cidade = $result->cidade;
						$CIDADE = registro($result->cidade, "cidades", "nome", "cod_cidades");
						$estado = $result->estado;
						$cep = $result->cep;
						
						
						$cod = "<li class='$li' onclick=\"preencherCliente('$id', '$tipo', '$nome', '$razao_social', '$cpf_cnpj', '$rg_ie', ";
						$cod .= "'$data_nascimento', '$email', '$fone1', '$fone2', '$endereco', '$numero', '$bairro', ";
						$cod .= "'$cidade', '$CIDADE', '$estado', '$cep');\">";
						
						
						
						switch ($tipo) {
							case 'f':
								$cod .="$nome</li>";
								break;
							
							default:
								$cod .="$nome / $razao_social</li>";
								break;
						}
						
	         			echo $cod;
	         			
						if($li=="li1"){
							$li= "li2";
						}else{
							$li="li1";
						}
	         		}
				} else {
					echo 'ERROR: Existe um problema com a query.';
				}
			} else {
				// Dont do anything.
			} // There is a search.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
	mysqli_close($conexao);
?>