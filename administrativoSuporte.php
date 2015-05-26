<?php
session_start();
include "templates/upLogin.inc.php";

$conn = TConnection::open(ALCUNHA);

extract($_POST);

$id_usuario = getIdCookieLogin($_COOKIE["login"]);
$email = registro($id_usuario, 'usuario', 'email');
$telefone = registro($id_usuario, 'usuario', 'telefone1');



if(!isset($prioridade)){
	?>
	<script type="text/javascript">
		function showTipo(valor){
			$('#Outro').hide();
			$('#Ferramenta_com_defeito').hide();
			$('#Lentidao').hide();
			$('#'+valor).show();
		}
		$(function(){
			showTipo($('#tipo').val());
		});

		function filtro(){
			var retorno = true;
			var alerta = '';
			if($('input[name="captcha"]').val() != $('input[name="captcha2"]').val()){
				retorno = false;
				alerta = 'Captcha difernte da imagem.';
			}
			if(!retorno){
				alert(alerta);
			}
			return retorno;
		}
	</script>
	<form style='width:50%;' class='form' method="post" action="administrativoSuporte.php" enctype="multipart/form-data" onsubmit='return filtro();'>
		<div class='column'>
			<label for='prioridade'>Prioridade do suporte</label>
			<select id='prioridade' name='prioridade'>
				<option value='Baixa'>Baixa</option>
				<option value='Média'>Média</option>
				<option value='Alta'>Alta</option>
			</select>
			<label for='tipo'>Tipo de problema</label>
			<select id='tipo' name='tipo' onchange="showTipo(this.value);">
				<option value='Outro'>Outro</option>
				<option value='Ferramenta_com_defeito'>Ferramenta com defeito</option>
				<option value='Lentidao'>Lentidão</option>
				<option value='Financeiro'>Financeiro</option>
			</select>

			<span id='Ferramenta_com_defeito' align='left'>
			<label>Marque a ferramenta que encontra-se com defeito</label>
				<?php



				$criterio = new TCriteria;
				$criterio->add(new TFilter('id_usuario', '=', $id_usuario));

				$sql= new TSqlSelect;
				$sql->setEntity('credenciais');
				$sql->addColumn('ferramenta');
				$sql->setCriteria($criterio);
				$result = $conn->query($sql->getInstruction());
				for($i=0; $i<$result->rowCount(); $i++){
					extract($result->fetch(PDO::FETCH_ASSOC));
					
					$criterio = new TCriteria;
					$criterio->add(new TFilter('ferramenta', '=', $ferramenta));

					$sql= new TSqlSelect;
					$sql->setEntity('ferramentas');
					$sql->addColumn('nome');
					$sql->setCriteria($criterio);
					$result2 = $conn->query($sql->getInstruction());
					extract($result2->fetch(PDO::FETCH_ASSOC));
					echo "<label for='$ferramenta' style='padding:0px;'>";
					echo "<input id='$ferramenta' type='checkbox' name='Ferramenta_com_defeito[]' value='$ferramenta'> ";
					echo "$nome</label><br>";
				}

				?>
			</span>
			<span id='Lentidao'>
				<label>
				Você já verificou a velocidade da sua internet com sua operadora, 
				e utilizou a ferramenta de velocímetro <a target='black_' href='http://www.speedtest.net/pt/'>Speed Test</a>?
				</label>
			</span>
			<span>
				<label>Algo que você queira acrescentar no pedido de suporte?</label>
				<textarea name='outro' class='ckeditor'></textarea>	
			</span>

			<label for='email'>Confirme seu e-mail</label>
			<input id='email' type='text' class='email' value='<?php echo $email ?>'>
			<label for='telefone'>Confirme seu Telefone</label>
			<input type='text' class='email' value='<?php echo $telefone ?>' <?php echo mascara('Telefone', 14) ?>>
			<?php
				
				include("plugins/simple-php-captcha-master/simple-php-captcha.php");
				$_SESSION['captcha'] = simple_php_captcha();
				echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code">';

			?>
			<input type='hidden' name='captcha' value='<?php echo $_SESSION['captcha']['code']?>'>
			<input type='text' class='email' name='captcha2' placeholder='Digite o que você vê na imagem'>
			<div class='submit-wrap'>
				<input type='submit' value='Enviar' class='submit'>
			</div>
		</div>
	</form>
	<?php
}else{

	

	$corpo = "Solicitação de suporte\n";
	$corpo.= "Prioridade: $prioridade\n";
	$corpo.= "Natureza do suporte: $tipo\n";
	if($tipo=='Ferramenta_com_defeito'){
		$corpo.= "Ferramentas com defeito: ";
		foreach ($Ferramenta_com_defeito as $key => $value) {
			$corpo.=" $value\n";
		}
	}
	$corpo.= "E-mail: $email\n";
	$corpo.= "Telefone: $telefone\n";



	$email = new enviar_email($email, EMAIL_ROCKET, 'Pedido de suporte $prioridade', $corpo);
	if($email->enviarEmail()){
		echo "Seu pedido de suporte foi solicitado e em breve entraremos em contato com você ";
		echo "atravês do seu contato $email $telefone.";
	}else{
		echo "Infelizmente não foi possível enviar o pedido de suporte.<br>";
		echo "Por favor tente entrar em contato atravês do e-mail ".EMAIL_ROCKET;
	}

}
	
//end all
include "templates/downLogin.inc.php";

?>