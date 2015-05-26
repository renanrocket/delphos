<?php
extract($_COOKIE);
unset($_COOKIE["login"]);
unset($_COOKIE["senha"]);
setcookie("login", "");
setcookie("senha", "");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
        <title>Delphos :: Logout</title>
	    <!--   META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
        <meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
        <style type='text/css'>
            @import url(css/tagsLogin.css);
            @import url(css/classLogin.css);
            @import url(css/idLogin.css);
            @import url(plugins/ResponsiveMultiColumnForm/css/component.css);
        </style>
        <link rel='shortcut icon' href='img/ico.png'>
        
	<body id='bodyLogin'>

		<?php

        #arquivo de cenexão com o banco
        include "inc/funcoes.inc.php";
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
			file_exists("conecta.php") ?
			include "conecta.php" : $conexao = null;
			$sql = query("select conectasrc from cliente where id='".$_COOKIE["id_empresa"]."'");
			extract(mysqli_fetch_assoc($sql));
			include $conectasrc;
            //fim
		}else{
			echo "Impossível se conectar com o banco de dados.";
			die;
		}

        $sql = query("update usuario set online='0' where login='$login' and senha='$senha'");
        echo "<center>";
        echo "<img id='imgindex' src='img/ico_white.png'><br>";
        echo "<div class='form'>";
            echo "<div class='column'>";
                echo "<label>Você está saindo<br>do sistema Delphos.</label>";
                echo "<br><br>";
                echo "<img src='img/cadeado.png'>";
            echo "</div>";
        echo "</div>";
        echo "</center>";

        echo "<meta HTTP-EQUIV='refresh' CONTENT='1;URL=index.php'>";

        include "inc/copy.inc";
        mysqli_close($conexao);
		?>
	</body>
</html>