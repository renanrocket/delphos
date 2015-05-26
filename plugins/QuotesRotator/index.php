<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Banner</title>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		<script src="js/modernizr.custom.js"></script>
		<script src="../../js/mascara.js"></script>
	</head>
	<?php 
		$sorteio = rand(0,100);
		if($sorteio>30){
			echo "<body>";
		}else{
			echo "<body align='center'>";
		}
	?>
	

		<?php
			
			include "rocket.php";

			function query($instrucao, $conect = null, $testar = null) {
				if (is_null($conect)) {
					global $conexao;
					$conect = $conexao;
				}
				$pagina = explode("/", $_SERVER['PHP_SELF']);
				$include = "";
				for ($i = 3; $i < count($pagina); $i++) {
					$include .= "../";
				}
				$include .= "inc/msgErro.inc";
				if ($testar) {
					$sql = $instrucao;
				} else {
					$sql = mysqli_query($conect, $instrucao) or die(
					include $include);
				}

				return $sql;
			}

			function showImagemProduto($idProduto) {

				$caminhoCheck = "../../";

				$script = "";

				$sql = query("select * from produto_imagem where id_produto='$idProduto'");
				if (mysqli_num_rows($sql) > 0) {

					extract(mysqli_fetch_assoc($sql));

					if (!file_exists($caminhoCheck . $miniatura)) {
						$miniatura = $caminhoCheck . "img/icones/pesquisaProduto.png";
					} else {
						$miniatura = $caminhoCheck . $miniatura;
					}

					$script .= $miniatura;

				} else {
					$script .= $miniatura = $caminhoCheck . "img/icones/pesquisaProduto.png";
				}

				return $script;
			}

			// funcao que corrige o problema do ponto e da virgula em valores que envolve moeda. essa funcao recebem um valor float
			function real($num, $ponto = null) {
				if ($ponto) {
					$ponto = ".";
					$ponto2 = ",";
				} else {
					$ponto = ",";
					$ponto2 = ".";
				}
				$stringAtnes = explode($ponto2, $num);
				$stringAntes = strlen($stringAtnes[0]);
				$num = substr($num, 0, $stringAntes + 3);
				//$num= round($num,2);
				if ($num) {
					$num = str_replace(".", $ponto, $num);
					if (strstr($num, $ponto) == false) {
						$num .= $ponto . "00";
					}
					$check = explode($ponto, $num);
					if (strstr($num, $ponto) == true and strlen($check[1]) < 2) {
						$num .= "0";
					}
				} else {
					$num = "00" . $ponto . "00";
				}

				return $num;
			}

			//funcao calcular preco do produto por id de produto
			//funcao recebe o id do produto e recebe true de acordo com o valor q deseja se retornar entre (ml, descMax)
			function precoProduto($id = null, $ml = false, $descMax = false) {
				if ($id) {
					$sql = query("select valor_compra, mlpor, descMaxpor from produto where id='$id'");

					if (mysqli_num_rows($sql)) {
						extract(mysqli_fetch_assoc($sql));

						$sql = query("select * from produto_tributacao where id_produto='$id'");
						for ($i = $tributacaoValor = 0; $i < mysqli_num_rows($sql); $i++) {
							extract(mysqli_fetch_assoc($sql));
							if ($tipo_valor == 0) {
								$tributacaoValor += $valor;
							} elseif ($tipo_valor == 1) {
								$tributacaoValor += $valor_compra * $valor / 100;
							}
						}
						$valor_custo = $tributacaoValor + $valor_compra;
						if ($ml) {
							return round(($mlpor / 100 * $valor_custo), 2);
						}
						if ($descMax) {
							$ml = round(($mlpor / 100 * $valor_custo), 2);
							return round($ml - ($descMaxpor / 100 * $ml), 2);
						}
					} else {
						return "";
					}
				} else {
					return "";
				}

			}
			
			//funcao para selecionar a mascara nas input text
			##  Tipos de mascara ##
			/*
			 * Integer Telefone TelefoneCall Cpf Cep Cnpj Romanos Data Hora Valor Valor2 Valor3 Area Placa
			 */
			function mascara($mascara, $tamanho = null, $codExtra = null , $onKeyDown = null, $onKeyPress = null, $onKeyUp = null) {
				$cod = "onKeyDown='Mascara(this,$mascara); $onKeyDown' onKeyPress='Mascara(this,$mascara); $onKeyPress' onKeyUp='Mascara(this,$mascara); $onKeyUp'";
			
				if ($tamanho) {
					$cod .= " maxlength='$tamanho'";
				}
				if ($codExtra) {
					$cod .= " " . $codExtra;
				}
			
				return $cod;
			}

			extract($_GET);
			if(isset($nome) and isset($email) and isset($tel) and isset($produto)){
				$corpo = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
				$corpo .= "<html>";
				$corpo .= "<head>";
				$corpo .= "<meta http-equiv=\"content-Type\" content=\"text/html; charset=UTF-8\" />";
				$corpo .= "<title>Delphos</title>";
				$corpo .= "</head>";
				$corpo .= "<body>";
				$corpo .= "<div style=\"";
				$corpo .= "font-size:18px;";
				$corpo .= "font-family: Arial, Helvetica, sans-serif;";
				$corpo .= "border: solid 2px #4F0E11;";
				$corpo .= "padding: 10px;";
				$corpo .= "border-radius: 10px;";
				$corpo .= "\" >";
				$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/boneco_duvida.png' style='float: right;'>";
				$corpo .= "<a href='http://www.rocketsolution.com.br'>";
				$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/logo.png' style='margin-right: 30px; margin-bottom: 20px;'><br>";
				$corpo .= "</a>";
				$corpo .= "Cliente solicitou maiores informações sobre um produto.<br>";
				$corpo .= "Nome: <b style='color:red;'>$nome</b><br>";
				$corpo .= "Telefone: <b style='color:red;'>$tel</b><br>";
				$corpo .= "E-mail: <b style='color:red;'>$email</b><br>";
				$corpo .= "Produto: <b style='color:red;'>$produto</b><br>";
				$corpo .= "I.A. Delphos";
				$corpo .= "</div>";
				$corpo .= "</body>";
				$corpo .= "</html>";
			
				$headers = "MIME-Version: 1.0\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= 'From: falecom@rocketsolution.com.br' . "\r\n";
				$headers .= 'Reply-To: falecom@rocketsolution.com.br' . "\r\n";
				$headers .= 'X-Mailer: PHP/' . phpversion();
			
				if (mail("falecom@rocketsolution.com.br", "Delphos - Venda!", $corpo, $headers)) {
					$msg = "Sua solicitação foi enviada e em breve retornaremos a você.";
				} else {
					$msg = "Aconteceu um problema para enviar sua solicitação. Tente novamente mais tarde.";
				}
			}else{
				$msg = null;
			}
			echo $msg;


		if($sorteio>30){

			$produtoMax = 4;
			
			$sql = query("select * from produto where status='1'");
			$produtoQtd = mysqli_num_rows($sql);

			do {
				$produto1 = mt_rand(0, $produtoQtd - 1);
				$produto2 = $produto1 + $produtoMax;
			} while($produto2>$produtoQtd);

			$sql = query("select * from produto where status='1' limit $produto1, $produtoMax");
			echo "<div class='container'>";
			echo "<div class='main' style='width: 100%;'>";
			echo "<div id='cbp-qtrotator' class='cbp-qtrotator' style='width: 100%;'>";

			for ($i = 0; $i < mysqli_num_rows($sql); $i++) {
				extract(mysqli_fetch_assoc($sql));

				echo "<div class='cbp-qtcontent'>";
				echo "<img src='img/ofertas_rocket.png' style='float:left; margin-left: 0;'>";
				echo "<img src='" . showImagemProduto($id) . "'>";
				echo "<blockquote>";
				echo "<h1>$nome</h1>";
				$descricao = str_replace("<p>", "", $descricao);
				$descricao = str_replace("</p>","<br>", $descricao);
				echo "<h3>$descricao</h3>";
				echo "<p style='float:right;'>R$ " . real(precoProduto($id, true))."</p>";
				echo "<footer></footer>";
				echo " <input type='button' onclick='contratar($id);' value='Maiores informações'></footer>";
				echo "</blockquote>";
				echo "</div>";
				
				echo "<div style='display:none;' id='$id'>";
					echo "<form method='get' action='index.php' enctype='multipart/form-data'>";
						echo "<label for='nome'>Nome</label><br>";
  						echo "<input type='text' name='nome' placeholder='Jonathan'><br>";
						echo "<label for='email'>E-mail</label><br>";
  						echo "<input type='text' name='email' placeholder='jonathan@gmail.com'><br>";
						echo "<label for='tel'>Telefone</label><br>";
  						echo "<input type='text' name='tel' placeholder='(88) 1234.1234' ".mascara("Telefone", 14)."><br>";
  						echo "<input type='hidden' name='produto' value='$id $nome'><br>";
						echo "<input type='submit' value='Enviar'>";
					echo "</form>";
				echo "</div>";

			}
			echo "</div></div></div>";
		}else{
			echo "<iframe width='640' height='360' src='http://www.youtube.com/embed/K-frEtsIAfw?rel=0' frameborder='0' allowfullscreen></iframe>";
		}
		
		?>
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.cbpQTRotator.min.js"></script>
		<script>
			$(function() {
				/*
				 - how to call the plugin:
				 $( selector ).cbpQTRotator( [options] );
				 - options:
				 {
				 // default transition speed (ms)
				 speed : 700,
				 // default transition easing
				 easing : 'ease',
				 // rotator interval (ms)
				 interval : 8000
				 }
				 - destroy:
				 $( selector ).cbpQTRotator( 'destroy' );
				 */

				$('#cbp-qtrotator').cbpQTRotator({
					speed : 500,
					easing : 'ease',
					interval : 9000
				});

			});
			
			function contratar(produto){
				$('.cbp-qtcontent').hide(500);
				$('#'+produto).show(500);
				
			}
		</script>
	</body>
</html>
