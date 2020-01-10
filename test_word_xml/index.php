<?php

/*
Пример создан сайтом: http://blogjquery.ru/php-word-generaciya
*/

if (isset($_GET['download'])) {
//Скачка файла
Header("Content-Type: x-force-download");
Header('Content-Disposition: attachment; filename="zakaz.doc"');
readfile("zakaz.doc");
}
else {
header('Content-Type: text/html; charset=utf-8');
ini_set("display_errors",1);
error_reporting(E_ALL);
?>



<?php
$tovarname = array(                                                       //товары для записи в таблицу
array('Смартфон LG Phone',   'шт.', '1', 5000, 5000),
array('Компьютер PC Asus',   'шт.', '1', 7000, 7000),
array('Флеш карта USB 16Гб', 'шт.', '2', 800,  1600)
);

$sum=0; 
for ($i=0; $i < count($tovarname); $i++){
	$sum+=$tovarname[$i][4]; 
}  //общая сумма заказа
$iinomer = 0;                                                             //номер п/п в таблице
$tabletrtovari = '';                                                      //товары


//шапка перед таблицей
$tableshapka = '
<w:p w:rsidR="00655BB2" w:rsidRDefault="00655BB2"><w:pPr><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr></w:p>
<w:p w:rsidR="00B80A3E" w:rsidRPr="00293078" w:rsidRDefault="00293078" w:rsidP="00293078"><w:pPr><w:jc w:val="center"/><w:rPr><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r w:rsidRPr="00293078"><w:rPr><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t xml:space="preserve">Заказ №</w:t></w:r><w:r w:rsidRPr="00293078"><w:rPr>
<w:sz w:val="30"/><w:szCs w:val="30"/></w:rPr><w:t>'.mt_rand(1,100).'</w:t></w:r><w:r w:rsidRPr="00293078"><w:rPr><w:b/><w:sz w:val="24"/>
<w:szCs w:val="24"/></w:rPr><w:t xml:space="preserve"> от '.date("d.m.Y").' г.</w:t></w:r></w:p>
';


//товары для записи в word
for($i=0; $i<count($tovarname); $i++) {

$iinomer++;

//строим строки с товарами
$tabletrtovari .= '
<w:tr w:rsidR="005268E3" w:rsidTr="005268E3">

<w:tc><w:tcPr><w:tcW w:w="534" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:proofErr w:type="spellStart"/><w:r>
<w:t>'.$iinomer.'</w:t></w:r><w:proofErr w:type="spellEnd"/><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

<w:tc><w:tcPr><w:tcW w:w="5987" w:type="dxa"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="left"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:r>
<w:t>'.$tovarname[$i][0].'</w:t></w:r><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

<w:tc><w:tcPr><w:tcW w:w="425" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:r>
<w:t>'.$tovarname[$i][1].'</w:t></w:r><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

<w:tc><w:tcPr><w:tcW w:w="992" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:r>
<w:t>'.$tovarname[$i][2].'</w:t></w:r><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

<w:tc><w:tcPr><w:tcW w:w="1134" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:proofErr w:type="spellStart"/><w:r>
<w:t>'.round($tovarname[$i][3]).'</w:t></w:r><w:proofErr w:type="spellEnd"/><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

<w:tc><w:tcPr><w:tcW w:w="1134" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:r>
<w:t>'.round($tovarname[$i][4]).'</w:t></w:r><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc>

</w:tr>
'; }


//узнаем всю сумму заказа
$tabletrallsumma = '
<w:tr w:rsidR="00562E87" w:rsidTr="005268E3">
<w:tc><w:tcPr><w:tcW w:w="9072" w:type="dxa"/><w:gridSpan w:val="5"/><w:tcBorders><w:left w:val="nil"/><w:bottom w:val="nil"/></w:tcBorders></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="00562E87" w:rsidRDefault="00562E87" w:rsidP="00562E87"><w:pPr><w:jc w:val="right"/><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00562E87"><w:rPr><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>Всего</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1134" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00562E87" w:rsidRPr="005268E3" w:rsidRDefault="005268E3" w:rsidP="005268E3"><w:pPr><w:jc w:val="center"/><w:rPr><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r><w:r>
<w:t>'.round($sum).'</w:t></w:r><w:r><w:rPr><w:lang w:val="en-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc></w:tr>
';


$template = file_get_contents("word.xml");
$template = str_replace("{table-shapka}", $tableshapka, $template);             //строим шапку
$template = str_replace("{table-tr-tovari}", $tabletrtovari, $template);        //строим строки с товарами
$template = str_replace("{table-tr-allsumma}", $tabletrallsumma, $template);    //строим цену товаров всего с доставкой
//echo $template;

file_put_contents("zakaz.doc", $template);                                      //записываем результат в файл

//автоматом скачивает файл
if (!isset($_GET['download'])) {echo '<script type="text/javascript">document.location.href="' . $_SERVER['REQUEST_URI'] . '?download"</script>';}

}
?>