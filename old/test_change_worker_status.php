<?php

//переделываю статус fired=1 в status=8

	include_once '../DBWork.php';

    $rez = array();
    $rez2 = array();

	$msql_cnnct = ConnectToDB();

    $query = "UPDATE `spr_workers` SET `status`='8' WHERE `fired` = '1'";

    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

	echo 'Ok!!! ThE eNd';

?>