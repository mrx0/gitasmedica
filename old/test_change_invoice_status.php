<?php

//смена статусов нарядов (переход на новую систему 2018.10.17)

	include_once '../DBWork.php';

    $rez = array();
    $rez2 = array();

	$msql_cnnct = ConnectToDB();

    $query = "UPDATE `journal_invoice` SET `status`='0', `closed_time`='0' WHERE `status` <> '9'";

    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

	$query = "UPDATE `journal_invoice` SET `status`='5', `closed_time`=`create_time` WHERE `summ` = `paid` AND `summ` <> '0' AND `summins` = '0' AND `status` <> '9'";

    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

	//$query = "UPDATE `journal_invoice` SET `status`='0', `closed_time`='0' WHERE `summ`='0' AND `summins` = '0'";

    //mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $query = "UPDATE `journal_invoice` SET `status`='5', `closed_time`=`create_time` WHERE `summins` <> '0' AND `status` <> '9'";

    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    //$query = "UPDATE `journal_invoice` SET `status`='0', `closed_time`='0' WHERE `summ` <> `paid` AND `summins` = '0'";

    //mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

	echo 'Ok!!! ThE eNd';

?>