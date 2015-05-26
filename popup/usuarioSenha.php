<?php
include_once "../templates/upLoginImp.inc.php";

$sql = query("select id, nome, login from usuario where id='".$_GET["id"]."'");
extract(mysqli_fetch_assoc($sql));
echo "Alterar senha do usuario $nome ($login)";

echo "<form action='usuarioSenha2.php' method='post' enctype='multipart/form-data'>";
echo "<input type='hidden' name='id' value='$id'>";
echo "<table style='width:100%'>";
echo "<tr>";
echo "<td class='td1'>Senha antiga:</td><td class='td2'><input type='password' name='antiga'></td>";
echo "</tr><tr>";
echo "<td class='td1'>Senha nova:</td><td class='td2'><input type='password' name='nova'></td>";
echo "</tr><tr>";
echo "<td class='td1'>Repita a senha nova:</td><td class='td2'><input type='password' name='repitenova'></td>";
echo "</tr><tr>";
echo "<td class='td1' colspan='2'><input type='submit' class='btnEnviar' value='Enviar'></td>";
echo "</tr>";
echo "</table>";
echo "</form>";

include_once "../templates/downLoginImp.inc.php";
?>