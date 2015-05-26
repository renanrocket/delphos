<?php
include "templates/upLogin.inc.php";


//all
extract($_GET);
extract($_POST);

if(isset($op)){
    
    if($op=="novo" or $op=="editar"){
            
        $valida = true;
        $info = "";
        $data_fundacao = formataDataInv($data_fundacao);
        
        if(isset($_FILES['imgsrc']['name'])){
            //tratando a imagem
            $pasta = END_ARQ."logo/";
            if(!file_exists($pasta)){
                mkdir($pasta, 755, true);
            }
            /* formatos de imagem permitidos */
            $permitidos = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
            $nome_imagem = $_FILES['imgsrc']['name'];
            $tamanho_imagem = $_FILES['imgsrc']['size'];
            
            /* pega a extensão do arquivo */
            $ext = strtolower(strrchr($nome_imagem, "."));
            
            /*  verifica se a extensão está entre as extensões permitidas */
            if (in_array($ext, $permitidos)) {
            
                /* converte o tamanho para KB */
                $tamanho = round($tamanho_imagem / 1024);
            
                if ($tamanho < 1024) {//se imagem for até 1MB envia
                    $nome_atual = md5(uniqid(time())) . $ext;
                    //nome que dará a imagem
                    $tmp = $_FILES['imgsrc']['tmp_name'];
                    //caminho temporário da imagem
            
                    /* se enviar a foto, insere o nome da foto no banco de dados */
                    if (move_uploaded_file($tmp, $pasta . $nome_atual)) {
                        $instrucaoImg = "update empresa set imgsrc='" . $pasta . $nome_atual . "' ";
                        if($op=="editar"){
                            $instrucaoImg .= "where id='$id'";
							if (file_exists(registro($id, "empresa", "imgsrc"))) {
								unlink(registro($id, "empresa", "imgsrc"));
							}
                        }
                        //echo "<img src='$pasta" . $nome_atual . "' id='previsualizar'>";
                        //imprime a foto na tela
                    } else {
                        $info .= "Falha ao enviar a imagem.<br>";
                        $valida= false;
                    }
                } else {
                    $info .= "A imagem deve ser de no máximo 1MB.<br>";
                    $valida= false;
                }
            } else {
                $info .= "A logomarca da empresa é obrigatória.<br>Somente são validos arquivos de imagem.<br>";
                $valida= false;
				$id= $imgsrc = $estado = null;
            }   
        }
        
        
        
        if($valida){
            
            $numero = turnZero($numero);

            if($op=="editar"){
                $instrucao = "UPDATE empresa SET `razao_social` = '$razao_social', `nome` = '$nome', `cnpj` = '$cnpj', ";
                $instrucao .= "`data_fundacao` = '$data_fundacao', `email` = '$email', `fone1` = '$fone1', `fone2` = '$fone2', `fone3` = '$fone3', ";
                $instrucao .= "`endereco` = '$endereco', `numero` = '$numero', `complemento` = '$complemento', `bairro` = '$bairro', `cidade` = '$cidade', ";
                $instrucao .= "`estado` = '$estados', `cep` = '$cep', `usarTimbrado` = '$usarTimbrado' where";
                //atualizar banco de dados master
                $sql = query(str_replace("UPDATE empresa SET", "UPDATE cliente SET", $instrucao)." id='".$_COOKIE["id_empresa"]."'", $conexaoMaster);
                $instrucao .= " id='$id'";
                $info = "Empresa editada com sucesso.";
            }else{
                $instrucao = "INSERT INTO `empresa` (`razao_social`,`nome`,`cnpj`,`data_fundacao`,`email`,";
                $instrucao.= "`fone1`,`fone2`,`fone3`,`endereco`,`numero`,`complemento`,`bairro`,`cidade`,`estado`,`cep`,";
                $instrucao.= "`usarTimbrado`) VALUES ('$razao_social','$nome','$cnpj','$data_fundacao','$email','$fone1',";
                $instrucao.= "'$fone2','$fone3','$endereco','$numero','$complemento','$bairro','$cidade','$estados','$cep','$usarTimbrado')";
                $info = "Empresa cadastrada com sucesso.";
            }
            
            //correção do timbrado
            //caso o usuario sete essa empresa como como timbrado então devemos deletar todas as outras q estiver com o timbrado marcaod como sim.
            $sql = query("update empresa set usarTimbrado=0");
                
            $sql = query($instrucao);
            if($op=="novo"){
                $id = ultimaId("empresa");
                if(mysqli_num_rows(query("select * from empresa"))==1){
                	$info.= "<br><a href='indexUsuario.php'>Clique aqui</a> para poder visualizar as funções do sistema.";
                }
            }
            if(isset($instrucaoImg)){
                $sql = query($instrucaoImg); 
            }
            empresa("editar", $id);
            info($info);
        }else{
            info($info, "red");
            empresa($op, $id, $imgsrc, $razao_social, $nome, $cnpj, $data_fundacao, $email, $fone1, $fone2, $fone3, $endereco, $numero, $complemento, $bairro, $cidade, $estado , $cep, $usarTimbrado);
        }
        
            
        
    }elseif($op=="visualizar"){
        
        empresa("editar", $id);
    }

    
}else{
    empresa();
}

	
//end all

include "templates/downLogin.inc.php";
?>