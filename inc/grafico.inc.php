<?php
extract($_GET);
$tamanho = "width='$width' height='$height'";

$labelsNUM = count($labels);
$datasetsNUM = count($legenda);

for($i=0, $LABELS = null; $i<$labelsNUM; $i++){
	if($i!=0){
		$LABELS .= ", ";
	}
	$LABELS .= "'".$labels[$i]."'";
}
$r1 = 220;
$g1 = 220;
$b1 = 220;

$r2 = 151;
$g2 = 187;
$b2 = 205;

for($i=0, $variacao = 85; $i<10; $i++){
	
	$r1 = mt_rand(0, 220);
	$g1 = mt_rand(0, 220);
	$b1 = mt_rand(0, 220);
	
	//$r2 = mt_rand(0, 220);
	//$g2 = mt_rand(0, 220);
	//$b2 = mt_rand(0, 220);
	
	$fillColor[] = "'rgba(".($r1-$variacao*$i).",".($g1-$variacao*$i).",".($b1-$variacao*$i).",0.5)'";
	//$fillColor[] = "'rgba(".($r2+$variacao*$i).",".($g2+$variacao*$i).",".($b2+$variacao*$i).",0.5)'";
	
	$strokeColor[] = $pointColor[] = "'rgba(".($r1-$variacao*$i).",".($g1-$variacao*$i).",".($b1-$variacao*$i).",1)'";
	//$strokeColor[] = $pointColor[] = "'rgba(".($r2+$variacao*$i).",".($g2+$variacao*$i).",".($b2+$variacao*$i).",1)'";
	
}

$pointStrokerColor = "'#fff'";

function random_color_part() {
    return str_pad(dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
$linha = $datasetsNUM * $labelsNUM;
for($i=0; $i<$linha; $i++){
	$color[] = "'#".random_color()."'";
}

if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
	
	for($i = $j = 1, $data[$j] = null; $i<=$linha; $i++){
		if($i!=1 and (($i - 1)%(count($datasets)/$datasetsNUM))==0 and $datasetsNUM!=1){
			$j++;
			$data[$j] = null;
		}
		$data[$j] .= $datasets[(($i) - 1)];
		if(($i%(count($datasets)/$datasetsNUM))!=0 or $datasetsNUM==1){
			$data[$j] .= ", ";
		}
	}
	
}else{
	for($i = 0; $i<$linha; $i++){
		$valor[] = $datasets[$i];
	}
}

if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
	$contador = $datasetsNUM;
}else{
	$contador = $linha;
}

for($i=1, $DATASETS = $cor = null; $i<=$contador; $i++){
	if($i!=1){
		$DATASETS .= ", \n";
	}
	
	$DATASETS .= "{\n";
	if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
		$DATASETS .= "fillColor : ".$fillColor[$i - 1].",\n";
		$DATASETS .= "strokeColor : ".$strokeColor[$i - 1].",\n";
		if($tipo!="Bar"){
			$DATASETS .= "pointColor : ".$pointColor[$i - 1].",\n";
			$DATASETS .= "pointStrokeColor : ".$pointStrokerColor.",\n";
		}
		$DATASETS .= "data : [".$data[$i]."]\n";
		$cor[] = $fillColor[$i-1];
	}else{
		$DATASETS .= "value : ".$valor[$i - 1].",\n";
		$DATASETS .= "color : ".$color[$i - 1]."\n";
		$cor[] = $color[$i-1];
	}
	
	$DATASETS .= "}\n";
}

if($tipo=="Line" or $tipo=="Bar" or $tipo =="Radar"){
	$CharData = "{\n";
	$CharData .= "labels: [ $LABELS ] ,\n";
	$CharData .= "datasets: [\n $DATASETS \n] \n}";
}else{
	$CharData = "[\n";
	$CharData .= "$DATASETS \n]";
}

?>
<!doctype html>
<html>
	<head>
		<script src="../plugins/chart/docs/Chart.js"></script>
		<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
		<style>
			canvas{
			}
		</style>
		<style type="text/css">
			#legenda{
				font-family: arial;
				font-size: 10px;
				text-align: left;
				width: 50px;
				white-space: nowrap;
			}
			#legenda span{
				height: 20px;
				width: 20px;
				padding-right: 10px;
				margin-right: 10px;
			}
		</style>
	</head>
	<body style='background-color:white; padding-top:10px;'>
		<center>
		<canvas id="canvas" <?php echo $tamanho; ?>></canvas>
		<script>
			var ChartData = <?php echo $CharData; ?>
	
			var myRadar = new Chart(document.getElementById("canvas").getContext("2d")).<?php echo $tipo; ?>(ChartData);
		</script>
		
		<?php
			if(count($legenda)>1){
				echo "<div id='legenda'><h3>Legenda</h3>";
				for($l=0; $l<count($legenda); $l++){
					$cor[$l] = str_replace("'", "", $cor[$l]);
					echo "<span style='background-color:".$cor[$l].";'></span>".$legenda[$l]."<br>";
				}
				echo "</div>";
			}
		?>
		</center>
	</body>
</html>
