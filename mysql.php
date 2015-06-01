<?php

extract($_POST);

if(isset($query)){
    //deletar esse codigo apois migração do app.ado
    file_exists("conecta.php") ?
        include "conecta.php" : $conexao = null;

    $sql = query("select conectasrc from cliente where status='1'");
    for($i=0; $i<mysqli_num_rows($sql); $i++){
        extract(mysqli_fetch_assoc($sql));
        file_exists($conectasrc) ? include $conectasrc : $conexao = null;

    }
}else{
    ?>
    <form enctype="multipart/form-data" action="mysql.php">
        <textarea name="query"></textarea><br>
        <input type="submit">
    </form>
    <?php
}

?>