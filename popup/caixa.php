<?php

include_once "../templates/upLoginImp.inc.php";


//all
if (empty($_POST["op"])){//se caso a operacao n existir, ou seja n for editar entao ela ira acontecer o seguinte
    
    //caso o post da caixa e o get da caixaID n existir ou seja caso o usuario abrir a pagina pela primeira vez
    if (empty($_POST["caixa"]) and empty($_GET["caixaID"])){
        echo "<form method='post' action='caixa.php' enctype='multipart/form-data'>";
            echo "Digite o nome do novo caixa:<br>";
            echo "<input type='text' name='caixa'><br>";
            echo "Uma breve descri&ccedil;&atilde;o<br>";
            echo "<textarea name='descricao'></textarea><br>";
            echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
        echo "</form>";
    } elseif(!empty($_POST["caixa"]) and empty($_GET["caixaID"])){// caso o usuario tiver clicado para inserir uma nova caixa
        extract($_POST);// caixa descricao
        if (mysqli_num_rows(query("select * from caixa where nome='$caixa'"))){//verifica se a caixa q ele qr inserir ja existe
            echo "J&aacute; existe uma caixa com esse nome $caixa.<br>";
            echo "<meta http-equiv=Refresh content='3; URL=caixa.php'>";
        }else{// caso n exista conflito de nomes de caixa inserir a caixa
            $sql = query("insert into caixa (nome, descricao) values ('$caixa', '$descricao')");
            if (mysqli_affected_rows($conexao)){
                echo "Caixa $caixa inserida no Banco de Dados.<br>";
                echo "<meta http-equiv=Refresh content='3; URL=caixa.php'>";
            }else{
                include_once "../inc/msgErro.php";
            }
        }
    }
    if (empty($_GET["caixaID"])){// exibir as caixa existentes, mesmo o usuario fazendo procedimentos de formularios acimas, ele ira
    //exibir as caixas existentes.
        echo "<table>";
        echo "<tr>";
        echo "<td>ID</td>";
        echo "<td>Nome da caixa</td>";
        echo "<td>Descri&ccedil;&atilde;o</td>";
        echo "</tr>";
        
        $sql= query("select * from caixa");
        
        
        while ($reg = mysqli_fetch_assoc($sql)){
            extract($reg); // id nome descricao
            echo "<tr>";
            echo "<form method='get' action='caixa.php' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='caixaID' value='$id'>";
            echo "<td><input type='submit' value='$id'></td>";
            echo "<td>$nome</td>";
            echo "<td>$descricao</td>";
            echo "</form>";
            echo "</tr>";
        }
        echo "</table>";
    }else{//formulario para editar  caixa
        extract($_GET); //caixaID
        
        $sql= query("select * from caixa where id='$caixaID'");
        extract($reg = mysqli_fetch_assoc($sql)); //id nome descricao
        echo "<form method='post' action='caixa.php' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='op' value='editar'>";
            echo "<input type='hidden' name='id' value='$id'>";
            echo "Nome da caixa:<br>";
            echo "<input type='text' name='caixa' value='$nome'><br>";
            echo "Uma breve descri&ccedil;&atilde;o<br>";
            echo "<textarea name='descricao'>$descricao</textarea><br>";
            echo "<input type='submit' class='btnEnviar' value='Enviar'><br>";
        echo "</form>";
    
    }
}else{
    extract($_POST);// op id caixa descricao
    if (mysqli_num_rows(query("select * from caixa where nome='$caixa' and id<>'$id'"))){//verifica se a caixa q ele qr inserir ja existe
        echo "J&aacute; existe uma caixa com esse nome $caixa.<br>";
        echo "<meta http-equiv=Refresh content='3; URL=caixa.php'>";
    }else{// caso n exista conflito de nomes de caixa inserir a caixa
        $sql = query("update caixa set nome='$caixa', descricao='$descricao' where id='$id'");
        if (mysqli_affected_rows($conexao)){
            echo "Caixa $caixa atualizada no Banco de Dados.<br>";
            echo "<meta http-equiv=Refresh content='3; URL=caixa.php'>";
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