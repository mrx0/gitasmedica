<?php

//test_fl_createDailyReport.php

include_once 'DBWork.php';

$rez = array();

$msql_cnnct = ConnectToDB ();

$query = "SELECT * FROM `fl_journal_daily_report` WHERE `nal`='0' OR `beznal` = '0'";

$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

$number = mysqli_num_rows($res);

if ($number != 0){
    while ($arr = mysqli_fetch_assoc($res)){
        array_push($rez, $arr);
    }
}

//var_dump($rez);

foreach ($rez as $item) {

    var_dump($item['id']);

    $nal = 0;
    $beznal = 0;

    $nal = $item['cashbox_nal'] + $item['cashbox_cert_nal'] + $item['temp_orto_nal'] + $item['temp_specialist_nal'] + $item['temp_analiz_nal'] + $item['temp_solar_nal'] - $item['temp_giveoutcash'];
    $beznal = $item['cashbox_beznal'] + $item['cashbox_cert_beznal'] + $item['temp_orto_beznal'] + $item['temp_specialist_beznal'] + $item['temp_analiz_beznal'] + $item['temp_solar_beznal'];

    var_dump($nal);
    var_dump($beznal);

    echo $nal . ' + ' . $beznal . ' = ' . $item['summ'];

    var_dump($item['arenda']);

    echo $nal . ' + ' . $beznal . ' + ' . $item['arenda'] . ' = ' . $item['itogSumm'];
    
}

?>