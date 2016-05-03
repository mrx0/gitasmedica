<?php

$file = fopen('paths.js', 'r');
$text = fread($file, filesize('paths.js'));
fclose($file);
//$file = fopen('path2.js', 'w');
$i=0;
$t = '0';
//echo $text;
//echo strpos($text, 'Berrdlarus');
while (strpos($text, 'path:') != FALSE){
	//echo strpos($text, 'Berrdlarus').'<br />';
	$i++;
	$t = mb_substr($text, strpos($text, 'path:')+7, strpos($text, "z'")-strpos($text, 'path:')-6);
	echo '
				<div id="t'.$i.'root1"
					status-path=\'
					"stroke": "#74675C", 
					"stroke-width": 1, 
					"fill": "#FC0", 
					"fill-opacity": 1\' 
					class="mapArea" 
					data-path="'.$t.'">
				</div>
	';
	
	
	$text = substr_replace($text, '', strpos($text, 'path:'), strpos($text, "z'")-strpos($text, 'path:')+2);

}
echo $text;
//fwrite($file, $text);
//fclose($file);

?>