<?php
/*
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* "PHP & Jquery image upload & crop"
* Date: 2008-11-21
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*/
error_reporting (E_ALL ^ E_NOTICE);
session_start(); //Do not remove this
include "../inc/funcoes.inc.php";
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
	file_exists("../conecta.php") ?
	include "../conecta.php" : $conexao = null;
	$sql = query("select conectasrc, alcunha from cliente where id='".$_COOKIE["id_empresa"]."'");
	extract(mysqli_fetch_assoc($sql));
	define("END_ARQ", CONST_ARQ."/".$alcunha."/");
	include "../".$conectasrc;
	//fim
}else{
	echo "Impossível se conectar com o banco de dados.";
	die;
}
//only assign a new timestamp if the session variable is empty
if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0){
    $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
	$_SESSION['user_file_ext']= "";
}
#########################################################################################################
# CONSTANTS																								#
# You can alter the options below																		#
#########################################################################################################

// Fora estas variáveis existe uma outra: $_GET["idDeletar"] & $_GET["a"]=delete que quando setada irá buscar na tabela esta id e irá deleta-la

$id = $_GET["id"];													// Id de referencia
$atributo_referencia = "id_usuario";								// Qual atributo na tabela este id está referenciado
$atributo_imagem = "imagem";										// Qual o atributo da imagem na tabela
$atributo_miniatura = "miniatura";									// Qual o atributo da miniatura na tabela
$tabela = "usuario_imagem";											// A tabela a qual as imagens seram associadas
$upload_dir = "../".END_ARQ."img_usuario"; 									// The directory for the images to be saved in
$upload_path = $upload_dir."/";										// The path to where the image will be saved
$large_image_prefix = "redimensionado_";							// The prefix name to large image
$thumb_image_prefix = "miniatura_";									// The prefix name to the thumb image
$large_image_name = $large_image_prefix.$_SESSION['random_key'];    // New name of the large image (append the timestamp to the filename)
$thumb_image_name = $thumb_image_prefix.$_SESSION['random_key'];    // New name of the thumbnail image (append the timestamp to the filename)
$max_file = "3"; 													// Maximum file size in MB
$max_width = "500";													// Max width allowed for the large image
$thumb_width = "100";												// Width of thumbnail image
$thumb_height = "100";												// Height of thumbnail image
// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";	// initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext.= strtoupper($ext)." ";
}


##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################
function resizeImage($image,$width,$height,$scale) {
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);  
			break;
    }
	
	chmod($image, 0777);
	return $image;
}
//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);  
			break;
    }
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

//Image Locations
$large_image_location = $upload_path.$large_image_name.$_SESSION['user_file_ext'];
$thumb_image_location = $upload_path.$thumb_image_name.$_SESSION['user_file_ext'];

//Create the upload directory with the right permissions if it doesn't exist
if(!is_dir($upload_dir)){
	mkdir($upload_dir, 0777);
	chmod($upload_dir, 0777);
}

//Check to see if any images with the same name already exist
if (file_exists($large_image_location)){
	if(file_exists($thumb_image_location)){
		$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name.$_SESSION['user_file_ext']."\" alt=\"Miniatura\"/>";
	}else{
		$thumb_photo_exists = "";
	}
   	$large_photo_exists = "<img src=\"".$upload_path.$large_image_name.$_SESSION['user_file_ext']."\" alt=\"Imagem\"/>";
} else {
   	$large_photo_exists = "";
	$thumb_photo_exists = "";
}

