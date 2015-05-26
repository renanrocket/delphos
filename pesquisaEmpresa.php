<?php
include "templates/upLogin.inc.php";

echo "<center>";
    echo "<table id='gradient-style' summary='Resultado da pesquisa'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th scope='col'>ID</th>";
                echo "<th scope='col'>Nome</th>";
                echo "<th scope='col'>Endereco</th>";
                echo "<th scope='col'>Telefone</th>";
            echo "</tr>";
        echo "</thead>";
        $sql = query("select id, razao_social, nome, endereco, numero, complemento, bairro, cidade, estado, fone1 from empresa");
        $linha = mysqli_num_rows($sql);
        echo "<tfoot>";
            echo "<tr>";
                echo "<td colspan='6' align='center'>Existem $linha empresa(s) cadastrada(s)</td>";
            echo "</tr>";
        echo "</tfoot>";

        echo "<tbody>";
for($i=0; $i<$linha; $i++){
    extract(mysqli_fetch_assoc($sql));
    echo "<tr>";
    echo "<form method='get' action='cadastrarEmpresa.php' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='id' value='$id'>";
        echo "<input type='hidden' name='op' value='visualizar'>";
        echo "<td><input name='id' type='submit' value='$id'></td>";
        echo "<td>$razao_social $nome</td>";
        echo "<td>$endereco, $bairro - ".registro($cidade, "cidades", "nome" , "cod_cidades")." - ".registro($estado, "estados", "sigla", "cod_estados")."</td>";
        echo "<td style='white-space:nowrap;'>$fone1</td>";
    echo "</form>";
    echo "</tr>";
}

echo "</tbody>
    </table>
</center>";

include "templates/downLogin.inc.php";
?>

