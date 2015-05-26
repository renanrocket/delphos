<?php
include_once "../templates/upLoginImp.inc.php";
?>
<style type="text/css">
	body{
		margin: 0;
		padding: 0;
		text-align: center;
	}
	#div_main{
		width: 744px;
		height: 1051px;
		padding:0;
		margin:0;
	}
	#span_top{
		height: 42px;
		width: 100%;
		display: block;
	}
	.div_borda{
		width: 16px;
		height: 90px;
		display: table-cell;
	}
	.div_etiqueta{
		background-color: white;
		border-bottom: solid 2px white;
		width: 229px;
		height: 90px;
		display: table-cell;
	}
	.div_etiqueta_dentro{
		text-transform: uppercase;
		padding: 1px;
	}
	.div_mid{
		width: 16px;
		height: 90px;
		display: table-cell;
	}
	.rotate{
		font-weight: bolder;
		font-size: 14px;
		padding: 0;
		margin: 0;
		float:right;
		position: relative;
		bottom: -30px;
		right: -8px;
		-webkit-transform: rotate(-90deg);
		-moz-transform: rotate(-90deg);
		-ms-transform: rotate(-90deg);
		-o-transform: rotate(-90deg);
		transform: rotate(-90deg);

		/* also accepts left, right, top, bottom coordinates; not required, but a good idea for styling */
		-webkit-transform-origin: 50% 50%;
		-moz-transform-origin: 50% 50%;
		-ms-transform-origin: 50% 50%;
		-o-transform-origin: 50% 50%;
		transform-origin: 50% 50%;

		/* Should be unset in IE9+ I think. */
		filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
	}
</style>

<?php

extract($_GET);
echo "<div id='div_main'>";
	echo "<span id='span_top'></span>";
for($i=0; $i<30; $i+=3){
	if(isset($item[$i])){
		if($idItem[$i]!=0){
			$sql = query("select * from produto where id='$idItem[$i]'");
			extract(mysqli_fetch_assoc($sql));
			$cod1 = "<div class='div_etiqueta_dentro'>";
			$cod1 .= "$nome $modelo";
			$cod1 .= "<span class='rotate'>R$ ".real(precoProduto($id, "true"))."</span>";
			if(!$cod_barra){
				$cod_barra = $id;
			}
			$cod_barra = str_pad($cod_barra, 14, "0", STR_PAD_LEFT);
			$cod1 .= "<iframe src='../inc/codBarras.inc.php?cod=$cod_barra&w=160&h=50' id='codBarrasH' style='width:160px; height:50px; display:inline-block;'></iframe>";
			$cod1 .= "</div>";	
		}else{
			$cod1 = "";
		}
	}else{
		$cod1 = "";
	}
	if(isset($item[$i+1])){
		if($idItem[$i+1]!=0){
			$sql = query("select * from produto where id='".$idItem[$i+1]."'");
			extract(mysqli_fetch_assoc($sql));
			$cod2 = "<div class='div_etiqueta_dentro'>";
			$cod2 .= "$nome $modelo";
			$cod2 .= "<span class='rotate'>R$ ".real(precoProduto($id, "true"))."</span>";
			if(!$cod_barra){
				$cod_barra = $id;
			}
			$cod_barra = str_pad($cod_barra, 14, "0", STR_PAD_LEFT);
			$cod2 .= "<iframe src='../inc/codBarras.inc.php?cod=$cod_barra&w=160&h=50' id='codBarrasH' style='width:160px; height:50px; display:inline-block;'></iframe>";
			$cod2 .= "</div>";	
		}else{
			$cod2 = "";
		}
	}else{
		$cod2 = "";
	}
	if(isset($item[$i+2])){
		if($idItem[$i+2]!=0){
			$sql = query("select * from produto where id='".$idItem[$i+2]."'");
			extract(mysqli_fetch_assoc($sql));
			$cod3 = "<div class='div_etiqueta_dentro'>";
			$cod3 .= "$nome $modelo";
			$cod3 .= "<span class='rotate'>R$ ".real(precoProduto($id, "true"))."</span>";
			if(!$cod_barra){
				$cod_barra = $id;
			}
			$cod_barra = str_pad($cod_barra, 14, "0", STR_PAD_LEFT);
			$cod3 .= "<iframe src='../inc/codBarras.inc.php?cod=$cod_barra&w=160&h=50' id='codBarrasH' style='width:160px; height:50px; display:inline-block;'></iframe>";
			$cod3 .= "</div>";	
		}else{
			$cod3 = "";
		}
	}else{
		$cod3 = "";
	}
	echo "<div class='div_borda'></div>";
	echo "<div class='div_etiqueta'>".$cod1."</div>";
	echo "<div class='div_mid'></div>";
	echo "<div class='div_etiqueta'>".$cod2."</div>";
	echo "<div class='div_mid'></div>";
	echo "<div class='div_etiqueta'>".$cod3."</div>";
	echo "<div class='div_borda'></div>";
	echo "<span style='display:table-row;'></span>";
}
echo "</div>";



include_once "../templates/downLoginImp.inc.php";
?>