if (isset($_POST["upload"])) { 
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
	$userfile_size = $_FILES['image']['size'];
	$userfile_type = $_FILES['image']['type'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
	
	//Only process if the file is a JPG, PNG or GIF and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
		
		foreach ($allowed_image_types as $mime_type => $ext) {
			//loop through the specified image types and if they match the extension then break out
			//everything is ok so go and check file size
			if($file_ext==$ext && $userfile_type==$mime_type){
				$error = "";
				break;
			}else{
				$error = "Apenas os seguintes arquivos <strong>".$image_ext."</strong> são aceitas<br />";
			}
		}
		//check if the file size is above the allowed limit
		if ($userfile_size > ($max_file*1048576)) {
			$error.= "Imagens devem estar com o tamanho maximo de ".$max_file."MB";
		}
		
	}else{
		$error= "Selecione uma imagem para fazer o upload";
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){
		
		if (isset($_FILES['image']['name'])){
			//this file could now has an unknown file extension (we hope it's one of the ones set above!)
			$large_image_location = $large_image_location.".".$file_ext;
			$thumb_image_location = $thumb_image_location.".".$file_ext;
			
			
						
			//put the file ext in the session so we know what file to look for once its uploaded
			$_SESSION['user_file_ext']=".".$file_ext;
			
			move_uploaded_file($userfile_tmp, $large_image_location);
			chmod($large_image_location, 0777);
			
			$imagem = str_replace("../", "", $large_image_location);
			$miniatura = str_replace("../", "", $thumb_image_location);
			
			$instrucao  = "insert into $tabela ($atributo_referencia, $atributo_imagem, $atributo_miniatura) values ";
			$instrucao .= "('$id', '$imagem', '$miniatura')";
			$sql = query($instrucao);
			
			$width = getWidth($large_image_location);
			$height = getHeight($large_image_location);
			//Scale the image if it is greater than the width set above
			if ($width > $max_width){
				$scale = $max_width/$width;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}else{
				$scale = 1;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh the page to show the new uploaded image
		header("location:".$_SERVER["PHP_SELF"]."?id=$id");
		exit();
	}
}

if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//Scale the image to the thumb_width set above
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	//Reload the page again to view the thumbnail
	header("location:".$_SERVER["PHP_SELF"]."?id=$id");
	exit();
}


if ($_GET['a']=="delete"){
//get the file locations
	if(isset($_GET["t"])){
		$large_image_location = $upload_path.$large_image_prefix.$_GET['t'];
		$thumb_image_location = $upload_path.$thumb_image_prefix.$_GET['t'];
		
		$sql = query("delete from $tabela where $atributo_referencia='$id' and $atributo_imagem='$large_image_location'");
	}else{
		$sql = query("select $atributo_imagem as large_image_location, $atributo_miniatura as thumb_image_location from $tabela where id='".$_GET["idDeletar"]."'");
		extract(mysqli_fetch_assoc($sql));
		
		$sql = query("delete from $tabela where $atributo_referencia='$id' and $atributo_imagem='$large_image_location'");
		
		$large_image_location = "../".$large_image_location;
		$thumb_image_location = "../".$thumb_image_location;
	}
	
	if (file_exists($large_image_location)) {
		unlink($large_image_location);
	}
	if (file_exists($thumb_image_location)) {
		unlink($thumb_image_location);
	}
	if(isset($_GET["t"])){
		header("location:".$_SERVER["PHP_SELF"]."?id=$id");	
	}else{
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
			echo "window.opener.location.reload();";
			echo "window.close()"; 
		echo "</script>";
	}
	
	
	
	
	exit(); 
}
?>

<?php
		//js especifico da pagina;
	echo "
		<script type='text/javascript' src='../js/jquery-pack.js'></script>
		<script type='text/javascript' src='../js/jquery.imgareaselect.min.js'></script>
		";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html> 
	<head>
		<title>
		<?php
        
        $titulo = explode("/", $_SERVER['PHP_SELF']);
        count($titulo)>0 ? $linha = (count($titulo) - 1) : $linha = 0;
        
        $sql = query("select nome as Titulo from ferramentas where ferramenta='".$titulo[$linha]."'");
        if(mysqli_num_rows($sql)==0){
            $Titulo = "Delphos";
        }else{
            extract(mysqli_fetch_assoc($sql));
        }
		//set hora certa
		date_default_timezone_set('America/Sao_Paulo');
		
		//includes do php para todas as paginas
		//include "conecta.php";
		//include "inc/funcoes.inc.php";
		
		?>
		</title>
		<!-- 	META TAG PARA CORRIGIR PROBLEMAS DOS ACENTOS PHP -->
		<meta http-equiv="content-Type" content="text/html; charset=UTF-8" /> 
        <style type="text/css">
            @import url(../css/cssImp.css);
        </style>
	</head>
	<body>
	<link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />
<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);
	?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("Voce precisa fazer uma selecao.");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
});

</script>
<?php }?>
<h1>Upload de imagem</h1>
<?php
//Display error message if there are any
if(strlen($error)>0){
	echo "<ul><li><strong>Erro!</strong></li><li>".$error."</li></ul>";
}
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
	echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
	echo "<p><a href=\"".$_SERVER["PHP_SELF"]."?id=$id&a=delete&t=".$_SESSION['random_key'].$_SESSION['user_file_ext']."\">Delete imagens</a></p>";
	echo "<p><a href=\"".$_SERVER["PHP_SELF"]."?id=$id\">Envie outra imagem</a></p>";
	//Clear the time stamp session and user file extension
	$_SESSION['random_key']= "";
	$_SESSION['user_file_ext']= "";
}else{
	if(strlen($large_photo_exists)>0){?>
		<h2>Criando uma miniatura</h2>
		<div align="center">
			<img src="<?php echo $upload_path.$large_image_name.$_SESSION['user_file_ext'];?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
			<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
				<img src="<?php echo $upload_path.$large_image_name.$_SESSION['user_file_ext'];?>" style="position: relative;" alt="Thumbnail Preview" />
			</div>
			<br style="clear:both;"/>
			<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"]."?id=$id";?>" method="post">
				<input type="hidden" name="x1" value="" id="x1" />
				<input type="hidden" name="y1" value="" id="y1" />
				<input type="hidden" name="x2" value="" id="x2" />
				<input type="hidden" name="y2" value="" id="y2" />
				<input type="hidden" name="w" value="" id="w" />
				<input type="hidden" name="h" value="" id="h" />
				<input type="submit" name="upload_thumbnail" value="Salvar miniatura" id="save_thumb" />
			</form>
		</div>
	<hr />
	<?php 	} ?>
	<h2>Selecione a imagem</h2>
	<form name="photo" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"];?>" method="post">
	Imagem: <input type="file" name="image" size="30" /> <input type="submit" name="upload" class='btnEnviar' value="Enviar" />
	</form>
<?php } ?>


<?php

	include_once "../templates/downLoginImp.inc.php";
?>
	
<script language="JavaScript" type="text/javascript">
	window.opener.location.reload(); 
</script>
