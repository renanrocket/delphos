<html>
<head>
</head>

<title>
Delphos
</title>

<link rel='shortcut icon' href='img/ico.png'>
<meta http-equiv='content-Type' content='text/html; charset=UTF-8' />

<style type='text/css'>

	@import url(http://fonts.googleapis.com/css?family=Montserrat);

	
	body{
		padding: 0px;
		margin: 0px;
		font-family: "Montserrat", sans-serif;
		font-size: 100%;
	}
	a{
		text-decoration: none;
	}
	#div1{
		background: #47a3da no-repeat 45% 0 fixed;
		background-image: url('img/back_home.jpg');
		background-size: cover;
  		width:100%;
  		height:100%;
	}
	#span_logo{
		position: absolute;
		top: 15px;
		color: white;
		padding-left: 130px;
		height: 50px;
	}
	#span_logo p{
		color: white;
	}
	#span_logo img{
		height: 40px;
		position: absolute;
		top: 0px;
		left: 15px;
	}
	#span_login{
		position: absolute;
		top: 30px;
		right: 30px;
	}
	#span_login a{
		padding: 10px;
		border: 1px solid #FFF;
		border-radius: 5px;
		color: white;
		-webkit-transition: all 1s ease-in-out;
		-moz-transition: all 1s ease-in-out;
		-o-transition: all 1s ease-in-out;
		transition: all 1s ease-in-out;
	}
	#span_login a:hover{
		background-color: #5b0508;
		-webkit-transition: all 1s ease-in-out;
		-moz-transition: all 1s ease-in-out;
		-o-transition: all 1s ease-in-out;
		transition: all 1s ease-in-out;
	}
	#span_login a:active{
		background-color: white;
		
	}
	
	#mid{
		position: absolute;
		top: 90px;
		width: 100%;
		text-align: center;
		color: white;
	}
	#mid input[type='text']{
		font-family: "Montserrat", sans-serif;
		border: solid 3px white;
		width:300px;
		padding: 10px;
		padding-right: 115px;
		position: relative;
		left:49px;
		-webkit-appearance: none;
		border-radius: 4px;
	}
	#mid input[type='button']{
		font-family: "Montserrat", sans-serif;
		border: none;
		padding: 10px;
		padding-top: 11px;
		padding-bottom: 11px;
		position: relative;
		left:-64px;
		background-color: #5b0508;
		color: white;
		border-radius: 4px;
		cursor: pointer;
	}
	#mid input[type='button']:hover{
		background-color: #89090d;
	}
	#back{
		display: none;
		width: 100%;
		height: 100%;
	}
	#circle{
		width: 300px;
		height: 300px;
		background-color: #47a3da;
		border-radius: 500px;
		position: absolute;
		top: 15%;
		left: 32%;
		text-align: center;
		padding: 100px;
		display: inline-block;
		color: white;
	}
	#div2{
		background: #FFF no-repeat 50% 50% fixed;
		background-image: url('img/logo_delphos.png');
		opacity: 0.5;
  		width:100%;
  		height:100%;
	}
	#div2 span{
		opacity: 1;
	}
</style>
<script type='text/javascript' src='js/jquery.js'></script> <!-- biblioteca jquery 1.9.1 -->
<script type='text/javascript'>
	function acessar(){
		$('#back').toggle(1000);
	}
</script>


<body>

<div id='div1'>
	<span id='span_logo'>
		<a href='http://www.rocketsolution.com.br'>
			<img src='img/logo-white.png'>
			<p>
				Soluções Rápidas
			</p>
		</a>
	</span>
	<span id='span_login'>
		<a href='login.php' onclick='acessar();'>Acessar sua conta</a>
	</span>
	<span id='mid'>
		<h1>Uma excelente forma de administrar o seu negócio.</h1>
		<p>Ainda não tem sua assinatura?</p>
		<form method='get' action='gestor/cadastrarEmpresaUsuario.php' enctype='multipart/form-data'>
			<input type='text' name='email' placeholder='Digite seu e-mail'>
			<input type='button' value='Experimente'>
		</form>
	</span>
	<div id='back'>
		<span id='circle'>
			<form class='form' action='login.php' enctype='multipart/form-data'>
				<img src='img/ico_white.png'><br>
				<label for='empresa'>Nome da Empresa</label>
				<input type='text' name='empresa' autocomplete='off' placeholder='Rocket Solution'>
				<input type='hidden' name='id_empresa'>
			</form>
		</span>
	</div>
	
</div>
<div id='div2'>

</div>


</body>
</html>