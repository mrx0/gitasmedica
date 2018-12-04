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

var_dump($rez);

?>