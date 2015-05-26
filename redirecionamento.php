<script type='text/javascript' src='js/jquery.js'></script>
<script type="text/javascript">
	$(function() {
		var body_login = document.getElementById('bodyLogin');
		var body_loginW = body_login.offsetWidth;
		var div_login = document.getElementById('login');
		var div_loginW = div_login.offsetWidth;
		var left = (parseInt(body_loginW) - parseInt(div_loginW)) / 2;
		document.getElementById('login').style.left = left + "px";
	}); 
</script>
<script type="text/javascript">
	//evitar que o usuario saia antes de carregar totalmente a pagina
	sairPagina.confirm = false;
</script>
<body id='bodyLogin'>
	<center>
	<div class="form">
		<table class='column'>
			<tr>
				<td align='center'><img src='img/cadeado.png'></td>
				<td align='center'>
				    <h1>Área restrita</h1>
					Você não possui as credenciais
					<br>
					para acessar essa parte do sistema.
					<br>
					Por favor <a href='index.php?end=<?php echo $_SERVER['REQUEST_URI']?>'>volte</a>.
				</td>
			</tr>
		</table>
	</div>
	</center>
</body>
</html>