<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
    
    //caso o post da musculo e o get da musculoID n existir ou seja caso o usuario abrir a pagina pela primeira vez
    if (empty($_POST["musculo"]) and empty($_GET["musculoID"])){
        echo "<form method='post' action='musculo.php' enctype='multipart/form-data'>";
            echo "Digite o nome do musculo:<br>";
            echo "<input type='text' name='musculo'><br>";
            echo "Uma breve descri&ccedil;&atilde;o<br>";
            echo "<textarea name='descricao'></textarea><br>";
            echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
        echo "</form>";
    } elseif(!empty($_POST["musculo"]) and empty($_GET["musculoID"])){// caso o usuario tiver clicado para inserir uma nova musculo
        extract($_POST);// musculo descricao
        if (mysqli_num_rows(query("select * from musculos where nome='$musculo'"))){//verifica se a musculo q ele qr inserir ja existe
            echo "J&aacute; existe uma musculo com esse nome $musculo.<br>";
            echo "<meta http-equiv=Refresh content='3; URL=musculo.php'>";
        }else{// caso n exista conflito de nomes de musculo inserir a musculo
            $sql = query("insert into musculos (nome, descricao) values ('$musculo', '$descricao')");
            if (mysqli_affected_rows($conexao)){
                echo "Musculo $musculo inserida no Banco de Dados.<br>";
                echo "<meta http-equiv=Refresh content='3; URL=musculo.php'>";
            }else{
                include_once "../inc/msgErro.php";
            }
        }
    }
    if (empty($_GET["musculoID"])){// exibir as musculo existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
    //exibir as musculos existentes.
        echo "<table>";
        echo "<tr>";
        echo "<td>ID</td>";
        echo "<td>Nome do musculo</td>";
        echo "<td>Descri&ccedil;&atilde;o</td>";
        echo "</tr>";
        
        $sql= query("select * from musculos order by nome");
        
        
        while ($reg = mysqli_fetch_assoc($sql)){
            extract($reg); // id nome descricao
            echo "<tr>";
            echo "<form method='get' action='musculo.php' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='musculoID' value='$id'>";
            echo "<td><input type='submit' value='$id'></td>";
            echo "<td>$nome</td>";
            echo "<td>$descricao</td>";
            echo "</form>";
            echo "</tr>";
        }
        echo "</table>";
    }else{//formulario para editar  musculo
        extract($_GET); //musculoID
        
        $sql= query("select * from musculos where id='$musculoID'");
        extract($reg = mysqli_fetch_assoc($sql)); //id nome descricao
        echo "<form method='post' action='musculo.php' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='op' value='editar'>";
            echo "<input type='hidden' name='id' value='$id'>";
            echo "Nome do musculo:<br>";
            echo "<input type='text' name='musculo' value='$nome'><br>";
            echo "Uma breve descri&ccedil;&atilde;o<br>";
            echo "<textarea name='descricao'>$descricao</textarea><br>";
            echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
        echo "</form>";
    
    }
}else{
    extract($_POST);// op id musculo descricao
    if (mysqli_num_rows(query("select * from musculos where nome='$musculo' and id<>'$id'"))){//verifica se a musculo q ele qr inserir ja existe
        echo "J&aacute; existe uma musculo com esse nome $musculo.<br>";
        echo "<meta http-equiv=Refresh content='3; URL=musculo.php'>";
    }else{// caso n exista conflito de nomes de musculo inserir a musculo
        $sql = query("update musculos set nome='$musculo', descricao='$descricao' where id='$id'");
        if (mysqli_affected_rows($conexao)){
            echo "Musculo $musculo atualizada no Banco de Dados.<br>";
            echo "<meta http-equiv=Refresh content='3; URL=musculo.php'>";
        }else{
            include_once "../inc/msgErro.php";
        }
    }
}

    
//end all

include_once "../templates/downLoginImp.inc.php";

?>
<script language="JavaScript" type="text/javascript">
    window.opener.location.reload(); 
</script> 