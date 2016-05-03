<?php

$file = fopen('paths.js', 'r');
$text = fread($file, filesize('paths.js'));
fclose($file);
$file = fopen('path2.js', 'w');
$i=0;
//echo $text;
//echo strpos($text, 'Berrdlarus');
while (strpos($text, 'Berrdlarus') != FALSE){
	//echo strpos($text, 'Berrdlarus').'<br />';
	$i++;
	$text = substr_replace($text, 'ttt'.$i, strpos($text, 'Berrdlarus'), strlen('Berrdlarus'));

}
echo $text;
fwrite($file, $text);
fclose($file);

?>